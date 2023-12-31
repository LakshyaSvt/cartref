<?php

namespace App;

use App\Size;
use App\Models\Product;
use App\ProductCategory;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;


class ProductSubcategory extends Model
{
    protected $table = "product_subcategories";

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    
    public function products()
    {
        return $this->hasMany(Product::class, 'subcategory_id', 'id')->where('admin_status', 'Accepted');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    
}
