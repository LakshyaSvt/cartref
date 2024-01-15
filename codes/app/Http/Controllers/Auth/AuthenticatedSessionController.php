<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Config;
use App\Productcolor;
use App\Productsku;
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        Session::put('login', true);
        Session::put('register', false);
        $request->authenticate();

        $request->session()->regenerate();
        Session::put('login', false);

        if(Auth::check()){
            $this->saveLoggedInCart();
        }
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }


    public function saveLoggedInCart(){
        $userID = 0;
        if (Auth::check()) {
            $userID = auth()->user()->id;
        } else {
            if (session('session_id')) {
                $userID = session('session_id');
            } else {
                $userID = rand(1111111111, 9999999999);
                session(['session_id' => $userID]);
            }
        }

        $id = session('session_id') ?? '';
        $session_cart = \Cart::session($id);
        $session_cartItems = $session_cart->getContent();
        $obj = json_decode(json_encode($session_cartItems));
        $keys = array_keys((array)$obj);

        //logged-in user cart
        $cart = \Cart::session($userID);

        if(isset($keys) && is_array($keys) && count($keys) > 0){
            foreach ($keys as $key) {
                $cartItem = $obj->{$key};
                $product_id = $cartItem->attributes->product_id;

                if(!isset($product_id)){
                    break;
                }
                $product = \App\Models\Product::find($product_id);

                if(!isset($product)){
                    break;
                }

                //copy values
                $size = $cartItem->attributes->size;
                $color = $cartItem->attributes->color;
                $max_g_need = $cartItem->attributes->g_plus;
                $qty = $cartItem->quantity;
                $offer_price = $cartItem->price;
                $customized_image = $cartItem->attributes->customized_image;
                $original_file = $cartItem->attributes->original_file;
                $requireddocument = $cartItem->attributes->requireddocument;
                $ordertype = $cartItem->attributes->type;
                $availablestock = $product->productskus->where('size', $size)->where('color', $color)->first()->available_stock;

                /**
                 * Generate random row id
                 * If the product is already in the cart then fetch that product cart row id and assign
                 */

                // check if same product is already in the user's cart
                $validatecartrowid = \Cart::session($userID)->getContent()
                    ->where('attributes.product_id', $product->id)
                    ->where('attributes.size', $size)
                    ->where('attributes.color', $color)
                    ->where('attributes.g_plus', $max_g_need)
                    ->where('attributes.customized_image', null)
                    ->first();


                // if product exists in the cart then fetch cart row id
                if (!empty($validatecartrowid)) {
                    // restrict user to add more than allowed qty of the each product
                    if ($validatecartrowid->quantity >= Config::get('icrm.order_options.max_qty_per_product')) {
                        Session::flash('warning', 'You can only purchase maximum ' . Config::get('icrm.order_options.max_qty_per_product') . ' quantity of each product per order!');
                        return redirect()->route('product.slug', ['slug' => $product->slug]);
                    } else {
                        $cartrowid = $validatecartrowid->id;

                        // if size is a part of sku field then fetch weight from sku row
                        if (Config::get('icrm.product_sku.size') == 1) {
                            // check if color is a part of sku fields then fetch weight from sku row for size + color
                            if (Config::get('icrm.product_sku.color') == 1) {
                                // get sku weight for size + color
                                $skuweight = Productsku::where('product_id', $product->id)->where('status', 1)->where('size', $size)->where('color', $color)->first();
                            } else {
                                // get sku weight for size
                                $skuweight = Productsku::where('product_id', $product->id)->where('status', 1)->where('size', $size)->first();
                            }

                            if (empty($skuweight)) {
                                // sku is empty so fetch default product dimensions
                                $cartweight = $product->weight * ($validatecartrowid->quantity + 1);
                            } else {
                                // sku weight
                                $cartweight = $skuweight->weight * ($validatecartrowid->quantity + 1);
                            }
                        } else {
                            $cartweight = $product->weight * ($validatecartrowid->quantity + 1);
                        }
                    }
                } else {
                    // Generate random row id
                    $cartrowid = mt_rand(1000000000, 9999999999);
                }

                /**
                 * Check if stock management is enabled
                 * If same sku with other varient is already added then fetch its quantity
                 * Check if stock exceeded
                 */

                if (Config::get('icrm.stock_management.feature') == 1) {
                    $sameproduct = \Cart::session($userID)->getContent()
                        ->where('attributes.product_id', $product->id)
                        ->where('attributes.size', $size)
                        ->where('attributes.color', $color);

                    if (count($sameproduct) > 0) {
                        $alreadyincart = $sameproduct->sum('quantity');
                    } else {
                        $alreadyincart = 0;
                    }

                    if ($alreadyincart + $qty > $availablestock) {
                        Session::flash('danger', 'Only ' . $availablestock . ' item available! and out of which you have already added ' . $alreadyincart . ' item in ' . Config::get('icrm.cart.name') . '.');
                        return redirect()->route('product.slug', ['slug' => $product->slug]);
                    }
                }


                if (!empty($max_g_need)) {
                    // calculate max g plus value
                    $gpluscharges = $max_g_need * $product->cost_per_g;

                    $maxgplus = new \Darryldecode\Cart\CartCondition(array(
                        'name' => 'maxgplus',
                        'type' => 'maxgplus',
                        'value' => '+' . $gpluscharges,
                        'order' => 1,
                    ));
                } else {
                    $maxgplus = new \Darryldecode\Cart\CartCondition(array(
                        'name' => 'maxgplus',
                        'type' => 'maxgplus',
                        'value' => '+0',
                        'order' => 1,
                    ));
                    $gpluscharges = '0';
                }

                if (isset($cartweight)) {
                    $cartweight = $cartweight;
                } else {
                    // $cartweight = $this->product->weight;

                    // if size is a part of sku field then fetch weight from sku row
                    if (Config::get('icrm.product_sku.size') == 1) {
                        // check if color is a part of sku fields then fetch weight from sku row for size + color
                        if (Config::get('icrm.product_sku.color') == 1) {
                            // get sku weight for size + color
                            $skuweight = Productsku::where('product_id', $product->id)->where('status', 1)->where('size', $size)->where('color', $color)->first();
                        } else {
                            // get sku weight for size
                            $skuweight = Productsku::where('product_id', $product->id)->where('status', 1)->where('size', $size)->first();
                        }

                        if (empty($skuweight)) {
                            // sku is empty so fetch default product dimensions
                            $cartweight = $product->weight;
                        } else {
                            // sku weight
                            $cartweight = $skuweight->weight;
                        }
                    } else {
                        $cartweight = $product->weight;
                    }
                }


                if (Config::get('icrm.tax.type') == 'subcategory') {
                    /**
                     * Add Tax according to product subcategory tax value
                     */

                    // or add multiple conditions from different condition instances
                    $tax = new \Darryldecode\Cart\CartCondition(array(
                        'name' => 'tax',
                        'type' => 'tax',
                        'value' => $product->productsubcategory->gst . '%',
                        'order' => 2
                    ));
                } else {
                    $tax = new \Darryldecode\Cart\CartCondition(array(
                        'name' => 'tax',
                        'type' => 'tax',
                        'value' => '+0',
                        'order' => 2
                    ));
                }

                // add to cart with conditions
                $item = array(
                    'id' => $cartrowid,
                    'name' => $product->name,
                    'price' => $offer_price,
                    'quantity' => $qty,
                    'attributes' => array(
                        'size' => $size,
                        'product_id' => $product->id,
                        'customized_image' => $customized_image,
                        'original_file' => $original_file,
                        'color' => $color,
                        'g_plus' => $max_g_need,
                        'cost_per_g' => $product->cost_per_g,
                        'g_plus_charges' => $gpluscharges,
                        'weight' => $cartweight,
                        'hsn' => $product->productsubcategory->hsn,
                        'gst' => $product->productsubcategory->gst,
                        'type' => $ordertype,
                        'requireddocument' => $requireddocument,
                    ),
                    'conditions' => [$maxgplus, $tax]
                );
                $cart->add($item);
            }
        }
        session()->forget('session_id');
    }
}
