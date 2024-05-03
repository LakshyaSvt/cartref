<?php

namespace App\Http\Controllers\Admin;


use App\Helper\FileHandler;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Cart;
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
    public function index()
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $status = request()->status;
        $roles = request()->roles ? json_decode(request()->roles) : [];
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = User::count();
        }
        /* Query Builder */
        $users = User::with('role')
            ->when(isset($status), function ($query) use ($status) {
                $query->where('status', (int)$status);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('mobile', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('street_address_1', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('street_address_2', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pincode', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('city', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('state', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('brand_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('gender', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->when(is_array($roles) && count($roles) > 0, function ($query) use ($roles) {
                $query->whereHas('role', function ($q) use ($roles) {
                    $q->whereIn('name', $roles)
                        ->orWhereIn('id', $roles);
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($users);
    }

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

    public function editOrCreate(Request $request)
    {
        $request->validate([
            'email' => "required|unique:users,email,{$request->id}",

        ]);

        $user = User::with('address')->find($request->id);
        $msg = 'User updated successfully';
        if (!isset($user)) {
            $data = $request->all();
            $data['password'] = Hash::make($request->password);

            $user = User::create($data);
            $msg = 'User added successfully';
        } else {
            $user->update($request->except('password'));
        }

        if ($request->has('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return response()->json(['status' => 'success', 'msg' => $msg, 'data' => $user]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return ApiResource
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return new ApiResource($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['status' => 'success', 'msg' => $user->name . ' Deleted Successfully']);
    }

    public function deliveryboy()
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $status = request()->status;
        $roles = request()->roles ? json_decode(request()->roles) : [];
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = User::count();
        }
        /* Query Builder */
        $users = User::with('role', 'dbshowcases')
            ->when(isset($status), function ($query) use ($status) {
                $query->where('status', (int)$status);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('mobile', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('street_address_1', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('street_address_2', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pincode', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('city', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('state', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('brand_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('gender', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->when(is_array($roles) && count($roles) > 0, function ($query) use ($roles) {
                $query->whereHas('role', function ($q) use ($roles) {
                    $q->whereIn('name', $roles)
                        ->orWhereIn('id', $roles);
                });
            })
            ->whereHas('dbshowcases', function ($query) {
                $query->whereNotNull('deliveryboy_id')
                    ->where('order_status', 'Purchased');
            })
            ->latest()
            ->paginate($rows);

        //Response

        return new ApiResource($users);
    }

    public function vendorFetch()
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $status = request()->status;
        $roles = request()->roles ? json_decode(request()->roles) : [];
        $rows = request()->rows ?? 25;


        if ($rows == 'all') {
            $rows = User::count();
            /* Query Builder */
        }
        $users = User::with('role', 'dborder', 'dbproduct')
            ->when(isset($status), function ($query) use ($status) {
                $query->where('status', (int)$status);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('mobile', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('street_address_1', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('street_address_2', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pincode', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('city', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('state', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('brand_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('gender', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->when(is_array($roles) && count($roles) > 0, function ($query) use ($roles) {
                $query->whereHas('role', function ($q) use ($roles) {
                    $q->whereIn('name', $roles)
                        ->orWhereIn('id', $roles);
                });
            })
            ->latest()
            ->paginate($rows);


        //Response

        return new ApiResource($users);
    }

    public function customerFetch()
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $status = request()->status;
        $roles = request()->roles ? json_decode(request()->roles) : [];
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = User::count();
        }
        /* Query Builder */
        $users = User::with('role','userorder', 'dbcart','dbwishlist')
            ->when(isset($status), function ($query) use ($status) {
                $query->where('status', (int)$status);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('mobile', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('street_address_1', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('street_address_2', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pincode', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('city', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('state', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('brand_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('gender', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->when(is_array($roles) && count($roles) > 0, function ($query) use ($roles) {
                $query->whereHas('role', function ($q) use ($roles) {
                    $q->whereIn('name', $roles)
                        ->orWhereIn('id', $roles);
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($users);
    }
}
