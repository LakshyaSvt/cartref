<?php

namespace App;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Seshac\Shiprocket\Shiprocket;
use TCG\Voyager\Models\User;


class Order extends Model
{

    use SoftDeletes;

    protected $appends = ['color_image', 'color_link', 'late_fees', 'status_color_class'];

    protected $fillable = [
        'requirement_document',
        'customized_image',
        'original_file',
        'order_status',
        'order_substatus',
        'tracking_url',
        'tax_invoice',
        'is_return_window_closed'
    ];

    public function orderlifecycle()
    {
        return $this->belongsTo(Orderlifecycle::class, 'id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id', 'id');
    }

    public function subcategory()
    {
        return $this->belongsTo(ProductSubcategory::class, 'product_subcategory_id', 'id');
    }


    public function productcolor()
    {
        return $this->belongsTo(Productcolor::class, 'product_id', 'id');
    }

    public function scopeRoleWise($query)
    {

        if (!empty(request('label'))) {
            if (request('label') == 'Request For Return') {
                $query->whereIn('order_status', ['Requested For Return', 'Return Request Accepted', 'Return Request Rejected', 'Returned']);
            } else {
                $query->where('order_status', request('label'));
            }

        }

        if (request('order_id')) {
            $query->where('order_id', request('order_id'));
        }

        if (request('order_awb')) {
            $query->where('order_awb', request('order_awb'));
        }


        /**
         * Update order status according to dtdc tracking API
         */


        // if admin show all data else show only individual data
        if (auth()->user()->hasRole(['admin'])) {
            $query;
        } elseif (auth()->user()->hasRole(['Client'])) {
            $query;
        } elseif (auth()->user()->hasRole(['Vendor'])) {
            $query->where('vendor_id', auth()->user()->id);
        } else {
            $query->where('vendor_id', auth()->user()->id);
        }

        if (auth()->user()->hasRole(['admin', 'Client', 'Vendor'])) {
            if (Config::get('icrm.shipping_provider.shiprocket') == 1) {
                // $this->shiprockettrackorder();
            }

            if (Config::get('icrm.shipping_provider.dtdc') == 1) {
                $this->dtdctrackorder();
            }
        }

        // $query->leftJoin('showcases', 'orders.order_id', '=', 'showcases.order_id')
        //     ->whereNull('showcases.id');

        return $query->orderBy('updated_at', 'desc');
    }

