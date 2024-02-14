<?php

namespace App\Http\Controllers\Actions\Showroom;

use App\Showcase;

class MarkAsPickedUp
{
    public static function pickup($id)
    {
        $showcase = Showcase::where('order_id', $id)->whereIn('order_status', ['Accepted'])->count();
        if (count($showcase) > 0) {
            return [
                'status' => 'error',
                'msg' => "Order not found or may be already marked as pickeup",
            ];
        }

        return self::markaspickedup($id);
    }

    private static function markaspickedup($id)
    {
        /**
         * Move under manufacturing
         */

        Showcase::where('order_id', $id)->whereIn('order_status', ['Accepted'])
            ->update([
                'order_status' => 'Out For Showcase'
            ]);

        return [
            'status' => 'success',
            'msg' => "Selected orders successfully marked as picked-up",
        ];
    }
}