<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Model\admin\Address;
use App\Model\admin\Cart;
use App\Model\admin\Coupon;
use App\Model\admin\CouponUsage;
use App\Model\admin\Customer;
use App\Model\admin\Order;
use App\Model\admin\OrderDetail;
use App\Model\admin\Product;
use App\Model\admin\Voucher;
use App\Model\admin\VoucherUsage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mail;

class OrderController extends Controller
{

    public function finalOrder(Request $request)
    {
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            $validator=Validator::make($request->all(), [
            'payment_option'           => 'required',
            ]);
            if ($validator->passes()) {
             $carts     = Cart::where('user_id', $customer->id)->get();

                ///main Order
                $order = new Order;
                $order->order_code = 'CAR-'.date('y').'-'.date('n').'-'.date('j').'-'.time();
                $order->user_id = $customer->id;
                $order->payment_type = 'cod';
                $order->delivery_viewed = '0';
                $order->payment_status_viewed = '0';
                $order->code = 'V-'.date('Ymd-His') . rand(10, 99);
                $order->date = strtotime('now');
                $order->save();
                
                //Orders details storing
                $subtotal = 0;
                $tax = 0;
                $shipping = 0;
                $coupon_discount = 0;
                $voucher_discount = 0;
                $finalTotal = 0;


                $sort_order=1;
                foreach ($carts as $cartItem) {
                    $product = Product::find($cartItem['product_id']);
                    $subtotal += $cartItem['price'] * $cartItem['quantity'];
                    $tax += $cartItem['tax'] * $cartItem['quantity'];
                    $coupon_discount += $cartItem['discount'];
                    $product_variation = $cartItem['variation'];

                    $count = $sort_order++;
                    $finalCount = sprintf("%'03d", $count);

                    $order_detail = new OrderDetail;
                    $order_detail->sub_order_code = 'VES-'.date('y').'-'.date('n').'-'.date('j').'-'.time().'-'.$finalCount;
                    $order_detail->order_id = $order->id;
                    $order_detail->customer_id = $customer->id;
                    $order_detail->seller_id = $product->vendor_id;
                    $order_detail->product_id = $product->id;
                    $order_detail->variation = $product_variation;
                    $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                    $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                    $order_detail->shipping_type = $cartItem['shipping_type'];
                    $order_detail->product_referral_code = $cartItem['product_referral_code'];
                    $order_detail->shipping_cost = $cartItem['shipping_cost'];

                    $shipping += $order_detail->shipping_cost;

                    $order_detail->quantity = $cartItem['quantity'];
                    $order_detail->save();

                    $product->num_of_sale += $cartItem['quantity'];
                    $product->save();

                    $order->seller_id = $product->vendor_id ?? null;
                }

            if ($finalTotal==0) {
              $order->grand_total += $subtotal;
            }else{
              $order->grand_total = $finalTotal;
            }
              $order->save();
            if ($order) {
                        $result = array(
                                 'status'           => true,
                                 'message'          => 'Your order has been placed successfully.' ,
                                 'order_code'       =>  $order->code,
                                );
                            return response()->json($result,200);
            }

           }else{

            $result = array(
            'status'        => false,
            'message'       => 'Input Field Required',
            'data'        => $validator->errors()
            );
            return response()->json($result,422);
        }
        }else{
            return response()->json([
                    'status'     => false,
                    'message'    => 'Customer Not Found!!',
            ], 401);
        }
    }


}