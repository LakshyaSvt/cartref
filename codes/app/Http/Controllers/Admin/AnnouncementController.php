<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
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
            $rows = Announcement::count();
        }
        /* Query Builder */
        $announcements = Announcement::with('users')
            ->when(isset($status), function ($query) use ($status) {
                $query->where('is_active', (int)$status);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->whre(function ($query) use ($keyword) {
                    $query->orWhere('message', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('priority', 'LIKE', '%' . $keyword . '%');
                    $query->orWhereHas('user', function ($query) use ($keyword) {
                        $query->orWhere('name', $keyword);
                        $query->orWhere('email', $keyword);
                        $query->orWhere('brand_name', $keyword);
                    });
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
            'message' => "required",
            'priority' => 'required'
        ]);

        $announcement = Announcement::create([
            'message' => $request->message,
            'priority' => $request->priority,
            'for_all_vendors' => $request->for_all_vendors,
        ]);

        if ($request->has('sellers')) {
            $sellers = $request->sellers;
            foreach ($sellers as $seller)
                $announcement->users()->attach($seller);
        }

        return response()->json(['status' => 'success', 'msg' => 'Announcement Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ApiResource
     */
    public function show($id)
    {
        $announcement = Announcement::with('users')->findOrFail($id);
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
        $announcement = Announcement::with('users')->findOrFail($id);
        $announcement->update($request->all());

        if ($request->has('sellers')) {
            $sellers = $request->sellers;
            $announcement->users()->sync($sellers);
        }

        $status = 'success';
        $msg = 'Announcement updated successfully';
        if ($request->filled('is_active')) {
            if ($request->is_active) {
                $status = 'success';
                $msg = 'Announcement Published Successfully';
            } else {
                $status = 'warning';
                $msg = 'Announcement Unpublished Successfully';
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
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();
        return response()->json(['status' => 'success', 'msg' => 'Announcement Deleted Successfully']);
    }
}
