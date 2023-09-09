<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Importo il model
use App\Models\Apartment;
use App\Models\View;

class GuestController extends Controller
{
    public function index()
    {
        $apartments = Apartment::all();
        return view("home", compact("apartments"));
    }

    public function show($id)
    {
        $apartment = Apartment::findOrFail($id);

        // Registra una nuova visualizzazione
        $view = new View();
        $view->apartment_id = $id;
        $view->IP_address = request()->ip(); // Ottieni l'indirizzo IP del visitatore
        $view->date = now(); // Imposta la data corrente
        $view->save();


        return view("show", compact("apartment"));
    }
}
