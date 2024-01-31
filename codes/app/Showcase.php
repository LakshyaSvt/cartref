<?php

namespace App;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;


class Showcase extends Model
{

    use SoftDeletes;

    protected $fillable = ['order_status', 'status', 'showcase_timer', 'is_timer_extended', 'is_discount_applied'];

    protected $appends = ['color_image', 'color_link', 'nac_charges', 'status_color_class', 'new_order_status'];

    public function scopeRolewise($query)
    {
//        $this->updateDelayAcceptance();
        if (!empty(request('label'))) {
            if (request('label') == 'Showcased') {
                $query->whereIn('order_status', ['Showcased', 'Moved to bag']);
            }
//            elseif(request('label') == 'Delay Acceptance'){
//                $query->where(['is_order_accepted' => 0, 'order_status' => 'Delay Acceptance']);
//            }
            elseif (request('label') == 'Non Acceptance') {
                $query->where(['is_order_accepted' => 0, 'order_status' => 'Non Acceptance']);
            } elseif (request('label') == 'Accepted') {
                $query->where(['is_order_accepted' => 1]);
            } else {
                $query->where('order_status', request('label'));
            }
        }

        if (request('order_id')) {
            $query->where('order_id', request('order_id'));
        }

        if (Auth::user()->hasRole(['Vendor'])) {
            $query->where('vendor_id', auth()->user()->id);
        }

        if (Auth::user()->hasRole(['Delivery Head'])) {
            $query->where('pickup_city', auth()->user()->city);
            $query->where('is_order_accepted', true);
        }

        if (Auth::user()->hasRole(['Delivery Boy'])) {
            $query->where('deliveryboy_id', auth()->user()->id);
            $query->where('is_order_accepted', true);
        }

        return $query->orderBy('updated_at', 'desc');
    }

//    public function  updateDelayAcceptance(){
//        Showcase::where(['is_order_accepted' => 0, 'order_status' => 'New Order'])->where('created_at', '<',now()->subMinute(30))->update([
//            'order_status' => 'Delay Acceptance'
//        ]);
//
//    }


    public function save(array $options = [])
    {


        if (auth()->user()->hasRole(['Delivery Head'])) {
            if (!empty($this->deliveryboy_id)) {
                Showcase::where('order_id', $this->order_id)->update([
                    'deliveryhead_id' => auth()->user()->id,
                    'deliveryboy_id' => $this->deliveryboy_id,
                ]);
            }

        }


        parent::save();

    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id', 'id');
    }

    public function deliveryboy()
    {
        return $this->belongsTo(User::class, 'deliveryboy_id', 'id');
    }

    public function deliveryhead()
    {
        return $this->belongsTo(User::class, 'deliveryhead_id', 'id');
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function getColorImageAttribute()
    {
        if ($this->color) {
            $colorimage = Productcolor::where('color', $this->color)
                ->where('product_id', $this->product_id)
                ->first();

            if (isset($colorimage)) {
                return $colorimage->main_image;
            }
        }
        return $this->product->image;
    }

    public function getColorLinkAttribute()
    {
        if ($this->color) {
            return route('product.slug', ['slug' => $this->product->slug, 'color' => $this->color]);
        }
        return route('product.slug');
    }

    public function getNacChargesAttribute()
    {
        $rate = 10.35 / 100;
        if (!$this->is_order_accepted && $this->order_status == 'Non Acceptance') {
            return round($this->product_offerprice * $rate, 0);
        }
        return 0;
    }

    public function getStatusColorClassAttribute()
    {
        if ($this->order_status == 'Non Acceptance') {
            return 'text-red-600 text-xl';
        }
        if ($this->order_status == 'Under manufacturing') {
            return 'text-orange-500';
        }
        if ($this->order_status == 'Delivered' || $this->order_status == 'Scheduled for pickup' || $this->order_status == 'Purchased') {
            return 'text-green-500';
        }
        if ($this->order_status == 'Returned' || $this->order_status == 'Cancelled') {
            return 'text-red-500';
        }
        return 'text-gray-700';
    }

    public function getNewOrderStatusAttribute()
    {
        if ($this->order_status == 'Out For Showcase') {
            return 'Pickup';
        }
        if ($this->order_status == 'Showcased') {
            return 'Handover';
        }
        return $this->order_status;
    }

}
