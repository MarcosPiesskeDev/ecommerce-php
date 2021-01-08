<?php
namespace db\MethodsDb;

use \db\ConnectionDb\ConnectionDb;
use PDO;

class MethodsDb{
    private function setParams($statement, $params = [])
    {
        foreach ($params as $key => $value){
            $statement->bindParam($key, $value);
        }
    }

    public function query($rawQuery, $params = [])
    {
        $conn = new ConnectionDb();
        $stmt = $conn->getConn()->prepare($rawQuery);
        $this->setParams($stmt, $params);
        $stmt->execute();
        return $stmt;
    }

    public function select($rawQuery, $params = []) : array
    {
        $stmt = $this->query($rawQuery, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);       
    }
}