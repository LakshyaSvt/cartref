<?php

namespace App\Http\Controllers;

use App\Exports\ActionItemExport;
use App\Exports\ViewExporter;
use App\Imports\ProductImport;
use App\ProductCategory;
use App\ProductSubcategory;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductBulkUploadController extends Controller
{
    public function uploadPage()
    {
        $categories = ProductCategory::with('subcategory')->where('status', true)->get();
        return view('vendor.voyager.products.bulk-upload',compact('categories'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'subcategory_id' => 'required',
        ]);
        $category = ProductCategory::findOrFail(request()->category_id);
        $sub_category = ProductSubcategory::findOrFail(request()->subcategory_id);

        try {
            $imp = Excel::import(new ProductImport($category->id, $sub_category->id), $request->file);

            $response = [
                'message' => 'Products uploaded successfully',
                'alert-type' => 'success',
            ];
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $response = array(
                'message' => 'Oops! Error occurred. Please Try again Later.',
                'alert-type' => 'error'
            );
        }

        return redirect()->back()->with($response);
    }

    public function export_product_dummy() {
        return Excel::download(
            new ActionItemExport(),
            'CARTREFS PRODUCT-UPLOAD.xlsx'
        );
    }
}
