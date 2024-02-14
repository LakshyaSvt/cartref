<?php

namespace App\Http\Controllers\DeliveryHead;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\User;
use App\Showcase;
use Illuminate\Http\Request;

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
            ->where('pickup_city', auth()->user()->city)
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

        $showcases = Showcase::where('pickup_city', auth()->user()->city)->where('is_order_accepted', true)->get();
        $new_order = $showcases->where('order_status', 'New Order')->count();
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

    public function getDeliveryBoys()
    {
        /* Query Parameters */
        $roles = ['Delivery Boy'];
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = User::count();
        }
        /* Query Builder */
        $users = User::with('role')
            ->where('status', 1)
            ->whereHas('role', function ($query) use ($roles) {
                $query->whereIn('name', $roles)
                    ->orWhereIn('id', $roles);
            })
            ->whereDoesntHave('dbshowcases', function ($query) {
                $query->where('status', 1);
            })
            ->where('city', auth()->user()->city)
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($users);
    }

    public function assignDeliveryBoy(Request $request, $order_id)
    {
        //return auth()->user()->city;
        $orders = Showcase::where('pickup_city', auth()->user()->city)
            ->where('is_order_accepted', true)
            ->where('order_id', $order_id)
            ->get();

        if (count($orders) <= 0) {
            return response()->json(['status' => 'error', 'msg' => 'Orders not found']);
        }

        $deliveryBoy = User::where(['id' => $request->user_id, 'status' => '1'])
            ->whereHas('role', function ($query) {
                $query->whereIn('name', ['Delivery Boy']);
            })
            ->whereDoesntHave('dbshowcases', function ($query) {
                $query->where('status', 1);
            })
            ->where('city', auth()->user()->city)
            ->firstOrFail();

        Showcase::where('order_id', $order_id)->update([
            'deliveryhead_id' => auth()->user()->id,
            'deliveryboy_id' => $deliveryBoy->id,
        ]);

        return response()->json(['status' => 'success', 'msg' => $deliveryBoy->name . ' assigned successfully']);
    }
}
