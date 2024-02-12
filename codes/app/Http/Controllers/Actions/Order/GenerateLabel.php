<?php

namespace App\Http\Controllers\Actions\Order;

use App\Order;
use Illuminate\Support\Facades\Config;
use Seshac\Shiprocket\Shiprocket;

class GenerateLabel
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

        /* Check if the order is selected */
        if ($ids[0] == 0) {
            $response = [
                'status' => 'warning',
                'msg' => "You haven't selected any order to generate shipping label",
            ];
        }

        if (Config::get('icrm.shipping_provider.shiprocket') == 1) {
            $response = self::shiprocketgeneratelabel($ids);
        }

        return $response;
    }


    private static function shiprocketgeneratelabel($ids)
    {
        $orders = Order::whereIn('id', $ids)->get();

        $token = Shiprocket::getToken();
        $orderIds = ['ids' => json_encode($orders->pluck('shipping_order_id'))];
        $response = Shiprocket::generate($token)->invoice($orderIds);

        if (isset(json_decode($response)->invoice_url)) {
            foreach ($orders as $order) {
                $order->update([
                    'order_status' => 'Ready To Dispatch',
                    'tax_invoice' => json_decode($response)->invoice_url,
                ]);
            }
            return [
                'status' => 'success',
                'msg' => "Label generated successfully",
            ];
        }
        return [
            'status' => 'warning',
            'msg' => "Label could not generate",
        ];
    }
}