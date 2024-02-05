<?php

namespace App\Http\Controllers\Admin\Actions\Order;

use App\Order;
use Seshac\Shiprocket\Shiprocket;

class CancelShipment
{
    public static function cancel($ids): array
    {
        /**
         * Check if the order is selected
         */

        if ($ids[0] == 0) {
            return [
                'status' => 'warning',
                'msg' => "You haven't selected any order to schedule pickup",
            ];
        }

        /**
         * cancel order awb
         */
        return self::cancelawb($ids);
    }


    private static function cancelawb($ids)
    {
        $orders = Order::whereIn('id', $ids)
            ->whereIn('order_status', ['Scheduled For Pickup', 'Ready to Dispatch'])
            ->get();

        if (count($orders) == 0) {
            return [
                'status' => 'error',
                'msg' => "You can only cancel shipments of orders whose shipping is initiated and scheduled for pickup or ready to dispatch",
            ];
        }

        $token = Shiprocket::getToken();
        $cancelshipmentsids = $orders->pluck('shipping_order_id');
        $response = Shiprocket::order($token)->cancel(['ids' => $cancelshipmentsids]);

        if (isset(json_decode($response)->status)) {
            if (json_decode($response)->status == 200) {
                if (auth()->user()->hasRole(['Vendor'])) {
                    Order::whereIn('id', $ids)->update([
                        'order_status' => 'Cancelled',
                        'order_substatus' => 'Cancelled by seller'
                    ]);
                }

                if (auth()->user()->hasRole(['admin', 'Client'])) {
                    Order::whereIn('id', $ids)->update([
                        'order_status' => 'Cancelled',
                        'order_substatus' => 'Cancelled by admin'
                    ]);
                }

                return [
                    'status' => 'success',
                    'msg' => json_decode($response)->message,
                ];
            } else {
                return [
                    'status' => 'error',
                    'msg' => json_decode($response)->message,
                ];
            }

        } else {
            return [
                'status' => 'error',
                'msg' => json_decode($response)->message,
            ];
        }

    }
}