<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Model\Front\ProductCart;
use App\Model\Workstation\Customer;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MobileApiCartController extends Controller
{
    public function cart_add(Request $request){
    	$customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
        $validator=Validator::make($request->all(), [
                'product_id' 				=> 'required|max:255',
                'product_qty' 				=> 'required',
            ]);

            if ($validator->passes()) {
                $cart = ProductCart::where('user_id', $customerId)->where('product_id', $request->product_id)->first();
                if(!empty($cart)){
                   $cart->product_qty +=  $request->product_qty;
                   $cart->save();
                   $msg = 'Updated';
                }else{
                    $cart = new ProductCart();
                    $cart->product_id = $request->product_id;
                    $cart->user_id = $customerId;
                    $cart->product_qty = $request->product_qty;
                    $cart->save();
                    $msg = 'added';
                }

            if ($cart) {
            	$cart = array(
            			'cart_info' => $cart
            			);
                    $data = array(
                        'status'      	=> true,
                        'message'     	=> 'Cart '.$msg.' Successfully!' ,
                        'data'      	=> $cart,
                        );
                    return response()->json($data,200);
                }
            else{
                    return response()->json([
                        'message'   =>'Something went wrong !',
                        'status'    =>false,
                        'data'      =>null,
                    ]);
              }
        }else{

                $result = array(
                'status'        => false,
                'message'       => 'Input Field Required',
                'data'        => $validator->errors()
                );
                return response()->json($result,422);
            }
        }else
        {
            return response()->json([
                'status'     => false,
                'message'    => 'User Not Found',
                'data'       =>null,
            ], 404);
        }
    }

    public function cart_list(Request $request){
    	$customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            $array = array();
            // $list = ProductCart::select('product_id', DB::raw('sum(product_qty) as product_qty'))->where('user_id', $customer->id)->groupBy('product_id')->get();
            $list = ProductCart::where('user_id', $customer->id)->get();
            foreach ($list as $key => $cart) {
                $product = Product::where('id',$cart->product_id)->first();
                $product_image = ProductImage::where('product_id', $cart->product_id)->first();
                $array[]= array(
                      'id' => $cart->id ,
                      'product_qty' => $cart->product_qty ,
                      'product_id'  => $product->id,
                      'product_slug'  => $product->slug,
                      'product_name'  => $product->product_name,
                      'mrp_paper_book'  => $product->mrp_paper_book,
                      'listing_price'  => $product->listing_price,
                      'product_image'  => $product_image->image,
                       );
                    
            }
            $array = array(
            		'cart_list' => $array );
            $result = array(
                    'status'        => true,
                    'message'       => 'User Cart Data Fetched',
                    'data'      	=>  $array,
                );

            return response()->json($result,200);
        }
        else{
            return response()->json([
                'status'     => false,
                'message'    => 'User Not Found',
                'data'       => null,
            ], 401);
        }
    }

    public function cart_delete(Request $request, $id){
    	$customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            $cart=ProductCart::where('id', $id)->first();
            if($cart){
            	$cart->delete();
                $result = array(
                            'status'        =>true,
                            'message'       => 'User Cart Deleted Successfully!',
                        );
                return response()->json($result,200);
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Cart Not Found',
                    'data'       =>null,
                ], 401);
            }
        }else{
            return response()->json([
                'status'     => false,
                'message'    => 'User Not Found',
                'data'       =>null,
            ], 401);
        }
    }
}
