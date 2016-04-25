<?php
namespace Api\Test;

use PHPCurl\CurlHttp\HttpClient;
use PHPCurl\CurlHttp\HttpResponse;

class ApiClient
{
    /**
     * @var string
     */
    private $authToken;

    /**
     * @var string
     */
    private $host;

    private $http;

    /**
     * ApiClient constructor.
     *
     * @param string     $host
     * @param HttpClient $http
     */
    public function __construct($host, HttpClient $http = null)
    {
        $this->http = $http ?: new HttpClient();
        $this->host = $host;
    }

    /**
     * @param string $authToken
     */
    public function setAuthToken($authToken)
    {
        $this->authToken = $authToken;
    }

    /**
     * Register new user
     *
     * @param  array $user (firstName, lastName, email, password, picture)
     * @return object
     *
     * Example: $client->registerUser([
     *  'firstName' => 'Alexander'
     *  'lastName'=> 'Pushkin',
     *  'email' => 'sashs@nashe-vse.ru',
     *  'password' => 'd4n73s l0h',
     * ]);
     */
    public function registerUser(array $user)
    {
        return $this->post('/user', $user);
    }

    /**
     * @param string $email
     * @param string $password
     * @return string Auth token
     */
    public function getTokenByEmail($email, $password)
    {
        return $this->post('/token', ['email' => $email, 'password' => $password])
            ->token;
    }

    /**
     * @param string $fbToken
     * @return string Auth token
     */
    public function getTokenByFacebook($fbToken)
    {
        return $this->post('/token', ['fbToken' => $fbToken])
            ->token;
    }

    public function confirmEmail($email)
    {
        return $this->post('/email/confirm/' . urlencode($email));
    }

    public function requestPasswordReset($email)
    {
        return $this->post('/password/link/' . urlencode($email));
    }

    public function updatePassword($token, $password)
    {
        return $this->post('/password/reset/' . urlencode($token), ['password' => $password]);
    }

    /**
     * Get current user info
     *
     * @return object
     */
    public function getCurrentUser()
    {
        return $this->get('/user');
    }

    /**
     * Update user data
     *
     * @param array $request
     * @return object
     */
    public function updateUser(array $request)
    {
        return $this->put('/user', $request);
    }

    public function getCabEstimates($lat1, $lon1, $lat2, $lon2)
    {
        return $this->get("/cab/$lat1/$lon1/$lat2/$lon2");
    }

    /**
     * start search
     *
     * @param  int    $location wego location id
     * @param  string $in       yyyy-mm-dd
     * @param  string $out      yyyy-mm-dd
     * @param  int    $rooms
     * @return int wego search id
     */
    public function startHotelSearch($location, $in, $out, $rooms)
    {
        return $this->post("/hotel/search/$location/$in/$out/$rooms");
    }

    /**
     * get search results
     *
     * @param  int $id   wego search id
     * @param  int $page page number
     * @return array
     */
    public function getHotelSearchResults($id, $page = 1)
    {
        return $this->get("/hotel/search-results/$id/$page");
    }

    /**
     * Create a new Travel
     * @param array $travel
     * @return int
     */
    public function createTravel(array $travel)
    {
        return $this->post('/travel', $travel)->id;
    }

    /**
     * Create a new Comment
     *
     * @param int    $id
     * @param string $text
     * @return int
     */
    public function addTravelComment($id, $text)
    {
        return $this->post(sprintf('/travel/%s/comment', urlencode($id)), [
            'travel_id' => $id,
            'text'      => $text,
        ])->id;
    }
    
    public function getTravelComments(int $id, int $limit, int $offset)
    {
        $url = sprintf('/travel/%d/comments?', urlencode($id)) 
            . http_build_query([
                'limit' => $limit,
                'offset' => $offset,
            ]);
        
        return $this->get($url);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getTravel($id)
    {
        return $this->get('/travel/' . urlencode($id));
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getTravelsByCategory($name)
    {
        return $this->get('/travel/by-category/' . urlencode($name));
    }

    /**
     * @param       $id
     * @param array $travel
     */
    public function updateTravel($id, array $travel)
    {
        $this->put('/travel/' . urlencode($id), $travel);
    }

    /**
     * @param $id
     * @return void
     */
    public function deleteTravel($id)
    {
        $this->delete('/travel/' . urlencode($id));
    }

    /**
     * @param int $id
     * @return object
     */
    public function addTravelToFavorites($id)
    {
        return $this->post('/travel/favorite/' . urlencode($id));
    }

    /**
     * @param int $id
     * @return object
     */
    public function removeTravelFromFavorites($id)
    {
        return $this->delete('/travel/favorite/' . urlencode($id));
    }

    /**
     * @return array
     */
    public function getFavoriteTravels()
    {
        return $this->get('/travel/favorite');
    }

    private function addAuth(array $headers)
    {
        $headers[] = 'Authorization: Token ' . $this->authToken;
        return $headers;
    }

    /**
     * @param HttpResponse $response
     * @return mixed
     */
    private function parse(HttpResponse $response)
    {
        if ($response->getCode() !== 200) {
            $message = "HTTP ERROR {$response->getCode()}\n"
                . implode("\n", $response->getHeaders())
                . "\n\n" . $response->getBody();
            throw new ApiClientException($message, $response->getCode());
        }
        return json_decode($response->getBody());
    }

    private function get($url, array $headers = [])
    {
        $headers = $this->addAuth($headers);
        $response = $this->http->get($this->host . $url, $headers);
        return $this->parse($response);
    }

    private function post($url, array $body = [], array $headers = [])
    {
        $headers = $this->addAuth($headers);
        $response = $this->http->post($this->host . $url, json_encode($body), $headers);
        return $this->parse($response);
    }

    private function put($url, array $body = [], array $headers = [])
    {
        $headers = $this->addAuth($headers);
        $response = $this->http->put($this->host . $url, json_encode($body), $headers);
        return $this->parse($response);
    }

    private function delete($url, array $headers = [])
    {
        $headers = $this->addAuth($headers);
        $response = $this->http->delete($this->host . $url, $headers);
        return $this->parse($response);
    }
}
