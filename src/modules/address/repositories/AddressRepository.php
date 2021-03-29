<?php

namespace HcodeEcom\modules\address\repositories;

use HcodeEcom\db\MethodsDb;
use HcodeEcom\modules\address\interfaces\IAddress;
use HcodeEcom\modules\address\models\Address;

class AddressRepository implements IAddress{

    public function getAllAddresses()
    {
        $address = new Address();
        $conn = new MethodsDb();

        $results = $conn->select(
            "SELECT 
                a.id, a.id_person, a.address, a.complement, a.city, a.state, a.country, a.n_zipcode, a.date_register
            FROM
                address a;
            "
        );
        
        $addressList = [];
        
        foreach($results as $value){
            $address->setId($value['id']);
            $address->setIdPerson($value['id_person']);
            $address->setAddress($value['address']);
            $address->setComplement($value['complement']);
            $address->setCity($value['city']);
            $address->setState($value['state']);
            $address->setCountry($value['country']);
            $address->setZipCode($value['n_zipcode']);
            $address->setDateRegister($value['date_register']);

            $addList = [
                'id'            => $address->getId(),
                'id_person'     => $address->getIdPerson(),
                'address'       => $address->getAddress(),
                'complement'    => $address->getComplement(),
                'city'          => $address->getCity(),
                'state'         => $address->getState(),
                'country'       => $address->getCountry(),
                'zip_code'      => $address->getZipCode(),
                'date_register' => $address->getDateRegister(),
            ];

            array_push($addressList, $addList);
        }
        
        return $addressList;
    }

    public function getAddressById(int $idAddress)
    {
        $address = new Address();
        $conn = new MethodsDb();

        $result = $conn->select(
            "SELECT 
                a.id, a.id_person, a.address, a.complement, a.city, a.state, a.country, a.n_zipcode, a.date_register
            FROM
                address a
            WHERE 
                a.id = '".$idAddress."';
            "
        );
        
        $addressList = [];
        
        foreach($result as $value){
            $address->setId($value['id']);
            $address->setIdPerson($value['id_person']);
            $address->setAddress($value['address']);
            $address->setComplement($value['complement']);
            $address->setCity($value['city']);
            $address->setState($value['state']);
            $address->setCountry($value['country']);
            $address->setZipCode($value['n_zipcode']);
            $address->setDateRegister($value['date_register']);

            $addList = [
                'id'            => $address->getId(),
                'id_person'     => $address->getIdPerson(),
                'address'       => $address->getAddress(),
                'complement'    => $address->getComplement(),
                'city'          => $address->getCity(),
                'state'         => $address->getState(),
                'country'       => $address->getCountry(),
                'zip_code'      => $address->getZipCode(),
                'date_register' => $address->getDateRegister(),
            ];

            array_push($addressList, $addList);
        }
        
        return $addressList[0];
    }

    public function createAddress(Address $address)
    {
        $conn = new MethodsDb();

        $conn->query(
            "INSERT INTO
                address (id_person, address, complement, city, state, country, n_zipcode)
            VALUES
                (
                    '".$address->getIdPerson()."',
                    ".$address->getAddress().",
                    ".$address->getComplement().",
                    ".$address->getCity().",
                    ".$address->getCountry()."
                    ".$address->getState().",
                    '".$address->getZipCode()."'
                );    
            "
        );

    }

    public function updateAddress(int $idAddress, Address $address)
    {
        $conn = new MethodsDb();

        $conn->query(
            "UPDATE
	            address a
            SET 
                id_person  = '".$address->getIdPerson()."', 
                address    = ".$address->getAddress().", 
                complement = ".$address->getComplement().", 
                city 	   = ".$address->getCity().", 
                state 	   = ".$address->getState().", 
                country	   = ".$address->getCountry().", 
                n_zipcode  = '".$address->getZipCode()."',
            WHERE
                a.id = '".$idAddress."';
            "
        );
    }

    public function deleteAddressById(int $idAddress)
    {
        $conn = new MethodsDb();

        $conn->query(
            "DELETE
            FROM
                address a
            WHERE
                a.id = '".$idAddress."';
            "
        );
    }
}