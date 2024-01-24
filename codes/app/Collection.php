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
        'url',
        'group_name',
        'status',
        'order_id',
        'image',
        'desktop_visiblity',
        'desktop_columns',
        'mobile_visiblity',
        'mobile_columns',
        'background_color',
        'tablet_visiblity',
        'tablet_columns',
        'desktop_carousel',
        'tablet_carousel',
        'mobile_carousel',
        'desktop_gap',
        'tablet_gap',
        'mobile_gap',
        'background_image',
        'background_opacity',
        'font_size'
    ];
    public function collections()
    {
        return $this->belongsTo(CollectionImage::class, 'id', 'collection_id')->where('status', 1)->orderBy('order_id', 'asc');
    }
}
