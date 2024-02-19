<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Vendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'brand_name',
        'contact_name',
        'email_address',
        'contact_number',
        'registered_company_name',
        'gst_number',
        'marketplaces'
    ];
}
