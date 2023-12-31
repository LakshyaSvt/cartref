<?php

namespace App;

use App\Color;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;


class Productcolor extends Model
{
    protected $fillable = ['status'];


    public function colors()
    {
        return $this->belongsTo(Color::class, 'color', 'name');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

}
