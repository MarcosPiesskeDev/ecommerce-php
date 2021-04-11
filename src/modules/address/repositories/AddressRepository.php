<?php

namespace HcodeEcom\modules\address\repositories;

use Exception;
use HcodeEcom\db\MethodsDb;
use HcodeEcom\modules\address\interfaces\IAddress;
use HcodeEcom\modules\address\models\Address;

class AddressRepository implements IAddress{

    const SESSION_ERROR = "AddressError";

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

        if ($address->getId() !== null || !empty($address->getId())){
            $this->updateAddress($address->getId(), $address);
            exit();
        }

        $conn->insert(
            "INSERT INTO 
                address(id_person, address, complement, city, country, state, n_zipcode, n_residence)
            VALUES
                (
                    '".$address->getIdPerson()."',
                    '".$address->getAddress()."',
                    '".$address->getComplement()."',
                    '".$address->getCity()."',
                    '".$address->getCountry()."',
                    '".$address->getState()."',
                    ".$address->getZipCode().",
                    '".$address->getNResidence()."'
                );    
            "
        );

    }

    public function updateAddress(int $idAddress, Address $address)
    {
        $conn = new MethodsDb();

        $conn->query(
            "UPDATE
	            address
            SET 
                id_person  = '".$address->getIdPerson()."', 
                address    = ".$address->getAddress().", 
                complement = ".$address->getComplement().", 
                city 	   = ".$address->getCity().", 
                state 	   = ".$address->getState().", 
                country	   = ".$address->getCountry().", 
                n_zipcode  = '".$address->getZipCode()."',
                n_residence = '".$address->getNResidence()."'
            WHERE
                id = '".$idAddress."';
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

    public static function getAddressDataFromCep(string $zipCode)
    {
        $nZipCode = str_replace("-", "", $zipCode);
        
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "http://viacep.com.br/ws/".$nZipCode."/json/");
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $data = json_decode(curl_exec($curl));

        if(!empty($data->erro)){
            throw new Exception("This zip code is not valid.");
        }
        
        curl_close($curl);

        return $data;    
    }

    public function setAddressDataFromCep(Address $address, string $zipCode)
    {
        $data = AddressRepository::getAddressDataFromCep($zipCode);

        if (isset($data->logradouro) && $data->logradouro){
            $address->setAddress($data->logradouro.", ".$data->bairro);
            $address->setComplement($data->complemento);
            $address->setCity($data->localidade);
            $address->setState($data->uf);
            $address->setCountry('Brazil');
            $address->setZipCode($zipCode);
        }
    }

    public static function setMsgError($msg)
	{

		$_SESSION[AddressRepository::SESSION_ERROR] = $msg;

	}

	public static function getMsgError()
	{

		$msg = (isset($_SESSION[AddressRepository::SESSION_ERROR])) ? $_SESSION[AddressRepository::SESSION_ERROR] : "";

		AddressRepository::clearMsgError();

		return $msg;

	}

	public static function clearMsgError()
	{

		$_SESSION[AddressRepository::SESSION_ERROR] = NULL;

	}
}