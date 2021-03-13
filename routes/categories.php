<?php

require __DIR__.'/../vendor/autoload.php';

use HcodeEcom\modules\category\models\Category;
use HcodeEcom\modules\category\repository\CategoryRepository;
use HcodeEcom\modules\product\models\Product;
use HcodeEcom\modules\product\repository\ProductRepository;
use HcodeEcom\modules\user\repositories\UserRepository;
use HcodeEcom\pages\Page;
use HcodeEcom\pages\PageAdmin;

$app->get("/admin/categories", function(){
    UserRepository::verifyLogin();
    $categoryRepo = new CategoryRepository();
    $categories = $categoryRepo->getAllCategories();
 
    $page = new PageAdmin();
 
    $page->setTpl("categories", [
       'category' => $categories,
    ]);
 });
 
 $app->get("/admin/categories/create", function(){
    UserRepository::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl("categories-create");
 });
 
 $app->post("/admin/categories/create", function(){
    UserRepository::verifyLogin();
 
    $categoryRepo = new CategoryRepository();
    $category = new Category();
 
    $category->setCategory($_POST['category']);
 
    $categoryRepo->createCategory($category);
 
    header('Location: /admin/categories');
    exit();
 });
 
 $app->get("/admin/categories/:idCategory", function($idCategory){
       UserRepository::verifyLogin();
       $categoryRepo = new CategoryRepository();
 
       $category = $categoryRepo->getCategoryById((int)$idCategory);
       
       $page = new PageAdmin();
 
       $page->setTpl("categories-update", [
          'category' => $category['category']
       ]);
 });
 
 $app->post("/admin/categories/:idCategory", function($idCategory){
    UserRepository::verifyLogin();
 
    $categoryRepo = new CategoryRepository();
    $category = new Category;
    
    $category->setCategory($_POST['category']);
 
    $categoryRepo->updateCategoryById($idCategory,  $category);
 
    header("Location: /admin/categories");
    exit();
 
 });
 
 $app->get("/admin/categories/:idCategory/delete", function($idCategory){
    UserRepository::verifyLogin();
 
    $categoryRepo = new CategoryRepository();
 
    $category = $categoryRepo->getCategoryById((int)$idCategory);
 
    $categoryRepo->deleteCategoryById($category['category']['id']);
 
    header('Location: /admin/categories');
    exit();
 });

 $app->get("/categories/:idCategory", function($idCategory){
   $categoryRepo = new CategoryRepository();

   $category = $categoryRepo->getCategoryById((int)$idCategory);
   
   $page = new Page();
   $page->setTpl('category', [
      'category' => $category['category'],
      'products' => $categoryRepo->getProductsFromCategoryRelated((int)$idCategory),
   ]);
   exit();
});

 $app->get("/admin/categories/:idCategory/products", function($idCategory){
    UserRepository::verifyLogin();
   $categoryRepo = new CategoryRepository();

   $category = $categoryRepo->getCategoryById((int)$idCategory);
   $page = new PageAdmin();

   $page->setTpl("categories-products", [
      'category' => $category['category'],
      'productsRelated' => $categoryRepo->getProductsFromCategoryRelated((int)$idCategory),
      'productsNotRelated' => $categoryRepo->getProductsFromCategoryRelated((int)$idCategory, false),
   ]);
   exit();
 });

 $app->get("/admin/categories/:idCategory/products/:idProduct/add", function($idCategory, $idProduct){
   UserRepository::verifyLogin();
  $categoryRepo = new CategoryRepository();
  $productRepo = new ProductRepository();
  $product = $productRepo->getProductById((int)$idProduct);
  $categoryRepo->addProductOnCategory((int)$idCategory, $product);

  header("Location: /admin/categories/".$idCategory."/products");
  exit();
});

$app->get("/admin/categories/:idCategory/products/:idProduct/remove", function($idCategory, $idProduct){
   UserRepository::verifyLogin();
  $categoryRepo = new CategoryRepository();
  $productRepo = new ProductRepository();
  $product = $productRepo->getProductById((int)$idProduct);
  $categoryRepo->removeProductOnCategory((int)$idCategory, $product);

  header("Location: /admin/categories/".$idCategory."/products");
  exit();
});