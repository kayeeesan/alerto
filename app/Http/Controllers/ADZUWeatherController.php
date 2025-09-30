<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ADZUWeatherController extends Controller
{
    public function getWeather()
    {
        $response = Http::get("https://api.weatherlink.com/v1/NoaaExt.json", [
            'user' => '001D0A002457',
            'pass' => 't@b1ngd@gat',
            'apiToken' => '3BFA895305E34127A232DF10508F51E6'
        ]);

        return $response->json();
    }
}
