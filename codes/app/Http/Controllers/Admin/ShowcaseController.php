<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Order;
use App\Productcolor;
use Illuminate\Http\Request;

class ShowcaseController extends Controller
{
    public function index(Request $request)
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $status = request()->status;
        //$parent_category_id = request()->parent_category_id;
        //$sub_category_id = request()->sub_category_id;
        //$seller_id = request()->seller_id;
        $rows = request()->row_count ?? 25;

        if ($rows == 'all') {
            $rows = Order::count();
        }
        /* Query Builder */
        $products = Order::with('product', 'vendor', 'subcategory', 'productcolor')
            ->when(isset($status), function ($query) use ($status) {
                $query->where('order_status', $status);
            })
            //->when(isset($parent_category_id), function ($query) use ($parent_category_id) {
            //    $query->where('category_id', $parent_category_id);
            //})
            //->when(isset($sub_category_id), function ($query) use ($sub_category_id) {
            //    $query->where('subcategory_id', $sub_category_id);
            //})
            //->when(isset($seller_id), function ($query) use ($seller_id) {
            //    $query->where('seller_id', $seller_id);
            //})
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->orWhere('order_id', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('order_value', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('product_sku', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('size', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('color', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('product_offerprice', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('customer_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('customer_email', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('customer_alt_contact_number', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_streetaddress1', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_streetaddress2', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_pincode', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_city', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pickup_state', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('style_id', 'LIKE', '%' . $keyword . '%');

                    $query->orWhereHas('product', function ($query) use($keyword){
                        $query->orWhere('name','LIKE','%'.$keyword.'%');
                        $query->orWhere('slug','LIKE','%'.$keyword.'%');
                        $query->orWhere('brand_id','LIKE','%'.$keyword.'%');
                    });
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($products);
    }

    public function fetchOrder(Request $request, $id)
    {
        $product = Order::with('sizes', 'colors')->findOrFail($id)->append('json_images');
        //Response
        return new ApiResource($product);
    }

    public function updateProductStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required'],
        ]);
        $status = $request->status;
        $product = Order::findOrFail($id);
        $product->update(['admin_status' => $status]);

        if ($status == 'Accepted') {
            $status = 'success';
            $msg = 'Product Published Successfully';
        } else {
            $status = 'warning';
            $msg = 'Product Unpublished Successfully';
        }
        //Response
        return new ApiResource(['status' => $status, 'msg' => $msg]);
    }

    public function fetchProductColors(Request $request, $id)
    {
        $product = Order::findOrFail($id);
        $colors = Productcolor::where('product_id', $product->id)->get();
        //Response
        return new ApiResource(['colors' => $colors, 'product' => $product]);
    }

    public function fetchProductColor(Request $request, $id)
    {
        $color = Productcolor::findOrFail($id)->append('json_more_images');
        //Response
        return new ApiResource($color);
    }

}
