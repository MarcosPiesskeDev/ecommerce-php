<?php

namespace HcodeEcom\modules\address\models;

class Address{
    private $id;
    private $idPerson;
    private $address;
    private $complement;
    private $city;
    private $state;
    private $country;
    private $zipCode;
    private $dateRegister;
    
    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->$id = $id;
    }

    public function getIdPerson()
    {
        return $this->idPerson;
    }

    public function setIdPerson($idPerson)
    {
        $this->idPerson = $$idPerson;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getComplement()
    {
        return $this->complement;
    }

    public function setComplement($complement)
    {
        $this->complement = $complement;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function getZipCode()
    {
        return $this->zipCode;
    }

    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    public function getDateRegister()
    {
        return $this->dateRegister;
    }

    public function setDateRegister($dateRegister)
    {
        $this->dateRegister = $dateRegister;
    }
}