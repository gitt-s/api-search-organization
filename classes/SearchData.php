<?php


class SearchData extends MainData
{

    public $country;
    public $building;

    public function __construct($name, $city, $address, $country, $building)
    {
        parent::__construct($name, $city, $address);

        $this->country = $country;
        $this->building = $building;

    }

}