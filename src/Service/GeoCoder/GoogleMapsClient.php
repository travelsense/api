<?php
namespace Api\Service\GeoCoder;

use Api\Exception\GeocoderException;

class GoogleMapsClient
{
    private $url;
    private $key;

    public function __construct(string $url, string $key)
    {
        $this->url = $url;
        $this->key = $key;
    }

    public function geocode($latlng)
    {
        $request = file_get_contents(urlencode($this->url).'?latlng='.$latlng.'&key='.urlencode($this->key));
        $response = json_decode($request, true);

        if ($response['status']=='OK') {
            $tag = [
                'city' => $response['results'][0]['address_components'][3]['long_name'],
                'state' => $response['results'][0]['address_components'][5]['long_name'],
                'country' => $response['results'][0]['address_components'][6]['long_name']
            ];
            return $tag;
        } else {
            throw new GeocoderException();
        }
    }
}
