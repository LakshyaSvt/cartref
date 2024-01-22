<?php

namespace App\Http\Controllers\Admin;

use App\Component;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Carbon\Carbon;
use FileHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ComponentController extends Controller
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
        $component = request()->category_id;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = Component::count();
        }
        /* Query Builder */
        $components = Component::when(isset($status), function ($query) use ($status) {
            $query->where('status', (int) $status);
        })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1
                        ->orWhere('title_1', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('title_2', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('description', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('page_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('button', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('url', 'LIKE', '%' . $keyword . '%')
                    ;
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($components);
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
            'page_name' => 'required',
            'title_1' => "required",
            'url' => 'required',
            'image' => 'nullable|image|max:2048'
        ]);


        $component = Component::create([
            'title_1' => $request->title_1,
            'title_2' => $request->title_2,
            'description' => $request->description,
            'button' => $request->button,
            'url' => $request->url,
            'page_name' => $request->page_name,
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = FileHandler::upload($file, 'components');

            if ($path) {
                //file moved and save to db
                $component->image = $path;
                $component->save();
            }
        }
        return response()->json(['status' => 'success', 'msg' => $component->title_1 . ' Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $component = Component::findOrFail($id);
        return new ApiResource($component);
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
            'image' => 'nullable|image|max:2048'
        ]);

        $component = Component::findOrFail($id);
        $component->update($request->all());

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uploadedPath = FileHandler::upload($file, 'components');
            /* file moved */
            if ($uploadedPath) {
                /* deleting existing file */
                FileHandler::delete($component->image);
                /*replace string with new one*/
                $component->update(['image' => $uploadedPath]); //update db
            }
        }

        $status = 'success';
        $msg = $component->title_1 . ' updated successfully';
        if ($request->filled('status')) {
            if ($request->status) {
                $status = 'success';
                $msg = $component->title_1 . ' Published Successfully';
            } else {
                $status = 'warning';
                $msg = $component->title_1 . ' Unpublished Successfully';
            }
        }
        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $component]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $component = Component::findOrFail($id);
        $component->deleted_at = Carbon::now();
        $component->save();
        return response()->json(['status' => 'success', 'msg' => $component->title_1 . ' Deleted Successfully']);
    }
}
