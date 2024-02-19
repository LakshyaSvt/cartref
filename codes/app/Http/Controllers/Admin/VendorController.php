<?php

namespace App\Http\Controllers\Admin;

use App\Vendor;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VendorController extends Controller
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
            $rows = Vendor::count();
        }
        /* Query Builder */
        $vendors = Vendor::when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query
                        ->orWhere('brand_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('contact_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('email_address', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('contact_number', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('registered_company_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('gst_number', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('marketplaces', 'LIKE', '%' . $keyword . '%')
                    ;
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($vendors);
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
            'brand_name' => ['required'],
            'contact_name' => ['required'],
            'email_address' => ['required'],
            'contact_number' => ['required'],
        ]);

        $vendor = Vendor::create([
            'brand_name' => $request->brand_name,
            'contact_name' => $request->contact_name,
            'email_address' => $request->email_address,
            'contact_number' => $request->contact_number,
            'registered_company_name' => $request->registered_company_name,
            'gst_number' => $request->gst_number,
            'marketplaces' => $request->marketplaces
        ]);

        return response()->json(['status' => 'success', 'msg' => $vendor->contact_name . ' Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ApiResource
     */
    public function show($id)
    {
        $vendor = Vendor::findOrFail($id);
        return new ApiResource($vendor);
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
        $vendor = Vendor::findOrFail($id);
        $vendor->update($request->all());

        $status = 'success';
        $msg = $vendor->contact_name . ' updated successfully';

        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $vendor]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->deleted_at = Carbon::now();
        $vendor->save();
        return response()->json(['status' => 'success', 'msg' => $vendor->contact_name . ' Deleted Successfully']);
    }
}
