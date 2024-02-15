<?php

namespace App\Http\Controllers\Actions\Showroom;

use App\Showcase;
use Carbon\Carbon;

class MarkAsShowcased
{

    public static function showcase($ids)
    {
        /**
         * Check if the order is selected
         */

        if ($ids == 0) {
            return [
                'status' => 'warning',
                'msg' => "You haven't selected any order to mark as handovered",
            ];
        }

        /**
         * move to under manufacturing
         */

        return self::markasshowcased($ids);
    }

    private static function markasshowcased($ids)
    {
        $orders = Showcase::whereIn('id', $ids)
            ->whereIn('order_status', ['Out For Showcase'])
            ->get();

        /**
         * Check if order exists
         */
        if (count($orders) == 0) {
            return [
                'status' => 'error',
                'msg' => "You can only mark out for showcase orders as handovered",
            ];
        }


        Showcase::whereIn('id', $ids)
            ->whereIn('order_status', ['Out For Showcase'])
            ->update([
                'order_status' => 'Showcased',
                'showcase_timer' => Carbon::now()->addMinutes($orders->count() * 5)
            ]);

        return [
            'status' => 'success',
            'msg' => "Selected orders successfully marked as handovered",
        ];

    }


}
