<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\NewsLetter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NewsLetterController extends Controller
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
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = NewsLetter::count();
        }
        /* Query Builder */
        $newsletters = NewsLetter::when(isset($keyword), function ($query) use ($keyword) {
            $query->where(function ($query1) use ($keyword) {
                $query1->orWhere('email', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('created_at', 'LIKE', '%' . $keyword . '%');
            });
        })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($newsletters);
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
            'email' => ['required', 'unique:newsletters']
        ]);

        $newsletter = NewsLetter::create([
            'email' => $request->email,
        ]);

        return response()->json(['status' => 'success', 'msg' => $newsletter->email . ' Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $newsletter = NewsLetter::findOrFail($id);
        return new ApiResource($newsletter);
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
            'email' => "nullable|unique:newsletters,email,{$id}"
        ]);
        $newsletter = NewsLetter::findOrFail($id);
        $newsletter->update($request->all());
        return response()->json(['status' => 'success', 'msg' => 'Newsletter Updated Successfully', 'data' => $newsletter]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $newsletter = NewsLetter::findOrFail($id);
        $newsletter->deleted_at = Carbon::now();
        $newsletter->save();
        return response()->json(['status' => 'success', 'msg' => $newsletter->email . ' Deleted Successfully']);
    }
}
