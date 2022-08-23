<?php

namespace App\Http\Controllers\Workstation;
use App\Http\Controllers\Controller;
use App\Model\Workstation\Author;
use App\Model\Workstation\BillingAddress;
use App\Model\Workstation\Category;
use App\Model\Workstation\CategoryAttributeRelation;
use App\Model\Workstation\Customer;
use App\Model\Workstation\BillType;
use App\Model\Workstation\Language;
use App\Model\Workstation\PaymentMethod;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductAttribute;
use App\Model\Workstation\ProductImage;
use App\Model\Workstation\ProductOrder;
use App\Model\Workstation\ProductOrderBilling;
use App\Model\Workstation\ProductOrderList;
use App\Model\Workstation\Publisher;
use App\Model\Workstation\RelationProductAttribute;
use App\Model\Workstation\RelationProductCategory;
use App\Model\Workstation\WorkstationUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;


class BillingController extends Controller
{

    public function billingList(Request $request)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array = array();
                $list = ProductOrderBilling::where('billing_status',0)->get();
                foreach ($list as $key => $value) {
                $productOrder = ProductOrder::where('id',$value->order_code_id)->first();
                $customer = Customer::where('id',$productOrder->user_id)->first();
                $billingAddress = BillingAddress::where('id',$productOrder->billing_address_id)->first();
                    $array[]= array(
                      'id' => $value->id ,
                      'order_code' => $productOrder->order_code ,
                      'order_code_id' => $productOrder->id ,
                      'price' => $productOrder->final_price,
                      'customer_name' => $customer->name ,
                      'customer_code' => $customer->user_code ,
                      'customer_number' => $customer->mobile ,
                      'billing_status' => $value->billing_status ,
                      'address' => $billingAddress->address ?? null,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Orders Data Fetched',
                                    'data' =>$array,
                                );

                return response()->json($result,200);
            
            }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    } 

    public function billingProcessedList(Request $request)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array = array();
                $list = ProductOrderBilling::where('billing_status',1)->get();
                foreach ($list as $key => $value) {
                $productOrder = ProductOrder::where('id',$value->order_code_id)->first();
                $customer = Customer::where('id',$productOrder->user_id)->first();
                $billingAddress = BillingAddress::where('id',$productOrder->billing_address_id)->first();
                    $array[]= array(
                      'id' => $value->id ,
                      'order_code' => $productOrder->order_code ,
                      'order_code_id' => $productOrder->id ,
                      'price' => $productOrder->final_price,
                      'customer_name' => $customer->name ,
                      'customer_code' => $customer->user_code ,
                      'customer_number' => $customer->mobile ,
                      'billing_status' => $value->billing_status ,
                      'address' => $billingAddress->address ?? null,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Billing Processed data Fetched !',
                                    'data' =>$array,
                                );

                return response()->json($result,200);
            
            }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    } 


    public function billingCancelList(Request $request)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array = array();
                $list = ProductOrderBilling::where('billing_status',3)->get();
                foreach ($list as $key => $value) {
                $productOrder = ProductOrder::where('id',$value->order_code_id)->first();
                $customer = Customer::where('id',$productOrder->user_id)->first();
                $billingAddress = BillingAddress::where('id',$productOrder->billing_address_id)->first();
                    $array[]= array(
                      'id' => $value->id ,
                      'order_code' => $productOrder->order_code ,
                      'order_code_id' => $productOrder->id ,
                      'price' => $productOrder->final_price,
                      'customer_name' => $customer->name ,
                      'customer_code' => $customer->user_code ,
                      'customer_number' => $customer->mobile ,
                      'billing_status' => $value->billing_status ,
                      'address' => $billingAddress->address ?? null,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Billing Cancelled data Fetched !',
                                    'data' =>$array,
                                );

                return response()->json($result,200);
            
            }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    } 


        public function billingHoldList(Request $request)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array = array();
                $list = ProductOrderBilling::where('billing_status',2)->get();
                foreach ($list as $key => $value) {
                $productOrder = ProductOrder::where('id',$value->order_code_id)->first();
                $customer = Customer::where('id',$productOrder->user_id)->first();
                $billingAddress = BillingAddress::where('id',$productOrder->billing_address_id)->first();
                    $array[]= array(
                      'id' => $value->id ,
                      'order_code' => $productOrder->order_code ,
                      'order_code_id' => $productOrder->id ,
                      'price' => $productOrder->final_price,
                      'customer_name' => $customer->name ,
                      'customer_code' => $customer->user_code ,
                      'customer_number' => $customer->mobile ,
                      'billing_status' => $value->billing_status ,
                      'address' => $billingAddress->address ?? null,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Billing Holdon data Fetched !',
                                    'data' =>$array,
                                );

                return response()->json($result,200);
            
            }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    } 


    public function newBillingProcess(Request $request)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array = array();
                $value = ProductOrderBilling::where('billing_status',0)->orderBy('billing_request_date','asc')->first();
                
                if ($value){
                $productOrder = ProductOrder::where('id',$value->order_code_id)->first();
                
                $paymentMethod = PaymentMethod::where('id',$productOrder->payment_id)->first();
                $customer = Customer::where('id',$productOrder->user_id)->first();
                $billType = BillType::get();


                $subOrders = array();
                $subOrders = ProductOrderList::where('order_code_id' ,$productOrder->id)->get();
                foreach ($subOrders as $key => $order) {
                  $product = Product::where('id', $order->product_id)->first();

                  $subOrderList[]= array(
                      'id' => $order->id ,
                      'sub_order_code' => $order->sub_order_code ,
                      'order_status' => $order->order_status,
                      'price' => $order->price,
                      'product_sku' => $product->sku,
                      'product_name' => $product->product_name,
                      'product_qty' => $order->product_qty,
                       );
                }
                $now = now();
                    $array[]= array(
                      'id' => $value->id ,
                      'order_code' => $productOrder->order_code ,
                      'transaction_code' => $value->transaction_code,
                      'bill_no' => $value->bill_no,
                      'customer_name' => $customer->name ,
                      'billing_date' => Carbon::parse($now)->toDateString(),
                      'payment_method' => $paymentMethod->payment_name,
                      'sub_totol' => $productOrder->price,
                      'delivery_charge_amt' => $productOrder->delivery_charge_amt,
                      'final_total' => $productOrder->final_price ,
                      'subOrderList'  => $subOrderList,
                      'billType' =>$billType,


                       );
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Billing Process Data Fetched',
                                    'data' =>$array,
                                );

                return response()->json($result,200);
                }else{
                    
                    return response()->json([
                    'status'     => false,
                    'message'    => 'Billing Process Data not found',
                    'data'       =>null,
                ], 422);
                }
                    
                
            
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    } 


    public function singleBillingProcess(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array = array();
                $value = ProductOrderBilling::where('id',$id)->first();
                if ($value){
                $productOrder = ProductOrder::where('id',$value->order_code_id)->first();
                $paymentMethod = PaymentMethod::where('id',$productOrder->payment_id)->first();
                $customer = Customer::where('id',$productOrder->user_id)->first();
                $billType = BillType::get();
                $subOrders = array();
                $subOrders = ProductOrderList::where('order_code_id' ,$productOrder->id)->get();
                foreach ($subOrders as $key => $order) {
                  $product = Product::where('id', $order->product_id)->first();
                  $subOrderList[]= array(
                      'id' => $order->id ,
                      'sub_order_code' => $order->sub_order_code ,
                      'order_status' => $order->order_status,
                      'price' => $order->price,
                      'product_sku' => $product->sku,
                      'product_name' => $product->product_name,
                      'product_qty' => $order->product_qty,
                       );
                }
                $now = now();
                    $array[]= array(
                      'id' => $value->id ,
                      'order_code' => $productOrder->order_code ,
                      'transaction_code' => $value->transaction_code,
                      'bill_no' => $value->bill_no,
                      'customer_name' => $customer->name ,
                      'billing_date' => Carbon::parse($now)->toDateString(),
                      'payment_method' => $paymentMethod->payment_name,
                      'sub_totol' => $productOrder->price,
                      'delivery_charge_amt' => $productOrder->delivery_charge_amt,
                      'final_total' => $productOrder->final_price ,
                      'subOrderList'  => $subOrderList,
                      'billType' =>$billType,


                       );
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Billing Process Data Fetched',
                                    'data' =>$array,
                                );

                return response()->json($result,200);
                }else{
                    
                    return response()->json([
                    'status'     => false,
                    'message'    => 'Billing Process Data not found',
                    'data'       =>null,
                ], 422);
                }
                    
                
            
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    } 


    public function placeOrderUpdate(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'billing_status' => 'required',
            ]);
            if ($validator->passes()) {
            $order = ProductOrderBilling::find($id);
            $subOrders = ProductOrderBilling::where('order_code_id',$order->order_code_id)->get();
            if($order){
              foreach ($subOrders as $key => $sub) {
            $data = ProductOrderBilling::where('id',$sub->id)->first();
            $data->billing_status = $request->billing_status;
            $data->bill_type_id = $request->bill_type_id;
            $data->bill_no = $request->bill_no;
            $data->billing_date = \Carbon\Carbon::now()->format('Y-m-d');
 
            $data->ky_cancel_reason = $request->ky_cancel_reason;
            $data->save();
              }

                    $data = array(
                        'status'  =>true,
                        'message' =>'Billing Process Updated Successfully!' ,
                        );
                    return response()->json($data,200);
            }else{
                    return response()->json([
                        'message'   =>'Billing Process Data Not found !',
                        'status'    =>false,
                        'data'      =>null,
                    ]);
                }
                }else{

                $result = array(
                'status'        => false,
                'message'       => 'Input Field Required',
                'data'    => $validator->errors()
                );
                return response()->json($result,422);
            }
        }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 404);
            }
    }

}
