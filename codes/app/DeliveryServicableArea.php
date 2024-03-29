<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DeliveryServicableArea extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'city', 'abbreviation', 'state', 'start_at', 'end_at', 'status',
    ];
}
