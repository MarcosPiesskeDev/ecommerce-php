<?php

namespace HcodeEcom\modules\category\repository;

use HcodeEcom\db\MethodsDb;
use HcodeEcom\modules\category\interfaces\ICategory;
use HcodeEcom\modules\category\models\Category;

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
}