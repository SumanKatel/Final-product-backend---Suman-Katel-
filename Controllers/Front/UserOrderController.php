<?php

namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use App\Model\Workstation\BillingAddress;
use App\Model\Workstation\Customer;
use App\Model\Workstation\DeliveryAddressPrice;
use App\Model\Workstation\District;
use App\Model\Workstation\Municipality;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductImage;
use App\Model\Workstation\ProductOrder;
use App\Model\Workstation\ProductOrderList;
use App\Model\Workstation\State;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;


class UserOrderController extends Controller
{

    public function getAllOrder(Request $request)
    {
            $customerId = $request->customerId;
            $customer=Customer::find($customerId);
            if ($customer) {
            $orders = array();

            $orders = Order::where('user_id',$customer->id)->orderBy('created_at','desc')->get();
                foreach ($orders as $key => $order) {
                    //sub orders
                    $subOrders = OrderDetail::where('order_id',$order->id)->orderBy('created_at','desc')->get();
                    $subOrderArray = array();
                        foreach ($subOrders as $key => $subOrder) {
                            $product = Product::where('id',$subOrder->product_id)->first();

                            $subOrderArray[]= array(
                              'id'              => $subOrder->id ,
                              'order_id'        => $order->id ,
                              'sub_order_code'    => $subOrder->sub_order_code ,
                              'product_id'    =>$product->id,
                              'product_name'    =>$product->product_name,
                              'product_qty'     =>$subOrder->product_qty,
                              'product_price'     =>$subOrder->price,
                              'product_image'     => $productImage,
                              'order_status'      => $subOrder->order_status,
                            );
                        }
                    $orders[]= array(
                          'id'                => $order->id ,
                          'order_code'        => $order->order_code ,
                          'order_date'        => Carbon::parse($order->date)->toDateString(),
                          'order_time'        => Carbon::parse($order->date)->format('H:i A'),
                          'grand_total'       => $order->grand_total ,
                          'delivery_status'   => ucfirst(str_replace('_', ' ', $order->delivery_status) ,
                          'payment_status'    => ucfirst(str_replace('_', ' ', $order->payment_status) ,
                          'payment_type'      => ucfirst(str_replace('_', ' ', $order->payment_type) ,
                          'subOrders'         => $subOrderArray,
                        );
                }
                    $result = array(
                                    'status'        => true,
                                    'message'       => 'Product Order Fetched !',
                                    'order' => $array,
                                );

                return response()->json($result,200);
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Customer User Not Found',
                    'data'       =>null,
                ], 401);
            }
        
    }
  
}
