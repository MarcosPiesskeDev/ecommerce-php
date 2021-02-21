<?php

namespace HcodeEcom\modules\category\interfaces;

use HcodeEcom\modules\category\models\Category;

interface ICategory{

    public function getAllCategories();

    public function getCategoryById(int $id);

    public function createCategory(Category $category);

    public function updateCategoryById(int $id, Category $category);

    public function deleteCategoryById(int $id);
}