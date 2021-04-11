<?php

namespace HcodeEcom\modules\cart\repository;

use HcodeEcom\db\MethodsDb;
use HcodeEcom\modules\cart\interfaces\ICart;
use HcodeEcom\modules\cart\models\Cart;
use HcodeEcom\modules\product\models\Product;
use HcodeEcom\modules\product\repository\ProductRepository;
use HcodeEcom\modules\user\repositories\UserRepository;

require __DIR__.'../../../../../vendor/autoload.php';

class CartRepository implements ICart{

    const SESSION = "Cart";
    const SESSION_ERROR = "CartError";

    public static function getCartFromSession()
    {
        $cartRepo = new CartRepository();
        $cart = new Cart();

        if (!empty($_SESSION[CartRepository::SESSION]) && !empty($_SESSION[CartRepository::SESSION]['id']) && (int)$_SESSION[CartRepository::SESSION]['id'] > 0){
            $cartRepo->getCartById((int)$_SESSION[CartRepository::SESSION]['id']);
        }else{
           
            if (!empty($_SESSION[CartRepository::SESSION]) && !empty($_SESSION[CartRepository::SESSION][0])){
                $cartFromId = $cartRepo->getCartBySessionId($_SESSION[CartRepository::SESSION][0]);
                $cart->setId($cartFromId['id']);
                $cart->setSecuritySessionId($cartFromId['security_session_id']);
                $cart->setIdUser($cartFromId['id_user']);
                $cart->setIdAddress($cartFromId['id_address']);
                $cart->setFreight($cartFromId['freight']);
                $cart->setDateRegister($cartFromId['date_register']);
            }

                if( $cart->getId() == null || !(int)$cart->getId() > 0){
                    $cart->setSecuritySessionId(session_id());
                    $userRepo = new UserRepository();
                    if ($userRepo->getUserFromSession() === false){
                        if (UserRepository::checkUserLogin(false)){
                            $userSession = $userRepo->getUserFromSession();
                            $cart->setIdUser($userSession->getId());
                        }
                    }

                    $cartRepo->createCart($cart);
    
                    $cartRepo->setToSession($cart);
    
                }
        }
        return $cart;
    }

    public function setToSession(Cart $cart)
    {
        $_SESSION[CartRepository::SESSION] = [
            $cart->getSecuritySessionId(),
            $cart->getIdUser(),
            $cart->getIdAddress(), 
            $cart->getFreight(), 
            $cart->getDateRegister(),
        ];
    }

    public function getCartBySessionId(string $sessionId)
    {
        $conn = new MethodsDb();

        $result = $conn->select(
            "SELECT 
                id, security_session_id, id_user, id_address, freight, date_register
            FROM
                cart 
            WHERE 
                security_session_id = '".$sessionId."';
            "
        );

        if ($result == []){
            return false;
        }

        return $result[0];
    }

    public function getAllCarts()
    {
        $conn = new MethodsDb();

        return $conn->select(
            "SELECT 
	            c.id, c.security_session_id, c.id_user, c.id_address, c.freight, c.date_register
            FROM
	            cart c;
            "
        );
    }

    public function getCartById(int $id)
    {

        $conn = new MethodsDb();

        $result = $conn->select(
            "SELECT 
                c.id, c.security_session_id, c.id_user, c.id_address, c.freight, c.date_register
            FROM
                cart c
            WHERE 
                c.id = ".$id.";
            "
        );

        return $result[0];
    }

    public function createCart(Cart $cart)
    {

        if (!empty($this->getCartBySessionId($cart->getSecuritySessionId()))){
            $dbCart = $this->getCartBySessionId($cart->getSecuritySessionId());
            $cart->setSecuritySessionId($dbCart['security_session_id']);
            $cart->setIdUser($cart->getIdUser() == NULL ? $dbCart['id_user'] : $cart->getIdUser());
            $cart->setIdAddress($cart->getIdAddress() == NULL ? $dbCart['id_address'] : $cart->getIdAddress());
            $cart->setFreight($cart->getFreight() == NULL ? $dbCart['freight'] : $cart->getFreight());
            $cart->setDateRegister(date("Y-m-d H:i:s"));
            $this->updateCart((int)$dbCart['id'], $cart);
        }else{

            if(empty($cart->getIdUser())){
                $cart->setIdUser(0);
                $cart->setIdAddress(0);
                $cart->setFreight(0);
                $cart->setDateRegister(date("Y-m-d H:i:s"));    
            }
            $conn = new MethodsDb();

            $conn->query(
                "INSERT INTO 
                    cart(security_session_id, id_user, id_address, freight, date_register)
                VALUES 
                    (
                        '".$cart->getSecuritySessionId()."', 
                        ".$cart->getIdUser().", 
                        ".$cart->getIdAddress().", 
                        ".$cart->getFreight().", 
                        '".$cart->getDateRegister()."' 
                    );
                "
            );
        }
    }
    
    public function updateCart(int $id, Cart $cart)
    {
        $conn = new MethodsDb();

        $conn->query(
            "UPDATE
	            cart c
            SET
	            security_session_id =  '".$cart->getSecuritySessionId()."', 
                id_user =  ".$cart->getIdUser().",
                id_address =  ".$cart->getIdAddress().",
                freight = ".$cart->getFreight()."
            WHERE 
                c.id = '".$id."';
            "
        );
    }

    public function deleteCartById(int $id)
    {
        $conn = new MethodsDb();

        $conn->query(
            "DELETE 
            FROM 
                cart c 
            WHERE 
                c.id = '".$id."';   
            "
        );
    }

