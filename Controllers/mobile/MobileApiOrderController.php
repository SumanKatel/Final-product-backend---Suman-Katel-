<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Model\Workstation\Customer;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductOrder;
use App\Model\Workstation\ProductOrderList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MobileApiOrderController extends Controller
{
    public function finalOrder(Request $request){

        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
        $validator=Validator::make($request->all(), [
            ]);

            if ($validator->passes()) {
                if (!empty($request->cartList)){
                     
                    $order = new ProductOrder;
                    $order->order_code = 'KY-'.date('y').'-'.date('n').'-'.date('j').'-'.time();
                    $order->user_id = $customer->id;
                    $order->status = 0; 
                    if($request->payment_id==1){
                     $order->order_status = 0;    
                    }else{
                     $order->order_status = 4;     
                    }
                    $order->order_datetime = date('Y-m-d H:i:s');
                    $order->payment_id = $request->payment_id;
                    $order->payment_status = 0;
                    $order->price = $request->price;
                    $order->delivery_address_id = $request->delivery_address_id;
                    $order->billing_address_id = $request->billing_address_id;
                    $order->discount = $request->discount;
                    $order->delivery_charge_amt = $request->delivery_charge_amt;
                    $order->final_price = $request->final_price;

                    $order->save();
            
                    //order_status==4 (esewa payment fail)

                    if ($order) {
                        $sort_order=1;
                        if($request->is_buy == 2){
                            $count = $sort_order++;
                            $finalCount = sprintf("%'03d", $count);
                            $product=Product::where('id',$request->cartList['product_id'])->first();
                            $data = new ProductOrderList;
                            $data->order_code_id = $order->id;
                            $ran=random_int(100, 999);
                            $data->sub_order_code = 'KY-'.date('y').'-'.date('n').'-'.date('j').'-'.time().'-'.$finalCount;
                            $data->product_id = $product->id;
                            $data->user_id = $customer->id;
                            $data->product_qty = $request->product_qty;
                            $itemamt=$request->product_qty*$product->listing_price;
                            $data->price = $itemamt;
                            $data->order_datetime = date('Y-m-d H:i:s');
                            // $data->order_status = 0;
                            if($order->payment_id==1){
                             $data->order_status = 0;    
                            }else{
                             $data->order_status = 4;     
                            }
                            $data->save();
                            if($data){
                                $product->decrement('opening_stock', $data->product_qty);
                                
                                  $subOrder = ProductOrderList::where('id',$data->id)->first();
                                    if($subOrder){
                                        $data->delivery_charge_amt = $order->delivery_charge_amt;
                                        $data->save();
                                    }
                            }
                        }else{
                            foreach ($request->cartList as $key => $cart) {
                            // dd($cart);
                                $count = $sort_order++;
                                $finalCount = sprintf("%'03d", $count);
                                    $product=Product::where('id',$cart['product_id'])->first();
                                    $data = new ProductOrderList;
                                    $data->order_code_id = $order->id;
                                    $ran=random_int(100, 999);
                                    $data->sub_order_code = 'KY-'.date('y').'-'.date('n').'-'.date('j').'-'.time().'-'.$finalCount;
                                    $data->product_id = $product->id;
                                    $data->user_id = $customer->id;
                                    if($request->is_buy == 1){
                                        $data->product_qty = $request->product_qty;
                                        $itemamt=$request->product_qty*$product->listing_price;
                                    }else{
                                        $data->product_qty = $cart['product_qty'];
                                        $itemamt=$cart['product_qty']*$product->listing_price;
                                    }
                                    $data->price = $itemamt;
                                    $data->order_datetime = date('Y-m-d H:i:s');
                                    // $data->order_status = 0;
                                    if($order->payment_id==1){
                                     $data->order_status = 0;    
                                    }else{
                                     $data->order_status = 4;     
                                    }
                                    $data->save();
                                    if($data){
                                        $product->decrement('opening_stock', $data->product_qty);
                                        // return $cart['id'];
                                        if($request->is_buy !== 1){
                                            ProductOrderList::delete_cart_data($cart['id']);
                                        }
                                          $subOrder = ProductOrderList::where('id',$data->id)->first();
                                            if($subOrder){
                                                $data->delivery_charge_amt = $order->delivery_charge_amt;
                                                $data->save();
                                            }
                                    }
                        
                                    // $cart->delete();
                            }
                        }
                        
                        
                      
                                    
                         if ($order->payment_id==1) {
                           sendOrderToMobile($customer->mobile,$order->order_code);
                        }
                     
                        // sendOrderToMobile($customer->mobile,$order->order_code);
                    
                        $data = array(
                            'status'        => true,
                            'message'       => 'Your order is confirmed. Thank you for shopping with kitabyatra.' ,
                            'order_code'    => $order->order_code,
                            'order_id'    => $order->id,
                            );
                        return response()->json($data,200);
                    }
                }else{
                    $data = array(
                            'status'        => false,
                            'message'       => 'Your Cart is empty' ,
                            );
                        return response()->json($data,401);
                }
            //here to
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
}
