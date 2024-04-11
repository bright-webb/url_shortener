<?php
namespace App\Services;

use GuzzleHttp\Client;

class SafeBrowsingService {
    protected $client;
    protected $apiKey;

    public function __construct() {
        $this->client = new Client([
            'base_uri' => 'https://www.virustotal.com/vtapi/v2/',
        ]);
        $this->apiKey = env('VIRUSTOTAL_API_KEY');
    }

    public function scanURL($url){
        $response = $this->client->post('url/scan', [
            'form_params' => [
                'apikey' => $this->apiKey,
                'url' => $url,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getReport($resource){
        $response = $this->client->get('url/report', [
            'query' => [
                'apikey' => $this->apiKey,
                'resource' => $resource,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

}