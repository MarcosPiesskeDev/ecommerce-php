<?php

namespace HcodeEcom\modules\user\models;

class User {
    
    private $id;
    private $idPerson;
    private $username;
    private $password;
    private $isAdmin;
    private $dateRegister;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIdPerson()
    {
        return $this->idPerson;
    }

    public function setIdPerson($idPerson)
    {
        $this->idPerson = $idPerson;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): string
    {
        return $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): string
    {
        return $this->password = $password;
    }

    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    public function setIsAdmin($isAdmin)
    {
        return $this->isAdmin = $isAdmin;
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