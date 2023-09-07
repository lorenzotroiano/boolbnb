<?php

namespace App\Http\Controllers\Apartment;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Apartment;
use App\Models\Image;
use App\Models\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApartmentController extends Controller
{
    public function index()
    {
        $apartments = Apartment::all();
        return view("home", compact("apartments"));
    }

    public function show($id)
    {
        $apartment = Apartment::findOrFail($id);

        return view("show", compact("apartment"));
    }


    public function create()
    {
        $services = Service::all();
        return view('create', compact('services'));
    }

    public function store(Request $request)
    {
        // Recupera i dati dal form inviato
        $data = $request->all();

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

        // Push per Matteo
    }
}
