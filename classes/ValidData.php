<?php


class ValidData extends MainData
{

    public $token;

    public function __construct($name, $city, $address, $token)
    {
        parent::__construct($name, $city, $address);
        $this->token = $token;
        $this->validate();
    }

    public function validate ()
    {
        if ( $this->token  != "7B556C5DDF04E67CBB626917052607A1") {
            throw new TokenException;
        }
        if (!$this->name || $this->name == "")
        {
            throw new NameException;
        }

        if (!$this->city || $this->city == "")
        {
            throw new CityException;
        }

        if (!$this->address || $this->address == "")
        {
            throw new AddressException;
        }
    }




}