<?php

namespace App\Traits;

use App\Models\Product;

trait ProductTrait
{
    public function productTrait($val)
    {
        return $val;
    }


    public function searchProduct($searchVal)
    {


        $products = Product::select('id', 'name', 'image', 'description', 'price', 'quantity', 'brand_id', 'category_id')->with(['category:id,name', 'brand:id,name'])
            ->where('products.name', 'like', '%' . $searchVal . '%')
            ->orWhereHas('category', function ($query) use ($searchVal) {
                $query->where('categories.name', 'like', '%' . $searchVal . '%');
            })
            ->orWhereHas('brand', function ($query) use ($searchVal) {
                $query->where('brands.name', 'like', '%' . $searchVal . '%');
            })
            ->paginate();


        return $products;
    }

    public function filterProduct($filterCategory, $filterBrand)
    {


        $products = Product::select('id', 'name', 'image', 'description', 'price', 'quantity', 'brand_id', 'category_id')->with(['category:id,name', 'brand:id,name'])
            ->orWhereHas('category', function ($query) use ($filterCategory) {
                $query->whereIn('categories.name',  $filterCategory);
            })
            ->orWhereHas('brand', function ($query) use (
                $filterBrand
            ) {
                $query->whereIn(
                    'brands.name',
                    $filterBrand
                );
            })
            ->paginate(8);
        return $products;
    }
}
