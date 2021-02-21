<?php

namespace HcodeEcom\modules\person\models;

class Person{

    private $id;
    private $name;
    private $email;
    private $nPhone;
    private $dateRegister;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getNPhone()
    {
        return $this->nPhone;
    }

    public function setNPhone($nPhone)
    {
        $this->nPhone = $nPhone;
    }

    public function getDateRegister()
    {
        return $this->dateRegister;
    }

    public function setDateRegister($dateRegister)
    {
        return $this->dateRegister = $dateRegister;
    }
}