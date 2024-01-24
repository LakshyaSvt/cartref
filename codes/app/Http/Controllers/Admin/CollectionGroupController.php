<?php

namespace App\Http\Controllers\Admin;

use App\Collection;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Carbon\Carbon;
use FileHandler;
use Illuminate\Http\Request;


class CollectionGroupController extends Controller
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
        $slider = request()->category_id;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = Collection::count();
        }
        /* Query Builder */
        $collections = Collection::when(isset($status), function ($query) use ($status) {
                $query->where('status', (int) $status);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('group_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('url', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('category', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('font_size', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('background_color', 'LIKE', '%' . $keyword . '%')
                    ;
                });
            })
            ->orderBy('order_id')
            ->paginate($rows);

        //Response
        return new ApiResource($collections);
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


        $slider = Collection::create([
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
        $slider = Collection::findOrFail($id);
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

        $slider = Collection::findOrFail($id);

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
        $slider = Collection::findOrFail($id);
        $slider->deleted_at = Carbon::now();
        $slider->save();
        return response()->json(['status' => 'success', 'msg' => $slider->category . 'Slider Deleted Successfully']);
    }
}
