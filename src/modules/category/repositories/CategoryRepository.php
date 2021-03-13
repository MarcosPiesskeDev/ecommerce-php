<?php

namespace HcodeEcom\modules\category\repository;

use HcodeEcom\db\MethodsDb;
use HcodeEcom\modules\category\interfaces\ICategory;
use HcodeEcom\modules\category\models\Category;
use HcodeEcom\modules\product\models\Product;
use HcodeEcom\modules\product\repository\ProductRepository;

class CategoryRepository implements ICategory{


    public function getAllCategories()
    {
        $conn = new MethodsDb();

        return  $conn->select(
            "SELECT 
                id, category, date_register
            FROM
                category
            ORDER BY
                category"
        );
    }

    public function getCategoryById(int $id)
    {
        $conn = new MethodsDb();
        $category = new Category();

        $result = $conn->select(
            "SELECT 
                c.id, c.category, c.date_register
            FROM
                category c
            WHERE
                c.id = '".$id."'"
        );

        $data = $result[0];

        $category->setCategory($data['category']);
        $category->setDateRegister($data['date_register']);

        return [
            "category" => [
                'id'            =>  $id,
                'category'      =>  $category->getCategory(),
                'date_regsiter' =>  $category->getDateRegister(),
            ],
        ];
    }

    public function createCategory(Category $category)
    {
        $conn = new MethodsDb();
        if(empty($category->getId())){
            $category->setId(0);
        }
       
        $result = $conn->select("CALL p_category_save(
            '".$category->getId()."',
            '".$category->getCategory()."'
            )"
        );

        $data = $result[0];

        $this->updateCategoryOnHtml();
        $category->setId($data['id']);
        $category->setCategory($data['category']);
    }

    public function updateCategoryById(int $id, Category $category)
    {
        $conn = new MethodsDb();

        if(empty($id)){
            $id = 0;
        }
       
        $result = $conn->select("CALL p_category_save(
            '".$id."',
            '".$category->getCategory()."'
            )"
        );

        $data = $result[0];

        $category->setId($data['id']);
        $category->setCategory($data['category']);
    }

    public function deleteCategoryById(int $id)
    {
        $conn = new MethodsDb();

        $conn->query(
            "DELETE
            FROM
                category
            WHERE 
                id = '".$id."'
            ");

        $this->updateCategoryOnHtml();
    }

    public function updateCategoryOnHtml()
    {
        $html = [];

        foreach($this->getAllCategories() as $row){
            array_push($html, "<li><a href=/categories/".$row['id'].">".$row['category']."</a></li>");
        }

        file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));
    }

    public function getProductsFromCategoryRelated(int $idCategory, $related = true)
    {
        $product = new Product();
        $productRepo = new ProductRepository();
        $conn = new MethodsDb();
        $data = [];
        if ($related === true){
            $results = $conn->select(
            "SELECT 
                p.id, p.name, p.price, p.width, p.height, p.length, p.weight, p.url, p.date_register
            FROM 
                product p
            WHERE
                p.id IN (
            SELECT 
                p.id
            FROM
                product p
            INNER JOIN 
                product_category pc
            ON
                p.id = pc.id_product
            WHERE pc.id_category = '".$idCategory."'
            );"
        );

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
                $product->setPhoto($productRepo->checkPhoto($value['id']));
        
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
        }else{
            $results = $conn->select(
                "SELECT 
                p.id, p.name, p.price, p.width, p.height, p.length, p.weight, p.url, p.date_register
                FROM 
                    product p
                WHERE
                    p.id NOT IN (
                SELECT 
                    p.id
                FROM
                    product p
                INNER JOIN 
                    product_category pc
                ON
                    p.id = pc.id_product
                WHERE pc.id_category = '".$idCategory."'
                );"
            );
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
                $product->setPhoto($productRepo->checkPhoto($value['id']));
        
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
    }

    public function addProductOnCategory(int $idCategory, $product)
    {
        $conn = new MethodsDb();
        $conn->query(
            "INSERT INTO 
                product_category (id_category, id_product) 
            VALUES 
                ('".$idCategory."', '".$product['product']['id']."');
            "
        );  
    }

    public function removeProductOnCategory(int $idCategory, $product)
    {
        $conn = new MethodsDb();
        $conn->query(
            "DELETE FROM 
                product_category 
            WHERE 
                id_category = '".$idCategory."' 
            AND
                id_product = '".$product['product']['id']."';
            "
        );
    }
}