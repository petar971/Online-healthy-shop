<?php


namespace AppBundle\Service\Product;


use AppBundle\Entity\Product;

interface ProductServiceInterface
{
    public function edit(Product $product);
    public function create(Product $product);
    public function delete(Product $product);
    public function getOne($id);
    public function getGroupProduct($category);
    public function getAll();

}