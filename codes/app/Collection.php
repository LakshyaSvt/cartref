<?php

namespace App;

use App\CollectionImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Collection extends Model
{
    use SoftDeletes;

    protected $fillabe = [
        'category',
//        ''
    ];
    public function collections()
    {
        return $this->belongsTo(CollectionImage::class, 'id', 'collection_id')->where('status', 1)->orderBy('order_id', 'asc');
    }
}
