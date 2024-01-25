<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Seo;
use Carbon\Carbon;
use Illuminate\Http\Request;


class SeoController extends Controller
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
            $rows = Seo::count();
        }
        /* Query Builder */
        $categories = Seo::when(isset($status), function ($query) use ($status) {
            $query->where('status', (int)$status);
        })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('url', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('meta_title', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('meta_keywords', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('header_script', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('footer_script', 'LIKE', '%' . $keyword . '%');
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
            'url' => 'required',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'meta_keywords' => 'required',
        ]);

        $seo = Seo::create([
            'url' => $request->url,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'header_script' => $request->header_script,
            'footer_script' => $request->footer_script,
        ]);

        return response()->json(['status' => 'success', 'msg' => $seo->url . ' Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seo = Seo::findOrFail($id);
        return new ApiResource($seo);
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
        $seo = Seo::findOrFail($id);
        $seo->update($request->all());

        $status = 'success';
        $msg = $seo->url . ' updated successfully';

        if ($request->filled('status')) {
            if ($request->status) {
                $status = 'success';
                $msg = $seo->url . ' Published Successfully';
            } else {
                $status = 'warning';
                $msg = $seo->url . ' Unpublished Successfully';
            }
        }
        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $seo]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $seo = Seo::findOrFail($id);
        $seo->deleted_at = Carbon::now();
        $seo->save();
        return response()->json(['status' => 'success', 'msg' => $seo->url . ' Deleted Successfully']);
    }
}
