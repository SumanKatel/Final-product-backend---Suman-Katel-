<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Model\admin\Address;
use App\Model\admin\Cart;
use App\Model\admin\Coupon;
use App\Model\admin\CouponUsage;
use App\Model\admin\Customer;
use App\Model\admin\District;
use App\Model\admin\Municipality;
use App\Model\admin\Product;
use App\Model\admin\State;
use App\Model\admin\Voucher;
use App\Model\admin\VoucherUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    
    public function addressAdd(Request $request){
        $customerId = $request->customerId;
        $customer   = Customer::find($customerId);
        if ($customer) {
        $validator=Validator::make($request->all(), [
            'phone'           => 'required|digits:10',
            'title'           => 'required',
            'address'         => 'required',
            'state_id'        => 'required',
            'municipality_id' => 'required',
            'district_id'     => 'required',
            ]);

            if ($validator->passes()) {
                $municipality = Municipality::where('id',$request->municipality_id)->first();
                if ($municipality->is_delivery_available==1) {
                    $address = new Address;
                    $address->title             = $request->title;
                    $address->user_id           = $customer->id;
                    $address->address           = $request->address;
                    $address->state_id          = $request->state_id;
                    $address->municipality_id   = $request->municipality_id;
                    $address->district_id       = $request->district_id;
                    $address->phone             = $request->phone;
                    $address->save();
    
                    $data = array(
                        'status'        => true,
                        'message'       => 'Address added Successfully!' ,
                        );
                    return response()->json($data,200);
                }else{
                        return response()->json([
                            'message'   =>'Delivery can not available on your address !!',
                            'status'    =>false,
                        ],401);
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

    public function addressList(Request $request){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            $array = array();
            $list = Address::where('user_id', $customer->id)->get();
            foreach ($list as $key => $address) {

                $state = State::where('id',$address->state_id)->first();
                $district = District::where('id',$address->district_id)->first();
                $municipality = Municipality::where('id',$address->municipality_id)->first();

                $array[]= array(
                      'address_id'           => $address->id ,
                      'address_title'        => $address->title ,
                      'address'              => $address->address ,
                      'phone'                => $address->phone ,
                      'state_name'           => $state->state_name_en ,
                      'district_name'        => $district->district_name_en ,
                      'municipality_name'    => $municipality->location_name_en ,

                       );
                    
            }
            $result = array(
                    'status'        => true,
                    'message'       => 'Data Fetched!!',
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

   public function addressUpdate(Request $request,$id){
        $customerId = $request->customerId;
        $customer   = Customer::find($customerId);
        if ($customer) {
        $validator=Validator::make($request->all(), [
            'phone'           => 'required|digits:10',
            'title'           => 'required',
            'address'         => 'required',
            'state_id'        => 'required',
            'municipality_id' => 'required',
            'district_id'     => 'required',
            ]);

            if ($validator->passes()) {
                $municipality = Municipality::where('id',$request->municipality_id)->first();
                if ($municipality->is_delivery_available==1) {
                    $address = Address::find($id);
                    $address->title             = $request->title;
                    $address->user_id           = $customer->id;
                    $address->address           = $request->address;
                    $address->state_id          = $request->state_id;
                    $address->municipality_id   = $request->municipality_id;
                    $address->district_id       = $request->district_id;
                    $address->phone             = $request->phone;
                    $address->save();
    
                    $data = array(
                        'status'        => true,
                        'message'       => 'Address update Successfully!' ,
                        );
                    return response()->json($data,200);
                }else{
                        return response()->json([
                            'message'   =>'Delivery can not available on your address !!',
                            'status'    =>false,
                        ],401);
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

    public function updateAddressInCart(Request $request){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            if ($request->address_id == null) {
                return response()->json([
                            'message'   =>'Please add shipping address first.',
                            'status'    =>false,
                        ],401);
            }else{

                $carts     = Cart::where('user_id', $customer->id)->get();
                foreach ($carts as $key => $cartItem) {
                    $cartItem->address_id = $request->address_id;
                    $cartItem->save();
                }
            $result = array(
                    'status'        => true,
                    'message'       => 'Address store to cart.',
                );
            }
            return response()->json($result,200);
        }else{
            return response()->json([
                    'status'     => false,
                    'message'    => 'Customer Not Found!!',
            ], 401);
        }
    }


    public function applyCouponCode(Request $request){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            $validator=Validator::make($request->all(), [
            'code'           => 'required',
            ]);

            if ($validator->passes()) {

                $coupon = Coupon::where('code', $request->code)->first();
                $now = date('Y-m-d');
                $activeCou = Coupon::where('code', $request->code)->where('start_date','<=',$now)->where('end_date','>=',$now)->first();
                if ($coupon != null) {
                    if ($activeCou!= null) {
                        if (CouponUsage::where('user_id', $customer->id)->where('coupon_id', $coupon->id)->first() == null) {
                            $totalCoupon = CouponUsage::where('coupon_id', $coupon->id)->count();
                            if(($coupon->total_coupon_number)>$totalCoupon){
                            $coupon_details = json_decode($coupon->details);
                            $carts = Cart::where('user_id', $customer->id)
                                            ->get();
                                $subtotal = 0;
                                $tax = 0;
                                $shipping = 0;
                                $coupon_discount = 0;

                                foreach ($carts as $key => $cart) {
                                    $totalProductCost = $cart->quantity*$cart->price;
                                    $subtotal += $totalProductCost;
                                }

                                if ($coupon->discount_type == 'percentage') {
                                    $coupon_discount = ($subtotal * $coupon->discount) / 100;
                                } elseif ($coupon->discount_type == 'flat') {
                                    $coupon_discount = $coupon->discount;
                                }
                                $finalTotal = $subtotal-$coupon_discount;

                            $response_message = array(
                                 'status'        => true,
                                 'message'       => 'Coupon has been applied!' ,
                                 'coupon_discount'  =>  $coupon_discount,
                                 'finalTotal'       =>  $finalTotal,
                                );
                            return response()->json($response_message,200);
                            } else {

                                $response_message = array(
                                 'status'        => false,
                                 'message'       => 'Coupon limit exceeded !' ,
                                );
                            return response()->json($response_message,401);
                        }
                        
                        } else {
                            $response_message = array(
                                 'status'        => false,
                                 'message'       => 'You already used this coupon!' ,
                                );
                            return response()->json($response_message,401);
                        }
                    } else {
                        $response_message = array(
                                 'status'        => false,
                                 'message'       => 'Coupon expired!' ,
                                );
                            return response()->json($response_message,401);
                    }
                    } else {
                        $response_message = array(
                                     'status'        => false,
                                     'message'       => 'Invalid coupon!' ,
                                    );
                                return response()->json($response_message,401);
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


    public function applyVoucher(Request $request){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            $validator=Validator::make($request->all(), [
            'voucher_id'           => 'required',
            ]);

            if ($validator->passes()) {
                        $voucher = Voucher::where('id', $request->voucher_id)->first();
                        $now = date('Y-m-d');
                        $activeCou = Voucher::where('id', $request->voucher_id)->where('start_date','<=',$now)->where('end_date','>=',$now)->first();
                        if ($voucher != null) {
                            if ($activeCou!= null) {
                                if (VoucherUsage::where('user_id', $customer->id)->where('is_applied',1)->where('voucher_id', $voucher->id)->first() == null) {
                                    $carts = Cart::where('user_id', $customer->id)->get();
                                    $subtotal = 0;
                                    $tax = 0;
                                    $shipping = 0;
                                    $voucher_discount = 0;

                                    foreach ($carts as $key => $cart) {
                                        $totalProductCost = $cart->quantity*$cart->price;
                                        $subtotal += $totalProductCost;
                                    }

                                    $voucher_discount = $voucher->discount;

                                    $finalTotal = $subtotal-$voucher_discount;

                                $response_message = array(
                                     'status'           => true,
                                     'message'          => 'Voucher has been applied!' ,
                                     'voucher_discount' =>  $voucher_discount,
                                     'finalTotal'       =>  $finalTotal,
                                    );
                                return response()->json($response_message,200);
                        } else {
                            $response_message = array(
                                 'status'        => false,
                                 'message'       => 'You already used this voucher!' ,
                                );
                            return response()->json($response_message,401);
                        }
                    } else {
                        $response_message = array(
                                 'status'        => false,
                                 'message'       => 'Voucher expired!' ,
                                );
                            return response()->json($response_message,401);
                    }
                    } else {
                        $response_message = array(
                                     'status'        => false,
                                     'message'       => 'Invalid voucher!' ,
                                    );
                                return response()->json($response_message,401);
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
