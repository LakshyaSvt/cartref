<?php

namespace App\Http\Controllers\Panel;


use App\Helper\FileHandler;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ApiResource
     */

    public function uploadImages(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'images' => 'required'
        ]);

        $files = $request->file('images');
        $json_images = [];

        foreach ($files as $file) {
            $uploadedPath = FileHandler::upload($file, 'users');
            if ($uploadedPath) {
                $json_images[] = $uploadedPath;
            }
        }
        return response()->json(['status' => 'success', 'msg' => 'Images uploaded successfully', 'data' => $json_images]);

    }

    public function uploadCheck(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'file' => 'required'
        ]);

        $file = $request->file('file');
        $json_images = [];

        $uploadedPath = FileHandler::upload($file, 'users');
        if ($uploadedPath) {
            $json_images[] = (object)['download_link' => $uploadedPath, 'original_name' => $file->getClientOriginalName()];
        }
        return response()->json(['status' => 'success', 'msg' => 'Cheque uploaded successfully', 'data' => $json_images]);

    }

    public function edit(Request $request)
    {
        $request->validate([
            'email' => "required|unique:users,email,{$request->user()->id}",
        ]);

        $user = User::with('address')->findOrFail($request->user()->id);

        $user->update($request->except('password'));

        if ($request->has('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $msg = 'User updated successfully';
        return response()->json(['status' => 'success', 'msg' => $msg, 'data' => $user]);
    }

}
