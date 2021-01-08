<?php
namespace db\ConnectionDb;

use \PDO;
use PDOException;
use Throwable;

require '../global.php';

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
            die(json_encode(['outcome' => true]));
        }catch(PDOException $pdoe){
            print "Connection PDO Error-> ".$pdoe->getMessage();
            die();
        }catch(Throwable $t){
            print "Connection Error-> ".$t->getMessage();
            die();
        }
    }

    public function getConn(){
        return $this->conn;
    }
}
