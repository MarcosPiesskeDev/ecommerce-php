<?php

namespace HcodeEcom\modules\product\repository;

use HcodeEcom\db\MethodsDb;
use HcodeEcom\modules\product\interfaces\IProduct;
use HcodeEcom\modules\product\models\Product;

class ProductRepository implements IProduct{

    public function checkPhoto($idPhoto)
    {
        if (file_exists(
            $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
            "resources" . DIRECTORY_SEPARATOR . 
            "site" . DIRECTORY_SEPARATOR . 
            "img" . DIRECTORY_SEPARATOR . 
            "products" . DIRECTORY_SEPARATOR . 
            $idPhoto . ".jpg"
        )){
            $url = "/resources/site/img/products/" . $idPhoto . ".jpg";
        }else{
            $url = "/resources/site/img/crossword.png";
        }

        return $url;
    }

    public function setPhotoByExtension(int $idPhoto, $file){
        $extension = explode('.', $file["name"]);
        $extension = end($extension);

        switch ($extension){
            case "jpg":
            case "jpeg":    
                $image = imagecreatefromjpeg($file['tmp_name']);
            break;
            case "png":
                $image = imagecreatefrompng($file['tmp_name']);
            break;
            case "gif":
                $image = imagecreatefromgif($file['tmp_name']);
        }

        $target =  $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
        "resources" . DIRECTORY_SEPARATOR . 
        "site" . DIRECTORY_SEPARATOR . 
        "img" . DIRECTORY_SEPARATOR . 
        "products" . DIRECTORY_SEPARATOR . 
        $idPhoto . ".jpg";

        imagejpeg($image, $target);
        imagedestroy($image);
        $this->checkPhoto($idPhoto);
    }

    public function getAllProducts()
    {
        $conn = new MethodsDb();
        $product = new Product();

        $results = $conn->select(
            "SELECT 
                p.id, p.name, p.price, p.width, p.height, p.length, p.weight, p.url, p.date_register 
            FROM 
                product p
            ORDER BY
                p.name"
        );
        
        $data = [];
       foreach($results as $value){
        $product->setId($value['id']);
        $product->setName($value['name']);
        $product->setPrice($value['price']);
        $product->setWidth($value['width']);
        $product->setHeight($value['height']);
        $product->setLength($value['length']);
        $product->setWeight($value['weight']);
        $product->setUrl($value['url']);
        $product->setDateRegister($value['date_register']);
        $product->setPhoto($this->checkPhoto($value['id']));

        $i = [
            'id'            => $product->getId(),
            'name'          => $product->getName(),
            'price'         => $product->getPrice(),
            'width'         => $product->getWidth(),
            'height'        => $product->getHeight(),
            'length'        => $product->getLength(),
            'weight'        => $product->getWeight(),
            'url'           => $product->getUrl(),
            'date_register' => $product->getDateRegister(),
            'photo'         => $product->getPhoto(),
        ];
        array_push($data, $i);
    }
      
        return $data;
    }

    public function getProductById(int $id)
    {
        $this->checkPhoto($id);
        $conn = new MethodsDb();
        $product = new Product();

        $result = $conn->select(
            "SELECT 
                p.id, p.name, p.price, p.width, p.height, p.length, p.weight, p.url, p.date_register 
            FROM 
                product p
            WHERE p.id = '".$id."'"
        );

        $data = $result[0];

        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setWidth($data['width']);
        $product->setHeight($data['height']);
        $product->setLength($data['length']);
        $product->setWeight($data['weight']);
        $product->setUrl($data['url']);
        $product->setDateRegister($data['date_register']);
        $product->setPhoto($this->checkPhoto($id));

        return [
            'product' => [
                'id'            => $id,
                'name'          => $product->getName(),
                'price'         => $product->getPrice(),
                'width'         => $product->getWidth(),
                'height'        => $product->getHeight(),
                'length'        => $product->getLength(),
                'weight'        => $product->getWeight(),
                'url'           => $product->getUrl(),
                'date_register' => $product->getDateRegister(),
                'photo'         => $product->getPhoto(),
            ],
        ];
    } 

