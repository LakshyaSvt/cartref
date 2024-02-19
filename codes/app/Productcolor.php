<?php

namespace App;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Productcolor extends Model
{
    use SoftDeletes;

    protected $fillable = ['color', 'status', 'more_images', 'main_image'];


    public function colors()
    {
        return $this->belongsTo(Color::class, 'color', 'name');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function getJsonMoreImagesAttribute()
    {
        return json_decode($this->more_images) ?? [];
    }
}
