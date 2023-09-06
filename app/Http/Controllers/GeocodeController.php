<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeocodeController extends Controller
{
    public function getCoordinates($address)
    {
        $apiKey = '2hSUhlhHixpowSvWwlyl6oARrDT01OsD';  // sostituisci con la tua chiave API
        $endpoint = "https://api.tomtom.com/search/2/geocode/" . urlencode($address) . ".json?key={$apiKey}";

        $response = Http::get($endpoint);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data['results'])) {
                return response()->json($data['results'][0]['position']);
            }
        }

        return response()->json(['error' => 'Unable to fetch coordinates'], 500);
    }
}
