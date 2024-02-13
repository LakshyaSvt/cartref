<?php

namespace App\Http\Controllers\Vendor;

use App\Color;
use App\Helper\FileHandler;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Imports\ProductImport;
use App\Models\Product;
use App\ProductCategory;
use App\Productcolor;
use App\Productsku;
use App\ProductSubcategory;
use App\Size;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function fetchProducts(Request $request)
    {
        /* Query Parameters */
        $keyword = request()->keyword;
        $status = request()->status;
        $parent_category_id = request()->parent_category_id;
        $sub_category_id = request()->sub_category_id;
        $seller_id = request()->seller_id;
        $rows = request()->row_count ?? 25;

        if ($rows == 'all') {
            $rows = Product::count();
        }
        /* Query Builder */
        $products = Product::with('productcategory', 'productsubcategory')
            ->where('seller_id', auth()->user()->id)
            ->when(isset($status), function ($query) use ($status) {
                $query->where('admin_status', $status);
            })
            ->when(isset($parent_category_id), function ($query) use ($parent_category_id) {
                $query->where('category_id', $parent_category_id);
            })
            ->when(isset($sub_category_id), function ($query) use ($sub_category_id) {
                $query->where('subcategory_id', $sub_category_id);
            })
            ->when(isset($seller_id), function ($query) use ($seller_id) {
                $query->where('seller_id', $seller_id);
            })
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('slug', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('sku', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('product_tags', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('brand_id', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('style_id', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate($rows);

        //Response
        return new ApiResource($products);
    }

    public function editOrCreateProduct(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => "required|unique:products,slug,{$request->id}",
            'sku' => "required|unique:products,sku,{$request->id}",
            'description' => 'required',
            'features' => 'required',
            'category_id' => 'required|exists:product_categories,id',
            'subcategory_id' => 'required|exists:product_subcategories,id',
            'offer_price' => 'required|numeric',
            'mrp' => 'required|numeric',
            'length' => 'required|numeric|min:0.5',
            'breadth' => 'required|numeric|min:0.5',
            'height' => 'required|numeric|min:0.5',
            'weight' => 'required|numeric|min:0.5',
            //'size_guide' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::with('sizes', 'colors')->find($request->id);
        $msg = 'Product updated successfully';
        if (!isset($product)) {
            $product = new Product;
            $msg = 'Product added successfully';
        }
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->sku = $request->sku;

        $product->mrp = (float)$request->mrp;
        $product->offer_price = (float)$request->offer_price;

        $product->category_id = $request->category_id;
        $product->subcategory_id = $request->subcategory_id;
        $product->brand_id = $request->brand_id;
        $product->style_id = $request->style_id;
        $product->occasion_id = $request->occasion_id;
        $product->gender_id = $request->gender_id;

        $product->length = $request->length;
        $product->breadth = $request->breadth;
        $product->height = $request->height;
        $product->weight = $request->weight;

        $product->features = $request->features;
        $product->flash_sale = $request->flash_sale;
        $product->specifications = $request->specifications;
        $product->description = $request->description;
        $product->product_tags = $request->product_tags;

        $product->image = $request->image;
        $product->images = ($request->json_images);

        $product->seller_id = auth()->user()->id;
        $product->save();

        //colors
        if (isset($request->multi_selected_colors)) {
            $colors = json_decode($request->multi_selected_colors);
            $product->colors()->sync([]);
            foreach ($colors as $color) {
                $color = trim($color);
                $item = Color::where('status', 1)->where('name', 'LIKE', '%' . $color . '%')->first();
                if (isset($item)) {
                    $product->colors()->attach($item->id);
                }
            }
        }

        //sizes
        if (isset($request->multi_selected_sizes)) {
            $sizes = json_decode($request->multi_selected_sizes);
            $product->sizes()->sync([]);
            foreach ($sizes as $size) {
                $size = trim($size);
                $item = Size::where('status', 1)->where('name', 'LIKE', '%' . $size . '%')->first();
                if (isset($item)) {
                    $product->sizes()->attach($item->id);
                }
            }
        }

        /* Creat Skus */
        $productSku = new Product();
        $productSku->createskus($product->id);
        $productSku->createcolors($product->id);
        /* Creat Skus */

        return response()->json(['status' => 'success', 'msg' => $msg, 'data' => $product]);
    }

    public function deleteProductImage(Request $request)
    {
        $request->validate([
            'image' => 'required'
        ]);
        $image = $request->image;
        if (FileHandler::delete($image)) {
            return response()->json(['status' => 'success', 'msg' => 'Image deleted successfully']);
        }
        return response()->json(['status' => 'warning', 'msg' => 'Image not found']);
    }

    public function uploadProductImages(Request $request)
    {
        $request->validate([
            'images' => 'required'
        ]);

        $files = $request->file('images');
        $json_images = [];

        foreach ($files as $file) {
            $uploadedPath = FileHandler::upload($file, 'products');
            if ($uploadedPath) {
                array_push($json_images, $uploadedPath);
            }
        }
        return response()->json(['status' => 'success', 'msg' => 'Images uploaded successfully', 'data' => $json_images]);
    }

    public function fetchProduct(Request $request, $id)
    {
        $product = Product::with('sizes', 'colors')->where('seller_id', auth()->user()->id)->findOrFail($id);
        //Response
        return new ApiResource($product);
    }

    public function updateProductStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required'],
        ]);
        $status = $request->status;
        $product = Product::where('seller_id', auth()->user()->id)->findOrFail($id);
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

    public function bulkExcelUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:20000', //20MB
            'category_id' => 'required',
            'subcategory_id' => 'required',
        ]);
        $category = ProductCategory::findOrFail(request()->category_id);
        $sub_category = ProductSubcategory::findOrFail(request()->subcategory_id);

        try {
            $import = new ProductImport($category->id, $sub_category->id);
            Excel::import($import, $request->file);

            $importedRows = $import->getRowCount(); // Get the count of successfully imported rows

            $response = [
                'status' => 'success',
                'msg' => 'Products uploaded successfully',
                'data' => "<strong>" . $importedRows . "</strong> Rows added",
            ];
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            //Error Handling
            $failures = $e->failures();
            $all_errors = $e->errors();
            $errormessage = '';

            //Get the error's row position and msg
            foreach ($failures as $failure) {
                $err = implode('', $failure->errors());
                $errormessage .= " At Row <strong>" . ($failure->row() + 1) . "</strong>, ";
                $errormessage .= "<span>" . $err . "</span>";
                $errormessage .= " where values are " . json_encode(array_values($failure->values()));
                $errormessage .= "<br>\n";
            }

            $response = [
                'status' => 'error',
                'msg' => 'Some Error Occurred',
                'data' => $errormessage
            ];
        }

        return response()->json($response);
    }

    public function fetchProductColors(Request $request, $id)
    {
        $product = Product::findOrFail($id);
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

    public function updateProductColorStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required'],
        ]);
        $status = $request->status;

        $productColor = Productcolor::findOrFail($id);
        $product = Product::findOrFail($productColor->product_id);
        $targetColor = Color::where('name', $productColor->color)->first();

        if ($status) {
            $product->colors()->attach($targetColor);
        } else {
            $product->colors()->detach($targetColor);
        }
        $productColor->update(['status' => $status]);

        if ($request->filled('status')) {
            if ($request->status) {
                $status = 'success';
                $msg = $targetColor->name . ' Published Successfully';
            } else {
                $status = 'warning';
                $msg = $targetColor->name . ' Unpublished Successfully';
            }
        }
        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $productColor]);

    }

    public function deleteImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required'
        ]);
        $image = $request->image;
        $productColor = Productcolor::findOrFail($id)->append('json_more_images');
        $json_images = $productColor->json_more_images;

        if (in_array($image, $json_images)) {
            FileHandler::delete($image);

            $key = array_search($image, $json_images);
            array_splice($json_images, $key, 1);
            $productColor->update(['more_images' => json_encode($json_images)]);
            return response()->json(['status' => 'success', 'msg' => 'Image deleted successfully']);
        }

        return response()->json(['status' => 'warning', 'msg' => 'Image not found']);
    }

    public function uploadImages(Request $request, $id)
    {
        $request->validate([
            'images' => 'required'
        ]);

        $files = $request->file('images');
        $productColor = Productcolor::with('product')
            ->whereHas('product', function ($query) {
                $query->where('seller_id', auth()->user()->id);
            })
            ->findOrFail($id)
            ->append('json_more_images');
        $json_images = $productColor->json_more_images;

        foreach ($files as $file) {
            $uploadedPath = FileHandler::upload($file, 'productcolors');
            if ($uploadedPath) {
                $json_images[] = $uploadedPath; //array_push
            }
        }
        $productColor->update(['more_images' => json_encode($json_images)]);
        return response()->json(['status' => 'success', 'msg' => 'Images uploaded successfully']);

    }

    public function uploadMainImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);
        $productColor = Productcolor::with('product')
            ->whereHas('product', function ($query) {
                $query->where('seller_id', auth()->user()->id);
            })
            ->findOrFail($id);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $uploadedPath = FileHandler::upload($file, 'productcolors');
            /* file moved */
            if ($uploadedPath) {
                /* deleting existing file */
                FileHandler::delete($productColor->main_image);

                //replace string with new one
                $productColor->update(['main_image' => $uploadedPath]); //update db
                return response()->json(['status' => 'success', 'msg' => 'Image updated successfully']);
            }
        }

        return response()->json(['status' => 'error', 'msg' => 'Something went wrong']);
    }

    public function fetchSizesByColorId(Request $request, $product_id, $color_id)
    {
        $productColor = Productcolor::with('product')
            ->whereHas('product', function ($query) {
                $query->where('seller_id', auth()->user()->id);
            })
            ->findOrFail($color_id);

        $sizes = Productsku::with('product')
            ->whereHas('product', function ($query) {
                $query->where('seller_id', auth()->user()->id);
            })
            ->where([
                'product_id' => $product_id,
                'color' => $productColor->color,
            ])
            ->orderBy('size', 'ASC')
            ->get();
        return new ApiResource($sizes);
    }

    public function fetchSizeById(Request $request, $product_id, $color_id, $size_id)
    {
        $product = Product::where(['id' => $product_id, 'seller_id' => auth()->user()->id])->firstOrFail();
        $productColor = Productcolor::findOrFail($color_id);
        $size = Productsku::where([
            'id' => $size_id,
            'product_id' => $product_id,
            'color' => $productColor->color,
        ])
            ->firstOrFail();

        return new ApiResource($size);
    }

    public function updateSizeById(Request $request, $product_id, $color_id, $size_id)
    {
        $product = Product::where(['id' => $product_id, 'seller_id' => auth()->user()->id])->firstOrFail();
        $productColor = Productcolor::findOrFail($color_id);
        $size = Productsku::where([
            'id' => $size_id,
            'product_id' => $product_id,
            'color' => $productColor->color,
        ])
            ->firstOrFail();

        $size->update($request->all());
        $status = 'success';

        $msg = $size->size . ' updated successfully';
        if ($request->filled('status')) {
            if ($request->status) {
                $status = 'success';
                $msg = $size->size . ' Published Successfully';
            } else {
                $status = 'warning';
                $msg = $size->size . ' Unpublished Successfully';
            }
        }

        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $size]);
    }

}
