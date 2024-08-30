<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Brand;
use App\Models\Heart;
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
        $loggedInUserId = \Auth::id();

        // Query the 'heart' table to get all user_ids where user_id matches the logged-in user's ID
        $hearts = Heart::where('user_id', $loggedInUserId)
            ->pluck('product_id') // Get only the 'user_id' column
            ->toArray(); // Convert the collection to an array






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
                ->with(['hearts:id,user_id,product_id', 'category:id,name', 'brand:id,name'])
                ->paginate(4);
        }



        $cart = session()->get('cart', []);
        session()->put('cart', $cart);


        $cartCount = count($cart);

        return Inertia::render('Home/Home', ['searchTextFilter' => $searchVal, 'nextPageUrl' => $products->nextPageUrl(), 'hearts' => $hearts, 'products' => $products,  'cartCountItems' => $cartCount, 'cartItems' => $cart,  'categories' => $categories, 'brands' => $brands,]);
    }
}
