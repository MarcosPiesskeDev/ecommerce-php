<?php
require __DIR__.'/../vendor/autoload.php';

use HcodeEcom\modules\product\models\Product;
use HcodeEcom\modules\product\repository\ProductRepository;
use HcodeEcom\modules\user\repositories\UserRepository;
use HcodeEcom\pages\Page;
use HcodeEcom\pages\PageAdmin;

$app->get("/admin/products", function(){
    
    UserRepository::verifyLogin();
    $productRepo = new ProductRepository();
    $products = $productRepo->getAllProducts();
    $page = new PageAdmin();

    $page->setTpl("products", [
        'product' => $products,
    ]);
});

$app->get("/admin/products/create", function(){
    UserRepository::verifyLogin();
    $page = new PageAdmin();

    $page->setTpl("products-create");
});

$app->post("/admin/products/create", function(){
    UserRepository::verifyLogin();
    $product = new Product();
    $productRepo = new ProductRepository();

    $product->setName($_POST['name']);
    $product->setPrice($_POST['price']);
    $product->setWidth($_POST['width']);
    $product->setHeight($_POST['height']);
    $product->setLength($_POST['length']);
    $product->setWeight($_POST['weight']);
    $product->setUrl($_POST['url']);

    $productRepo->createProduct($product);
    header('Location: /admin/products');
    exit();
});

$app->get("/admin/products/:idProduct", function($idProduct){
    
    UserRepository::verifyLogin();
    $productRepo = new ProductRepository();

    $product = $productRepo->getProductById((int)$idProduct);
    $page = new PageAdmin();

    $page->setTpl("products-update", [
        'product' => [
            'id'     =>  $product['product']['id'],
            'name'   =>  $product['product']['name'],
            'price'  =>  $product['product']['price'],
            'width'  =>  $product['product']['width'],
            'height' =>  $product['product']['height'],
            'length' =>  $product['product']['length'],
            'weight' =>  $product['product']['weight'],
            'url'    =>  $product['product']['url'],
            'photo'  =>  $product['product']['photo'],
        ],
    ]);
});

$app->post("/admin/products/:idProduct", function($idProduct){
    UserRepository::verifyLogin();
    $productRepo = new ProductRepository();
    $product = new Product();

    $product->setName($_POST['name']);
    $product->setPrice($_POST['price']);
    $product->setWidth($_POST['width']);
    $product->setHeight($_POST['height']);
    $product->setLength($_POST['length']);
    $product->setWeight($_POST['weight']);
    $product->setUrl($_POST['url']);
    $product->setPhoto($_FILES['file']);
    
    $productRepo->updateProductById($idProduct, $product);
    $productRepo->setPhotoByExtension($idProduct, $_FILES['file']);

    header('Location: /admin/products');
    exit();
});

$app->get("/admin/products/:idProduct/delete", function($idProduct){
    UserRepository::verifyLogin();
    $productRepo = new ProductRepository();
    $productRepo->deleteProductById($idProduct);
    header('Location: /admin/products');
    exit();
});

$app->get("/products/:url", function($url){
    $productRepo = new ProductRepository();
    $product = $productRepo->getProductFromURL($url);
    
    $page = new Page();
    $page->setTpl('product-detail', [
        'product' => $product,
        'categories'=> $productRepo->getCategoryFromURL($product['id']),
    ]);
});