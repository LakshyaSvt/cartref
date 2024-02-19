<?php

namespace App\Http\Controllers\Actions\Order;
use App\Order;

class MarkAsShipped
{

    public static function shipped($ids): array
    {
        /**
         * Check if the order is selected
         */

        if ($ids[0] == 0) {
            return [
                'msg' => "You haven't selected any order to mark as a shipped",
                'status' => 'warning',
            ];
        }

        /**
         * move to under manufacturing
         */

        return self::markAsShipped($ids);
    }


    private static function markAsShipped($ids)
    {
        $orders = Order::whereIn('id', $ids)
            ->whereIn('order_status', ['Ready to dispatch'])
            ->get();


        /**
         * Check if order exists
         */
        if (count($orders) == 0) {
            return [
                'msg' => "You can only mark ready to dispatch orders as shipped",
                'status' => 'error',
            ];
        }

        /**
         * Move under manufacturing
         */

        Order::whereIn('id', $ids)
            ->whereIn('order_status', ['Ready to dispatch'])->update([
                'order_status' => 'Shipped'
            ]);

        return [
            'msg' => "Selected orders successfully marked as shipped",
            'status' => 'success',
        ];

    }

}