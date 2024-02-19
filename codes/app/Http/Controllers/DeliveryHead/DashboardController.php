<?php

namespace App\Http\Controllers\DeliveryHead;

use App\Http\Controllers\Controller;
use App\Showcase;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchCount()
    {
        $newShowcase = Showcase::where(function ($q) {
            $q->where('deliveryhead_id', auth()->user()->id)
                ->orWhere('dropoff_city', auth()->user()->city)
                ->where('order_status', 'New Order');
        })
        ->where('is_order_accepted', true)
        ->count();

        $showcase = Showcase::where(function ($q) {
            $q->where('deliveryhead_id', auth()->user()->id)
                ->orWhere('dropoff_city', auth()->user()->city);
        })
        ->where('is_order_accepted', true)
        ->count();

        return response()->json([
            'newShowcase' => $newShowcase,
            'showcase' => $showcase,
        ]);
    }


}
