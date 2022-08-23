<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Model\Workstation\Customer;
use App\Model\Workstation\Product;
use App\Model\admin\ProductRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MobileApiProductRatingController extends Controller
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
}
