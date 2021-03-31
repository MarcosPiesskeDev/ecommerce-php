<?php

use HcodeEcom\modules\address\models\Address;
use HcodeEcom\modules\cart\repository\CartRepository;
use HcodeEcom\modules\product\models\Product;
use HcodeEcom\modules\product\repository\ProductRepository;
use HcodeEcom\modules\user\repositories\UserRepository;
use HcodeEcom\pages\Page;


require __DIR__.'/../vendor/autoload.php';

$app->get("/cart", function(){

    $cartRepo = new CartRepository();
    $cart = CartRepository::getCartFromSession();
    $page = new Page;

    $page->setTpl("cart", [
        'cart' => [
            'id'                               =>    $cart->getId(),
            'idUser'                      =>    $cart->getIdUser(),
            'idAddress'               =>    $cart->getIdAddress(),
            'securitySessionId' =>    $cart->getSecuritySessionId(),
            'freight'                     =>    $cart->getFreight(),
            'dateRegister'         =>    $cart->getDateRegister(),
        ],
        'products'=>$cartRepo->getProductByIdCart($cart->getId()),
        'error'=>CartRepository::getMsgError(),
        'totals'  => $cartRepo->calculateTotalByCartId($cart->getId(), isset($_GET['zipCode']) ? $_GET['zipCode'] : "undefined"),
    ]);
});

$app->get("/cart/:idProduct/add", function($idProduct){
    $productRepo = new ProductRepository();
    $cartRepo = new CartRepository();
    $product = new Product();

    $productById = $productRepo->getProductById($idProduct);
    
    $cSession = CartRepository::getCartFromSession();

    $quantity = (!empty($_GET['quantity']) ? (int)$_GET['quantity'] : 1);

    $product->setId($productById['product']['id']);
    $product->setName($productById['product']['name']);
    $product->setPrice($productById['product']['price']);
    $product->setWidth($productById['product']['width']);
    $product->setHeight($productById['product']['height']);
    $product->setLength($productById['product']['length']);
    $product->setWeight($productById['product']['weight']);
    $product->setUrl($productById['product']['url']);
    $product->setDateRegister($productById['product']['date_register']);
    $product->setPhoto($productById['product']['photo']);

    for ($i = 0; $i < $quantity; $i++){
        $cartRepo->addProductOnCart($cSession->getId(), $product);
    }
    $receiveData = $cartRepo->setFreightPriceByCart($cSession, isset($_GET['zipCode']) ? $_GET['zipCode'] : 'undefined');
    header("Location: /cart?zipCode=".$receiveData['zipCode'].'&getTime='.(int)$receiveData["resultXml"]->PrazoEntrega);
    exit();
});

$app->get("/cart/:idProduct/minus", function($idProduct){
    $productRepo = new ProductRepository();
    $cartRepo = new CartRepository();
    $product = new Product();

    $productById = $productRepo->getProductById($idProduct);

    $cSession = CartRepository::getCartFromSession();

    $product->setId($productById['product']['id']);
    $product->setName($productById['product']['name']);
    $product->setPrice($productById['product']['price']);
    $product->setWidth($productById['product']['width']);
    $product->setHeight($productById['product']['height']);
    $product->setLength($productById['product']['length']);
    $product->setWeight($productById['product']['weight']);
    $product->setUrl($productById['product']['url']);
    $product->setDateRegister($productById['product']['date_register']);
    $product->setPhoto($productById['product']['photo']);

    $cartRepo->removeProductOnCart($cSession->getId(), $product);
    $receiveData = $cartRepo->setFreightPriceByCart($cSession, isset($_GET['zipCode']) ? $_GET['zipCode'] : 'undefined');
  
    header("Location: /cart?zipCode=".$receiveData['zipCode'].'&getTime='.(int)$receiveData["resultXml"]->PrazoEntrega);
    exit();
});

$app->get("/cart/:idProduct/remove", function($idProduct){
    $productRepo = new ProductRepository();
    $cartRepo = new CartRepository();
    $product = new Product();

    $productById = $productRepo->getProductById($idProduct);

    $cSession = CartRepository::getCartFromSession();

    $product->setId($productById['product']['id']);
    $product->setName($productById['product']['name']);
    $product->setPrice($productById['product']['price']);
    $product->setWidth($productById['product']['width']);
    $product->setHeight($productById['product']['height']);
    $product->setLength($productById['product']['length']);
    $product->setWeight($productById['product']['weight']);
    $product->setUrl($productById['product']['url']);
    $product->setDateRegister($productById['product']['date_register']);
    $product->setPhoto($productById['product']['photo']);

    $cartRepo->removeProductOnCart($cSession->getId(), $product, true);
    
    $receiveData = $cartRepo->setFreightPriceByCart($cSession, isset($_GET['zipCode']) ? $_GET['zipCode'] : 'undefined');
    
    header("Location: /cart?zipCode=".$receiveData['zipCode'].'&getTime='.(int)$receiveData["resultXml"]->PrazoEntrega);
    exit();
});

$app->post("/cart/freight", function(){

    $cartRepo = new CartRepository();
    
    $cSession = CartRepository::getCartFromSession();

    $receiveData = $cartRepo->setFreightPriceByCart($cSession, $_POST['zipcode']);

    header("Location: /cart?zipCode=".$receiveData['zipCode']."&getTime=".(int)$receiveData ["resultXml"]->PrazoEntrega);
    exit();
});

$app->get("/checkout", function(){
    UserRepository::checkUserLogin(false);

    $sCart = CartRepository::getCartFromSession();
    
    //$address = new Address();    

    $page = new Page();

    $page->setTpl("checkout", [

    ]);
});

