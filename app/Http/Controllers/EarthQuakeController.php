<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class EarthQuakeController extends Controller
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

    public function getEarthQuakeData()
    {
        try {
            $url = "https://earthquake.phivolcs.dost.gov.ph/";
            $response = $this->client->get($url, ['verify' => false]);

            $html = (string) $response->getBody();

            $crawler = new Crawler($html);

            // select rows inside the earthquake table
            $rows = $crawler->filter('table.MsoNormalTable tr')->each(function (Crawler $row, $i) {
                // skip header row
                if ($i === 0) return null;

                $cols = $row->filter('td')->each(function (Crawler $col) {
                    return trim($col->text());
                });

                if (count($cols) < 6) return null;

                return [
                    'datetime' => $cols[0] ?? '',
                    'latitude' => $cols[1] ?? '',
                    'longitude' => $cols[2] ?? '',
                    'depth'    => $cols[3] ?? '',
                    'magnitude'=> $cols[4] ?? '',
                    'location' => $cols[5] ?? '',
                ];
            });

            // remove null rows
            $data = array_values(array_filter($rows));

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
