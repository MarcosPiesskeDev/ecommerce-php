<?php

namespace HcodeEcom\modules\user\repositories;

require __DIR__.'../../../../../vendor/autoload.php';
require __DIR__.'../../../../global.php';

use Exception;
use Hcode\mail\Mailer;
use HcodeEcom\db\MethodsDb;
use HcodeEcom\modules\person\models\Person;
use HcodeEcom\modules\user\interfaces\IUser;
use HcodeEcom\modules\user\models\User;

class UserRepository implements IUser{

    const SESSION = "User";
    const LOGIN_ERROR = "UserError";
    const REGISTER_ERROR = "UserRegisterError";

    public static function getUserFromSession()
    {
        $user = new User();
        if(isset($_SESSION[UserRepository::SESSION]) && (int)$_SESSION[UserRepository::SESSION]['id'] > 0){
            $user->setId($_SESSION[UserRepository::SESSION]['id']);
            $user->setIdPerson($_SESSION[UserRepository::SESSION]['idPerson']);
            $user->setUsername($_SESSION[UserRepository::SESSION]['username']);
            $user->setPassword($_SESSION[UserRepository::SESSION]['password']);
            $user->setIsAdmin($_SESSION[UserRepository::SESSION]['isAdmin']);
            $user->setDateRegister($_SESSION[UserRepository::SESSION]['dateRegister']);
        }
        return $user;
    }

    public static function checkUserLogin($inAdmin = true)
    {
        if (empty($_SESSION[UserRepository::SESSION]) 
        || 
        !$_SESSION[UserRepository::SESSION] 
        || 
        !(int)$_SESSION[UserRepository::SESSION]['id'] > 0) {
           return false;
        }else{
            if ($inAdmin === true && (bool)$_SESSION[UserRepository::SESSION]['isAdmin'] === true){
                return true;
            }else if ($inAdmin === false){
                return true;
            }else{
                return false;
            }
        }
    }

    public function login($username, $password): User
    {
        $conn = new MethodsDb();

        $results = $conn->select(
            "SELECT 
                u.id, u.id_person, u.username, u.password, u.is_admin, u.date_register  
            FROM 
                user u
            WHERE
                username = '".$username."'"
        );

        if(count($results) === 0){
            throw new Exception("Incorrect credentials, try again.");
        }

        $data = $results[0];
      

        $passDecrypted = openssl_decrypt($data['password'], 'AES-256-CBC', pack('a16', getenv('SECRET')), 0, pack('a16', getenv('SECRET_IV')));
        
        if($passDecrypted === $password){
            $user = new User();

            $user->setId($data['id']);
            $user->setIdPerson($data['id_person']);
            $user->setUsername($data['username']);
            $user->setPassword($data['password']);
            $user->setIsAdmin($data['is_admin']);
            $user->setDateRegister($data['date_register']);

            $_SESSION[UserRepository::SESSION]  = [
                'id'            => $user->getId(),
                'idPerson'      => $user->getIdPerson(),
                'username'      => $user->getUsername(),
                'password'      => $user->getPassword(),
                'isAdmin'       => $user->getIsAdmin(),
                'dateRegister'  => $user->getDateRegister()
            ];
            
            return $user;

        }else{
            throw new Exception("Incorrect credentials, try again.");
        }
    }

