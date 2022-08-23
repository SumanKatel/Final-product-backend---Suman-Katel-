<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Model\Workstation\BillingAddress;
use App\Model\Workstation\Customer;
use App\Model\Workstation\District;
use App\Model\Workstation\Municipality;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductImage;
use App\Model\Workstation\ProductOrder;
use App\Model\Workstation\ProductOrderList;
use App\Model\Workstation\ProductOrderTrack;
use App\Model\Workstation\State;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class MobileApiUserOrderController extends Controller
{
    public function getAllOrder(Request $request)
    {
      $customerId = $request->customerId;
      $customer=Customer::find($customerId);
      if ($customer) {
      $array = array();
      $orders = ProductOrder::where('user_id',$customer->id)->where('status',0)->orderBy('created_at', 'desc')->get();
          foreach ($orders as $key => $order) {
            $address = BillingAddress::where('id',$order->delivery_address_id)->first();
            $state = State::where('id',$address->state_id)->first();
            $district = District::where('id',$address->district_id)->first();
            $municipality = Municipality::where('id',$address->municipality_id)->first();
            $subOrderArray = array();
            $subOrders = ProductOrderList::where('order_code_id',$order->id)->get();
                foreach ($subOrders as $key => $subOrder) {
                    $product = Product::where('id',$subOrder->product_id)->first();
                    $productImage = ProductImage::where('product_id', $product->id)->where('is_desktop_thumbnail',1)->first();
                    if ($productImage) {
                     $productImage = $productImage->image;
                    }else{
                      $productImage = DEFAULT_IMG;
                    }

                    $subOrderArray[]= array(
                      'id' => $subOrder->id ,
                      'sub_order_code' => $subOrder->sub_order_code ,
                      'product_name' =>$product->product_name,
                      'product_qty' =>$subOrder->product_qty,
                      'product_price' =>$subOrder->price,
                      'product_image' => $productImage,
                    );
                }
              $array[]= array(
                    'id' => $order->id ,
                    'order_code' => $order->order_code ,
                    'order_date' => Carbon::parse($order->order_datetime)->toDateString(),
                    'order_time' => Carbon::parse($order->order_datetime)->format('H:i A'),
                    'final_price' => $order->final_price ,
                    'state_name' => $state->state_name_np ?? null,
                    'district_name' => $district->district_name_en ?? null,
                    'municipality_name' => $municipality->location_name_en ?? null,
                    'subOrder' =>$subOrderArray,
                  );
          }
           $array = array(
                      'order' => $array
                    );
              $result = array(
                              'status'        => true,
                              'message'       => 'Product Order Fetched !',
                              'data'         => $array,
                          );

          return response()->json($result,200);
      }
      else{
          return response()->json([
              'status'     => false,
              'message'    => 'Customer User Not Found',
              'data'       =>null,
          ], 401);
      }
    }

    public function getAllForeignOrder(Request $request)
    {
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
        $array = array();
        $subOrderArray = array();
        $orders = ProductOrder::where('user_id',$customer->id)->where('status',0)->where('is_foreign_order',1)->get();
            foreach ($orders as $key => $order) {
                $address = BillingAddress::where('id',$order->delivery_address_id)->first();
                $state = State::where('id',$address->state_id)->first();
                $district = District::where('id',$address->district_id)->first();
                $municipality = Municipality::where('id',$address->municipality_id)->first();
                $subOrders = ProductOrderList::where('id',$order->id)->get();
                    foreach ($subOrders as $key => $subOrder) {
                        $product = Product::where('id',$subOrder->product_id)->first();
                        $productImage = ProductImage::where('product_id', $product->id)->where('is_desktop_thumbnail',1)->first();
                        if ($productImage) {
                         $productImage = $productImage->image;
                        }else{
                          $productImage = DEFAULT_IMG;
                        }

                        $subOrderArray[]= array(
                          'id' => $subOrder->id ,
                          'sub_order_code' => $subOrder->sub_order_code ,
                          'product_name' =>$product->product_name,
                          'product_qty' =>$subOrder->product_qty,
                          'product_price' =>$subOrder->price,
                          'product_image' => $productImage,
                        );
                    }
                $array[]= array(
                      'id' => $order->id ,
                      'order_code' => $order->order_code ,
                      'order_date' => Carbon::parse($order->order_datetime)->toDateString(),
                      'order_time' => Carbon::parse($order->order_datetime)->format('H:i A'),
                      'final_price' => $order->final_price ,
                      'state_name' => $state->state_name_np ?? null,
                      'district_name' => $district->district_name_en ?? null,
                      'municipality_name' => $municipality->location_name_en ?? null,
                      'subOrder' =>$subOrderArray,
                    );
            }
            $array = array(
                      'order' => $array
                    );
             $result = array(
                            'status'        => true,
                            'message'       => 'Product Order Fetched !',
                            'data' => $array,
                        );

            return response()->json($result,200);
        }
        else{
            return response()->json([
                'status'     => false,
                'message'    => 'Customer User Not Found',
                'data'       =>null,
            ], 401);
        }
        
    }
    public function cancelOrder(Request $request,$id)
    {
    $customerId = $request->customerId;
    $customer=Customer::find($customerId);
    if ($customer) {
        $validator = Validator::make($request->all(), [
          'user_cancel_reason'        => 'required',
      ]);
      if ($validator->passes()) {
          $sub_order = ProductOrderList::where('id',$id)->first();

        if ($sub_order) {
          // $order->order_status = 5;
          // $order->save();
          // $subOrderList = ProductOrderList::where('order_code_id',$order->id)->get();
          // foreach ($subOrderList as $key => $subOrder) {

          //   $data = ProductOrderList::where('id',$subOrder->id)->first();
            
              $sub_order->order_status = 5;
              $sub_order->user_cancel_reason =$request->user_cancel_reason;
              $sub_order->save();
            
            
          }
          $result = array(
                      'status'        => true,
                      'message'       => 'Order Cancel Successfully',
                      // 'data'          => $array,
                    );
          return response()->json($result,200);
                   

      } else{
        $result = array(
          'status'     => 422,
          'message' => 'Input Field Required',
          'data'    => $validator->errors()
        );
        return response()->json($result,200);
      }
    }else{
        return response()->json([
            'status'     => false,
            'message'    => 'Customer User Not Found',
            'data'       =>null,
        ], 401);
    }
  }

  public function cancel_order_list(Request $request)
    {
      $customerId = $request->customerId;
      $customer=Customer::find($customerId);
      if ($customer) {
        $array = array();
        $orders = ProductOrder::where('user_id', $customer->id)->where('order_status',5)->get();
        $subOrderArray = array();
        foreach($orders as $order_info){
          $order = ProductOrder::where('id',$order_info->id)->first();
          $address = BillingAddress::where('id',$order->delivery_address_id)->first();
          $state = State::where('id',$address->state_id)->first();
          $district = District::where('id',$address->district_id)->first();
          $municipality = Municipality::where('id',$address->municipality_id)->first();
        
          $subOrders = ProductOrderList::where('user_id',$customer->id)->where('order_code_id', $order->id)->get();
            foreach ($subOrders as $key => $subOrder) {
              $product = Product::where('id',$subOrder->product_id)->first();
              $productImage = ProductImage::where('product_id', $product->id)->where('is_desktop_thumbnail',1)->first();
              if ($productImage) {
               $productImage = $productImage->image;
              }else{
                $productImage = DEFAULT_IMG;
              }

              $subOrderArray[]= array(
                'id' => $subOrder->id ,
                'sub_order_code' => $subOrder->sub_order_code ,
                'product_name' =>$product->product_name,
                'product_qty' =>$subOrder->product_qty,
                'product_price' =>$subOrder->price,
                'product_image' => $productImage,
                'user_cancel_reason' => $subOrder->user_cancel_reason,
                'order_status' => $subOrder->order_status,
              );
               
            }
            $array[]= array(
                      'id' => $order->id ,
                      'order_code' => $order->order_code ,
                      'order_date' => Carbon::parse($order->order_datetime)->toDateString(),
                      'order_time' => Carbon::parse($order->order_datetime)->format('H:i A'),
                      'final_price' => $order->final_price ,
                      'state_name' => $state->state_name_np ?? null,
                      'district_name' => $district->district_name_en ?? null,
                      'municipality_name' => $municipality->location_name_en ?? null,
                      'subOrder' =>$subOrderArray,
                    );
        }
        $array = array(
                  'order' => $array
                );
            $result = array(
                            'status'        => true,
                            'message'       => 'Cancelled Product Order Fetched !',
                            'data'         => $array,
                        );

        return response()->json($result,200);
      }
      else{
          return response()->json([
              'status'     => false,
              'message'    => 'Customer User Not Found',
              'data'       =>null,
          ], 401);
      }
    }

    public function delivered_order_list(Request $request)
    {
      $customerId = $request->customerId;
      $customer=Customer::find($customerId);
      if ($customer) {
      $array = array();
      $subOrderArray = array();
      $delivered_orders = ProductOrderTrack::where('user_id',$customer->id)->where('product_delivered',1)->get();
          foreach ($delivered_orders as $key => $d_order) {
            $order = ProductOrder::where('id', $d_order->order_id)->first();
            $address = BillingAddress::where('id',$order->delivery_address_id)->first();
            $state = State::where('id',$address->state_id)->first();
            $district = District::where('id',$address->district_id)->first();
            $municipality = Municipality::where('id',$address->municipality_id)->first();
            $subOrders = ProductOrderList::where('order_code_id',$order->id)->get();
                foreach ($subOrders as $key => $subOrder) {
                    $product = Product::where('id',$subOrder->product_id)->first();
                    $productImage = ProductImage::where('product_id', $product->id)->where('is_desktop_thumbnail',1)->first();
                    if ($productImage) {
                     $productImage = $productImage->image;
                    }else{
                      $productImage = DEFAULT_IMG;
                    }

                    $subOrderArray[]= array(
                      'id' => $subOrder->id ,
                      'sub_order_code' => $subOrder->sub_order_code ,
                      'product_name' =>$product->product_name,
                      'product_qty' =>$subOrder->product_qty,
                      'product_price' =>$subOrder->price,
                      'product_image' => $productImage,
                    );
                }
              $array[]= array(
                    'id' => $order->id ,
                    'order_code' => $order->order_code ,
                    'order_date' => Carbon::parse($order->order_datetime)->toDateString(),
                    'order_time' => Carbon::parse($order->order_datetime)->format('H:i A'),
                    'final_price' => $order->final_price ,
                    'state_name' => $state->state_name_np ?? null,
                    'district_name' => $district->district_name_en ?? null,
                    'municipality_name' => $municipality->location_name_en ?? null,
                    'subOrder' =>$subOrderArray,
                  );
          }
          $array = array(
                    'order' => $array
                  );
              $result = array(
                              'status'        => true,
                              'message'       => 'Delivered Product Order Fetched !',
                              'data'          => $array,
                          );

          return response()->json($result,200);
      }
      else{
          return response()->json([
              'status'     => false,
              'message'    => 'Customer User Not Found',
              'data'       =>null,
          ], 401);
      }
    }
}
