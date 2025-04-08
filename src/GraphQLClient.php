<?php

namespace LiveHeats;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GraphQLClient
{
    private Client $client;
    private array $headers;

    public function __construct(string $endpoint, array $headers = [])
    {
        $this->client = new Client(['base_uri' => $endpoint]);
        $this->headers = array_merge(['Content-Type' => 'application/json'], $headers);
    }

    public function query(string $query, array $variables = []): array
    {
        try {
            $response = $this->client->post('', [
                'headers' => $this->headers,
                'json' => [
                    'query' => $query,
                    'variables' => $variables,
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['errors'])) {
                throw new \Exception("GraphQL error: " . json_encode($body['errors']));
            }

            return $body['data'];
        } catch (RequestException $e) {
            throw new \Exception("HTTP Request failed: " . $e->getMessage());
        }
    }
}
