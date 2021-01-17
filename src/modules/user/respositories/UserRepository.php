<?php

namespace HcodeEcom\modules\user\repositories;

require __DIR__.'../../../../../vendor/autoload.php';

use Exception;
use HcodeEcom\db\MethodsDb;
use HcodeEcom\modules\person\models\Person;
use HcodeEcom\modules\user\interfaces\IUser;
use HcodeEcom\modules\user\models\User;

class UserRepository implements IUser{

    const SESSION = "User";

    public function login($username, $password): User
    {
        $conn = new MethodsDb();

        $results = $conn->select("SELECT * FROM user WHERE username = '".$username."'");

        if(count($results) === 0){
            throw new Exception("Incorrect credentials, try again.");
        }

        $data = $results[0];

        if($data['password'] === $password){
            $user = new User();

            $user->setId($data['id']);
            $user->setIdPerson($data['id_person']);
            $user->setUsername($data['username']);
            $user->setPassword($data['password']);
            $user->setIsAdmin($data['is_admin']);
            $user->setDateRegister($data['date_register']);

            $_SESSION[UserRepository::SESSION]  = [
                $user->getId(),
                $user->getIdPerson(),
                $user->getUsername(),
                $user->getPassword(),
                $user->getIsAdmin(),
                $user->getDateRegister()
            ];
            
            return $user;

        }else{
            throw new Exception("Incorrect credentials, try again.");
        }
    }

    public static function verifyLogin($inAdmin = true)
    {
        if(empty($_SESSION[UserRepository::SESSION]) 
        || 
        !$_SESSION[UserRepository::SESSION] 
        || 
        !(int)$_SESSION[UserRepository::SESSION][0] > 0 
        || 
        (bool)$_SESSION[UserRepository::SESSION][1] !== $inAdmin){
            header('Location: /admin/login');
            exit;
        }
    }

    public static function logout()
    {
        $_SESSION[UserRepository::SESSION] = NULL;
    }

    public function getAllUsers()
    {
        $conn = new MethodsDb();

        return $conn->select("SELECT * FROM user u INNER JOIN person p USING(id) ORDER BY p.name");
    }

    public function createUserAndPerson(User $user, Person $person)
    {
        $conn = new MethodsDb();
       
        $result = $conn->select("CALL p_user_save(
            '".$person->getName()."', 
            '".$user->getUsername()."', 
            '".$user->getPassword()."', 
            '".$person->getEmail()."', 
            '".$person->getNPhone()."', 
            '".$user->getIsAdmin()."'
            )"
        );

        $data = $result[0];        
        
        $person->setName($data['name']);
        $user->setUsername($data['username']);
        $user->setPassword($data['password']);
        $person->setEmail($data['email']);
        $person->setNPhone($data['n_phone']);
        $user->setIsAdmin($data['is_admin']);
    }

    public function getUserAndPersonById(int $id): array
    {
        $conn = new MethodsDb();
        $user = new User();
        $person = new Person();

        $result = $conn->select("SELECT * FROM user u INNER JOIN person p USING(id) WHERE u.id = '".$id."'");

        $data = $result[0];        
        
        $person->setName($data['name']);
        $user->setUsername($data['username']);
        $user->setPassword($data['password']);
        $person->setEmail($data['email']);
        $person->setNPhone($data['n_phone']);
        $user->setIsAdmin($data['is_admin']);

        return ["user"=>[
                'id'        => $id,
                'name'      => $person->getName(),
                'username'  => $user->getUsername(),
                'password'  => $user->getPassword(),
                'email'     => $person->getEmail(),
                'n_phone'   => $person->getNPhone(),
                'is_admin'  => $user->getIsAdmin()                
                ]
            ];
    }

    public function updateUserAndPersonById(int $idUser, array $data)
    {
        $conn = new MethodsDb();

        $user = new User();
        $person = new Person();

        $result = $conn->select("CALL p_user_update(
            '".$idUser."',
            '".$data['name']."', 
            '".$data['username']."', 
            '".$data['password']."', 
            '".$data['email']."', 
            '".$data['n_phone']."', 
            '".$data['is_admin']."'
            )"
        );

        $dataP = $result[0];        
        
        $person->setName($dataP['name']);
        $user->setUsername($dataP['username']);
        $user->setPassword($dataP['password']);
        $person->setEmail($dataP['email']);
        $person->setNPhone($dataP['n_phone']);
        $user->setIsAdmin($dataP['is_admin']);
    }

    public function deleteUserAndPersonById(int $idUser)
    {
        $conn = new MethodsDb();

        $conn->query("CALL p_user_delete('".$idUser."')");
    }
}