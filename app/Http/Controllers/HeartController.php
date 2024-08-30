<?php

namespace App\Http\Controllers;

use App\Models\Heart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function heart(Product $product)
    {
        $user = Auth::user();

        if ($product->isHeartedBy($user)) {
            $product->hearts()->where('user_id', $user->id)->delete();
            return response()->json(['status' => 'unhearted']);
        } else {
            $product->hearts()->create(['user_id' => $user->id]);
            return response()->json(['status' => 'hearted']);
        }
    }
}
