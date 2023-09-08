<?php

namespace App\Http\Controllers\Apartment;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Apartment;
use App\Models\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApartmentController extends Controller
{

    public function create()
    {
        $services = Service::all();
        return view('create', compact('services'));
    }

    public function store(Request $request)
    {
        // Recupera i dati dal form inviato
        $data = $request->validate(
            $this->getValidations(),
            $this->getValidationsMessage()
        );

        $img_path = Storage::put('uploads', $data['cover']);
        $data['cover'] = $img_path;
        // Chiamata API per ottenere latitudine e longitudine
        $address = $data['address'];
        $apiKey = env('TOMTOM_API_KEY');
        $endpoint = "https://api.tomtom.com/search/2/geocode/" . urlencode($address) . ".json?key={$apiKey}";

        $response = Http::get($endpoint);

        if ($response->successful()) {
            $apiData = $response->json();
            if (!empty($apiData['results'])) {
                $position = $apiData['results'][0]['position'];
                $data['latitude'] = $position['lat'];
                $data['longitude'] = $position['lon'];
            } else {
                // Potresti voler gestire l'errore: l'indirizzo non ha restituito risultati
                return redirect()->back()->with('error', 'Indirizzo non trovato.');
            }
        } else {
            // Potresti voler gestire l'errore: chiamata API fallita
            return redirect()->back()->with('error', 'Errore nel recupero delle coordinate.');
        }

        // Imposta il user_id dell'appartamento con l'id dell'utente autenticato
        $data['user_id'] = Auth::id();

        // Creazione dell'appartamento e associazione dei servizi
        $apartment = Apartment::create($data);
        $apartment->services()->attach($data['services']);

        // Reindirizza all'URL della vista 'show' per visualizzare l'appartamento appena creato
        return redirect()->route('show', $apartment->id);
    }

    public function edit($id)
    {
        $apartment = Apartment::findOrFail($id);

        $services = Service::all();
        return view("edit", compact("apartment", "services"));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(
            $this->getValidations(),
            $this->getValidationsMessage()
        );
        $apartment = Apartment::findOrFail($id);


        if (!array_key_exists("cover", $data))
            $data['cover'] = $apartment->cover;
        else {
            if ($apartment->cover) {

                $oldImgPath = $apartment->cover;
                Storage::delete($oldImgPath);
            }

            $data['cover'] = Storage::put('uploads', $data['cover']);
        }

        $apartment->update($data);
        $apartment->services()->sync($data['services']);
        return redirect()->route('show', $apartment->id);
    }

    public function getValidations()
    {
        return [
            "name" => "required|string|min:3",
            "description" => "nullable|string",
            "room" => "required|int|min:1|max:20",
            "bathroom" => "required|int|max:4",
            "mq" => "required|int|min:8",
            "address" => "required|string",
            "visible" => "required|boolean",
            "cover" => "image",
            "services" => "required|array|exists:services,id"
        ];
    }

    public function getValidationsMessage()
    {
        return [
            "name.required" => "Il campo Nome è obbligatorio.",
            "name.string" => "Il campo Nome deve essere una stringa.",
            "name.min" => "Il campo Nome deve contenere almeno 3 caratteri.",
            "description.string" => "Il campo Descrizione deve essere una stringa.",
            "room.required" => "Il campo Stanza è obbligatorio.",
            "room.int" => "Il campo Stanza deve essere un numero intero.",
            "room.min" => "Il campo Stanza deve essere almeno 4.",
            "room.max" => "Il campo Stanza non deve essere superiore a 20.",
            "bathroom.required" => "Il campo Bagno è obbligatorio.",
            "bathroom.int" => "Il campo Bagno deve essere un numero intero.",
            "bathroom.max" => "Il campo Bagno non deve essere superiore a 4.",
            "mq.required" => "Il campo Metri Quadrati (mq) è obbligatorio.",
            "mq.int" => "Il campo Metri Quadrati (mq) deve essere un numero intero.",
            "mq.min" => "Il campo Metri Quadrati (mq) deve essere almeno 10.",
            "address.required" => "Il campo Indirizzo è obbligatorio.",
            "address.string" => "Il campo Indirizzo deve essere una stringa.",
            "visible.required" => "Il campo visible è obbligatorio.",
            "visible.boolean" => "Il campo visible deve valore SI o NO.",
            // "cover.required" => "Il campo Copertina è obbligatorio.",
            "cover.image" => "Il campo Copertina deve essere un'immagine.",
            "services.required" => "Il campo Servizi è obbligatorio.",
            "services.array" => "Il campo Servizi deve essere un array.",
            "services.exists" => "Il campo Servizi contiene valori non validi."
        ];
    }
}
