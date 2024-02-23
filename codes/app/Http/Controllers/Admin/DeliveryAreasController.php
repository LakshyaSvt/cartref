<?php

namespace App\Http\Controllers\Admin;

use App\DeliveryServicableArea;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Illuminate\Http\Request;

class DeliveryAreasController extends Controller
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
            $rows = DeliveryServicableArea::count();
        }
        /* Query Builder */
        $announcements = DeliveryServicableArea
            ::when(isset($status), function ($query) use ($status) {
                $query->where('status', (int)$status);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->whre(function ($query) use ($keyword) {
                    $query->orWhere('city', 'LIKE', '%' . $keyword . '%');
                    $query->orWhere('state', 'LIKE', '%' . $keyword . '%');
                    $query->orWhere('start_at', 'LIKE', '%' . $keyword . '%');
                    $query->orWhere('end_at', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($announcements);
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
            'city' => "required",
            'state' => 'required',
            'start_at' => 'required',
            'end_at' => 'required|after:start_at'
        ]);

        $announcement = DeliveryServicableArea::create([
            'city' => $request->city,
            'abbreviation' => $request->abbreviation,
            'state' => $request->state,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
        ]);

        return response()->json(['status' => 'success', 'msg' => 'Area Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ApiResource
     */
    public function show($id)
    {
        $announcement = DeliveryServicableArea::findOrFail($id);
        return new ApiResource($announcement);
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
        $request->validate([
            'end_at' => 'nullable|after:start_at'
        ]);

        $announcement = DeliveryServicableArea::findOrFail($id);
        $announcement->update($request->all());

        $status = 'success';
        $msg = 'Area updated successfully';
        if ($request->filled('status')) {
            if ($request->status) {
                $status = 'success';
                $msg = 'Area Published Successfully';
            } else {
                $status = 'warning';
                $msg = 'Area Unpublished Successfully';
            }
        }
        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $announcement]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $announcement = DeliveryServicableArea::findOrFail($id);
        $announcement->delete();
        return response()->json(['status' => 'success', 'msg' => 'Area Deleted Successfully']);
    }
}
