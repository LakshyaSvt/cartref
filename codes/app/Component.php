<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Component extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'image',
        'title_1',
        'title_2',
        'description',
        'button',
        'url',
        'page_name',
        'status',
    ];
}
