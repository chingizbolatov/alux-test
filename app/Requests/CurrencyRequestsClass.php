<?php

namespace App\Requests;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class CurrencyRequestsClass
{
    public function getCurrencyList()
    {
        return $this->sendRequest('currency_list');
    }

    public function getCurrencyRatesList($pairs)
    {

        return $this->sendRequest('rates&pairs='. $pairs .'');
    }

    private function sendRequest($param)
    {
        $client = new Client();
        $url = 'https://currate.ru/api/?get=' . $param . '&key=50c3f1a74a7bfe3ee21426d4f7ee16f5';
        $response = $client->request('GET', $url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]
        );

        $response_body = [];

        if ($response->getStatusCode() == 200) {
            $response_body = json_decode($response->getBody()->getContents(), true);
            $response_body = $response_body['data'];
        } else {
            Log::warning("Bad request");
        }

        return $response_body;
    }
}
