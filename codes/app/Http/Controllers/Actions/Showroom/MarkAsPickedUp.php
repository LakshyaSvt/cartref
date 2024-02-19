<?php

namespace App\Http\Controllers\Actions\Showroom;

use App\Showcase;

class MarkAsPickedUp
{
    public static function pickup($id)
    {
        $showcase = Showcase::where('order_id', $id)->whereIn('order_status', ['Accepted'])->count();
        if ($showcase <= 0) {
            return [
                'status' => 'error',
                'msg' => "Order not found or may be already marked as pickup",
            ];
        }

        return self::markaspickedup($id);
    }

    private static function markaspickedup($id)
    {
        /**
         * Move under picked up by delivery boy
         */

        Showcase::where('order_id', $id)
            ->whereIn('order_status', ['Accepted'])
            ->update([
                'order_status' => 'Out For Showcase'
            ]);
        //dd($dd,$id);
        return [
            'status' => 'success',
            'msg' => "Selected orders successfully marked as picked-up",
        ];
    }
}