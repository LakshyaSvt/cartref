<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryComponentSlider extends Model
{

    use HasFactory;

    protected $fillable = [
        'title',
        'category_slug',
        'icon',
        'is_active',
        'order'
    ];
}
