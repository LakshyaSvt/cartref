<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $status = request()->status;
        $show_deleted = (int)request()->show_deleted;
        //$sub_category_id = request()->sub_category_id;
        //$seller_id = request()->seller_id;
        $rows = request()->row_count ?? 25;

        if ($rows == 'all') {
            $rows = Order::count();
        }
        /* Query Builder */
        $orders = Order::with('product', 'vendor', 'subcategory', 'productcolor')
            ->when(isset($status), function ($query) use ($status) {
                $query->where('order_status', $status);
            })
            ->when($show_deleted, function ($query) {
                $query->onlyTrashed();
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query
                        ->orWhere('order_id', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('product_sku', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('size', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('color', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('order_substatus', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('order_value', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('order_subtotal', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('order_total', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('customer_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('customer_email', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('customer_alt_contact_number', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_streetaddress1', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_streetaddress2', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_pincode', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_city', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_state', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('dropoff_streetaddress1', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('dropoff_streetaddress2', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('dropoff_pincode', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('dropoff_city', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('dropoff_state', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('shipping_provider', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('shipping_order_id', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('courier_name', 'LIKE', '%' . $keyword . '%');

                    $query->orWhereHas('product', function ($query) use ($keyword) {
                        $query->where(function ($query) use ($keyword) {
                            $query->orWhere('name', 'LIKE', '%' . $keyword . '%');
                            $query->orWhere('slug', 'LIKE', '%' . $keyword . '%');
                            $query->orWhere('brand_id', 'LIKE', '%' . $keyword . '%');
                        });
                    });
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($orders);
    }

    public function getOrderCount()
    {
        $orders = Order::get();

        $new_order = $orders->where('order_status', 'New Order')->count();
        $ready_to_dispatch = $orders->where('order_status', 'Ready To Dispatch')->count();
        $pending_pickup = $orders->where('order_status', 'Scheduled For Pickup')->count();

        $in_transit = $orders->where('order_status', 'Shipped')->count();
        $delivered = $orders->whereIn('order_status', 'Delivered')->count();
        $cancelled = $orders->where('order_status', 'Cancelled')->count();

        $rto = $orders->where('order_status', 'RTO')->count();
        $customer_return = $orders->whereIn('order_status', ['Requested For Return', 'Return Request Accepted', 'Return Request Rejected', 'Returned'])->count();
        $return_delivered = $orders->where('order_status', 'Returned')->count();

        $completed_in_90_days = $orders->where('created_at', '>', now()->subDays(60)->endOfDay())->count();
        $all = $orders->count();
        $others = $orders->where('order_status', 'Other')->count();

        return response()->json([
            'new_order' => $new_order,
            'ready_to_dispatch' => $ready_to_dispatch,
            'pending_pickup' => $pending_pickup,
            'in_transit' => $in_transit,
            'delivered' => $delivered,
            'cancelled' => $cancelled,
            'rto' => $rto,
            'customer_return' => $customer_return,
            'return_delivered' => $return_delivered,
            'completed_in_90_days' => $completed_in_90_days,
            'all' => $all,
            'others' => $others,
        ]);
    }

    public function schedulePickup(){

    }

    public function delete($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['status' => 'success', 'msg' => $order->order_id . ' Deleted Successfully']);
    }
}
