<?php


class MainData
{

    public $name;
    public $city;
    public $address;


    public function __construct ($name, $city, $address)
    {
        $this->name = $name;
        $this->city = $city;
        $this->address = $address;

    }

}