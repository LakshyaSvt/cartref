<?php

namespace App\Http\Controllers\Admin;

use App\Helper\FileHandler;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
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
            $rows = Post::count();
        }
        /* Query Builder */
        $posts = Post::with('category')
            ->when(isset($status), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1
                        ->orWhere('title', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('slug', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('excerpt', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('body', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('meta_keywords', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('seo_title', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('meta_description', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($posts);
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
            'title' => ['required'],
            'slug' => ['required', 'unique:posts'],
        ]);

        $post = Post::create([
            'title' => $request->title,
            'slug' => $request->slug,
            //'author_id' => $request->author_id,
            'category_id' => $request->category_id,
            'seo_title' => $request->seo_title,
            'excerpt' => $request->excerpt,
            'body' => $request->body,
            'image' => $request->image,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = FileHandler::upload($file, 'posts');
            if ($path) {
                //file moved and save to db
                $post->image = $path;
                $post->save();
            }
        }

        return response()->json(['status' => 'success', 'msg' => $post->title . ' Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ApiResource
     */
    public function show($id)
    {
        $post = Post::with('category')->findOrFail($id);
        return new ApiResource($post);
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
            'slug' => "nullable|unique:posts,slug,{$id}"
        ]);
        $post = Post::with('category')->findOrFail($id);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uploadedPath = FileHandler::upload($file, 'posts');
            if ($uploadedPath) {
                /* deleting existing file */
                FileHandler::delete($post->image);
                /*Replace string with new one*/
                $post->update(['image' => $uploadedPath]); //update db
            }
        }

        $post->update($request->except(['image','status']));

        if($request->filled('status')){
            $status = $request->status;
            if($status){
                $post->update(['status' => 'PUBLISHED']);
            }
            else{
                $post->update(['status' => 'DRAFT']);
            }
        }

        $status = 'success';
        $msg = $post->title . ' updated successfully';
        if ($request->filled('status')) {
            if ($request->status) {
                $status = 'success';
                $msg = $post->title . ' Published Successfully';
            } else {
                $status = 'warning';
                $msg = $post->title . ' Unpublished Successfully';
            }
        }
        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $post]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return response()->json(['status' => 'success', 'msg' => $post->title . ' Deleted Successfully']);
    }
}
