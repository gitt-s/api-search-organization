<?php

class Yandex extends SearchData
{

    public $url;
    public $max_result;
    public $response;
    public $geo_street;
    public $geo_city;
    public $geo_building;

    public function __construct($name, $city, $address, $country, $building, $max_result = 1)
    {
        parent::__construct($name, $city, $address, $country, $building);
        $this->max_result = $max_result;
        $this->responseYandex();
        $this->geoYandex();
    }


    public function responseYandex()
    {

        $params = array(
            'text' => "{$this->address}, {$this->building}, {$this->country}, {$this->city}, {$this->name} ",         // даные
            'results' => "{$this->max_result}",                                 // количество выводимых результатов
            'apikey'     => 'eaa7e8c6-407f-46d6-b4b0-08ad97d3c0dc',             // ваш api key
            'lang'    => 'ru_RU',
            'type'    => 'biz',
        );

        $this->response = json_decode(file_get_contents('https://search-maps.yandex.ru/v1/?' . http_build_query($params, '', '&')));


    }

    public function geoYandex()
    {
        $params = array(
            'apikey'     => '4ffe22c4-d9d8-4d2e-91c4-5cb56bf3d629',
            'format'     => 'json',
            'geocode'    => $this->response->features[0]->geometry->coordinates[0]. ',' . $this->response->features[0]->geometry->coordinates[1],
        );

        $response = json_decode(file_get_contents('https://geocode-maps.yandex.ru/1.x/?' . http_build_query($params, '', '&')));
        $ya_address = $response->response->GeoObjectCollection->featureMember[0]->GeoObject->metaDataProperty->GeocoderMetaData->Address;

        foreach ($ya_address->Components as $comp)
        {
            if ($comp->kind == 'locality')
                $this->geo_city = $comp->name;

            if ($comp->kind == 'street')
                $this->geo_street = $comp->name;

            if ($comp->kind == 'house')
                $this->geo_building = $comp->name;
        }

      //  print_r($response);

    }

    public function resultYandex()
    {
        $response = $this->response;
        $yandex = $response->features[0];

        $CompanyMetaData = $yandex->properties->CompanyMetaData;

            $results =
            [
                'platform' => 'yandex',
                'place_id' => $CompanyMetaData->id,
                'name' => $CompanyMetaData->name,
                'address' => [
                    'formatted' => $CompanyMetaData->address,
                    'city' => $this->geo_city,
                    'street' => $this->geo_street,
                    'building' => $this->geo_building,
                    'lat' => $yandex->geometry->coordinates[1],
                    'long' => $yandex->geometry->coordinates[0],
                     'office' => '',
                ],
                'zip' => $CompanyMetaData->postalCode,
                'url' => $CompanyMetaData->url,
                'listing_url' => 'https://yandex.ru/maps/org/'. $CompanyMetaData->id,
                'phone' => [
                    'formatted' => $CompanyMetaData->Phones[0]->formatted,
                    'country_code' => $CompanyMetaData->Phones[0]->country,
                    'prefix' => $CompanyMetaData->Phones[0]->prefix,
                    'number' => $CompanyMetaData->Phones[0]->number
                ]
            ];

        return $results;
    }


}