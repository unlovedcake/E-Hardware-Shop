<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCart extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'brand_name', 'category_name', 'product_id', 'user_id', 'quantity', 'price', 'total', 'image', 'created_at'];
}
