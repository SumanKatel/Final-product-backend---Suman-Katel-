<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Model\admin\Product;
use App\Model\admin\WishList;
use App\Model\admin\Rating;
use Illuminate\Http\Request;
use App\Model\admin\Customer;

use Illuminate\Support\Facades\Validator;

class WishController extends Controller
{
    public function wishAdd(Request $request){

        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
        $validator=Validator::make($request->all(), [
                'product_id'                => 'required',
            ]);
            if ($validator->passes()) {
                $wish = WishList::where('user_id',$customer->id)->where('product_id',$request->product_id)->first();
                if(!$wish)
                    {
                        $product = Product::where('id',$request->product_id)->first();
                        $crud = new WishList;
                        $crud->product_id = $product->id;
                        $crud->user_id = $customer->id;
                        $crud->save();

                        $data = array(
                            'status'        => true,
                            'message'       => 'Wish added Successfully!' ,
                            );
                        return response()->json($data,200);
                    }else{
                        $data = array(
                            'status'        => false,
                            'message'       => 'Already Added!' ,
                            );
                        return response()->json($data,401);
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

     public function wishList(Request $request){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            $array = array();
            $list = WishList::where('user_id', $customer->id)->get();
            foreach ($list as $key => $wish) {
                $product = Product::where('id',$wish->product_id)->first();

                $array[]= array(
                      'wish_id'           => $wish->id ,
                      'product_name'      => $product->title ,
                      'product_slug'      => $product->slug ,
                      'product_thumbnail' => asset($product->image) ,
                       );
                    
            }
            $result = array(
                    'status'        => true,
                    'message'       => 'Wish Data Fetched',
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

    public function wishDelete(Request $request, $id){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            $wish=WishList::where('id', $id)->first();
            if($wish){
                $wish->delete();
                $result = array(
                            'status'        =>true,
                            'message'       => 'WishList Deleted Successfully!',
                        );
                return response()->json($result,200);
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Wish Data Not Found',
                ], 401);
            }
        }else{
            return response()->json([
            'status'     => false,
            'message'    => 'Customer Not Found!!',
            ], 401);
        }
    }
    
    
     public function ratingAdd(Request $request){

        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
        $validator=Validator::make($request->all(), [
                'product_id'                => 'required',
                'rating_value'                => 'required',
                'review'                => 'required',
            ]);
            if ($validator->passes()) {
                $wish = Rating::where('customer_id',$customer->id)->where('product_id',$request->product_id)->first();
                if(!$wish)
                    {
                        $crud = new Rating;
                        $crud->product_id = $product->id;
                        $crud->customer_id = $customer->id;
                        $crud->rating_value = $request->rating_value;
                        $crud->review = $request->review;
                        $crud->save();

                        $data = array(
                            'status'        => true,
                            'message'       => 'Rating added Successfully!' ,
                            );
                        return response()->json($data,200);
                    }else{
                        $data = array(
                            'status'        => false,
                            'message'       => 'Already Added!' ,
                            );
                        return response()->json($data,401);
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
}
