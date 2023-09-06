<?php

namespace App\Http\Controllers\Apartment;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Image;
use App\Models\Service;

use Illuminate\Http\Request;

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
        // Recupera tutti i tipi e tutte le tecnologie dal database
        // $images = Image::all();
        $services = Service::all();

        // Carica la vista 'create' e passa i tipi e le tecnologie alla vista
        return view('create', compact('services'));
    }

    public function store(Request $request)
    {
        // Recupera i dati dal form inviato
        $data = $request->all();

        $apartment = Apartment::create($data);
        $apartment->services()->attach($data['services']);

        // $img_path = Storage::put('uploads', $data['images']);
        // $data['main_picture'] = $img_path;

        $apartment = Apartment::create($data);

        // Reindirizza all'URL della vista 'show' per visualizzare il progetto appena creato
        return redirect()->route('show', $apartment->id);
    }
}
