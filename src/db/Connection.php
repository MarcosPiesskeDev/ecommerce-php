<?php
namespace HcodeEcom\db\ConnectionDb;

use \PDO;
use PDOException;
use Throwable;

require __DIR__.'/../global.php';

class ConnectionDb {
    private $conn;

    public function __construct()
    {
        $db = getenv('DATABASE');
        $host = getenv('DB_HOST');
        $db_name = getenv('DB_NAME');
        $db_user = getenv('DB_USER');
        $db_pass = getenv('DB_PASSWORD');

        try{
            $this->conn = new PDO($db.':host='.$host.';dbname='.$db_name, $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }catch(PDOException $pdoe){
            die(json_encode(['PDO connection Error->' => $pdoe->getMessage()]));
        }catch(Throwable $t){
            die(json_encode(['Connection Error->' => $t->getMessage()]));
        }
    }

    public function getConn(){
        return $this->conn;
    }
}
