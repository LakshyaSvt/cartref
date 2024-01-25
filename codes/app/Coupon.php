<?php

namespace App;

use App\Models\CouponUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Coupon extends Model
{

    protected $fillable = [
        'type',
        'value',
        'code',
        'description',
        'min_order_value',
        'from',
        'to',
        'background_color',
        'is_coupon_for_all',
        'is_uwc',
        'user_email',
        'status'
    ];

    use SoftDeletes;
    public function sellers()
    {
        return $this->belongsToMany(User::class);
    }
    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'coupon_brand');
    }
    public function scopeRoleWise($query)
    {
        return $query
            ->when(auth()->user()->hasRole(['admin', 'Client']), function($query){
                return $query;
            })
            ->when(auth()->user()->hasRole(['Vendor']), function($q){
                return $q->whereHas('sellers', function($q){
                    return $q->where('users.id','=', auth()->user()->id);
                });
            })
            ->orderBy('updated_at', 'desc');
    }


    public function hasSellers($sellers)
    {
        if (is_array($sellers) && count($sellers) > 0) {
            foreach ($sellers as $seller) {
                if (!$this->sellers->contains($seller)) {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }

    }

    public function hasBrands($brands)
    {
        if (is_array($brands) && count($brands) > 0) {
            foreach ($brands as $brand) {
                if (!$this->brands->contains($brand)) {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }

    }

}
