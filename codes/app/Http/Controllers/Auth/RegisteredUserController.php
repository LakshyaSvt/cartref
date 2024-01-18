<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Productcolor;
use App\Productsku;
use App\Showcase;
use Illuminate\Http\Request;
use Craftsys\Msg91\Facade\Msg91;
use Illuminate\Validation\Rules;
use App\Notifications\WelcomeEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Config;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Notification;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        Session::put('register', true);
        Session::put('login', false);
        if (Config::get('icrm.auth.fields.companyinfo') == true) {
            // if company info is required
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'mobile' => ['required', 'numeric', 'digits:10', 'unique:users'],
                'company_name' => ['required'],
                'gst_number' => ['required', 'regex:"^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$"'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            Session::put('register', false);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'company_name' => $request->company_name,
                'gst_number' => $request->gst_number,
                'password' => Hash::make($request->password),
                'status' => '1',
            ]);


        } else {
            // if company info is not required
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'mobile' => ['required', 'numeric', 'digits:10', 'unique:users'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            Session::put('register', false);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'password' => Hash::make($request->password),
                'status' => '1',
            ]);
        }


        event(new Registered($user));

        Session::put('register', false);
        Auth::login($user);

        $this->saveLoggedInCart();
        $this->saveShowroomCart();

        return redirect()->intended(RouteServiceProvider::HOME);
    }


    public function saveLoggedInCart()
    {
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
        $sessionObj = json_decode(json_encode($session_cartItems));
        $sessionKeys = array_keys((array)$sessionObj);

        //logged-in user cart
        $cart = \Cart::session($userID);

        if (isset($sessionKeys) && is_array($sessionKeys) && count($sessionKeys) > 0) {
            foreach ($sessionKeys as $key) {
                $cartItem = $sessionObj->{$key};
                $product_id = $cartItem->attributes->product_id;

                if (!isset($product_id)) {
                    continue;
                }
                $product = \App\Models\Product::find($product_id);

                if (!isset($product)) {
                    continue;
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
//        session()->forget('session_id');
    }

    public function saveShowroomCart()
    {
        $id = session('session_id') ?? '';
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

        $session_cart = app('showcase')->session($id);
        $session_cartItems = $session_cart->getContent();
        $sessionObj = json_decode(json_encode($session_cartItems));
        $sessionKeys = array_keys((array) $sessionObj);

        //logged-in user cart
        $showcase = app('showcase')->session($userID);
        $showcasecontent = app('showcase')->session($userID)->getContent();

        /**
         * If showcase at home has products already
         * then check if the selected products is from same vendor else show error msg
         */
        if (isset($sessionKeys) && is_array($sessionKeys) && count($sessionKeys) > 0) {
            foreach ($sessionKeys as $key) {
                $cartItem = $sessionObj->{$key};
//                dd($cartItem);
                $product_id = $cartItem->attributes->product_id;

                if (!isset($product_id)) {
                    continue;
                }
                $product = \App\Models\Product::find($product_id);
                if (!isset($product)) {
                    continue;
                }

                //copy values
                $size = $cartItem->attributes->size;
                $color = $cartItem->attributes->color;
                $qty = $cartItem->quantity;
                $offer_price = $cartItem->price;
                $ordertype = $cartItem->attributes->type;

                if (count($showcasecontent) > 0) {
                    $notsamevendor = $showcasecontent->where('attributes.vendor_id', '==', $product->seller_id)->first();

                    if (empty($notsamevendor)) {
                        $msg = 'At a time you can request Showroom at home only from one vendor. <a href="' . url('/products/vendor/' . $product->seller_id) . '" style="text-decoration: underline; color: black; font-weight: 600;"> Click here </a> to browse products from ' . ucwords($product->vendor->brand_name) . ' vendor';
                        Session::flash('warning', $msg);
                        return redirect()->route('product.slug', ['slug' => $product->slug]);
                    }
                }

                /**
                 * Validate how many active showcases one customer can have
                 */

                if (Auth::check()) {
                    $activeshowcases = Showcase::where('user_id', auth()->user()->id)
                        ->where('order_status', 'New Order')
                        ->select('order_id')
                        ->groupBy('order_id')
                        ->count();

                    if ($activeshowcases == Config::get('icrm.showcase_at_home.active_orders')) {
                        Session::flash('warning', 'At a time you can place only ' . Config::get('icrm.showcase_at_home.active_orders') . ' active showcase at home orders.');
                        return redirect()->route('product.slug', ['slug' => $product->slug]);
                    }
                }

                /**
                 * Check if the allowed showcase at home products count exceeds
                 */
                if (count(app('showcase')->session($userID)->getContent()) == Config::get('icrm.showcase_at_home.order_limit')) {
                    Session::flash('warning', 'You can Showroom at home only ' . Config::get('icrm.showcase_at_home.order_limit') . ' items in one order.');
                    return redirect()->to(route('product.slug', ['slug' => $product->slug]))->with([
                        'warning' => 'You can Showroom at home only ' . Config::get('icrm.showcase_at_home.order_limit') . ' items in one order.',
                    ]);
                }

                /**
                 * Check if same product is already is showcase
                 */
                if (count($showcase->getContent()->where('attributes.product_id', $product->id)->where('attributes.size', $size)->where('attributes.color', $color)) > 0) {
                    Session::flash('warning', 'This product has already been added in the Showroom at home');
                    return redirect()->route('product.slug', ['slug' => $product->slug]);
                }

                /**
                 * Get the color image of the selected product
                 */
                $colorimage = Productcolor::where('product_id', $product->id)->where('color', $color)->first();

                if (empty($colorimage)) {
                    $colorimage = $product->image;
                } else {
                    $colorimage = $colorimage->main_image;
                }

                // add
                $showcase->add([
                    'id' => $color . $size . $product->id,
                    'name' => $product->name,
                    'price' => $offer_price,
                    'quantity' => '1',
                    'attributes' => array(
                        'product_id' => $product->id,
                        'slug' => $product->slug,
                        'image' => $colorimage,
                        'size' => $size,
                        'color' => $color,
                        'weight' => $product->weight,
                        'hsn' => $product->productsubcategory->hsn,
                        'gst' => $product->productsubcategory->gst,
                        'vendor_id' => $product->seller_id,
                        'vendor_city' => $product->vendor->city,
                        'type' => 'Showcase At Home'
                    )
                ]);

                Session::put('showcasevendor', $product->vendor->brand_name);
                Session::put('showcasevendorid', $product->seller_id);
            }
        }
    }
    public function redirect($client)
    {
        return Socialite::driver($client)->redirect();
    }

    public function googlecallback()
    {
        $client = Socialite::driver('google')->user();

        $user = User::where('email', $client->email)->first();

        if (!empty($user)) {
            $user->update([
                'client_token' => $client->token,
                'client_refresh_token' => $client->refreshToken,
                'client_name' => 'Google',
            ]);
        } else {
            $user = User::create([
                'name' => $client->name,
                'email' => $client->email,
                'client_id' => $client->id,
                'client_token' => $client->token,
                'client_refresh_token' => $client->refreshToken,
                'client_name' => 'Google',
            ]);


            // send welcome sms & email
            try {

                if (Config::get('icrm.sms.msg91.feature') == 1) {

                    if (!empty(auth()->user()->mobile)) {
                        if (!empty(Config::get('icrm.sms.msg91.welcome_flow_id'))) {
                            $mobile = '91' . auth()->user()->mobile;
                            $response = Msg91::sms()->to($mobile)->flow(Config::get('icrm.sms.msg91.welcome_flow_id'))->send();
                        }

                    }
                }


                $user->markEmailAsVerified();

                Auth::login($user);
                $this->saveLoggedInCart();
                $this->saveShowroomCart();

                Notification::route('mail', $user->email)->notify(new WelcomeEmail(auth()->user()));


                // echo 'done';
            } catch (\Craftsys\Msg91\Exceptions\ValidationException $e) {
                // issue with the request e.g. token not provided
                // echo 'issue with the request e.g. token not provided';
            } catch (\Craftsys\Msg91\Exceptions\ResponseErrorException $e) {
                // error thrown by msg91 apis or by http client
                // echo 'error thrown by msg91 apis or by http client';
            } catch (\Exception $e) {
                // something else went wrong
                // plese report if this happens :)
                // echo 'something else went wrong';
            }


        }


        $user->markEmailAsVerified();

        Auth::login($user);

        $this->saveLoggedInCart();
        $this->saveShowroomCart();




        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function facebookcallback()
    {
        $client = Socialite::driver('facebook')->user();

        $user = User::where('email', $client->email)->first();

        if (!empty($user)) {
            $user->update([
                'client_token' => $client->token,
                'client_refresh_token' => $client->refreshToken,
                'client_name' => 'Facebook',
            ]);
        } else {
            $user = User::create([
                'name' => $client->name,
                'email' => $client->email,
                'client_id' => $client->id,
                'client_token' => $client->token,
                'client_refresh_token' => $client->refreshToken,
                'client_name' => 'Facebook',
            ]);


            // send welcome sms & email
            try {

                if (Config::get('icrm.sms.msg91.feature') == 1) {

                    if (!empty(auth()->user()->mobile)) {
                        if (!empty(Config::get('icrm.sms.msg91.welcome_flow_id'))) {
                            $mobile = '91' . auth()->user()->mobile;
                            $response = Msg91::sms()->to($mobile)->flow(Config::get('icrm.sms.msg91.welcome_flow_id'))->send();
                        }

                    }
                }

                $user->markEmailAsVerified();

                Auth::login($user);
                $this->saveLoggedInCart();
                $this->saveShowroomCart();

                Notification::route('mail', $user->email)->notify(new WelcomeEmail(auth()->user()));

                // echo 'done';
            } catch (\Craftsys\Msg91\Exceptions\ValidationException $e) {
                // issue with the request e.g. token not provided
                // echo 'issue with the request e.g. token not provided';
            } catch (\Craftsys\Msg91\Exceptions\ResponseErrorException $e) {
                // error thrown by msg91 apis or by http client
                // echo 'error thrown by msg91 apis or by http client';
            } catch (\Exception $e) {
                // something else went wrong
                // plese report if this happens :)
                // echo 'something else went wrong';
            }


        }



        $user->markEmailAsVerified();



        Auth::login($user);


        $this->saveLoggedInCart();
        $this->saveShowroomCart();



        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
