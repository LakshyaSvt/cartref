<?php

namespace App\Models;

use App\Address;
use App\Order;
use App\Showcase;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use TCG\Voyager\Models\Role;

// extends \TCG\Voyager\Models\User

class User extends \TCG\Voyager\Models\User implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'client_id',
        'client_token',
        'client_refresh_token',
        'client_name',

        'name',
        'email',
        'gender',
        'mobile',
        'password',
        'role_id',
        'street_address_1',
        'street_address_2',
        'landmark',
        'pincode',
        'city',
        'state',
        'country',
        //side-info
        'avatar',
        'brand_store_rating',
        'status',
        'brand_name',
        'company_name',
        'gst_number',
        'gst_certificate',
        'company_pancard_number',
        'company_pancard',
        'signature',
        //bank-info
        'bank_name',
        'account_number',
        'ifsc_code',
        'bank_address',
        'cancelled_check',
        //brand-info
        'brands',
        'brand_description',
        'brand_logo',
        'brand_bg_color',
        'is_first_shopping'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function scopeVendors()
    {
        if (auth()->user()->hasRole('Vendor')) {
            return $this->where('status', '1')->where('id', auth()->user()->id);
        } else {
            return $this->where('status', '1')
                ->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['Admin', 'Vendor']);
                })
                ->orWhereHas('role', function ($q) {
                    $q->whereIn('name', ['Admin', 'Vendor']);
                });
        }
    }

    public function dbshowcases()
    {
        return $this->hasMany(Showcase::class, 'deliveryboy_id', 'id');
    }

    public function dborder()
    {
        return $this->hasMany(Order::class, 'vendor_id', 'id');
    }

    public function userorder()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function dbproduct(){
        return $this->hasMany(Product::class,'seller_id', 'id');
    }

    public function dbcart(){
        return $this->hasMany(Cart::class,'user_id', 'id');
    }

    public function dbwishlist(){
        return $this->hasMany(Wishlist::class,'user_id', 'id');
    }

    public function scopeDeliveryboy()
    {

        return $this->where('status', '1')
            ->whereHas('role', function ($q) {
                $q->whereIn('name', ['Delivery Boy']);
            })
            ->whereDoesntHave('dbshowcases', function ($q) {
                $q->where('status', 1);
            })
            ->where('city', auth()->user()->city);
    }

    public function scopeSeller($query)
    {
        return $query
            ->when(auth()->user()->hasRole(['admin', 'Client']), function ($que) {
                return $que->whereHas('role', function ($q) {
                    $q->whereIn('name', ['Vendor']);
                });
            })
            ->when(auth()->user()->hasRole(['Vendor']), function ($que) {
                return $que->whereHas('role', function ($q) {
                    $q->whereIn('name', ['Vendor']);
                    $q->where('users.id', auth()->user()->id);
                });
            });
    }
}
