<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class NetworkHelper
{
    public static function isOnline(): bool
    {
        try {
            $response = self::client()->timeout(3)->get('https://www.google.com/generate_204');
            if ($response->successful()) {
                return true;
            }
            Log::warning('Network check failed: non-200 from connectivity check.');
            return false;
        } catch (\Throwable $e) {
            Log::error('Network check exception: ' . $e->getMessage());
            return false;
        }
    }

    public static function client()
    {
        $http = Http::timeout(60);
        $certPath = storage_path('certs/cacert.pem');
        if (file_exists($certPath)) {
            return $http->withOptions(['verify' => $certPath]);
        }
        return $http->withoutVerifying();
    }
}
