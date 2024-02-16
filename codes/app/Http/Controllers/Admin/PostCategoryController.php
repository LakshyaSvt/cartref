<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;

class PostCategoryController extends Controller
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
            $rows = Category::count();
        }
        /* Query Builder */
        $categories = Category::when(isset($status), function ($query) use ($status) {
            $query->where('status', (int)$status);
        })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1
                        ->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('slug', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->orderBy('order')
            ->paginate($rows);

        //Response
        return new ApiResource($categories);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'slug' => ['required', 'unique:categories']
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return response()->json(['status' => 'success', 'msg' => $category->name . ' Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ApiResource
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);
        return new ApiResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'slug' => "nullable|unique:categories,slug,{$id}"
        ]);
        $category = Category::findOrFail($id);
        $category->update($request->all());

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
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['status' => 'success', 'msg' => $category->name . ' Deleted Successfully']);
    }
}
