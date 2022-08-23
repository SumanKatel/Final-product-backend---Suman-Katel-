<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Model\admin\Cart;
use App\Model\admin\Customer;
use App\Model\admin\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    
    public function cartAdd(Request $request){
        $customerId = $request->customerId;
        $customer   = Customer::find($customerId);
        if ($customer) {
        $validator=Validator::make($request->all(), [
                'product_id'                => 'required|max:255',
            ]);

            if ($validator->passes()) {
                
                $cart = Cart::where('user_id', $customer->id)->where('product_id',$request->product_id)->first();
                if($cart)
                {
                     $latestQ = $cart->quantity+1;
                     $cart->quantity = $latestQ;
                     $cart->save();
                
                    $data = array(
                        'status'        => true,
                        'message'       => 'Cart Update' ,
                        );
                    return response()->json($data,200);
                }else{
                    ///discounted Price
                    $product = Product::where('id',$request->product_id)->first();
                    $price = $product->unit_price;
                    $cart = new Cart;
                    $cart->product_id = $product->id;
                    $cart->price = $price;
                    $cart->user_id = $customer->id;
                    $cart->quantity   =   1;
                    $cart->save();
    
                if ($cart) {
                        $data = array(
                            'status'        => true,
                            'message'       => 'Cart added Successfully!' ,
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
                    'message'    => 'Customer Not Found!!',
                ], 401);
        }
    }

    public function cartList(Request $request){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            $array = array();
            $list = Cart::where('user_id', $customer->id)->get();
            foreach ($list as $key => $cart) {
                $product = Product::where('id',$cart->product_id)->first();

                $array[]= array(
                      'cart_id'           => $cart->id ,
                      'product_name'      => $product->title ,
                      'product_slug'      => $product->slug ,
                      'quantity'          => $cart->quantity ,
                      'slug'              => $product->slug ,
                      'sub_cart_price'    => $cart->price*$cart->quantity,
                      'product_thumbnail' => asset($product->image) ,
                       );
                    
            }
            $result = array(
                    'status'        => true,
                    'message'       => 'User Cart Data Fetched',
                    'data'          =>  $array,
                );

            return response()->json($result,200);
        }
        else{
            return response()->json([
                    'status'     => false,
                    'message'    => 'Customer Not Found!!',
            ], 401);
        }
    }

    public function cartDelete(Request $request, $id){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            $cart=Cart::where('id', $id)->first();
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
                ], 401);
            }
        }else{
            return response()->json([
            'status'     => false,
            'message'    => 'Customer Not Found!!',
            ], 401);
        }
    }
}
