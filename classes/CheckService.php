<?php


class CheckService extends SearchData
{
    public $service;


    public function __construct($name, $city, $address, $country, $building, $service)
    {
        parent::__construct($name, $city, $address, $country, $building);

        $this->service = $service;
    }

    /**
     *
     */
    public function optionService()
    {
        $name = $this->name;
        $city = $this->city;
        $address = $this->address;
        $country = $this->country;
        $building = $this->building;

        $yandex = new Yandex($name, $city, $address, $country, $building);
        $google = new Google($name, $city, $address, $country, $building);
        $twogis = new Twogis($name, $city, $address, $country, $building);

        $service = explode(",", $this->service);
        //  $result = array();
        foreach ($service as $value) {
            switch ($value) {

                case 'yandex':
                    $result['yandex'] = $yandex->resultYandex();
                    break;
                case 'google':
                    $result['google'] = $google->resultGoogle();
                    break;
                case '2gis':
                    $result['2gis'] = $twogis->resultTwogis();
                    break;
                case 'test':
                    $result[] = $yandex->geoYandex();
                    break;
                default:
                    $result['yandex'] = $yandex->resultYandex();
                    $result['google'] = $google->resultGoogle();
                    $result['2gis'] = $twogis->resultTwogis();
            }

        }

        return json_encode($result, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);

    }


}