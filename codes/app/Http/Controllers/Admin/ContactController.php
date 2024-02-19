<?php

namespace App\Http\Controllers\Admin;

use App\Contact;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContactController extends Controller
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
            $rows = Contact::count();
        }
        /* Query Builder */
        $contacts = Contact::when(isset($keyword), function ($query) use ($keyword) {
            $query->where(function ($query1) use ($keyword) {
                $query1
                    ->orWhere('name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('mobile', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('message', 'LIKE', '%' . $keyword . '%');
            });
        })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($contacts);
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
            'name' => 'required',
            'mobile' => 'required',
            'message' => 'required',
            'email' => ['required', 'unique:contacts']
        ]);

        $contact = Contact::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'message' => $request->message,
            'email' => $request->email,
        ]);

        return response()->json(['status' => 'success', 'msg' => $contact->name . ' Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return new ApiResource($contact);
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
            'email' => "nullable|unique:contacts,email,{$id}"
        ]);
        $contact = Contact::findOrFail($id);
        $contact->update($request->all());
        return response()->json(['status' => 'success', 'msg' => $contact->name . ' Updated Successfully', 'data' => $contact]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->deleted_at = Carbon::now();
        $contact->save();
        return response()->json(['status' => 'success', 'msg' => $contact->name . ' Deleted Successfully']);
    }
}
