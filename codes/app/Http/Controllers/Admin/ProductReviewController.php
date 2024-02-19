<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\ProductReview;
use Carbon\Carbon;
use Illuminate\Http\Request;


class ProductReviewController extends Controller
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
            $rows = ProductReview::count();
        }
        /* Query Builder */
        $reviews = ProductReview::with('user', 'product')
            ->when(isset($status), function ($query) use ($status) {
                $query->where('status', (int)$status);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    //local table fields
                    $query
                        ->orWhere('comment', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('rate', 'LIKE', '%' . $keyword . '%');
                    //product relation fields
                    $query->orWhereHas('product', function ($query) use ($keyword) {
                        $query->where('name', 'LIKE', '%' . $keyword . '%')
                            ->orWhere('slug', 'LIKE', '%' . $keyword . '%')
                            ->orWhere('sku', 'LIKE', '%' . $keyword . '%');
                    });
                    //user relation fields
                    $query->orWhereHas('user', function ($query) use ($keyword) {
                        $query->where(function ($query) use ($keyword) {
                            $query->orWhere('name', 'LIKE', '%' . $keyword . '%')
                                ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                                ->orWhere('mobile', 'LIKE', '%' . $keyword . '%');
                        });
                    });
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($reviews);
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
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'rate' => 'required',
        ]);

        $review = ProductReview::create($request->post());

        return response()->json(['status' => 'success', 'msg' => 'Review Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ApiResource
     */
    public function show($id)
    {
        $review = ProductReview::with('user', 'product')->findOrFail($id);
        return new ApiResource($review);
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
        $review = ProductReview::with('user', 'product')->findOrFail($id);
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'product_id' => 'nullable|exists:products,id',
        ]);
        $review->update($request->all());

        $status = 'success';
        $msg = 'Review updated successfully';

        if ($request->filled('status')) {
            if ($request->status) {
                $status = 'success';
                $msg = 'Review Published Successfully';
            } else {
                $status = 'warning';
                $msg = 'Review Unpublished Successfully';
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
    public function destroy($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->deleted_at = Carbon::now();
        $review->save();
        return response()->json(['status' => 'success', 'msg' => 'Review Deleted Successfully']);
    }
}