    public function addProductOnCart(int $idCart, Product $product)
    {
        $conn = new MethodsDb();

        $conn->query(
            "INSERT INTO
               product_cart(id_cart, id_product)
            VALUES
                ('".$idCart."', '".$product->getId()."');
            "
        );

        if (isset($_GET['zipCode']) && $_GET['zipCode'] != 'undefined'){
            $this->calculateTotalByCartId($idCart, $_GET['zipCode']);
        }
    }

    public function removeProductOnCart(int $idCart, Product $product, $all = false)
    {
        $conn = new MethodsDb();
        if ($all === true){
            $conn->query(
                "UPDATE
                    product_cart
                SET
                    date_removed = NOW()
                WHERE 
                    id_cart = '".$idCart."'
                AND
                    id_product = '".$product->getId()."'
                AND date_removed IS NULL;
                "
            );
        }else{
            $conn->query(
                "UPDATE
                    product_cart
                SET
                    date_removed = NOW()
                WHERE 
                    id_cart = '".$idCart."'
                AND
                    id_product = '".$product->getId()."'
                AND date_removed IS NULL LIMIT 1;
                "
            );
        }
        if (isset($_GET['zipCode']) && $_GET['zipCode'] != 'undefined'){
            $this->calculateTotalByCartId($idCart, $_GET['zipCode']);
        }
    }

    public function getProductByIdCart($idCart)
    {
        $conn = new MethodsDb();
        $productRepo = new ProductRepository();
        $product = new Product();

        if (!empty($idCart)){
            $results = $conn->select(
                "SELECT 
                    p.id, p.name, p.price, p.width, p.height, p.length, p.weight, p.url, p.date_register,
                    COUNT(*) AS amount, SUM(p.price) AS total_value
                FROM
                    product_cart pc
                INNER JOIN 
                    product p 
                ON 
                    pc.id_product = p.id
                WHERE
                    pc.id_cart = '".(int)$idCart."' AND pc.date_removed IS NULL
                GROUP BY
                    p.id, p.name, p.price, p.width, p.height, p.length, p.weight, p.url, p.date_register
                ORDER BY
                    p.name;
                "
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
                    'amount'        => $value['amount'],
                    'total_value'    => $value['total_value'],
                ];
                array_push($data, $i);
            }
            
            return $data;
        }
        return [];
    }

    public function getTotalProductByCartId(int $idCart)
    {
        $conn = new MethodsDb();
        $result = $conn->select(
            "SELECT 
                SUM(price) AS price, SUM(width) AS width, SUM(height) AS height, SUM(length) AS length, SUM(weight) AS weight, COUNT(*) AS amount
            FROM
                product p
            INNER JOIN 
                product_cart pc 
            ON 
                p.id = pc.id_product
            WHERE 
                pc.id_cart = '".$idCart."'
            AND
                pc.date_removed IS NULL;
            "
        );
   
        return count($result) > 0 ? $result[0] : [];
    }

    public static function setMsgError($msg)
	{

		$_SESSION[CartRepository::SESSION_ERROR] = $msg;

	}

	public static function getMsgError()
	{

		$msg = (isset($_SESSION[CartRepository::SESSION_ERROR])) ? $_SESSION[CartRepository::SESSION_ERROR] : "";

		CartRepository::clearMsgError();

		return $msg;

	}

	public static function clearMsgError()
	{

		$_SESSION[CartRepository::SESSION_ERROR] = NULL;

	}

    public function setFreightPriceByCart(Cart $cart, string $zipCode)
    {
        $zipCodeFormated = str_replace('-', '', $zipCode);

        $total = $this->getTotalProductByCartId((int)$cart->getId());

        if ($total['amount'] > 0){
            if ($total['height'] < 2) $total['height'] = 2;
            if ($total['length'] < 16) $total['length'] = 16;

            $qs = http_build_query([
                'nCdEmpresa'=>'',
				'sDsSenha'=>'',
				'nCdServico'=>'40010',
				'sCepOrigem'=>'09853120',
				'sCepDestino'=>$zipCodeFormated,
				'nVlPeso'=>str_replace('.', ',', $total['weight']),
				'nCdFormato'=>'1',
				'nVlComprimento'=>str_replace('.', ',', $total['length']),
				'nVlAltura'=>str_replace('.', ',', $total['height']),
				'nVlLargura'=>str_replace('.', ',', $total['width']),
				'nVlDiametro'=>'0',
				'sCdMaoPropria'=>'S',
				'nVlValorDeclarado'=> str_replace('.', ',', $total['price']),
				'sCdAvisoRecebimento'=>'S'
                ]);

            $xml = simplexml_load_file("http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?".$qs);

			$result = $xml->Servicos->cServico;
            
			if ($result->MsgErro[0] != '') {
				CartRepository::setMsgError($result->MsgErro[0]);
			} else {
				CartRepository::clearMsgError();
			}

            $cart->setFreight(CartRepository::formatValueToDecimal($result->Valor));

            $this->createCart($cart);

            $returnData = [
                'resultXml' => $result,
                'zipCode'=> $zipCode,
            ]; 

            return $returnData;
        }
    }

    public static function formatValueToDecimal($value):float
	{
		$value = str_replace('.', '', $value);
		return str_replace(',', '.', $value);
	}

    public function updateFreight(int $idCart, string $zipCode)
	{
		if ($zipCode != '') {
			$this->setFreightPriceByCart($this->getCartById($idCart), $zipCode);
		}
	}

    public function calculateTotalByCartId($idCart, string $zipCode = null)
    {
        if (!empty($idCart)){
            if ($zipCode != null){

                $cart = $this->getCartById((int)$idCart);

                $total = $this->getTotalProductByCartId((int)$idCart);
                
                return [
                    'subTotal' => $total['price'],
                    'total' => $total['price'] + $cart['freight'],
                ];
            }
        }
        return [
            'subTotal' => 0,
            'total' => 0,
        ];
    }
}