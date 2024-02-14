<?php

namespace App\Http\Controllers\DeliveryBoy;

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
            $q->where('deliveryboy_id', auth()->user()->id)
                ->where('order_status', 'Accepted');
        })
            ->where('is_order_accepted', true)
            ->count();

        $showcase = Showcase::where('deliveryboy_id', auth()->user()->id)
            ->where('is_order_accepted', true)
            ->count();

        return response()->json([
            'newShowcase' => $newShowcase,
            'showcase' => $showcase,
        ]);
    }


}
