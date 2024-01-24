<?php

namespace App\Http\Controllers\Admin;

use App\Collection;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Carbon\Carbon;
use App\Helper\FileHandler;
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
        $category = request()->category;
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
            ->orderBy('order_id','asc')
            ->paginate($rows);

        //Response
        return new ApiResource($collections);
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
            'category' => 'required',
            'group_name' => 'required',
            'url' => 'required',
            'tablet_columns' => 'required',
            'mobile_gap' => 'required',
            'mobile_columns' => 'required',
            'image' => 'nullable|image|max:2048',
            'background_image' => 'nullable|image|max:2048',
        ]);


        $collection = Collection::create([
            'category' => $request->category,
            'url' => $request->url,
            'group_name' => $request->group_name,
            'status' => $request->status,
            'order_id' => $request->order_id,
            'desktop_visiblity' => $request->desktop_visiblity,
            'desktop_columns' => $request->desktop_columns,
            'mobile_visiblity' => $request->mobile_visiblity,
            'mobile_columns' => $request->mobile_columns,
            'background_color' => $request->background_color,
            'tablet_visiblity' => $request->tablet_visiblity,
            'tablet_columns' => $request->tablet_columns,
            'desktop_carousel' => $request->desktop_carousel,
            'tablet_carousel' => $request->tablet_carousel,
            'mobile_carousel' => $request->mobile_carousel,
            'desktop_gap' => $request->desktop_gap,
            'tablet_gap' => $request->tablet_gap,
            'mobile_gap' => $request->mobile_gap,
            'background_opacity' => $request->background_opacity,
            'font_size' => $request->font_size
        ]);

        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $path = FileHandler::upload($file, 'collections');

            if ($path) {
                //file moved and save to db
                $collection->update(['background_image' => $path]);
            }
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = FileHandler::upload($file, 'collections');

            if ($path) {
                //file moved and save to db
                $collection->update(['image' => $path]);
            }
        }
        return response()->json(['status' => 'success', 'msg' => $collection->group_name . ' Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ApiResource
     */
    public function show($id)
    {
        $collection = Collection::findOrFail($id);
        return new ApiResource($collection);
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
            'image' => 'nullable|image|max:2048',
            'background_image' => 'nullable|image|max:2048',
        ]);

        $collection = Collection::findOrFail($id);

        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $path = FileHandler::upload($file, 'collections');

            if ($path) {
                //file moved and save to db
                $collection->update(['background_image' => $path]);
            }
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = FileHandler::upload($file, 'collections');

            if ($path) {
                //file moved and save to db
                $collection->update(['image' => $path]);
            }
        }

        $collection->update($request->except(['background_image', 'image']));


        $status = 'success';
        $msg = $collection->group_name . ' updated successfully';
        if ($request->filled('status')) {
            if ($request->status) {
                $status = 'success';
                $msg = $collection->group_name . ' Published Successfully';
            } else {
                $status = 'warning';
                $msg = $collection->group_name . ' Unpublished Successfully';
            }
        }
        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $collection]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $collection = Collection::findOrFail($id);
        $collection->deleted_at = Carbon::now();
        $collection->save();
        return response()->json(['status' => 'success', 'msg' => $collection->group_name . ' Deleted Successfully']);
    }
}
