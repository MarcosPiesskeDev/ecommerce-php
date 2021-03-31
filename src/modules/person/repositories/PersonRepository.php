<?php

namespace HcodeEcom\modules\person\repositories;

use HcodeEcom\db\MethodsDb;
use HcodeEcom\modules\person\models\Person;

class PersonRepository{

    public function getPersonById(int $idPerson)
    {
        $conn = new MethodsDb();
        $person = new Person();
        
        $result = $conn->select(
            "SELECT
                p.id, p.name, p.email, p.n_phone, p.date_register
            FROM
                person p
            WHERE 
                p.id = '".$idPerson."';
            "
        );

        $data = $result[0];

        $person->setId($data['id']);
        $person->setName($data['name']);
        $person->setEmail($data['email']);
        $person->setNPhone($data['n_phone']);
        $person->setDateRegister($data['date_register']);

        return $person;

    }
}