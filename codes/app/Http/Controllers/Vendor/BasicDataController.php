<?php

namespace App\Http\Controllers\Vendor;

use App\Brand;
use App\Color;
use App\Coupon;
use App\Gender;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\ProductCategory;
use App\ProductSubcategory;
use App\Size;
use App\Style;
use App\VendorPayment;


class BasicDataController extends Controller
{
    public function category()
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = ProductCategory::count();
        }
        /* Query Builder */
        $categories = ProductCategory::where('status', 1)
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('slug', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($categories);
    }

    /**
     * Display a listing of the resource.
     *
     * @return ApiResource
     */
    public function subcategory()
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $category = request()->category_id;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = ProductSubcategory::count();
        }
        /* Query Builder */
        $categories = ProductSubcategory::with('category')
            ->where('status', 1)
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
                        ->orWhere('gst', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($categories);
    }

    public function gender()
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = Gender::count();
        }
        /* Query Builder */
        $genders = Gender::where('status', 1)
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('name', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($genders);
    }

    public function size()
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = Size::count();
        }
        /* Query Builder */
        $size = Size::where('status', 1)
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('slug', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($size);
    }

    public function color()
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = Color::count();
        }
        /* Query Builder */
        $color = Color::where('status', 1)
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('slug', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('rgb', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($color);
    }

    public function style()
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = Style::count();
        }
        /* Query Builder */
        $styles = Style::where('status', 1)
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('slug', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($styles);
    }

    public function brand()
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = Brand::count();
        }
        /* Query Builder */
        $brands = Brand::where('status', 1)
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query1) use ($keyword) {
                    $query1->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('slug', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($brands);
    }

    public function vendorpayment()
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $status = request()->status;
        $rows = request()->rows ?? 25;
        $start_date = request()->start_date;
        $end_date = request()->end_date;

        if ($rows == 'all') {
            $rows = VendorPayment::count();
        }
        /* Query Builder */
        $vendorPayments = VendorPayment::with('user')
            ->where('vendor_id', auth()->user()->id)
            ->when(isset($status), function ($query) use ($status) {
                $query->where('status', (int)$status);
            })
            ->when(isset($start_date), function ($query) use ($start_date) {
                $query->where('billing_date', '>=', $start_date);
            })
            ->when(isset($end_date), function ($query) use ($end_date) {
                $query->where('billing_date', '<=', $end_date);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->whereHas('user', function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($vendorPayments);
    }

    public function coupon(){
        /* Query Parameters */
        $keyword = request()->keyword;
        $status = request()->status;
        $rows = request()->rows ?? 25;

        if ($rows == 'all') {
            $rows = Coupon::count();
        }
        /* Query Builder */
        $coupons = Coupon::with('brands','sellers')
            ->whereHas('sellers', function($q){
                return $q->where('users.id','=', auth()->user()->id);
            })
            ->when(isset($status), function ($query) use ($status) {
                $query->where('status', (int)$status);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query
                        ->orWhere('code', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('type', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('description', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('value', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('from', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('to', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('background_color', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('user_email', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('min_order_value', 'LIKE', '%' . $keyword . '%');

                    $query->orWhereHas('brands', function ($query) use ($keyword) {
                        $query->where(function ($query) use ($keyword) {
                            $query->orWhere('name', 'LIKE', '%' . $keyword . '%');
                        });
                    });
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($coupons);
    }
}
