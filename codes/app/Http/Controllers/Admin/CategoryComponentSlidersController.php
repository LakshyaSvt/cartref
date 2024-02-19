<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\CategoryComponentSlider;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CategoryComponentSlidersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $status = request()->status;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = CategoryComponentSlider::count();
        }
        /* Query Builder */
        $sliders = CategoryComponentSlider::when(isset($status), function ($query) use ($status) {
            $query->where('is_active', (int) $status);
        })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('title', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('category_slug', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('icon', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->orderBy('order')
            ->paginate($rows);

        //Response
        return new ApiResource($sliders);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required'],
            'icon' => ['required'],
            'category_slug' => ['required'],
        ]);

        $slider = CategoryComponentSlider::create([
            'title' => $request->title,
            'category_slug' => $request->category_slug,
            'icon' => $request->icon,
        ]);

        return response()->json(['status' => 'success', 'msg' => $slider->title . ' Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $slider = CategoryComponentSlider::findOrFail($id);
        return new ApiResource($slider);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $slider = CategoryComponentSlider::findOrFail($id);
        $slider->update($request->all());

        $status = 'success';
        $msg = $slider->title . ' updated successfully';

        if ($request->filled('is_active')) {
            if ($request->is_active) {
                $status = 'success';
                $msg = $slider->title . ' Published Successfully';
            } else {
                $status = 'warning';
                $msg = $slider->title . ' Unpublished Successfully';
            }
        }
        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $slider]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $slider = CategoryComponentSlider::findOrFail($id);
        $slider->delete();
        return response()->json(['status' => 'success', 'msg' => $slider->title . ' Deleted Successfully']);
    }
}
