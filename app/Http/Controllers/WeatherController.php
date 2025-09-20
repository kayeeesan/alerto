<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class WeatherController extends Controller
{
    public function getThunderstormAdvisory()
    {
        try {
            $client = new \GuzzleHttp\Client([
                'timeout' => 30,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]
            ]);
            
            $url = "https://www.pagasa.dost.gov.ph/regional-forecast/minprsd"; 
            $response = $client->get($url);
            $html = (string) $response->getBody();

            $advisory = $this->extractThunderstormAdvisory($html);

            if ($advisory) {
                return response()->json([
                    "status" => "success",
                    "message" => trim(strip_tags($advisory))
                ]);
            } else {
                return response()->json([
                    "status" => "success",
                    "message" => "As of today, there is no Thunderstorm Advisory Issued."
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Unable to fetch advisory data. Please try again later."
            ], 500);
        }
    }

    private function extractThunderstormAdvisory($html)
    {
        // Pattern 1: Look for "Thunderstorm Watch" with timestamp
        if (preg_match('/Thunderstorm Watch.*?Issued at:.*?\d{1,2}:\d{2} (AM|PM),.*?\d{1,2} \w+ \d{4}.*?Thunderstorm.*?MORE LIKELY.*?within.*?hours/si', $html, $matches)) {
            return $matches[0];
        }

        // Pattern 2: Look for thunderstorm advisory in div content
        if (preg_match('/<div[^>]*class="[^"]*field-item[^"]*"[^>]*>.*?Thunderstorm.*?(?:Watch|Advisory).*?Issued.*?\d.*?<\/div>/si', $html, $matches)) {
            return strip_tags($matches[0]);
        }

        // Pattern 3: Look for any thunderstorm mention with timing
        if (preg_match('/Thunderstorm.*?(?:likely|possible|expected).*?(?:within|for).*?\d+.*?hours/si', $html, $matches)) {
            return $matches[0];
        }

        // Pattern 4: Look for the specific content structure
        // if (preg_match('/Issued at:.*?\d{1,2}:\d{2} (AM|PM),.*?\d{1,2} \w+ \d{4}.*?Thunderstorm.*?MORE LIKELY.*?Greater Metro Manila Area/si', $html, $matches)) {
        //     return $matches[0];
        // }

        return null;
    }

    
}