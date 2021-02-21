<?php

namespace HcodeEcom\modules\product\interfaces;

use HcodeEcom\modules\product\models\Product;

interface IProduct{

    public function getAllProducts();

    public function getProductById(int $id);

    public function createProduct(Product $product);

    public function updateProductById(int $id, Product $product);

    public function deleteProductById(int $id);
} 