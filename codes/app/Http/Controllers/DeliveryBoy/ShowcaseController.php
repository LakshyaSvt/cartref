<?php

namespace App\Http\Controllers\DeliveryBoy;

use App\Http\Controllers\Actions\Showroom\MarkAsPickedUp;
use App\Http\Controllers\Actions\Showroom\MarkAsShowcased;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Productsku;
use App\Showcase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ShowcaseController extends Controller
{
    public function index(Request $request)
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $status = request()->status ? explode(',', request()->status) : [];
        $show_deleted = (int)request()->show_deleted;
        //$sub_category_id = request()->sub_category_id;
        //$seller_id = request()->seller_id;
        $rows = request()->row_count ?? 25;

        if ($rows == 'all') {
            $rows = Showcase::count();
        }
        /* Query Builder */
        $orders = Showcase::with('product', 'vendor', 'deliveryboy', 'deliveryhead')
            ->where('deliveryboy_id', auth()->user()->id)
            ->where('is_order_accepted', true)
            ->when(count($status) > 0, function ($query) use ($status) {
                $query->whereIn('order_status', $status);
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
                        ->orWhere('order_value', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('customer_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('customer_email', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('customer_alt_contact_number', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_streetaddress1', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_streetaddress2', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_pincode', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_city', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_state', 'LIKE', '%' . $keyword . '%');

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

    public function getShowcaseCount()
    {

        $showcases = Showcase::where('deliveryboy_id', auth()->user()->id)->where('is_order_accepted', true)->get();
        $new_order = $showcases->where('order_status', 'Accepted')->count();
        $pickup = $showcases->where('order_status', 'Out For Showcase')->count();
        $handover = $showcases->whereIn('order_status', ['Showcased', 'Moved to Bag'])->count();
        $purchased = $showcases->where('order_status', 'Purchased')->count();
        $returned = $showcases->where('order_status', 'Returned')->count();
        $cancelled = $showcases->where('order_status', 'Cancelled')->count();
        $all = $showcases->count();

        return response()->json([
            'new_order' => $new_order,
            'pickup' => $pickup,
            'handover' => $handover,
            'purchased' => $purchased,
            'returned' => $returned,
            'cancelled' => $cancelled,
            'all' => $all
        ]);
    }

    public function markAsPickup(Request $request)
    {
        $res = MarkAsPickedUp::pickup($request->order_id);

        return response()->json($res);
    }

    public function markAsShowcased(Request $request)
    {
        $res = MarkAsShowcased::showcase($request->order_ids);

        return response()->json($res);
    }

    public function addTime($id)
    {
        $orders = Showcase::where('order_id', $id)->where('order_status', 'Showcased')->get();

        if (count($orders) <= 0) {
            return response()->json([
                'status' => 'error',
                'msg' => "Showcase order not found",
            ]);
        }

        foreach ($orders as $order) {
            if (!$order->is_timer_extended) {
                $order->showcase_timer = Carbon::parse($order->showcase_timer)->addMinutes($orders->count() * 2.5);
                $order->is_timer_extended = true;
                $order->save();
            }
        }

        return response()->json([
            'status' => 'success',
            'msg' => "Showcase order time has been extended successfully",
        ]);
    }

    public function cancelOrder($id)
    {
        $orders = Showcase::where('order_id', $id)->get();

        if (count($orders) <= 0) {
            return response()->json([
                'status' => 'error',
                'msg' => "Showcase order not found",
            ]);
        }

        $orderStatus = true;
        foreach ($orders as $item) {
            if ($item->order_status != 'Showcased') {
                $orderStatus = false;
                break;
            }
        }
        if ($orderStatus) {
            $orders = Showcase::where('order_id', $id)->where('order_status', 'Showcased')->get();
            foreach ($orders as $order) {

                $order->update([
                    'order_status' => 'Returned',
                    'status' => '0',
                ]);

                if (Config::get('icrm.stock_management.feature') == 1) {
                    if (Config::get('icrm.product_sku.color') == 1) {
                        $updatestock = Productsku::where('product_id', $order->product_id)->where('color', $order->color)->where('size', $order->size)->first();
                    } else {
                        $updatestock = Productsku::where('product_id', $order->product_id)->where('size', $order->size)->first();
                    }

                    $updatestock->update([
                        'available_stock' => $updatestock->available_stock + $order->qty,
                    ]);
                }
            }

            return response()->json([
                'msg' => "Showcase order has been cancelled",
                'status' => 'warning',
            ]);
        } else {
            return response()->json([
                'msg' => "Failed to cancel the order",
                'status' => 'error',
            ]);
        }
    }
}
