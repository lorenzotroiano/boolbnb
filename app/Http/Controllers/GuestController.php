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
        $apartments = Apartment::with('services')->get();
        $services = Service::all();

        return response()->json([
            'apartments' => $apartments,
            'services' => $services
        ]);
    }

    public function show($id)
    {
        $apartment = Apartment::with('services')->findOrFail($id);
        $apartmentsponsors = ApartmentSponsor::all();

        // NUOVA VISUALIZZAZIONE
        $view = new View();
        $view->apartment_id = $id;
        $view->IP_address = request()->ip();
        $view->date = now();
        $view->save();

        return response()->json([
            'apartment' => $apartment,
            'apartmentsponsors' => $apartmentsponsors
        ]);
    }
}
