<?php

namespace App\Http\Controllers\Admin\Actions\Showroom;

use App\Showcase;

class MarkAsPickedUp
{
    public static function pickup($ids): array
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

        return self::markaspickedup($ids);
    }


    private static function markaspickedup($ids)
    {
        $orders = Showcase::whereIn('id', $ids)
            ->whereIn('order_status', ['New Order'])
            ->get();

        /**
         * Check if order exists
         */
        if (count($orders) == 0) {
            return [
                'status' => 'error',
                'msg' => "You can only mark new orders as picked-up",
            ];
        }

        /**
         * Move under manufacturing
         */

        Showcase::whereIn('id', $ids)
            ->whereIn('order_status', ['New Order'])
            ->update([
                'order_status' => 'Out For Showcase'
            ]);

        return [
            'status' => 'success',
            'msg' => "Selected orders successfully marked as picked-up",
        ];

    }
}