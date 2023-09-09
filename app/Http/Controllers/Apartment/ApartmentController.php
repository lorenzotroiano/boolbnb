<?php

namespace App\Http\Controllers\Apartment;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Apartment;
use App\Models\Message;
use App\Models\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApartmentController extends Controller
{
    public function dashboard()
    {
        $apartments = Apartment::all();
        return view("dashboard", compact("apartments"));
    }

    public function create()
    {
        // Recupera tutti i record dalla tabella 'Service' nel database
        $services = Service::all();

        // Restituisce la vista 'create' e passa l'elenco dei servizi alla vista
        return view('create', compact('services'));
    }

    public function store(Request $request)
    {
        // Recupera i dati dal form inviato e applica le regole di validazione definite
        $data = $request->validate(
            $this->getValidations(),
            $this->getValidationsMessage()
        );

        // Carica l'immagine del copertina e memorizza il percorso
        $img_path = Storage::put('uploads', $data['cover']);
        $data['cover'] = $img_path;

        // Chiamata API per ottenere latitudine e longitudine basata sull'indirizzo
        $address = $data['address'];
        $apiKey = env('TOMTOM_API_KEY');
        $endpoint = "https://api.tomtom.com/search/2/geocode/" . urlencode($address) . ".json?key={$apiKey}";

        $response = Http::get($endpoint);

        // Verifica se la chiamata API è stata eseguita con successo
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

        // Crea un nuovo appartamento utilizzando i dati validati e associati
        $apartment = Apartment::create($data);

        // Associa i servizi selezionati all'appartamento
        $apartment->services()->attach($data['services']);

        // Reindirizza all'URL della vista 'show' per visualizzare l'appartamento appena creato
        return redirect()->route('show', $apartment->id);
    }

    public function edit($id)
    {
        // Trova l'appartamento con l'id fornito
        $apartment = Apartment::findOrFail($id);

        // Verifica se l'utente autenticato è il proprietario dell'appartamento
        if (auth()->user()->id !== $apartment->user_id) {
            // Se l'utente non è il proprietario, restituisce un errore 403 (Permesso negato)
            abort(403, 'Non hai il permesso di modificare questa casa.');
        }

        // Recupera tutti i servizi disponibili
        $services = Service::all();

        // Restituisce la vista "edit" e passa l'appartamento e l'elenco dei servizi alla vista
        return view("edit", compact("apartment", "services"));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(
            $this->getValidations(),
            $this->getValidationsMessage()
        );

        $apartment = Apartment::findOrFail($id);

        // Verifica se l'utente autenticato è l'owner dell'appartamento
        if (auth()->user()->id !== $apartment->user_id) {
            abort(403, 'Non hai il permesso di modificare questa casa.');
        }

        // Verifica se l'utente autenticato è il proprietario dell'appartamento
        if (auth()->user()->id !== $apartment->user_id) {
            // Se l'utente non è il proprietario, restituisce un errore 403 (Permesso negato)
            abort(403, 'Non hai il permesso di modificare questa casa.');
        }

        // Gestisce il caricamento dell'immagine di copertina
        if (!array_key_exists("cover", $data)) {
            // Se l'immagine di copertina non è stata modificata, utilizza l'immagine esistente
            $data['cover'] = $apartment->cover;
        } else {
            // Se l'immagine di copertina è stata modificata
            if ($apartment->cover) {
                // Elimina l'immagine di copertina precedente dallo storage
                $oldImgPath = $apartment->cover;
                Storage::delete($oldImgPath);
            }

            // Carica la nuova immagine di copertina e memorizza il percorso
            $data['cover'] = Storage::put('uploads', $data['cover']);
        }

        // Aggiorna l'appartamento con i dati validati
        $apartment->update($data);

        // Sincronizza i servizi associati all'appartamento
        $apartment->services()->sync($data['services']);

        // Reindirizza all'URL della vista 'show' per visualizzare l'appartamento appena aggiornato
        return redirect()->route('show', $apartment->id);
    }

    public function delete($id)
    {
        $apartment = Apartment::FindOrFail($id);

        if (auth()->user()->id !== $apartment->user_id) {
            abort(403, 'Non hai il permesso di modificare questa casa.');
        }

        // Rimuove l'associazione tra l'appartamento e i servizi
        $apartment->services()->detach();

        // Elimina le immagini, messaggi e visualizzazioni  associate all'appartamento
        $apartment->images()->delete();
        $apartment->messages()->delete();
        $apartment->views()->delete();

        // Elimina l'appartamento dal database
        $apartment->delete();

        $apartment->delete();
        return redirect()->route('dashboard');
    }

    public function deletePicture($id)
    {
        $apartment = Apartment::FindOrFail($id);
        $defaultPath = "defaultImage/logoboolbnb.jpeg";


        // Verifica se l'appartamento ha una copertina (immagine di copertina)
        if (!$apartment->cover == $defaultPath) {
            // Se l'appartamento ha una copertina, elimina l'immagine dallo storage
            $oldImgPath = $apartment->cover;
            Storage::delete($oldImgPath);
        }

        // Imposta il campo di copertina dell'appartamento su null e salva l'appartamento
        $apartment->cover = $defaultPath;
        $apartment->save();

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
