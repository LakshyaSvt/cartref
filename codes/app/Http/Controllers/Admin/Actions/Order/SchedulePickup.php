<?php

namespace App\Http\Controllers\Admin\Actions\Order;

use App\Models\User;
use App\Order;
use Illuminate\Support\Facades\Config;
use Seshac\Shiprocket\Shiprocket;

class SchedulePickup
{
    //public function shouldActionDisplayOnDataType()
    //{
    //    if (auth()->user()->hasRole(['admin', 'Client', 'Vendor'])) {
    //        if (request('all') == true or request('label') == 'New Order' or request('label') == 'Under Manufacturing') {
    //            return $this->dataType->slug == 'orders';
    //        }
    //    }
    //}

    public static function schedule($ids): array
    {
        $response = [];

        /**
         * Check if the order is selected
         */

        if ($ids[0] == 0) {
            return [
                'status' => 'warning',
                'msg' => "You haven't selected any order to schedule pickup",
            ];
        }

        $orders = Order::whereIn('id', $ids)
            ->whereIn('order_status', ['New Order', 'Under Manufacturing'])
            ->get();

        if (count($orders) == 0) {
            return [
                'status' => 'error',
                'msg' => "You can only schedule pickup for new and under manufacturing orders",
            ];
        }

        /**
         * Check if the orders selected are for only one order group
         */

        if (count($orders->pluck('order_id')->unique()) > 1) {
            return [
                'status' => 'error',
                'msg' => "You can schedule pickup only for one order at a time.",
            ];
        }

        /**
         * Schedule pickup for these orders
         */

        if (Config::get('icrm.shipping_provider.shiprocket') == 1) {
            return self::shiprocketschedulepickup($ids, $orders);
        }
    }


