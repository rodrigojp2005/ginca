<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MapsController extends Controller
{
    public function proxy(Request $request)
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/js', [
            'key' => config('services.google_maps.key'),
            'libraries' => $request->get('libraries', 'geometry,streetView,places'),
            'callback' => $request->get('callback', 'initMap')
        ]);
        
        return response($response->body(), 200)
            ->header('Content-Type', 'application/javascript');
    }
}
