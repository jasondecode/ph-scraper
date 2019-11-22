<?php
namespace App\Services\Scraper\Http;

use GuzzleHttp\Client;

class GuzzleClient
{   
    /** @var \GuzzleHttp\Client */
    private $client;

    public function create($headers = [])
    {
        $headers = [
            "user-agent" => config('scraper.user_agent'),
            ":authority" => "www.producthunt.com",
            ":path" => "/frontend/graphql",
            ":scheme" => "https",
            "origin" => "https://www.producthunt.com",
            "referer" => "https://www.producthunt.com/",
            "x-requested-with" => "XMLHttpRequest",
            ":method" => "POST",
            "content-type" => "application/json",
            "pragma" => "no-cache",
            "accept" => "*/*"
        ];

        $this->client = new Client([
            'headers' => $headers,
            'cookies' => new \GuzzleHttp\Cookie\CookieJar,            
        ]);

        return $this;
    }

    public function get($url, $options = [])
    {   
        $clientResponse = $this->client->get($url, [
            'query' => $options
        ]);

        return $clientResponse->getBody()->getContents();
    }

    public function post($url, $options)
    {
        $clientResponse = $this->client->post($url, [
            'body' => $options
        ]);

        return $clientResponse->getBody()->getContents();
    }
}
