<?php
/**
 * Created by PhpStorm.
 * User: geroduppel
 * Date: 03.03.18
 * Time: 12:57
 */

namespace App\EmbersClient;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client as GuzzleClient;

class Client
{
    protected $client;

    protected $authToken;

    protected $url = "https://data.embers.city/v2/";

    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;

        $config = [
            'base_uri' => $this->getUrl()
        ];

        $this->client = new GuzzleClient($config);
    }

    public function getPolutionData()
    {
        try {
            $response = $this->client->request(
                'GET',
                'entities',
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorzation' => $this->getApiKey()
                    ],
                    'query' => [
                        'type' => 'AirQualityObserved',
                    ],
                ]
            );

            return $this->extractJsonBody($response);
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getApiKey()
    {
        return $this->apiKey;
    }

    protected function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $response
     * @return mixed
     */
    protected function extractJsonBody($response)
    {
        return json_decode($response->getBody()->getContents());
    }
}
