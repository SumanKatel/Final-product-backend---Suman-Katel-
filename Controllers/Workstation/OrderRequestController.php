<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Front\ProductCart;
use App\Model\Front\RequestProduct;
use App\Model\Workstation\Customer;
use App\Model\Workstation\Product;
use App\Model\Workstation\WorkstationUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderRequestController extends Controller
{

    public function list(Request $request){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
                $array = array();
                $requests = RequestProduct::where('status',0)->get();
                foreach ($requests as $key => $value) {
                    $product = Product::where('id',$value->product_id)->first();
                    $customer = Customer::where('id',$value->customer_id)->first();
                     $array[]= array(
                      'id'        => $value->id ,
                      'product_name' => $product->product_name,
                      'product_sku' => $product->sku,
                      'product_quantity' => $value->quantity,
                      'customer_name' => $customer->name,
                      'customer_mobile' => $customer->mobile,
                      'customer_email' => $customer->email,
                      'requested_date'  => Carbon::parse($value->created_at)->toDateString() ,
                       );
                }
                $data = array(
                        'status'        => true,
                        'message'       => 'Request Product List!' ,
                        'data'          => $array,
                        );
                    return response()->json($data,200);
        }else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 401);
        }
    }

    public function historyList(Request $request){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
                $array = array();
                $requests = RequestProduct::where('status',1)->get();
                foreach ($requests as $key => $value) {
                    $product = Product::where('id',$value->product_id)->first();
                    $customer = Customer::where('id',$value->customer_id)->first();
                     $array[]= array(
                      'id'        => $value->id ,
                      'product_name' => $product->product_name,
                      'product_sku' => $product->sku,
                      'product_quantity' => $value->quantity,
                      'customer_name' => $customer->name,
                      'customer_mobile' => $customer->mobile,
                      'customer_email' => $customer->email,
                      'requested_date'  => Carbon::parse($value->created_at)->toDateString() ,
                       );
                }
                $data = array(
                        'status'        => true,
                        'message'       => 'History Request Product List!' ,
                        'data'          => $array,
                        );
                    return response()->json($data,200);
        }else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 401);
        }
    }

  public function requestToCart(Request $request,$requestId){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
                $array = array();
                $data = RequestProduct::where('id',$requestId)->first();
                if ($data) {
                  $c = ProductCart::where('user_id', $data->customer_id)->where('product_id',$data->product_id)->first();
                    if (!$c) {
                    $cart = new ProductCart();
                    $cart->product_id = $data->product_id;
                    $cart->user_id = $data->customer_id;
                    $cart->product_qty = $data->quantity;
                    $cart->save();
                    if ($cart) {
                     $data->status = 1;
                     $data->save();
                    }
                  }
                }
                $data = array(
                        'status'        => true,
                        'message'       => 'Request added to cart!' ,
                        'data'          => $array,
                        );
                    return response()->json($data,200);
        }else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 401);
        }
    }
}
