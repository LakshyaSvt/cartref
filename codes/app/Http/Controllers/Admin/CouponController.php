<?php

namespace App\Http\Controllers\Admin;

use App\Coupon;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Carbon\Carbon;
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
        $coupons = Coupon::with('sellers', 'brands')
            ->when(isset($status), function ($query) use ($status) {
                $query->where('status', (int)$status);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('code', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('type', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('description', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('value', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('rate', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('from', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('to', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('background_color', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('user_email', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('min_order_value', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->whereHas('user', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('email', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->whereHas('brands', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'LIKE', '%' . $keyword . '%');
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
            'value' => 'required|integer',
            'code' => 'required|unique:coupons,code',
            'description' => 'required',
            'from' => 'required|date|before:to',
            'to' => 'required|date|after:from',
            'background_color' => 'required',
            'user_email' => 'nullable|email:rfc,dns',
        ]);

        $review = Coupon::create($request->post());

        if ($request->has('brands')) {
            $brands = $request->brands->toArray();
            foreach ($brands as $brand)
                $review->brands()->attach($brand);
        }

        return response()->json(['status' => 'success', 'msg' => $review->comment . ' Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ApiResource
     */
    public function show(Coupon $review)
    {
        return new ApiResource($review);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Coupon $review)
    {
        $request->validate([
            'code' => "required|unique:coupons,code,{$review->id}",
            'description' => 'required',
            'from' => 'required|date|before:to',
            'to' => 'required|date|after:from',
        ]);
        $review->update($request->all());

        if ($request->has('brands')) {
            $brands = $request->brands->toArray();
            $review->brands()->sync($brands);
        }

        $status = 'success';
        $msg = $review->comment . ' updated successfully';

        if ($request->filled('status')) {
            if ($request->status) {
                $status = 'success';
                $msg = $review->comment . ' Published Successfully';
            } else {
                $status = 'warning';
                $msg = $review->comment . ' Unpublished Successfully';
            }
        }
        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $review]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Coupon $review)
    {
        $review->deleted_at = Carbon::now();
        $review->save();
        return response()->json(['status' => 'success', 'msg' => $review->comment . ' Deleted Successfully']);
    }
}
