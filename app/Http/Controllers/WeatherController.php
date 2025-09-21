<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class WeatherController extends Controller
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
    }

    public function getThunderstormAdvisory()
    {
        return $this->fetchAdvisory(
            "https://www.pagasa.dost.gov.ph/regional-forecast/minprsd",
            "#thunderstorms > div"
        );
    }

    public function getRainfallAdvisory()
    {
        return $this->fetchAdvisory(
            "https://www.pagasa.dost.gov.ph/regional-forecast/minprsd",
            "#rainfalls > div",
            "No rainfall advisory found."
        );
    }

    /**
     * Generic fetcher for advisory sections
     */
    private function fetchAdvisory(string $url, string $selector)
    {
        try {
            $response = $this->client->get($url);
            $html = (string) $response->getBody();

            $crawler = new Crawler($html);
            $node = $crawler->filter($selector);

            if ($node->count() > 0) {
                return response()->json([
                    "status" => "success",
                    "message" => trim(strip_tags($node->text()))
                ]);
            }

            return response()->json([
                "status" => "success",
                "message" => "No advisory found in the given section."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Unable to fetch advisory data. Please try again later."
            ], 500);
        }
    }
}
