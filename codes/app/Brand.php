<?php

namespace App;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;


class Brand extends Model
{
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    
    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'name');
    }
}
