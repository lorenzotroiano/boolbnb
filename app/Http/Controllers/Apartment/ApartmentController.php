<?php

namespace App\Http\Controllers\Apartment;

use App\Models\ApartmentSponsor;
use App\Models\Image;
use App\Models\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Apartment;
use App\Models\Message;
use App\Models\Service;
use App\Models\Sponsor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApartmentController extends Controller
{

    // DASHBOARD
    public function dashboard()
    {
        $apartments = Apartment::all();
        $sponsors = Sponsor::all();
        return view("dashboard", compact("apartments", 'sponsors'));
    }


    // MESSAGE
    public function sendMessage(Request $request, $id)
    {
        // Se l'utente è autenticato
        if (Auth::check()) {
            $user = Auth::user();
            $data = $request->validate([
                'body' => 'required|string',
            ]);
            $data['name'] = $user->name;
            $data['email'] = $user->email;
        } else {
            // Se l'utente non è autenticato
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'body' => 'required|string',
            ]);
        }

        $data['apartment_id'] = $id;

        Message::create($data);

        return redirect()->back()->with('success', 'Messaggio inviato con successo!');
    }


    // CREATE
    public function create()
    {
        // Recupera tutti i record dalla tabella 'Service' nel database
        $services = Service::all();

        // Restituisce la vista 'create' e passa l'elenco dei servizi alla vista
        return view('create', compact('services'));
    }


    // STORE
    public function store(Request $request)
    {
        // Recupera i dati dal form inviato e applica le regole di validazione definite
        $data = $request->validate(
            $this->getValidations(),
            $this->getValidationsMessage()
        );

        // Carica l'immagine del copertina e memorizza il percorso
        $img_path = Storage::put('apartments', $data['cover']);
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

        // Carica le immagini extra
        foreach ($request->file('images') as $image) {
            $path = $image->store('images', 'public');
            
            // Creazione di un nuovo record Image e associarlo all'appartamento corrente
            $newImage = new Image();
            $newImage->image = $path;
            $apartment->images()->save($newImage);
        }

        // Associa i servizi selezionati all'appartamento
        $apartment->services()->attach($data['services']);

        // Reindirizza all'URL della vista 'show' per visualizzare l'appartamento appena creato
        return redirect()->route('show', $apartment->id);
    }


    // Show the sponsorship form
    public function showSponsorshipForm($id)
    {
        $apartment = Apartment::findOrFail($id);
        $sponsors = Sponsor::all();
        return view('sponsor', compact('apartment', 'sponsors'));
    }

    // Process the sponsorship form
    public function applySponsorship(Request $request, $id)
    {
        $apartment = Apartment::findOrFail($id);
        $sponsor = Sponsor::findOrFail($request->sponsor_id);

        // Prendi l'ultima sponsorizzazione per l'appartamento
        // Ottieni tutte le sponsorizzazioni per l'appartamento
        $sponsorships = $apartment->sponsors()->withPivot('end_date')->get();

        // Ordina la collezione in base alla end_date
        $lastSponsorship = $sponsorships->sortByDesc(function ($sponsorship) {
            return $sponsorship->pivot->end_date;
        })->first();


        // Se esiste una sponsorizzazione e la sua end_date è nel futuro,
        // usa quella come punto di partenza. Altrimenti, usa l'ora corrente.
        $now = ($lastSponsorship && $lastSponsorship->pivot->end_date > Carbon::now())
            ? Carbon::parse($lastSponsorship->pivot->end_date)
            : Carbon::now();

        $end_date = $now->copy()->addHours($sponsor->duration);

        $apartment->sponsors()->attach($sponsor->id, [
            'start_date' => $now,
            'end_date' => $end_date,
        ]);

        // Se la nuova end_date della sponsorizzazione è nel futuro, allora aggiorna la flag sponsor a 1
        if ($end_date > Carbon::now()) {
            $apartment->sponsor = 1;
            $apartment->save();
        }

        return redirect()->route('dashboard')->with('success', 'Sponsorship applied successfully!');
    }




    // AUTOCOMPLETE
    public function searchAddress(Request $request)
    {
        $query = $request->input('query');
        $apiKey = env('TOMTOM_API_KEY');
        $endpoint = "https://api.tomtom.com/search/2/search/{$query}.json?key={$apiKey}&language=en-US";

        $response = Http::get($endpoint);

        if ($response->successful()) {
            return $response->json();
        } else {
            return response()->json([], 500);
        }
    }




    public function show($id)
    {
        $apartment = Apartment::with(['services', 'messages'])->findOrFail($id);
        $apartmentsponsors = ApartmentSponsor::all();

        // Ottieni l'IP dell'utente
        $ip = request()->ip();

        // Verifica se è passato abbastanza tempo dalla precedente visualizzazione
        $lastView = View::where('apartment_id', $id)
            ->where('IP_address', $ip)
            ->where('created_at', '>', Carbon::now()->subHours(3))
            ->first();

        // Se non è stata trovata una visualizzazione recente, allora crea una nuova visualizzazione
        if(!$lastView) {
            $view = new View();
            $view->apartment_id = $id;
            $view->IP_address = $ip;
            $view->date = now();
            $view->save();
        }

        return view("show", compact("apartment", "apartmentsponsors"));
    }


    // EDIT
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


    // UPDATE
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
            $data['cover'] = Storage::put('apartments', $data['cover']);
        }

        // Gestione delle nuove immagini
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = Storage::put('apartment_images', $image);
                $apartment->images()->create(['path' => $path]);
            }
        }
        // Aggiorna l'appartamento con i dati validati
        $apartment->update($data);

        // Sincronizza i servizi associati all'appartamento
        $apartment->services()->sync($data['services']);

        // Reindirizza all'URL della vista 'show' per visualizzare l'appartamento appena aggiornato
        return redirect()->route('show', $apartment->id);
    }



    // DELETE
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

        foreach ($apartment->images as $image) {
            Storage::delete($image->path);
            $image->delete();
        }

        // Elimina l'appartamento dal database
        $apartment->delete();

        $apartment->delete();
        return redirect()->route('dashboard');
    }



    // DELETE PICTURE
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



    // VALIDATION
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
            "services" => "required|array|exists:services,id",
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:10000',
            'images' => 'array|max:3',
        ];
    }


    // MESSAGE VALIDATION
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
