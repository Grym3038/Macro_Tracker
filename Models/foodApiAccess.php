<?php

class FoodApiAccess
{
    private string $baseUrl;
    private string $apiKey;
    private array  $defaultHeaders;

    public function __construct()
    {
        $this->baseUrl = 'https://api.nal.usda.gov/fdc/v1';
        $this->apiKey  = 'sQgXROR2bo2d0seQZuplJdxcHsLXrwkgGysR5afW';
        $this->defaultHeaders = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
        ];
    }


    private function request(string $endpoint, string $method = 'GET', array $body = [], array $headers = []): array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($this->defaultHeaders, $headers));

        if (in_array($method, ['POST','PUT','PATCH'], true)) {
            $payload = json_encode($body, JSON_THROW_ON_ERROR);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON decode error: ' . json_last_error_msg());
        }

        if ($status < 200 || $status >= 300) {
            $message = $decoded['error']['message'] ?? $decoded['message'] ?? 'Unknown API error';
            throw new \Exception("API returned HTTP $status: $message");
        }

        return $decoded;
    }


    public function listFoods(
        int   $pageSize = 25,
        int   $pageNumber = 1,
        string $sortBy = 'dataType.keyword',
        string $sortOrder = 'asc'
    ): array {

        $endpoint = '/foods/list?api_key=' . urlencode($this->apiKey);
    
        $body = [
            'dataType'   => ['Foundation','SR Legacy', 'Survey', 'Branded'],
            'pageSize'   => $pageSize,
            'pageNumber' => $pageNumber,
            'sortBy'     => $sortBy,
            'sortOrder'  => $sortOrder,
            'format'       => 'abridged',
            'nutrients'    => [208, 204, 303, 291, 203, 539, 606, 605, 601],
        ];
    
        return $this->request($endpoint, 'POST', $body);
    }


    public function searchFood(
        string $query,
        int    $pageSize    = 25,
        int    $pageNumber  = 1,
        string $sortBy      = 'dataType.keyword',
        string $sortOrder   = 'asc'
    ): array {
        $params = [
            'query'      => $query,
            'dataType'     => ['Foundation', 'Branded'],
            'pageSize'   => $pageSize,
            'pageNumber' => $pageNumber,
            'sortBy'     => $sortBy,
            'sortOrder'  => $sortOrder,
            'api_key'    => $this->apiKey,
        ];
    
        $endpoint = '/foods/search?' . http_build_query($params);
    
        return $this->request($endpoint, 'GET');
    }

    public function getFoods(
            array $fdcIds
        ): array
    {
        // build endpoint with your api_key
        $endpoint = '/foods?api_key=' . urlencode($this->apiKey);

        // assemble JSON payload
        $body = [
            'fdcIds'    => $fdcIds,
            'format'    => 'abridged',
            'nutrients' => [203, 204, 205]
        ];

        return $this->request($endpoint, 'POST', $body);
    }

    public function getFood(
        int $fdcId
        ): array
    {
        $nutrientParams = implode('&', array_map(
            fn(int $n) => "nutrients={$n}",
            [208, 204, 303, 291, 203, 539, 606, 605, 601]
        ));

        // assemble endpoint with nutrients and API key
        $endpoint = "/food/{$fdcId}?{$nutrientParams}api_key=" . urlencode($this->apiKey);

        // fire the GET request
        return $this->request($endpoint, 'GET');
    }

}