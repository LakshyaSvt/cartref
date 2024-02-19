<?php

namespace App;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Productsku extends Model
{
    use SoftDeletes;
    protected $table = 'productsku';
    
    protected $fillable = ['available_stock', 'status', 'length', 'breath', 'height', 'weight'];   


    public function save(array $options = [])
    {

        parent::save();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
