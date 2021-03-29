<?php

namespace HcodeEcom\modules\address\interfaces;

use HcodeEcom\modules\address\models\Address;

interface IAddress{

    public function getAllAddresses();

    public function getAddressById(int $idAddress);

    public function createAddress(Address $address);

    public function updateAddress(int $idAddress, Address $address);
    
    public function deleteAddressById(int $idAddress);
}