    public static function verifyLogin($inAdmin = true)
    {
      
        if (UserRepository::checkUserLogin($inAdmin)) {
            if ($inAdmin){
                header('Location: /admin/login');
            }else{
                header('Location: /login');
            }
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

        return $conn->select(
            "SELECT 
                u.id, u.id_person, u.username, u.password, u.is_admin, u.date_register,
                p.name, p.email, p.n_phone, p.date_register 
            FROM 
                user u 
            INNER JOIN 
                person p 
            ON
                u.id_person = p.id
            ORDER BY 
                p.name"
        );
    }

    public function getUserById($idUser)
    {
        $conn = new MethodsDb();
        $user = new User();

        $result = $conn->select(
            "SELECT 
                u.id, u.id_person, u.username, u.password, u.is_admin, u.date_register
            FROM 
                user u
            WHERE
                u.id = '".$idUser."'"
        );

        if(count($result) === 0){
            throw new Exception("Doesn't exist any user with this id.");
        }

        $data = $result[0];

        $user->setIdPerson($data['id_person']);
        $user->setUsername($data['username']);
        $user->setPassword($data['password']);
        $user->setIsAdmin($data['is_admin']);
        $user->setDateRegister($data['date_register']);

        return [
            "user" => [
                'id'            => $idUser,
                'username'      => $user->getIdPerson(),
                'password'      => $user->getUsername(),
                'is_admin'      => $user->getIsAdmin(),
                'date_register' => $user->getDateRegister(),
            ],
        ];

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
        
        $passEncrypted = openssl_encrypt($data['password'], 'AES-256-CBC', pack('a16', getenv('SECRET')), 0, pack('a16',getenv('SECRET_IV')));
        
        $person->setName($data['name']);
        $user->setUsername($data['username']);
        $user->setPassword($passEncrypted);
        $person->setEmail($data['email']);
        $person->setNPhone($data['n_phone']);
        $user->setIsAdmin($data['is_admin']);
    }

    public function getUserAndPersonById(int $id): array
    {
        $conn = new MethodsDb();
        $user = new User();
        $person = new Person();

        $result = $conn->select(
            "SELECT 
                u.id, u.id_person, u.username, u.password, u.is_admin, u.date_register, 
                p.name, p.email, p.n_phone, p.date_register 
            FROM 
                user u 
            INNER JOIN 
                person p 
            ON 
                u.id_person = p.id
            WHERE 
                u.id = '".$id."'"
        );

        $data = $result[0];
        
        $passDecrypted = openssl_decrypt($data['password'], 'AES-256-CBC', pack('a16', getenv('SECRET')), 0, pack('a16',getenv('SECRET_IV')));

        $person->setName($data['name']);
        $user->setUsername($data['username']);
        $user->setPassword($passDecrypted);
        $person->setEmail($data['email']);
        $person->setNPhone($data['n_phone']);
        $user->setIsAdmin($data['is_admin']);

        return [
                "user" => [
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

        $passEncrypted = openssl_encrypt($data['password'], 'AES-256-CBC', pack('a16', getenv('SECRET')), 0, pack('a16',getenv('SECRET_IV')));

        $result = $conn->select("CALL p_user_update(
            '".$idUser."',
            '".$data['name']."', 
            '".$data['username']."', 
            '".$passEncrypted."', 
            '".$data['email']."', 
            '".$data['n_phone']."', 
            '".$data['is_admin']."'
            )"
        );

        $dataP = $result[0];
        
        $passEncrypted = openssl_encrypt($dataP['password'], 'AES-256-CBC', pack('a16', getenv('SECRET')), 0, pack('a16',getenv('SECRET_IV')));
        
        $person->setName($dataP['name']);
        $user->setUsername($dataP['username']);
        $user->setPassword($passEncrypted);
        $person->setEmail($dataP['email']);
        $person->setNPhone($dataP['n_phone']);
        $user->setIsAdmin($dataP['is_admin']);
    }

    public function deleteUserAndPersonById(int $idUser)
    {
        $conn = new MethodsDb();

        $conn->query("CALL p_user_delete('".$idUser."')");
    }

    public static function getUserByEmailToRecoverPass($email, $isAdmin = true)
    {
        $conn = new MethodsDb();

        $result = $conn->select(
            "SELECT 
                p.name, p.email, p.n_phone, p.date_register,
                u.id, u.id_person, u.username, u.password, u.is_admin, u.date_register
            FROM 
                person p 
            INNER JOIN 
                user u 
            ON
                p.id = u.id_person
            WHERE 
                p.email = '".$email."'"
        );

        if (count($result) === 0){
            throw new Exception("This e-mail doesn't exists");
            exit();
        }

        $data = $result[0];
      
        $resultP= $conn->select("CALL p_user_pass_recover_create('".$data['id']."', '".$_SERVER["REMOTE_ADDR"]."')");

        if ($resultP === 0){
            throw new Exception("This e-mail doesn't exists");
            exit();
        }

        $dataP = $resultP[0];

        $openssl = openssl_encrypt(
            $dataP['id'],
            'AES-256-CBC',
            pack('a16', getenv('SECRET')),
            0,
            pack('a16', getenv('SECRET_IV'))
        );

         $code = base64_encode($openssl);

         if($isAdmin === true){
            $link = "http://".getenv('VIRTUAL_HOST_DOMAIN')."/admin/forgot/reset?code=$code";
         }else{
            $link = "http://".getenv('VIRTUAL_HOST_DOMAIN')."/forgot/reset?code=$code";

         }
    
         $mailer = new Mailer($data['email'], $data['name'], "Change Hcode Store password", "forgot", [
             "name"=>$data['name'],
             "link"=>$link
         ]);
        
         $mailer->getMailer()->send();

         return $data;
    }

    public function validForgotDecrypt($code)
    {
        $idRecoverPass = openssl_decrypt(
            base64_decode($code),
            'AES-256-CBC',
            pack('a16', getenv('SECRET')),
            0,
            pack('a16', getenv('SECRET_IV'))
        );
        
        $conn = new MethodsDb();

        $result = $conn->select(
            "SELECT
                rpass.id, rpass.id_user, rpass.ip, rpass.date_recovery, rpass.date_register,
                u.id_person, u.username, u.password, u.is_admin, u.date_register,
                p.name, p.email, p.n_phone, p.date_register
            FROM
                recover_password rpass
            INNER JOIN
                user u ON u.id = rpass.id_user
            INNER JOIN
                person p ON p.id = u.id_person
            WHERE 
                rpass.id = '".$idRecoverPass."'
            AND
                rpass.date_recovery IS NULL
            AND
                DATE_ADD(rpass.date_register, INTERVAL 1 HOUR) >= NOW();"
        );

        if(count($result) === 0 ){
            throw new Exception("It was not possible recover this password");
        }

        return $result[0];
    }

    public static function setDateToForgotPassword($idRecoverPass)
    {
        $conn = new MethodsDb();

       $conn->query(
            "UPDATE
                recover_password
            SET
                date_recovery = NOW() WHERE id = '".$idRecoverPass."'"
        );
    }

    public static function setForgotPassword($idUser, $password)
    {
        $conn = new MethodsDb();

        $passEncrypted = openssl_encrypt($password, 'AES-256-CBC', pack('a16', getenv('SECRET')), 0, pack('a16',getenv('SECRET_IV')));

        $conn->query(
            "UPDATE
                user
            SET
                password = '".$passEncrypted."'
            WHERE  
                id = '".$idUser."'"
        );
    }

    public static function setErrorLogin($msg)
    {
        $_SESSION[UserRepository::LOGIN_ERROR] = $msg;   
    }

    public static function getErrorLogin()
    {
        $msg = (isset($_SESSION[UserRepository::LOGIN_ERROR]) && $_SESSION[UserRepository::LOGIN_ERROR]) ? $_SESSION[UserRepository::LOGIN_ERROR] : '';

        UserRepository::clearErrorLogin();

        return $msg;
    }

    public static function clearErrorLogin()
    {
        $_SESSION[UserRepository::LOGIN_ERROR] = NULL;
    }

    public static function setErrorRegister($msg)
    {
        $_SESSION[UserRepository::REGISTER_ERROR] = $msg;   
    }

    public static function getErrorRegister()
    {
        $msg = (isset($_SESSION[UserRepository::REGISTER_ERROR]) && $_SESSION[UserRepository::REGISTER_ERROR]) ? $_SESSION[UserRepository::REGISTER_ERROR] : '';

        UserRepository::clearErrorLogin();

        return $msg;
    }

    public static function clearErrorRegister()
    {
        $_SESSION[UserRepository::REGISTER_ERROR] = NULL;
    }
}