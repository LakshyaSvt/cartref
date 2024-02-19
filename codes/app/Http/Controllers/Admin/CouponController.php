<?php

namespace App\Http\Controllers\Admin;

use App\Coupon;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Illuminate\Http\Request;


class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ApiResource
     */
    public function index()
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $status = request()->status;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = Coupon::count();
        }
        /* Query Builder */
        $coupons = Coupon::with('brands')
            ->when(isset($status), function ($query) use ($status) {
                $query->where('status', (int)$status);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query
                        ->orWhere('code', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('type', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('description', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('value', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('from', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('to', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('background_color', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('user_email', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('min_order_value', 'LIKE', '%' . $keyword . '%');

                    $query->orWhereHas('brands', function ($query) use ($keyword) {
                        $query->where(function ($query) use ($keyword) {
                            $query->orWhere('name', 'LIKE', '%' . $keyword . '%');
                        });
                    });
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($coupons);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'value' => 'required',
            'code' => 'required|unique:coupons,code',
            'description' => 'required',
            'from' => 'required|date|before:to',
            'to' => 'required|date|after:from',
            'background_color' => 'required',
        ]);

        $coupon = Coupon::create($request->post());

        if ($request->has('brands')) {
            $brands = $request->brands;
            foreach ($brands as $brand)
                $coupon->brands()->attach($brand);
        }

        return response()->json(['status' => 'success', 'msg' => $coupon->code . ' Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ApiResource
     */
    public function show($id)
    {
        $coupon = Coupon::with('brands')->findOrFail($id);
        return new ApiResource($coupon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $request->validate([
            'code' => "nullable|unique:coupons,code,{$coupon->id}",
            'from' => 'nullable|date|before:to',
            'to' => 'nullable|date|after:from',
        ]);
        $coupon->update($request->all());

        if ($request->has('brands')) {
            $brands = $request->brands;
            $coupon->brands()->sync($brands);
        }

        $status = 'success';
        $msg = $coupon->code . ' updated successfully';

        if ($request->filled('status')) {
            if ($request->status) {
                $status = 'success';
                $msg = $coupon->code . ' Published Successfully';
            } else {
                $status = 'warning';
                $msg = $coupon->code . ' Unpublished Successfully';
            }
        }
        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $coupon]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return response()->json(['status' => 'success', 'msg' => $coupon->code . ' Deleted Successfully']);
    }
}