    private function dtdctrackorder()
    {
        $orders = Order::whereNotIn('order_status', ['New Order', 'Under Manufacturing', 'Scheduled For Pickup', 'Delivered', 'Cancelled'])
            ->where('shipping_provider', 'DTDC')
            ->get();

        foreach ($orders as $order) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://blktracksvc.dtdc.com/dtdc-api/rest/JSONCnTrk/getTrackDetails",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS =>
                    "
                {
                    \n\t\"TrkType\":\t\"cnno\",
                    \n\t\"strcnno\":\t\"$order->order_awb\",
                    \n\t\"addtnlDtl\":\t\"Y\"\n\t
                }
            "
            ,
                CURLOPT_HTTPHEADER => array(
                    // "Authorization: Basic ZTA4MjE1MGE3YTQxNWVlZjdkMzE0NjhkMWRkNDY1Og==",
                    // "Postman-Token: c096d7ba-830d-440a-9de4-10425e62e52f",
                    // "api-key: eb6e38f684ef558a1d64fcf8a75967",
                    // "cache-control: no-cache",
                    // "customerId: 259",
                    // "organisation-id: 1",
                    // "x-access-token: PL2435_trk:a1f86859bcb68b321464e07f159e9747",
                    "x-access-token: RO798_trk:bcddd52dd9f433c94376480fca276d9b",
                    'Content-Type: application/json',
                ),
            ));


            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                // error
                return;
            } else {
                $collection = json_encode(collect($response));
                $collection = json_decode($collection);
                $collection = collect(json_decode($collection[0]));

                // dd($collection);

                if (json_decode($collection)->status == 'SUCCESS') {
                    // update shipment status accordingly
                    if (json_decode($collection)->trackHeader->strStatus == 'Not Picked' or json_decode($collection)->trackHeader->strStatus == 'Pickup Scheduled') {
                        $order->update([
                            'order_status' => 'Ready To Dispatch',
                            'order_substatus' => ''
                        ]);
                    }

                    if (json_decode($collection)->trackHeader->strStatus == 'Booked' or json_decode($collection)->trackHeader->strStatus == 'In Transit' or json_decode($collection)->trackHeader->strStatus == 'Softdata Upload') {
                        $order->update([
                            'order_status' => 'Shipped',
                            'order_substatus' => ''
                        ]);
                    }

                    if (json_decode($collection)->trackHeader->strStatus == 'Cancelled' or json_decode($collection)->trackHeader->strStatus == 'CANCELLED') {
                        $order->update([
                            'order_status' => 'Cancelled',
                            'order_substatus' => ''
                        ]);
                    }

                    if (json_decode($collection)->trackHeader->strStatus == 'Shipped' or json_decode($collection)->trackHeader->strStatus == 'SHIPPED') {
                        $order->update([
                            'order_status' => 'Shipped',
                            'order_substatus' => ''
                        ]);
                    }

                    if (json_decode($collection)->trackHeader->strStatus == 'Delivered' or json_decode($collection)->trackHeader->strStatus == 'DELIVERED' or json_decode($collection)->trackHeader->strStatus == 'OTP Based Delivered') {
                        $order->update([
                            'order_status' => 'Delivered',
                            'order_substatus' => ''
                        ]);
                    }

                    if (json_decode($collection)->trackHeader->strStatus == 'RTO') {
                        $order->update([
                            'order_status' => 'RTO',
                            'order_substatus' => ''
                        ]);
                    }

                    if (
                        json_decode($collection)->trackHeader->strStatus != 'RTO' and
                        json_decode($collection)->trackHeader->strStatus != 'Delivered' and
                        json_decode($collection)->trackHeader->strStatus != 'DELIVERED' and
                        json_decode($collection)->trackHeader->strStatus != 'Shipped' and
                        json_decode($collection)->trackHeader->strStatus != 'SHIPPED' and
                        json_decode($collection)->trackHeader->strStatus != 'Cancelled' and
                        json_decode($collection)->trackHeader->strStatus != 'CANCELLED'
                        and json_decode($collection)->trackHeader->strStatus != 'In Transit'
                        and json_decode($collection)->trackHeader->strStatus != 'Softdata Upload'
                        and json_decode($collection)->trackHeader->strStatus != 'Not Picked'
                        and json_decode($collection)->trackHeader->strStatus != 'Booked'
                        and json_decode($collection)->trackHeader->strStatus != 'Pickup Scheduled'
                        and json_decode($collection)->trackHeader->strStatus != 'OTP Based Delivered'
                    ) {
                        $order->update([
                            'order_status' => 'Other',
                            'order_substatus' => json_decode($collection)->trackHeader->strStatus,
                        ]);
                    }


                }

            }
        }
    }

    private function shiprockettrackorder()
    {
        $orders = Order::whereNotIn('order_status', ['New Order', 'Under Manufacturing', 'Scheduled For Pickup', 'Delivered', 'Cancelled'])
            ->where('shipping_provider', 'Shiprocket')
            ->where('order_awb', '!=', null)
            ->where('shipping_id', '!=', null)
            ->get();

        foreach ($orders as $order) {
            // dd($order);
            $token = Shiprocket::getToken();
            $response = Shiprocket::track($token)->throwShipmentId($order->shipping_id);


            if (isset(json_decode($response)->tracking_data->track_url)) {
                $order->update([
                    'tracking_url' => json_decode($response)->tracking_data->track_url,
                ]);
            }


            if (isset(json_decode($response)->tracking_data->shipment_track)) {

                $currentstatus = strtoupper(json_decode($response)->tracking_data->shipment_track[0]->current_status);

                if (
                    $currentstatus == 'OUT FOR PICKUP'
                    or $currentstatus == 'AWB ASSIGNED'
                    or $currentstatus == 'LABEL GENERATED'
                    or $currentstatus == 'PICKUP SCHEDULED'
                    or $currentstatus == 'PICKUP GENERATED'
                    or $currentstatus == 'PICKUP QUEUED'
                    or $currentstatus == 'MANIFEST GENERATED'
                ) {
                    $order->update([
                        'order_status' => 'Ready To Dispatch',
                        'order_substatus' => '',
                    ]);
                }

                if (
                    $currentstatus == 'SHIPPED'
                    or $currentstatus == 'IN TRANSIT'
                    or $currentstatus == 'OUT FOR DELIVERY'
                    or $currentstatus == 'PICKED UP'
                ) {
                    $order->update([
                        'order_status' => 'Shipped',
                        'order_substatus' => '',
                    ]);
                }

                if ($currentstatus == 'CANCELLED') {
                    $order->update([
                        'order_status' => 'Cancelled',
                        // 'order_substatus' => '',
                    ]);
                }

                if ($currentstatus == 'DELIVERED') {
                    $order->update([
                        'order_status' => 'Delivered',
                        'order_substatus' => '',
                    ]);
                }


                if ($currentstatus == 'RTO INITIATED') {
                    $order->update([
                        'order_status' => 'Request For Return',
                        'order_substatus' => '',
                    ]);
                }


                if ($currentstatus == 'RTO DELIVERED') {
                    $order->update([
                        'order_status' => 'Returned',
                        'order_substatus' => '',
                    ]);
                }


                if (
                    $currentstatus != 'OUT FOR PICKUP'
                    and $currentstatus != 'AWB ASSIGNED'
                    and $currentstatus != 'LABEL GENERATED'
                    and $currentstatus != 'PICKUP SCHEDULED'
                    and $currentstatus != 'PICKUP GENERATED'
                    and $currentstatus != 'PICKUP QUEUED'
                    and $currentstatus != 'MANIFEST GENERATED'
                    and $currentstatus != 'SHIPPED'
                    and $currentstatus != 'IN TRANSIT'
                    and $currentstatus != 'OUT FOR DELIVERY'
                    and $currentstatus != 'PICKED UP'
                    and $currentstatus != 'CANCELLED'
                    and $currentstatus != 'DELIVERED'
                    and $currentstatus != 'RTO INITIATED'
                    and $currentstatus != 'RTO DELIVERED'
                ) {
                    $order->update([
                        'order_status' => 'Other',
                        'order_substatus' => $currentstatus,
                    ]);
                }

            }


        }

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

    public function getColorLinkAttribute(): string
    {
        if ($this->color) {
            return route('product.slug', ['slug' => $this->product->slug, 'color' => $this->color]);
        }
        return route('product.slug');
    }

    public function getLateFeesAttribute(): ?string
    {
        $sameDayEnd = date('Y-m-d 00:00:00', strtotime($this->created_at));
        $forward = Carbon::parse($sameDayEnd)->addDay(1);

        $hourDiff = round((time() - strtotime($forward)) / 3600, 1);

        $now = $forward;
        $now->format('Y-m-d H:i:s');
        $hours = 36;
        $modified = (clone $now)->add(new \DateInterval("PT{$hours}H"));


        $newDiff = round((strtotime($modified) - time()) / 3600, 0);

        if ($hourDiff > 36 && ($this->order_status == 'New Order' || $this->order_status == 'Under Manufacturing')) {
            return '<span class="text-red-500 font-bold text-base">LATE SHIPMENT <br>LPF: '
                . round($this->product_offerprice * 0.1035, 0) . '/- </span>';

        } else if ($hourDiff < 36 && ($this->order_status == 'New Order' || $this->order_status == 'Under Manufacturing')) {
            return
                '<span class="text-green-500 font-semibold text-base">
                    Please dispatch your order on or before ' . $modified->format('jS M, Y H:i A') . ' to avoid a late processing fees </span>';
        }

        return null;
    }

    public function getStatusColorClassAttribute(): string
    {
        if ($this->order_status == 'Cancelled') {
            return 'text-red-600 text-xl';
        }
        if ($this->order_status == 'Under manufacturing') {
            return 'text-orange-500';
        }
        if ($this->order_status == 'New Order' || $this->order_status == 'Delivered' || $this->order_status == 'Scheduled for pickup' || $this->order_status == 'Purchased') {
            return 'text-green-500';
        }
        if ($this->order_status == 'Returned' || $this->order_status == 'Cancelled') {
            return 'text-red-500';
        }
        return 'text-gray-700';
    }

}
