<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Importo il model
use App\Models\Apartment;

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

        return view("show", compact("apartment"));
    }
}
