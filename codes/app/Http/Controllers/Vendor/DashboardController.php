<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Order;
use App\Showcase;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchCount()
    {
        $product = Product::where('seller_id', auth()->user()->id)->count();
        $newOrder = Order::where('order_status', 'New Order')->where('vendor_id', auth()->user()->id)->where('type', 'Regular')->count();
        $order = Order::where('vendor_id', auth()->user()->id)->where('type', 'Regular')->count();
        $newShowcase = Showcase::where('vendor_id', auth()->user()->id)->where('order_status', 'New Order')->count();
        $showcase = Showcase::where('vendor_id', auth()->user()->id)->count();

        return response()->json([
            'product' => $product,
            'newOrder' => $newOrder,
            'order' => $order,
            'newShowcase' => $newShowcase,
            'showcase' => $showcase,
        ]);
    }


}
