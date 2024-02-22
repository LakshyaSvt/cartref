<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Order;
use App\Showcase;
use TCG\Voyager\Models\Post;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchCount()
    {
        $product = Product::count();
        $newOrder = Order::where('order_status', 'New Order')->where('type', 'Regular')->count();
        $order = Order::where('type', 'Regular')->count();
        $newShowcase = Showcase::where('order_status', 'Accepted')->count();
        $showcase = Showcase::count();
        $user = User::count();
        $post = Post::count();

        return response()->json([
            'product' => $product,
            'newOrder' => $newOrder,
            'order' => $order,
            'newShowcase' => $newShowcase,
            'showcase' => $showcase,
            'user' => $user,
            'post' => $post,
        ]);
    }


}
