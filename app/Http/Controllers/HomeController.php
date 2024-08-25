<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ProductTrait as TraitsProductTrait;

class HomeController extends Controller
{

    use TraitsProductTrait;
    public function index(Request $request)
    {


        // session()->forget('cart');
        // $cart = session()->get('cart', []);
        $searchVal = $request->searchValue;
        $filterCategory = $request->filterCategoryName;
        $filterBrand   = $request->filterBrandName;

        $categories = Category::all();
        $brands = Brand::all();



        //Convert to array
        if ($filterCategory && !is_array($filterCategory) || $filterBrand && !is_array($filterBrand)) {
            $filterCategory = explode(',', $filterCategory);
            $filterBrand = explode(',', $filterBrand);
        }

        $perPage = 4;
        $page = $request->page;



        if ($filterCategory && $filterBrand) {

            $products = $this->filterProduct($filterCategory, $filterBrand);
        } else if ($searchVal) {

            $products = $this->searchProduct($searchVal);
        } else  if ($request->wantsJson()) {


            $products = Product::select('id', 'name', 'image', 'description', 'price', 'quantity', 'brand_id', 'category_id')
                ->with(['category:id,name', 'brand:id,name'])
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json($products);
        } else {
            $products = Product::select('id', 'name', 'image', 'description', 'price', 'quantity', 'brand_id', 'category_id')
                ->with(['category:id,name', 'brand:id,name'])
                ->paginate(4);
        }


        $cart = session()->get('cart', []);
        session()->put('cart', $cart);


        $cartCount = count($cart);

        return Inertia::render('Home/Home', ['searchTextFilter' => $searchVal, 'nextPageUrl' => $products->nextPageUrl(), 'products' => $products,  'cartCountItems' => $cartCount, 'cartItems' => $cart,  'categories' => $categories, 'brands' => $brands,]);
    }
}
