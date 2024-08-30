<?php

// app/Http/Controllers/CartController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\TransactionCart;

class TransactionCartController extends Controller
{
    public function store(Request $request)
    {


        // Assuming request contains a list of cart items
        $cartItems = $request->input('cart_items'); // e.g., [{ 'product_id': 1, 'quantity': 2, 'price': 100 }, ...]



        DB::beginTransaction();

        try {
            // Create TransactionCart entries
            foreach ($cartItems as $item) {
                $product = Product::find($item['product_id']);

                if (!$product || $product->quantity < $item['quantity']) {
                    // Rollback and return error if product is not found or not enough stock
                    DB::rollBack();
                    return response()->json(['error' => 'Insufficient stock for product ID ' . $item['product_id']], 400);
                }

                // Create TransactionCart entry
                TransactionCart::create([

                    'product_id' => $item['product_id'],
                    'user_id' => \Auth::id(),
                    'name' => $item['name'],
                    'brand_name' => $item['brand_name'],
                    'category_name' => $item['category_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                    'image' => $item['image'][0],

                ]);

                // Update product quantity
                $product->quantity -= $item['quantity'];
                $product->save();
            }

            DB::commit();
            return response()->json(['message' => 'Transaction completed successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Transaction failed.'], 500);
        }
    }
}
