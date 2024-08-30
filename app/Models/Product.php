<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\Heart;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'image',
        'category_id',
        'brand_id',
        'expire_date'
    ];

    protected $casts = [
        'image' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }


    public function hearts()
    {
        return $this->hasMany(Heart::class);
    }

    public function isHeartedBy(User $user)
    {
        return $this->hearts()->where('user_id', $user->id)->exists();
    }
}
