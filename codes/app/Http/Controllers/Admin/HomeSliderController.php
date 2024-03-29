<?php

namespace App\Http\Controllers\Admin;

use App\HomeSlider;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Carbon\Carbon;
use FileHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class HomeSliderController extends Controller
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
        $category_id = request()->category_id;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = HomeSlider::count();
        }
        /* Query Builder */
        $sliders = HomeSlider::when(isset($status), function ($query) use ($status) {
            $query->where('status', (int)$status);
        })
            ->when(isset($category_id), function ($query) use ($category_id) {
                $query->whereHas('category', function ($query) use ($category_id) {
                    $query->where('id', $category_id);
                });
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('category', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('url', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('page_description', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
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
            'category' => 'required',
            'background_image' => 'nullable|image|max:2048',
            'mb_background_image' => 'nullable|image|max:2048'
        ]);


        $slider = HomeSlider::create([
            'category' => $request->category,
            'url' => $request->url,
            'page_description' => $request->page_description,
        ]);

        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $path = FileHandler::upload($file, 'homesliders');

            if ($path) {
                //file moved and save to db
                $slider->update(['background_image' => $path]);
            }
        }
        if ($request->hasFile('mb_background_image')) {
            $file = $request->file('mb_background_image');
            $path = FileHandler::upload($file, 'homesliders');

            if ($path) {
                //file moved and save to db
                $slider->update(['mb_background_image' => $path]);
            }
        }
        return response()->json(['status' => 'success', 'msg' => $slider->category . 'Slider Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $slider = HomeSlider::findOrFail($id);
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
        $request->validate([
            'category' => 'required',
            'background_image' => 'nullable|image|max:2048',
            'mb_background_image' => 'nullable|image|max:2048'
        ]);

        $slider = HomeSlider::findOrFail($id);

        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $uploadedPath = FileHandler::upload($file, 'homesliders');
            if ($uploadedPath) {
                /* deleting existing file */
                FileHandler::delete($slider->background_image);
                /*Replace string with new one*/
                $slider->update(['background_image' => $uploadedPath]); //update db
            }
        }
        if ($request->hasFile('mb_background_image')) {
            $file = $request->file('mb_background_image');
            $uploadedPath = FileHandler::upload($file, 'homesliders');
            if ($uploadedPath) {
                /* deleting existing file */
                FileHandler::delete($slider->mb_background_image);
                /*Replace string with new one*/
                $slider->update(['mb_background_image' => $uploadedPath]); //update db
            }
        }

        $slider->update($request->except(['background_image', 'mb_background_image']));


        $status = 'success';
        $msg = $slider->category . 'Slider updated successfully';
        if ($request->filled('status')) {
            if ($request->status) {
                $status = 'success';
                $msg = $slider->category . 'Slider Published Successfully';
            } else {
                $status = 'warning';
                $msg = $slider->category . 'Slider Unpublished Successfully';
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
        $slider = HomeSlider::findOrFail($id);
        $slider->deleted_at = Carbon::now();
        $slider->save();
        return response()->json(['status' => 'success', 'msg' => $slider->category . 'Slider Deleted Successfully']);
    }
}
