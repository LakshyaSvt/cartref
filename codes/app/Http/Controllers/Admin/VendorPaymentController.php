<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\User;
use App\VendorPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VendorPaymentController extends Controller
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
            $rows = VendorPayment::count();
        }
        /* Query Builder */
        $vendorPayments = VendorPayment::with('user')
            ->when(isset($status), function ($query) use ($status) {
                $query->where('status', (int)$status);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->orWhere('brand_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('contact_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('email_address', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('contact_number', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('registered_company_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('gst_number', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('marketplaces', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($vendorPayments);
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
            'vendor_id' => 'required|exists:users,id',
            'billing_date' => ['required'],
            'total' => ['required'],
        ]);

        $vendor = User::findOrFail($request->vendor_id);
        $vendorPayment = VendorPayment::create([
            'vendor_id' => $vendor->id,
            'billing_date' => date('Y-m-d', strtotime($request->billing_date)),
            'total' => floatval($request->total)
        ]);

        return response()->json(['status' => 'success', 'msg' => 'Payment Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ApiResource
     */
    public function show($id)
    {
        $vendorPayment = VendorPayment::with('user')->findOrFail($id);
        return new ApiResource($vendorPayment);
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
        $vendorPayment = VendorPayment::with('user')->findOrFail($id);
        $vendorPayment->update($request->except(['vendor_id','billing_date','total']));

        if ($request->has('vendor_id')) {
            $vendor = User::findOrFail($request->vendor_id);
            $vendorPayment->update([
                'vendor_id' => $vendor->id
            ]);
        }
        if ($request->has('billing_date')) {
            $vendorPayment->update([
                'billing_date' => date('Y-m-d', strtotime($request->billing_date))
            ]);
        }
        if ($request->has('total')) {
            $vendorPayment->update([
                'total' => floatval($request->total)
            ]);
        }

        $status = 'success'; $msg = 'Payment Updated Successfully';
        if ($request->filled('status')) {
            if ($request->status) {
                $msg = 'Payment Published Successfully';
            } else {
                $status = 'warning';
                $msg = 'Payment Unpublished Successfully';
            }
        }

        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $vendorPayment]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $vendorPayment = VendorPayment::findOrFail($id);
        $vendorPayment->deleted_at = Carbon::now();
        $vendorPayment->save();
        return response()->json(['status' => 'success', 'msg' => 'Payment Deleted Successfully']);
    }
}
