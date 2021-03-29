<?php

namespace HcodeEcom\modules\cart\interfaces;

use HcodeEcom\modules\cart\models\Cart;

interface ICart{

    public function getAllCarts();

    public function getCartById(int $id);

    public function createCart(Cart $cart);
    
    public function updateCart(int $id, Cart $cart);

    public function deleteCartById(int $id);
}
