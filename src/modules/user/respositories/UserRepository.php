<?php

namespace HcodeEcom\modules\repositories;

require __DIR__.'../../../../../vendor/autoload.php';

use Exception;
use HcodeEcom\db\MethodsDb;
use HcodeEcom\modules\interfaces\IUser;
use HcodeEcom\modules\models\User;

class UserRepository implements IUser{

    const SESSION = "User";

    public function login($username, $password)
    {
        $conn = new MethodsDb();

        $results = $conn->select("SELECT * FROM user WHERE username = '".$username."'");

        if(count($results) === 0){
            throw new Exception("Incorrect credentials, try again.");
        }

        $data = $results[0];

        //print_r($data['password']);
        //print_r($password);
        

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

   /* public static function getFromSession()
	{

		$user = new User();

		if (isset($_SESSION[UserRepository::SESSION]) && (int)$_SESSION[UserRepository::SESSION]['id'] > 0) {

            $_SESSION[UserRepository::SESSION]  = [
                $user->getId(),
                $user->getIdPerson(),
                $user->getUsername(),
                $user->getPassword(),
                $user->getIsAdmin(),
                $user->getDateRegister()
            ];

            return $user;

		}

		return $user;

	}*/


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
}