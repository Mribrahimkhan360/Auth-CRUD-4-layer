<?php


namespace App\Services;


use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts()
    {
//        return $this->productRepository->all();
        return $this->productRepository->paginateProducts(5);
    }


    public function getProductById($id)
    {
        return $this->productRepository->find($id);
    }

    public function createProduct(array  $data)
    {
<<<<<<< HEAD
        if (isset($data['discount_type']) && $data['discount_type'] == 0) {
            $data['discount_price'] = null;
        }
=======
>>>>>>> 0cc88cd26b634a398c983d8955ffb73ea96b4b80
        return $this->productRepository->create($data);
    }

    public function updateProduct($id,array $data)
    {
        return $this->productRepository->update($id,$data);
    }

    public function deleteProduct($id)
    {
        return $this->productRepository->delete($id);
    }
}
