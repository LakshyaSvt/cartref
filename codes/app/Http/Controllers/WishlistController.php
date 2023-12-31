<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Darryldecode\Cart\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wishlists = app('wishlist')->getContent();

        $products = Product::whereIn('id', $wishlists->pluck('id'))->get();


        return view('wishlist')->with([
            'products' => $products,
        ]);
    }

    
    
    public function wishlist(Request $request)
    {
        $product = Product::where('id', $request->product_id)->first();

        if(empty($product))
        {
            Session::flash('danger', 'There is something wrong. Please refresh the page and try again!');
            return redirect()->back();
        }

        $wishlist = app('wishlist');

        if($wishlist->get($product->id))
        {
            // remove
            $wishlist->remove($product->id);
            Session::flash('success', 'Product successfully removed from the wishlist!');
            
            return redirect()->back();
        }else{
            // add
            $wishlist->add(
                $product->id,
                $product->name,
                $product->offer_price,
                '1'
            );
            Session::flash('success', 'Product successfully added to the wishlist!');

            return redirect()->back();
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
