<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Model\Front\CampaignUsedByCustomer;
use App\Model\Front\OrderGift;
use App\Model\Front\ProductCart;
use App\Model\Front\RequestProduct;
use App\Model\Front\TrackProductOrder;
use App\Model\Workstation\Campaign;
use App\Model\Workstation\Customer;
use App\Model\Workstation\PaymentMethod;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductOrder;
use App\Model\Workstation\ProductOrderList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mail;

class OrderController extends Controller
{


    public function requestProduct(Request $request){

        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
        $validator=Validator::make($request->all(), [
                'quantity'                   => 'required',
            ]);

            if ($validator->passes()) {
                $requestCheck = RequestProduct::where('product_id',$request->product_id)->where('customer_id',$customer->id)->first();
                if ($requestCheck) {
                    $data = array(
                        'status'        => false,
                        'message'       => 'Your already request this product !' ,
                        );
                    return response()->json($data,401);
                }else{
                $order = new RequestProduct;
                $order->product_id = $request->product_id;
                $order->quantity = $request->quantity;
                $order->customer_id = $customer->id;
                $order->status = 0;
                $order->save();
                    $data = array(
                        'status'        => true,
                        'message'       => 'Your request is processed !' ,
                        );
                    return response()->json($data,200);

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





   public function finalOrder(Request $request){

        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
        $validator=Validator::make($request->all(), [
            ]);

            if ($validator->passes()) {
            //here from
                // $cartList = array();

                
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
                $order->is_foreign_order = $request->is_foreign_order;
                $order->delivery_address_id = $request->delivery_address_id;
                $order->billing_address_id = $request->billing_address_id;
                $order->discount = $request->discount;
                $order->delivery_charge_amt = $request->delivery_charge_amt;
                $order->final_price = $request->final_price;
                //for coupon
                if (!empty($request->offer_code)){
                    $nowDate = date('Y-m-d H:i:s');
                        $offercheck = Campaign::where('offer_code',$request->offer_code)->where('date_to','>=',$nowDate)->where('is_coupon',1)->first();
                        if ($offercheck) {
                            $checkUsed = CampaignUsedByCustomer::where('coupon_code_id',$offercheck->id)
                                            ->where('is_used',1)
                                            ->where('user_id',$customer->id)
                                            ->first();
                                        if (!$checkUsed) {
                                           $couponId = $offercheck->id; 
                                           $order->coupon_id = $couponId;
                                           $order->coupon_used = 1;
                                        }
                            }
                            
                }
                $order->save();


                //for coupon use
                if ($order->coupon_id) {
                   $coupon = new CampaignUsedByCustomer;
                   $coupon->user_id = $customer->id;
                   $coupon->coupon_code_id = $offercheck->id;
                   $coupon->order_id = $order->id;
                   $coupon->used_datetime = $nowDate;
                   $coupon->is_used = 1;
                   $coupon->save();
                }
                
                //order_status==4 (esewa payment fail)

                if ($order) {
                    $sort_order=1;
                    foreach ($request->cartList as $key => $cart) {
                        $count = $sort_order++;
                        $finalCount = sprintf("%'03d", $count);
                            $product=Product::where('id',$cart['product_id'])->first();
                            $data = new ProductOrderList;
                            $data->order_code_id = $order->id;
                            $ran=random_int(100, 999);
                            $data->sub_order_code = 'KY-'.date('y').'-'.date('n').'-'.date('j').'-'.time().'-'.$finalCount;
                            $data->product_id = $product->id;
                            $data->user_id = $customer->id;
                            $data->is_gift = $cart['is_gift'];
                            $data->product_qty = $cart['product_qty'];
                            $itemamt=$cart['product_qty']*$product->cost;
                            $data->price = $cart['product_price'];
                            $data->order_datetime = date('Y-m-d H:i:s');
                            // $data->order_status = 0;
                            if($order->payment_id==1){
                             $data->order_status = 0;    
                                $cartProduct = ProductCart::where('product_id',$cart['product_id'])->where('user_id',$customer->id)->first();
                                $cartProduct->delete();
                            }else{
                             $data->order_status = 4;     
                            }
                            $data->save();
                            if($data){
                                $product->decrement('opening_stock', $data->product_qty);

                            //for tracking order

                            $data = new TrackProductOrder;
                            $data->order_id = $order->id;
                            $data->user_id  = $customer->id;
                            $data->product_ordered  = 1;
                            $data->product_processed  = 1;
                            $data->save();
                            }

                        if($data->is_gift==1){
                        $gift = new OrderGift;
                        $gift->order_id = $order->id;
                        $gift->sub_order_id  = $data->id;
                        $gift->deliver_date  = $request->deliver_date;
                        $gift->deliver_to  = $request->deliver_to;
                        $gift->is_wrapping  = $request->is_wrapping;
                        $gift->wrapping_paper_id  = $request->wrapping_paper_id;
                        $gift->message  = $request->message;
                        $gift->status  = 1;
                        $gift->save();
                        }


                        if($data->is_gift==1){
                        $giftCart = CartGift::where('id',$request->cart_gift_id)->first();
                        $gift = new OrderGift;
                        $gift->order_id = $order->id;
                        $gift->sub_order_id  = $data->id;
                        $gift->deliver_date  = $giftCart->deliver_date;
                        $gift->deliver_to  = $giftCart->deliver_to;
                        $gift->is_wrapping  = $giftCart->is_wrapping;
                        $gift->wrapping_paper_id  = $giftCart->wrapping_paper_id;
                        $gift->message  = $giftCart->message;
                        $gift->status  = 1;
                        $gift->save();
                        }
                
                            // $cart->delete();
                        if ($sort_order=1) {
                            $subOrder = ProductOrderList::where('id',$data->id)->first();
                                        if($subOrder){
                                        $subOrder->delivery_charge_amt = $order->delivery_charge_amt;
                                        $subOrder->save();
                                        }
                        }
                    }
                    
                                
                     if ($order->payment_id==1) {
                       sendOrderToMobile($customer->mobile,$order->order_code);

                               $paymentMethod = PaymentMethod::where('id',$order->payment_id)->first();
                               $subOrderList = ProductOrderList::where('order_code_id',$order->id)->get();
                               $arrayName = array(
                                'customer_name'          => $customer->name,
                                'customer_email'         => $customer->email,
                                'order_code'             => $order->order_code,
                                'price'                  => $order->price,
                                'final_price'            => $order->final_price,
                                'delivery_charge_amt'    => $order->delivery_charge_amt,
                                'order_date'             => Carbon::parse($order->created_at)->toDateString() ,
                                'order_time'             => date('H:i A', strtotime($order->created_at)) ,
                                'payment_name'           => $paymentMethod->payment_name,
                                'subOrderList'           => $subOrderList,
                                'subject'                => 'Order Detail',

                                );
                            Mail::send('email.customer-order', $arrayName, function ($m) use ($arrayName) {
                            $mail_from = env('MAIL_USERNAME', 'noreply@kitabyatra.com');
                            $m->from($mail_from, 'KitabYatra');
                            $m->to($arrayName['customer_email'])
                            ->subject($arrayName['subject']);
                            });
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
    
    
       public function esewaSuccess(Request $request,$orderId)
   {
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
        $validator=Validator::make($request->all(), [
            ]);

            if ($validator->passes()) {
            //here from
                // $cartList = array();
            $orderdetail = ProductOrder::where('id',$orderId)->where('user_id',$customer->id)->first();
            
            $totalamt = $orderdetail->final_price;

            // esewa verification
            // $url = ESEWA_VERIFY_URL;
/*         $url = "https://uat.esewa.com.np/epay/transrec";
*/         //$url = "https://esewa.com.np/epay/transrec";
         /*'scd'=> 'NP-ES-KITABYATRA'*/
            /*$data =[
                'amt'=> 450,
                'rid'=> $request->refId,
                'pid'=> $request->oid,
                'scd'=> 'epay_payment'
            ];*/
            
            
            $url = "https://esewa.com.np/epay/transrec";
            $data =[
                'amt'=> $totalamt,
                'rid'=> $request->refId,
                'pid'=> $request->oid,
                'scd'=> 'NP-ES-KITABYATRA'
            ];

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            curl_close($curl);

            $xml   = simplexml_load_string($response);
            $verifydata = json_decode(json_encode((array) $xml), true);
            $verifydata = array($xml->getName() => $verifydata);

            if (isset($verifydata['response']['response_code']) && trim($verifydata['response']['response_code']) == 'Success') {
                $orderdetail->status = 3;
                $orderdetail->order_status = 0;
                //  update payment data
                $orderdetail->payment_id = 3;
                $orderdetail->payment_status = 1;
                $orderdetail->payment_price = $totalamt;
                $orderdetail->payment_time = date('Y-m-d H:i:s');
                $orderdetail->save();

                $orderdetail->reference_number = $request->refId;
                $orderdetail->auth_trans_ref_no = $request->oid;
                $orderdetail->esewa_amount = $request->amt;
                // $checkesewa->verified_datetime = date('Y-m-d H:i:s');
                // $checkesewa->status = 1;
                $orderdetail->save();
                
                $subOrder = ProductOrderList::where('order_code_id',$orderdetail->id)->get();
                foreach($subOrder as $sa){
                $a = ProductOrderList::where('id',$sa->id)->first();
                $a->order_status = 0;
                $a->save();
                }
            
            
                if ($orderdetail->payment_id==3) {
                       sendOrderToMobile($customer->mobile,$orderdetail->order_code);
                }
                
              


                        $data = array(
                        'status'        => true,
                        'message'       => 'Your order is confirmed. Payment with esewa.' ,
                        'order_code'    => $orderdetail->order_code,
                        'order_id'    => $orderdetail->id,
                        );
                    return response()->json($data,200);
                // show success page
            } else{
                $data = array(
                        'status'        => false,
                        'message'       => 'Transaction Failed' ,
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
        }else{
            return response()->json([
                'status'     => false,
                'message'    => 'User Not Found',
                'data'       =>null,
            ], 404);
        }

    }



public function esewaFail(Request $request,$orderId){

    $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {


        $data = array(
                        'status'        => true,
                        'message'       => 'Payment fail from esewa,Please Try again.' ,
                        );
                    return response()->json($data,200);
        }else{
            return response()->json([
                'status'     => false,
                'message'    => 'User Not Found',
                'data'       =>null,
            ], 404);
        }

    }
}
