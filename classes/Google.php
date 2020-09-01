<?php

class Google extends SearchData
{
    protected $id_response;
    public $response;

    public function __construct($name, $city, $address, $country, $building)
    {
        parent::__construct($name, $city, $address, $country, $building);
        $this->getIdGoogle();
        $this->responseGoogle();
    }

    private function getIdGoogle()
    {
        $data = urlencode($this->address . ' ' . $this->building . ', ' . $this->city . ', ' . $this->name . ', ' . $this->country);
        $params = array(
            'input'     => $data,         // даные
            'language'  => 'ru',
            'inputtype' => 'textquery',
            'fields'     => 'place_id',
            'key'       => 'AIzaSyBwvqy3O61I4uDF2b8uXIq7GYie0BNWzL8',             // ваш api key
        );
        $paramsJoined = array();

        foreach($params as $param => $value) {
            $paramsJoined[] = "$param=$value";
        }

        $this->id_response = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/place/findplacefromtext/json?' . implode('&', $paramsJoined)), true);


    }

    public function responseGoogle()
    {
        if($this->id_response['status'] == 'OK'){

            $id = $this->id_response['candidates'][0]['place_id'];

            $params = array(
                'placeid'     => $id,         // id
                'language'  => 'ru',
                'key'       => 'AIzaSyBwvqy3O61I4uDF2b8uXIq7GYie0BNWzL8',             // api key
            );
            $paramsJoined = array();

            foreach($params as $param => $value) {
                $paramsJoined[] = "$param=$value";
            }

            $this->response = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/place/details/json?' . implode('&', $paramsJoined)), true);

        }
    }

    public function resultGoogle()
    {

        $response = $this->response;

        $id = $response['result']['id'];

        foreach ($response['result']['address_components'] as $key=>$value){
         if ($value['types'][0] == 'route'){
             $street = $value['long_name'];
         };
         if ($value['types'][0] == 'street_number'){
             $building = $value['long_name'];
         };
         if ($value['types'][0] == 'postal_code'){
             $zip = $value['long_name'];
         };
         if ($value['types'][0] == 'locality'){
             $city = $value['long_name'];
         };

        }

        $results =
            [
            'platform' => 'google',
            'place_id' => $response['result']['place_id'],
            'name' => $response['result']['name'],
            'address' => [
                'formatted' => $response['result']['formatted_address'],
                'city' => $city,
                'street' => $street,
                'building' => $building,
                'lat' => $response['result']['geometry']['location']['lat'],
                'long' => $response['result']['geometry']['location']['lng'],
                'office' => '',
                          ],
                'zip' => $zip,
                'url' => !empty($response['result']['website'])?$response['result']['website']:'',
                'listing_url' => $response['result']['url'],
                'phone' => [
                    'formatted' => !empty($response['result']['formatted_phone_number'])?$response['result']['formatted_phone_number']:'',
                    'country_code' => '',
                    'prefix' => '',
                    'number' => '',
                            ],
            ];
        return $results;
    }

}