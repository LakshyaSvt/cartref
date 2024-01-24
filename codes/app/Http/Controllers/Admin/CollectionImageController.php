<?php

namespace App\Http\Controllers\Admin;

use App\CollectionImage;
use App\Helper\FileHandler;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Carbon\Carbon;
use Illuminate\Http\Request;


class CollectionImageController extends Controller
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
        $collection_id = request()->collection_id;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = CollectionImage::count();
        }
        /* Query Builder */
        $collections = CollectionImage::with('collection')
            ->when(isset($status), function ($query) use ($status) {
                $query->where('status', (int)$status);
            })
            ->when(isset($collection_id), function ($query) use ($collection_id) {
                $query->whereHas('collection', function ($query) use ($collection_id) {
                    $query->where('id', $collection_id);
                });
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('url', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->orderBy('order_id', 'desc')
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
            'collection_id' => 'required|exists:collections,id',
            'url' => 'required',
            'image' => 'nullable|image|max:2048',
            'mb_image' => 'nullable|image|max:2048'
        ]);


        $collection = CollectionImage::create([
            'collection_id' => $request->collection_id,
            'url' => $request->url
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = FileHandler::upload($file, 'collection-images');
            if ($path) {
                //file moved and save to db
                $collection->update(['image' => $path]);
            }
        }
        if ($request->hasFile('mb_image')) {
            $file = $request->file('mb_image');
            $path = FileHandler::upload($file, 'collection-images');
            if ($path) {
                //file moved and save to db
                $collection->update(['mb_image' => $path]);
            }
        }
        return response()->json(['status' => 'success', 'msg' => 'Collection Item Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ApiResource
     */
    public function show($id)
    {
        $collection = CollectionImage::findOrFail($id);
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
            'collection_id' => 'nullable|exists:collections,id',
            'image' => 'nullable|image|max:2048',
            'mb_image' => 'nullable|image|max:2048'
        ]);

        $collection = CollectionImage::with('collection')->findOrFail($id);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uploadedPath = FileHandler::upload($file, 'collection-images');
            if ($uploadedPath) {
                /* deleting existing file */
                FileHandler::delete($collection->image);
                /*Replace string with new one*/
                $collection->update(['image' => $uploadedPath]); //update db
            }
        }
        if ($request->hasFile('mb_image')) {
            $file = $request->file('mb_image');
            $uploadedPath = FileHandler::upload($file, 'collection-images');
            if ($uploadedPath) {
                /* deleting existing file */
                FileHandler::delete($collection->mb_image);
                /*Replace string with new one*/
                $collection->update(['mb_background_image' => $uploadedPath]); //update db
            }
        }

        $collection->update($request->except(['image', 'mb_image']));

        $status = 'success';
        $msg = 'Collection Item updated successfully';
        if ($request->filled('status')) {
            if ($request->status) {
                $status = 'success';
                $msg = 'Collection Item Published Successfully';
            } else {
                $status = 'warning';
                $msg = 'Collection Item Unpublished Successfully';
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
        $collection = CollectionImage::findOrFail($id);
        $collection->deleted_at = Carbon::now();
        $collection->save();
        return response()->json(['status' => 'success', 'msg' => 'Collection Item Deleted Successfully']);
    }
}
