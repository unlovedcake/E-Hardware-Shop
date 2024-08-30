<?php

namespace App\Http\Controllers;

use session;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {

        $carts = $request->session()->get('cart', []);
        $cartCount = count($carts);


        return Inertia::render('Home/Cart', ['carts' => $carts, 'cartCountItems' => $cartCount]);
    }

    public function addToCart(Request $request, $id)
    {
        //$product = Product::findOrFail($id)->with(['category', 'brand']);


        $product = Product::with(['category', 'brand'])->find($id);
        // $request->session()->forget('cart');




        if (!$product) {
            //return redirect()->route('products.index')->with('error', 'Product not found.');
        }

        $cart = $request->session()->get('cart', []);


        if (isset($cart[$id])) {
            if ($request->action == 'increment') {
                $cart[$id]['quantity']++;
            } else {
                if ($cart[$id]['quantity'] <=  1) {
                    return;
                }
                $cart[$id]['quantity']--;
            }
        } else {

            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'category_name' =>  $product->category->name,
                'brand_name' =>  $product->brand->name,
                'available_quantity' =>  $product->quantity,
                'quantity' =>  1,
                'price' => $product->price,
                'image' => $product->image,
                'description' => $product->description,
                'message' => '',

            ];
        }

        $request->session()->put('cart', $cart);
        $cart = session()->get('cart', []);
        session()->put('cart', $cart);


        //$cartCountItems = count($cart);

        // return response()->json([$cartCountItems]);
    }

    public function removeFromCart(Request $request, $id)
    {

        //$request->session()->forget('cart');

        $cart = $request->session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            $request->session()->put('cart', $cart);
        }
    }
}
