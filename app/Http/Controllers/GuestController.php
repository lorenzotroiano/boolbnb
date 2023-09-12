<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Importo il model
use App\Models\Apartment;
use App\Models\Service;
use App\Models\View;
use App\Models\ApartmentSponsor;

class GuestController extends Controller
{
    public function index()
    {
        $apartments = Apartment::all();



        return response()->json($apartments);
    }

    public function indexService()
    {

        $services = Service::all();

        return response()->json($services);
    }

    public function show($id)
    {
        $apartment = Apartment::findOrFail($id);
        $apartmentsponsors = ApartmentSponsor::all();

        // NUOVA VISUALIZZAZIONE
        $view = new View();
        $view->apartment_id = $id;
        $view->IP_address = request()->ip(); // Ottieni l'indirizzo IP del visitatore
        $view->date = now(); // Imposta la data corrente
        $view->save();



        return response()->json([
            'apartment' => $apartment,
            'apartmentsponsors' => $apartmentsponsors
        ]);
    }
}
