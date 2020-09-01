<?php


class Twogis extends SearchData
{

    public $gis_region_id;
    public $gis_region;
    public $response;
    public $max_result;


    public function __construct($name, $city, $address, $country, $building, $max_result = 5)
    {
        parent::__construct($name, $city, $address, $country, $building);
        $this->regionTwogis();
        $this->responseTwogis();

    }

    public function regionTwogis()
    {
        $url = 'https://catalog.api.2gis.ru/2.0/region/search?key=rueotd1801&q='.$this->city;
        $gis_region = json_decode(file_get_contents($url));
        $this->gis_region = $gis_region;
        $this->gis_region_id = $gis_region->result->items[0]->id;
        //return $this->gis_region_id;
    }

    public function responseTwogis()
    {
        $data = urlencode($this->address . ', ' . $this->building . ', ' . $this->name . ', ' . $this->country);

        $params = array(
            'q' => $data,         // даные
            'region_id'     => $this->gis_region_id,
            'page_size'    => $this->max_result,
            'fields'    => 'items.org,items.region_id,items.address,items.point,items.contact_groups',
            'key'    => 'rueotd1801'
        );

        foreach($params as $param => $value) {
            $paramsJoined[] = "$param=$value";
        }

        $this->response =json_decode(file_get_contents('http://catalog.api.2gis.ru/2.0/catalog/branch/search?' . implode('&', $paramsJoined)));

    }

    public function resultTwogis()
    {
        $response = $this->response;
        $item_data = $response->result->items[0];
        $gis_region = $this->gis_region;



        $results = [
            'platform' => '2gis',
            'place_id' => $item_data->id,
            'name' => $item_data->name,
            'address' => [
                'formatted' => $item_data->address_name,
                'city' => $gis_region->result->items[0]->name,
                'street' => $item_data->address->components[0]->street,
                'building' => $item_data->address->components[0]->number,
                'lat' => $item_data->point->lat,
                'long' => $item_data->point->lon,
                'office' => '',
            ],
            'zip' => $item_data->address->postcode,
            'url' => $item_data->url,
            'listing_url' => 'https://2gis.ru/firm/' . stristr($item_data->id, '_', true),
            'phone' => [
                'formatted' => $item_data->Phones[0]->formatted,
                'country_code' => $item_data->Phones[0]->country,
                'prefix' => $item_data->Phones[0]->prefix,
                'number' => $item_data->Phones[0]->number,
		],
	];

        return $results;

    }


}