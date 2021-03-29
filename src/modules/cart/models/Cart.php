<?php

namespace HcodeEcom\modules\cart\models;

class Cart{
    private $id;
    private $idUser;
    private $idAddress;
    private $securitySessionId;
    private $freight;
    private $dateRegister;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id; 
    }

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    public function getIdAddress()
    {
        return $this->idAddress;
    }

    public function setIdAddress($idAddress)
    {
        $this->idAddress = $idAddress;
    }

    public function getSecuritySessionId()
    {
        return $this->securitySessionId;
    }

    public function setSecuritySessionId($securitySessionId)
    {
        $this->securitySessionId = $securitySessionId;
    }

    public function getFreight()
    {
        return $this->freight;
    }

    public function setFreight($freight)
    {
        $this->freight = $freight;
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