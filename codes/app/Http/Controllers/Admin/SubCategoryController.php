<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\ProductSubcategory;
use Carbon\Carbon;
use FileHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class SubCategoryController extends Controller
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
        $category = request()->category_id;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = ProductSubcategory::count();
        }
        /* Query Builder */
        $categories = ProductSubcategory::with('category')
            ->when(isset($status), function ($query) use ($status) {
                $query->where('status', (int) $status);
            })
            ->when(isset($category), function ($query) use ($category) {
                $query->whereHas('category', function ($query) use ($category) {
                    $query->where('id', $category);
                });
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('slug', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('hsn', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('gst', 'LIKE', '%' . $keyword . '%')
                        //                        ->orWhere(function($query2) use($keyword) {
//                            $query2->whereHas('category', function ($query3) use ($keyword) {
//                                $query3->orWhere('name', 'LIKE', '%' . $keyword . '%')
//                                    ->orWhere('slug', 'LIKE', '%' . $keyword . '%');
//                            });
//                        })
                    ;
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($categories);
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
            'category_id' => 'required|exists:product_categories,id',
            'name' => "required|unique:product_subcategories,name|unique:product_categories,name",
            'slug' => ['required', 'unique:product_subcategories'],
            'image' => 'nullable|image|max:2048'
        ], [
            'category_id.exists' => 'Not an existing category',
        ]);


        $category = ProductSubcategory::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'category_id' => $request->category_id,
            'hsn' => $request->hsn,
            'gst' => $request->gst,
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = FileHandler::upload($file, 'product-subcategories');

            if ($path) {
                //file moved and save to db
                $category->image = $path;
                $category->save();
            }
        }
        return response()->json(['status' => 'success', 'msg' => $category->name . ' Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = ProductSubcategory::findOrFail($id);
        return new ApiResource($category);
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
            'category_id' => 'nullable|exists:product_categories,id',
            'slug' => "nullable|unique:product_subcategories,slug,{$id}",
            'image' => 'nullable|image|max:2048'
        ], [
            'category_id.exists' => 'Not an existing category',
            'image.max' => "The image must not be greater than 2 MB"
        ]);

        $category = ProductSubcategory::findOrFail($id);
        $category->update($request->all());

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uploadedPath = FileHandler::upload($file, 'product-subcategories');
            if ($uploadedPath) {
                /* deleting existing file */
                FileHandler::delete($category->image);

                /*Replace string with new one*/
                $category->update(['image' => $uploadedPath]); //update db
            }
        }

        $status = 'success';
        $msg = $category->name . ' updated successfully';
        if ($request->filled('status')) {
            if ($request->status) {
                $status = 'success';
                $msg = $category->name . ' Published Successfully';
            } else {
                $status = 'warning';
                $msg = $category->name . ' Unpublished Successfully';
            }
        }
        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $category]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = ProductSubcategory::findOrFail($id);
        $category->deleted_at = Carbon::now();
        $category->save();
        return response()->json(['status' => 'success', 'msg' => $category->name . ' Deleted Successfully']);
    }
}