    public function createProduct(Product $product)
    {
        $conn = new MethodsDb();

        $results = $conn->select("CALL p_product_save(
            '".(int)$product->getId()."', 
            '".$product->getName()."', 
            '".$product->getPrice()."', 
            '".$product->getWidth()."', 
            '".$product->getHeight()."', 
            '".$product->getLength()."', 
            '".$product->getWeight()."', 
            '".$product->getUrl()."')"
        );

        $data = $results[0];

        $product->setId($data['id']);
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setWidth($data['width']);
        $product->setHeight($data['height']);
        $product->setLength($data['length']);
        $product->setWeight($data['weight']);
        $product->setUrl($data['url']);
    }

    public function updateProductById(int $id, Product $product)
    {

        $conn = new MethodsDb();
        if(empty($id)){
            $id = 0;
        }

        $results = $conn->select("CALL p_product_save(
                '".$id."', 
                '".$product->getName()."', 
                '".$product->getPrice()."', 
                '".$product->getWidth()."', 
                '".$product->getHeight()."', 
                '".$product->getLength()."', 
                '".$product->getWeight()."', 
                '".$product->getUrl()."'
                )");

        $data = $results[0];

        $product->setId($id);
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setWidth($data['width']);
        $product->setHeight($data['height']);
        $product->setLength($data['length']);
        $product->setWeight($data['weight']);
        $product->setUrl($data['url']);
        $product->setPhoto($product->getPhoto());
    }

    public function deleteProductById(int $id)
    {
        $target = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
        "resources" . DIRECTORY_SEPARATOR . 
        "site" . DIRECTORY_SEPARATOR . 
        "img" . DIRECTORY_SEPARATOR . 
        "products" . DIRECTORY_SEPARATOR . 
        $id . ".jpg";

        if (file_exists($target)){
            unlink($target);
        }

        $conn = new MethodsDb();

        $conn->query(
            "DELETE 
            FROM 
                product p 
            WHERE 
                p.id = '".$id."'
            ");
    }

    public function getProductFromURL(string $url)
    {
        $product = new Product();
        $conn = new MethodsDb();

        $result = $conn->select(
            "SELECT 
                p.id, p.name, p.price, p.width, p.height, p.length, p.weight, p.url, p.date_register
            FROM 
                product p
            WHERE
                p.url = '".$url."'; 
            "
        );

        $data = [];
        foreach($result as $value){
            $product->setId($value['id']);
            $product->setName($value['name']);
            $product->setPrice($value['price']);
            $product->setWidth($value['width']);
            $product->setHeight($value['height']);
            $product->setLength($value['length']);
            $product->setWeight($value['weight']);
            $product->setUrl($value['url']);
            $product->setDateRegister($value['date_register']);
            $product->setPhoto($this->checkPhoto($value['id']));
    
            $i = [
                'id'            => $product->getId(),
                'name'          => $product->getName(),
                'price'         => $product->getPrice(),
                'width'         => $product->getWidth(),
                'height'        => $product->getHeight(),
                'length'        => $product->getLength(),
                'weight'        => $product->getWeight(),
                'url'           => $product->getUrl(),
                'date_register' => $product->getDateRegister(),
                'photo'         => $product->getPhoto(),
            ];
            array_push($data, $i);
        }
        return $data[0];
    }

    public function getCategoryFromURL(int $idProduct)
    {
        $conn = new MethodsDb();

        $result = $conn->select(
            "SELECT 
	            c.id, c.category, c.date_register
            FROM
	            category c
            INNER JOIN
	            product_category pc
            ON
	            c.id = pc.id_category
            WHERE pc.id_product = ".$idProduct.";
            "
        );
        return $result;
    }
}