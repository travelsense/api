<?php
namespace Api\Wego;

use PHPCurl\CurlHttp\HttpClient;
use PHPCurl\CurlHttp\HttpResponse;

class WegoHttpClient
{
    /**
     * @var HttpClient
     */
    private $http;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $tsCode;

    /**
     * Client constructor.
     *
     * @param string     $key
     * @param string     $tsCode
     * @param string     $apiUrl
     * @param HttpClient $http
     */
    public function __construct($key, $tsCode, $apiUrl = 'http://api.wego.com', HttpClient $http = null)
    {
        $this->key = $key;
        $this->tsCode = $tsCode;
        $this->apiUrl = $apiUrl;
        $this->http = $http ?: new HttpClient();
    }

    /**
     * Do HTTP GET
     *
     * @param  string $uri
     * @param  array  $query
     * @return array Parsed JSON response
     */
    public function get($uri, array $query)
    {
        $query['api_key'] = $this->key;
        $query['ts_code'] = $this->tsCode;
        $fullUrl = $this->apiUrl . $uri . '?' . http_build_query($query);
        $response = $this->http->get($fullUrl);
        return $this->parseResponse($response);
    }

    /**
     * Do HTTP POST
     *
     * @param  string $uri
     * @param  array  $request
     * @return array Parsed JSON response
     */
    public function post($uri, array $request)
    {
        $query = [
            'api_key' => $this->key,
            'ts_code' => $this->tsCode
        ];
        $fullUrl = $this->apiUrl . $uri . '?' . http_build_query($query);
        $response = $this->http->post($fullUrl, json_encode($request));
        return $this->parseResponse($response);
    }

    /**
     * @param HttpResponse $response
     * @return array
     */
    private function parseResponse(HttpResponse $response)
    {
        $json = json_decode($response->getBody(), true);
        if ($response->getCode() === 200) {
            return $json;
        }
        throw new WegoApiException(isset($json['error']) ? $json['error'] : 'Unknown error', $response->getCode());
    }
}