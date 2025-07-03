<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class NetworkHelper
{
    public static function isOnline(): bool
    {
        try {
            $response = Http::timeout(3)->get('https://www.google.com');


            if ($response->successful()) {
                return true;
            }

            Log::warning('Network check failed: Received non-200 from Google.');
            return false;
        } catch (\Exception $e) {
            Log::error('Network check exception: ' . $e->getMessage());
            return false;
        }
    }
}
