<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Model\Front\Coupon;
use App\Model\Front\ProductCart;
use App\Model\Workstation\Customer;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductOrder;
use App\Model\Workstation\ProductOrderList;
use App\Model\Workstation\WorkstationUser;
use App\Model\admin\CreditStore;
use App\Model\admin\CustomerSupport;
use App\Model\admin\CustomerSupportFile;
use App\Model\admin\Medium;
use App\Model\admin\ProductRating;
use App\Model\admin\SupportType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductRatingController extends Controller
{

        public function addRating(Request $request,$id){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
            if ($customer) {
            $validator=Validator::make($request->all(), [
                'rating'                   => 'required',
                'reviews'                  => 'required',
                'order_id'                 => 'required',
                ]);
                if ($validator->passes()) {
                    $product = Product::where('id',$id)->first();
                    if($product){
                    $rate['user_id'] = $customer->id;
                    $rate['rating'] = $request->rating;
                    $rate['reviews'] = $request->reviews;
                    $rate['product_id'] = $product->id;
                    $rate['status'] = 0;
                    $rate['order_id'] = $request->order_id;
                    $rateResult = ProductRating::create($rate);

                    if ($rateResult) {
                    $data = array(
                        'status'        => true,
                        'message'       => 'Product Rating added Successfully!' ,
                        );
                    return response()->json($data,200);
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
                    'message'    => 'User Not Found',
                    'data'       =>null,
                ], 404);
            }

    }

    public function getTotalCredit(Request $request)
    {
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
            if ($customer) {
            $nowDate = date('Y-m-d H:i:s');
            $couponsList = Coupon::where('published_date','=<',$nowDate)->where('coupon_expire_date','>=',$nowDate)->get();
            $array=array();
            foreach ($couponsList as $key => $coupons) {
                $expireDate = \Carbon\Carbon::parse($coupons->coupon_expire_date)->format('d-m-Y');

                if ($coupons->coupon_type_id==1) {
                    $coupon_value = 'Rs. '.$coupons->coupon_value;
                }else{
                    $coupon_value = $coupons->coupon_value. '%';
                }

                 $array[] = array(
                    'id' => $coupons->id,
                    'coupon_code' => $coupons->coupon_code,
                    'coupon_title' => $coupons->coupon_title,
                    'coupon_value' => $coupon_value,
                    'coupon_expire_date' => $expireDate,
                  );
                 }

             $result = array(
                                'status'        => true,
                                'message'       => 'Data Fetched',
                                'data'          => $array,
                            );
            return response()->json($result,200);
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
