<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class UrlCheckerService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function isUrlValid(string $url): bool
    {
        dump($url);
        try {
            $options = [
                'timeout' => 2,
                'max_redirects' => 1,
                'verify_peer' => false,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/121.0.0.0'
                ]
            ];

            // Pour Indeed, requête HEAD uniquement
            if (str_contains($url, 'indeed.com')) {
                $response = $this->httpClient->request('HEAD', $url, $options);
                return $response->getStatusCode() < 400;
            }

            // Pour les autres sites
            $response = $this->httpClient->request('HEAD', $url, $options);
            return $response->getStatusCode() < 400;
        } catch (\Exception $e) {
            return false;
        }
    }
}
