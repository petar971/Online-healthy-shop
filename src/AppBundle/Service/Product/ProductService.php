<?php


namespace AppBundle\Service\Product;


use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;

class ProductService implements ProductServiceInterface
{

    private $productRepository;
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository=$productRepository;
    }

    public function edit(Product $product)
    {
       $this->productRepository->update($product);
    }

    public function create(Product $product)
    {
         $this->productRepository->insert($product);
    }

    public function delete(Product $product)
    {
        $this->productRepository->remove($product);
    }

    public function getOne($id)
    {
       $this->productRepository->find($id);
    }

    public function getGroupProduct($category)
    {
        $this->productRepository->findBy(
            [
                'category' => $category
            ],
            [
                'price' => 'ASC'
            ]
        );
    }

    public function getAll()
    {
       $this->productRepository->findAll();
    }
}