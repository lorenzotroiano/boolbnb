<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Importo il model
use App\Models\Apartment;
use App\Models\Service;
use App\Models\View;
use App\Models\ApartmentSponsor;
use App\Models\Message;
use App\Models\Image;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $apartments = Apartment::with('services', 'images')
            ->when($request->get('name'), function ($query, $name) {
                return $query->where('name', 'LIKE', '%' . $name . '%');
            })
            ->when($request->get('room'), function ($query, $room) {
                if ($room <= 8) {
                    return $query->where('room', $room);
                } else {
                    return $query->where('room', '>', 8);
                }
            })
            ->when($request->get('bathroom'), function ($query, $bathroom) {
                return $query->where('bathroom', $bathroom);
            })
            ->when($request->get('mq'), function ($query, $mq) {
                return $query->where('mq', '<=', $mq);
            })
            ->when($request->get('services'), function ($query, $services) {
                $servicesArray = explode(',', $services);
                return $query->whereHas('services', function ($q) use ($servicesArray) {
                    $q->whereIn('services.id', $servicesArray);
                });
            })
            ->get();

        return response()->json($apartments);
    }

    public function indexService() {

        $services = Service::all();
        return response()->json($services);

    }

    public function show($id)
    {
        $apartment = Apartment::with('services', 'images')->findOrFail($id);
        $apartmentsponsors = ApartmentSponsor::all();

        // NUOVA VISUALIZZAZIONE
        $view = new View();
        $view->apartment_id = $id;
        $view->IP_address = request()->ip();
        $view->date = now();
        $view->save();

        return response()->json([
            'apartment' => $apartment,
            'apartmentsponsors' => $apartmentsponsors,
        ]);
    }

    public function sendMessage(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'body' => 'required|string',
        ]);

        $data['apartment_id'] = $id;

        Message::create($data);

        
        return response()->json([
            'success' => true,
            'message' => 'Messaggio inviato con successo!'
        ], 200);
    }
}