    private static function shiprocketschedulepickup($ids, $orders)
    {

        $customername = $orders[0]->customer_name;
        $customerphone = $orders[0]->customer_contact_number;
        $customeralternate_phone = $orders[0]->customer_alt_contact_number;
        $customeremail = $orders[0]->customer_email;
        $customeraddress_line_1 = $orders[0]->dropoff_streetaddress1;
        $customeraddress_line_2 = $orders[0]->dropoff_streetaddress2 . ' ' . $orders[0]->dropoff_landmark;
        $customerpincode = $orders[0]->dropoff_pincode;
        $customercity = $orders[0]->dropoff_city;
        $customerstate = $orders[0]->dropoff_state;
        $customercountry = $orders[0]->dropoff_country;

        if (!empty($orders[0]->company_name)) {
            $customername = $orders[0]->company_name;
        }

        $customergst = $orders[0]->gst_number;


        /**
         * If multi vendor then fetch vendor info
         * Else fetch seller info
         */

        if (Config::get('icrm.vendor.multiple_vendors') == 1) {
            /**
             * Get the vendor information
             * If vendor not available then redirect back with the error msg
             */

            $vendor = User::where('id', $orders[0]->vendor_id)->first();

            if (!empty($vendor)) {

                if (!empty($vendor->brand_name)) {
                    $clientname = $vendor->brand_name;
                } else {
                    $clientname = $vendor->name;
                }

                $clientemail = $vendor->email;
                $clientphone = $vendor->mobile;
                $clientalternate_phone = '';
                $clientaddress_line_1 = $vendor->street_address_1;
                $clientaddress_line_2 = $vendor->street_address_2 . ' ' . $vendor->landmark;
                $clientpincode = $vendor->pincode;
                $clientcity = $vendor->city;
                $clientstate = $vendor->state;
                $clientcountry = $vendor->country;
            } else {
                return [
                    'status' => 'error',
                    'msg' => "Seller information not updated in his profile",
                ];
            }

        } else {
            $clientname = setting('seller-name.name');
            $clientemail = setting('seller-name.email');
            $clientphone = setting('seller-name.phone');
            $clientalternate_phone = setting('seller-name.alt_number');
            $clientaddress_line_1 = setting('seller-name.street_address_1');
            $clientaddress_line_2 = setting('seller-name.street_address_2') . ' ' . setting('seller-name.landmark');
            $clientpincode = setting('seller-name.pincode');
            $clientcity = setting('seller-name.city');
            $clientstate = setting('seller-name.state');
            $clientcountry = setting('seller-name.country');
        }


        $num_pieces = $orders->sum('qty');
        $total = $orders[0]->order_total;
        $orderid = $orders[0]->order_id;
        $orderdate = $orders[0]->created_at->format('Y-m-d');

        $length = $orders->sum('length');
        $breadth = $orders->sum('width');
        $height = $orders->sum('height');
        $weight = $orders->sum('weight');

        $dimensionunit = 'cm';
        $weightunit = 'kg';

        $paymentmethod = $orders[0]->order_method;

        $token = Shiprocket::getToken();

        /**
         * Check if the pickup location is already registered?
         */

        $locations = Shiprocket::pickup($token)->getLocations();

        $pickuplocation = str_replace(' ', '', $vendor->brand_name . '-' . $vendor->id);

        if (isset(json_decode($locations)->data->shipping_address)) {
            // 0 means no and 1 means yes
            $locationexists = collect(json_decode($locations)->data->shipping_address)->where('pickup_location', $pickuplocation)->count();
        } else {
            $locationexists = 0;
        }


        if ($locationexists == 0) {
            // create address with pickuplocation name

            //Refer the above url for required parameteres
            $newLocation = [
                "pickup_location" => "$pickuplocation",
                "name" => "$clientname",
                "email" => "$clientemail",
                "phone" => $clientphone,
                "address" => "$clientaddress_line_1",
                "address_2" => "$clientaddress_line_2",
                "city" => "$clientcity",
                "state" => "$clientstate",
                "country" => "$clientcountry",
                "pin_code" => "$clientpincode"
            ];

            $location = Shiprocket::pickup($token)->addLocation($newLocation);
        }


        $orderitems = [];

        foreach ($orders as $order) {
            $orderitems[] = array(
                "name" => $order->product->name . ' / ' . $order->color . ' - ' . $order->size,
                "sku" => $order->product_sku,
                "units" => $order->qty,
                "hsn" => $order->subcategory->hsn, // not required
                "selling_price" => $order->product_offerprice
            );
        }


        // https://apidocs.shiprocket.in/?version=latest#247e58f3-37f3-4dfb-a4bb-b8f6ab6d41ec
        $orderDetails = [
            // refer above url for required parameters
            "mode" => "Surface",
            "request_pickup" => true,
            "print_label" => true,
            "generate_manifest" => true,

            "order_id" => $orderid . auth()->user()->id,
            "order_date" => "$orderdate",

            "channel_id" => "",

            "reseller_name" => "Reseller: [$clientname]",

            "company_name" => "$clientname",


            "billing_customer_name" => "$customername",
            "billing_last_name" => "",
            "billing_address" => "$customeraddress_line_1",
            "billing_address_2" => "$customeraddress_line_2",
            "billing_city" => "$customercity",
            "billing_pincode" => "$customerpincode",
            "billing_state" => "$customerstate",
            "billing_country" => "$customercountry",
            "billing_email" => "$customeremail",
            "billing_phone" => "$customerphone",
            "billing_alternate_phone" => "$customeralternate_phone",

            "shipping_is_billing" => true,

            "order_items" => $orderitems,

            "payment_method" => "$paymentmethod",

            "sub_total" => $total,
            "weight" => $weight,
            "length" => $length,
            "breadth" => $breadth,
            "height" => $height,

            "pickup_location" => "$pickuplocation",

            "customer_gstin" => "$customergst",

        ];

        // $channelSpecificOrder = true;
        $response = Shiprocket::order($token)->quickCreate($orderDetails);


        // if has any error
        if (isset(json_decode($response)->payload->error_message)) {
            return [
                'status' => 'error',
                'msg' => json_decode($response)->payload->error_message,
            ];
        }

        // if payload has required fields then update
        if (isset(json_decode($response)->payload)) {
            Order::whereIn('id', $ids)
                ->update([
                    'pickup_scheduled_date' => json_decode($response)->payload->pickup_scheduled_date,
                    'shipping_order_id' => json_decode($response)->payload->order_id,
                    'shipping_id' => json_decode($response)->payload->shipment_id,
                    'order_awb' => json_decode($response)->payload->awb_code,

                    'courier_name' => json_decode($response)->payload->courier_name,

                    'shipping_label' => json_decode($response)->payload->label_url,
                    'manifest_url' => json_decode($response)->payload->manifest_url,
                    'shipping_provider' => 'Shiprocket',
                ]);
        }


        // if schedulled successfully
        if (isset(json_decode($response)->status)) {
            if (json_decode($response)->status == '1') {

                Order::whereIn('id', $ids)->update([
                    'order_status' => 'Scheduled For Pickup'
                ]);

                return [
                    'status' => 'success',
                    'msg' => 'Order successfully scheduled with shiprocket awb number: ' . json_decode($response)->payload->awb_code . '.',
                ];
            } else {

                if (isset(json_decode($response)->payload->error_message)) {
                    return [
                        'status' => 'error',
                        'msg' => 'Failed to schedule: ' . json_decode($response)->payload->error_message,
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'msg' => 'Failed to schedule',
                    ];
                }

            }
        }

    }

}