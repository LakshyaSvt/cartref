<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CollectionImage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'image',
        'mb_image',
        'url',
        'collection_id',
        'status',
        'order_id',
    ];

    public function collection(){
        return $this->belongsTo(Collection::class);
    }
}
