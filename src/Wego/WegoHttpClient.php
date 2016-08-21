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
    private $url;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $ts_code;

    /**
     * Client constructor.
     *
     * @param string     $key
     * @param string     $ts_code
     * @param string     $url
     * @param HttpClient $http
     */
    public function __construct(
        string $key,
        string $ts_code,
        string $url = 'http://api.wego.com',
        HttpClient $http = null
    ) {
        $this->key = $key;
        $this->ts_code = $ts_code;
        $this->url = $url;
        $this->http = $http ?: new HttpClient();
    }

    /**
     * Do HTTP GET
     *
     * @param  string $uri
     * @param  array  $query
     * @return array Parsed JSON response
     */
    public function get(string $uri, array $query)
    {
        $query['api_key'] = $this->key;
        $query['ts_code'] = $this->ts_code;
        $full_url = $this->url . $uri . '?' . http_build_query($query);
        $response = $this->http->get($full_url);
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

    /**
     * Do HTTP POST
     *
     * @param  string $uri
     * @param  array  $request
     * @return array Parsed JSON response
     */
    public function post(string $uri, array $request)
    {
        $query = [
            'api_key' => $this->key,
            'ts_code' => $this->ts_code,
        ];
        $full_url = $this->url . $uri . '?' . http_build_query($query);
        $response = $this->http->post($full_url, json_encode($request));
        return $this->parseResponse($response);
    }
}
