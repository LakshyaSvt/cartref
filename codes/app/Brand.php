<?php

namespace App;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Brand extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'status', 'order_id'];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'name');
    }
}
