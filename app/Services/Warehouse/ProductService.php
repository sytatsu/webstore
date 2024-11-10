<?php

namespace App\Services\Warehouse;

use App\Models\Product;
use App\Repositories\Warehouse\ProductRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {
        //
    }

    public function getProductList(): Collection
    {
        return $this->productRepository->all(withRelations: [
            'brand',
            'category',
            'productVariants',
            'productVariants.variant',
        ]);
    }
}
