<?php

use HcodeEcom\modules\address\models\Address;
use HcodeEcom\modules\address\repositories\AddressRepository;
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

    if (empty($idProduct))

    $page->setTpl("cart", [
        'cart' => [
            'id'                =>    $cart->getId(),
            'idUser'            =>    $cart->getIdUser(),
            'idAddress'         =>    $cart->getIdAddress(),
            'securitySessionId' =>    $cart->getSecuritySessionId(),
            'freight'           =>    $cart->getFreight(),
            'dateRegister'      =>    $cart->getDateRegister(),
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
    $cartRepo = new CartRepository();
    $addressRepo = new AddressRepository();
    
    $sCart = CartRepository::getCartFromSession();

    $address = new Address();    
    
    $page = new Page();
    
    $zipCode = '';

    if(isset($_GET['zipCode']) && !empty($_GET['zipCode'])){
        $addressRepo->setAddressDataFromCep($address, $_GET['zipCode']);
        $zipCode = $_GET['zipCode'];
    }

    if(isset($_GET['zipcode']) && !empty($_GET['zipcode'])){
        $addressRepo->setAddressDataFromCep($address, $_GET['zipcode']);
        $zipCode = $_GET['zipcode'];
    }

    $page->setTpl("checkout", [
        'products' =>  $cartRepo->getProductByIdCart($sCart->getId()),
        'cartFreight' =>  $cartRepo->setFreightPriceByCart($sCart, $zipCode) ["resultXml"]->Valor[0],
        'productTotal' =>  $cartRepo->getTotalProductByCartId($sCart->getId()),
        'totals' => $cartRepo->calculateTotalByCartId($sCart->getId(), isset($zipCode) ? $zipCode : "undefined"),
        'address' => [
            "address" => $address->getAddress(),
            "complement" => $address->getComplement(),
            "city" => $address->getCity(),
            "state" => $address->getState(),
            "country" => $address->getCountry(),
            "zipCode" => $address->getZipCode(),
        ],
        'error' => AddressRepository::getMsgError()
    ]);
    exit();
});

$app->post("/checkout", function(){
    UserRepository::checkUserLogin(false);
    $address = new Address();
    $adressRepo = new AddressRepository();
    $cUser = UserRepository::getUserFromSession();

    isset($_POST['zipCode']) ? $_POST['zipCode'] = $_POST['zipCode'] : $_POST['zipCode'] ="undefined";
    
    if(!isset($_POST['zipCode']) || empty($_POST['zipCode'])){
        AddressRepository::setMsgError("You must to set a Zip code");
        header("Location: /checkout?zipcode=".$_POST['zipCode'].'&proceed=Finalizar+Compra');
        exit();
    }
    
    if(!isset($_POST['address']) || empty($_POST['address'])){
        AddressRepository::setMsgError("You must to set a Address");
        header("Location: /checkout?proceed=Finalizar+Compra");
        exit();
    }
    
    if(!isset($_POST['residenceNumber']) || empty($_POST['residenceNumber'])){
        var_dump("Caiu aqui");
        AddressRepository::setMsgError("You must to set your house number");
        header("Location: /checkout?zipcode=".$_POST['zipCode'].'&proceed=Finalizar+Compra');
        exit();
    }
    
    if(!isset($_POST['district']) || empty($_POST['district'])){
        AddressRepository::setMsgError("You must to set your district");
        header("Location: /checkout?zipcode=".$_POST['zipCode'].'&proceed=Finalizar+Compra');
        exit();
    }
    if(!isset($_POST['city']) || empty($_POST['city'])){
        AddressRepository::setMsgError("You must to set your city");
        header("Location: /checkout?zipcode=".$_POST['zipCode'].'&proceed=Finalizar+Compra');
        exit();
    }
    if(!isset($_POST['state']) || empty($_POST['state'])){
        AddressRepository::setMsgError("You must to set your state");
        header("Location: /checkout?zipcode=".$_POST['zipCode'].'&proceed=Finalizar+Compra');
        exit();
    }
    if(!isset($_POST['country']) || empty($_POST['country'])){
        AddressRepository::setMsgError("You must to set your country");
        header("Location: /checkout?zipcode=".$_POST['zipCode'].'&proceed=Finalizar+Compra');
        exit();
    }
    
    $address->setIdPerson((int)$cUser->getIdPerson());
    $address->setAddress($_POST['address']);
    $address->setComplement($_POST['complement']);
    $address->setCity($_POST['city']);
    $address->setState($_POST['state']);
    $address->setCountry($_POST['country']);
    $address->setZipCode($_POST['zipCode']);
    $address->setNResidence($_POST['residenceNumber']);

    $adressRepo->createAddress($address);

    header("Location: /order");
    exit();

});
    
