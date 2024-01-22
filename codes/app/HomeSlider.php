<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class HomeSlider extends Model
{
    use SoftDeletes;
    protected $table = 'homesliders';
    
    protected $fillable = [
        'category',
        'background_image',
        'mb_background_image',
        'url',
        'page_description',
        'status'
    ];
}
