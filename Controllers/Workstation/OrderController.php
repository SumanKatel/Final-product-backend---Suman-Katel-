<?php

namespace App\Http\Controllers\Workstation;
use App\Http\Controllers\Controller;
use App\Model\Front\OrderGift;
use App\Model\Front\WrapPaper;
use App\Model\Workstation\Author;
use App\Model\Workstation\BillingAddress;
use App\Model\Workstation\Campaign;
use App\Model\Workstation\Category;
use App\Model\Workstation\CategoryAttributeRelation;
use App\Model\Workstation\Country;
use App\Model\Workstation\Customer;
use App\Model\Workstation\District;
use App\Model\Workstation\Language;
use App\Model\Workstation\Municipality;
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
use App\Model\Workstation\RelationWarehouseOrder;
use App\Model\Workstation\State;
use App\Model\Workstation\WorkstationUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;


class OrderController extends Controller
{

    public function getOrderList(Request $request)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array = array();
                $list = ProductOrder::orderBy('id','desc')->where('is_foreign_order','!=',1)->get();
                foreach ($list as $key => $value) {
                $productOrder = ProductOrder::where('id',$value->id)->first();
                if($productOrder)
                {
                    $PO = $productOrder->order_code;
                }else{
                    $PO = '';
                }
                $customer = Customer::where('id',$value->user_id)->first();
                $product = Product::where('id',$value->product_id)->first();

                $relcategory = RelationProductCategory::where('product_id',$product->id)->where('parent_category_id',0)->first();
                $category = Category::where('id',$relcategory->category_id)->first();

                $relsubcategory = RelationProductCategory::where('product_id',$product->id)->where('parent_category_id','!=',0)->first();
                $subcategory = Category::where('id',$relsubcategory->category_id)->first();

                    $array[]= array(
                      'id' => $value->id ,
                      'order_code_id' => $value->order_code_id ,
                      'order_code' => $PO ,
                      'sub_order_code' => $value->sub_order_code ,
                      'customer_id' => $value->user_id ,
                      'customer_name' => $customer->name ,
                      'customer_email' => $customer->email ,
                      'customer_mobile' => $customer->mobile ,
                      'customer_code' => $customer->user_code ,
                      'product_id' => $product->id ,
                      'product_sku' => $product->sku ,
                      'product_name' => $product->product_name ,
                      'category_id' => $category->id,
                      'category_name' => $category->title,
                      'sub_category_id' => $subcategory->id,
                      'sub_category_name' => $subcategory->title,
                      'is_gift' => $value->is_gift,
                      'order_status' => $value->order_status,
                      'is_mobile_order'=>$value->is_mobile_order,
                      'is_foreign_order'=>$value->is_foreign_order,
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


    // public function newOrderProcess(Request $request)
    // {
    //     $wsId = $request->wsId;
    //     $workstation = WorkstationUser::find($wsId);
    //         if ($workstation) {
    //             $array = array();
    //             $value = ProductOrderList::where('order_status','0')->orderBy('order_datetime','desc')->first();
    //             if ($value) {
               
    //             $productOrder = ProductOrder::where('id',$value->order_code_id)->first();
    //             $paymentMethod = PaymentMethod::where('id',$productOrder->payment_id)->first();
    //             $customer = Customer::where('id',$value->user_id)->first();
    //             $product = Product::where('id',$value->product_id)->first();
    //             $productImage = ProductImage::where('product_id',$product->id)->where('is_desktop_thumbnail',1)->first();
    //             if ($productImage) {
    //              $finalImage = $productImage->image;
    //             }else{
    //               $finalImage = DEFAULT_IMG;
    //             }

    //             $relcategory = RelationProductCategory::where('product_id',$product->id)->where('parent_category_id',0)->first();
    //             $category = Category::where('id',$relcategory->category_id)->first();

    //             $relsubcategory = RelationProductCategory::where('product_id',$product->id)->where('parent_category_id','!=',0)->first();
    //             $subcategory = Category::where('id',$relsubcategory->category_id)->first();
    //             $now = now();

    //                 $array[]= array(
    //                   'id' => $value->id ,
    //                   'order_code' => $productOrder->order_code ,
    //                   'sub_order_code' => $value->sub_order_code ,
    //                   'workstation_employee_code' => $workstation->code ,
    //                   'order_date' => Carbon::parse($value->order_datetime)->toDateString(),
    //                   'order_time' => Carbon::parse($value->order_datetime)->format('H:i A'),

    //                   'processing_date' => Carbon::parse($now)->toDateString(),
    //                   'processing_time' => Carbon::parse($now)->format('H:i A'),

    //                   'customer_id' => $value->user_id ,
    //                   'customer_name' => $customer->name ,
    //                   'customer_mobile' => $customer->mobile ,
    //                   'product_id' => $product->id ,
    //                   'product_sku' => $product->sku ,
    //                   'product_name' => $product->product_name ,
    //                   'category_id' => $category->id,
    //                   'category_name' => $category->title,
    //                   'sub_category_id' => $subcategory->id,
    //                   'sub_category_name' => $subcategory->title,
    //                   'is_gift' => $value->is_gift,

    //                   'payment_method' => $paymentMethod->payment_name,
    //                   'product_qty' => $value->product_qty,
    //                   'product_image' => $finalImage,
    //                    );
    //             $result = array(
    //                                 'status'        =>true,
    //                                 'message'       => 'Order Process Data Fetched',
    //                                 'data' =>$array,
    //                             );
    //             return response()->json($result,200);
    //             }else{

    //               return response()->json([
    //                 'status'     => false,
    //                 'message'    => 'There is no order to process',
    //                 'data'       =>null,
    //             ], 401);

    //             }
    //         }
    //         else{
    //             return response()->json([
    //                 'status'     => false,
    //                 'message'    => 'Workstation User Not Found',
    //                 'data'       =>null,
    //             ], 401);
    //         }
    // } 



       public function newOrderProcess(Request $request)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array = array();
                $order = ProductOrder::where('order_status','0')->orderBy('order_datetime','desc')->first();
                if ($order) {
                $paymentMethod = PaymentMethod::where('id',$order->payment_id)->first();
                $customer = Customer::where('id',$order->user_id)->first();
                $address = BillingAddress::where('id',$order->delivery_address_id)->first();
                $state = State::where('id',$address->state_id)->first();
                $district = District::where('id',$address->district_id)->first();
                $municipality = Municipality::where('id',$address->municipality_id)->first();
                $country = Country::where('id',$address->country_id)->first();
                
                 if ($order->coupon_id) {
                  $coupon = Campaign::where('id',$order->coupon_id)->first();
                }
                 //sub orders
                    $subOrders = ProductOrderList::where('order_code_id',$order->id)->get();
                    $subOrderArray = array();
                        foreach ($subOrders as $key => $subOrder) {
                            
                             if ($subOrder->is_gift==1) {
                            $gift = OrderGift::where('sub_order_id',$subOrder->id)->first();
                            if ($gift->is_wrapping==1) {
                             $wrapPaper = WrapPaper::where('id',$gift->wrapping_paper_id)->first();
                            }
                          }
                            $product = Product::where('id',$subOrder->product_id)->first();

                            $relcategory = RelationProductCategory::where('product_id',$product->id)->where('parent_category_id',0)->first();
                            $category = Category::where('id',$relcategory->category_id)->first();

                            $relsubcategory = RelationProductCategory::where('product_id',$product->id)->where('parent_category_id','!=',0)->first();
                            $subcategory = Category::where('id',$relsubcategory->category_id)->first();

                            $productImage = ProductImage::where('product_id', $product->id)->where('is_desktop_thumbnail',1)->first();
                            if ($productImage) {
                             $productImage = $productImage->image;
                            }else{
                              $productImage = DEFAULT_IMG;
                            }
                            $subOrderArray[]= array(
                              'id' => $subOrder->id ,
                              'order_id' => $order->id ,
                              'sub_order_code' => $subOrder->sub_order_code ,
                              'product_id' =>$product->id,
                              'product_sku' =>$product->sku,
                              'product_name' =>$product->product_name,
                              'product_qty' =>$subOrder->product_qty,
                              'product_price' =>$subOrder->price,
                              'product_image' => $productImage,
                              'order_status'  => $subOrder->order_status,
                              'category_id' => $category->id,
                              'category_name' => $category->title,
                              'sub_category_id' => $subcategory->id,
                              'sub_category_name' => $subcategory->title,
                              'is_gift' => $order->is_gift,
                              'is_wrapping' =>$gift->is_wrapping ?? null,
                              'wrap_paper' => $wrapPaper->title ?? null,
                            );
                        }

                     $array[]= array(
                          'id' => $order->id ,
                          'order_code' => $order->order_code ,
                          'order_date' => Carbon::parse($order->order_datetime)->toDateString(),
                          'order_time' => Carbon::parse($order->order_datetime)->format('H:i A'),
                          'is_mobile_order'=> $order->is_mobile_order,
                          'customer_code' => $customer->user_code ,
                          'customer_name' => $customer->name ,
                          'customer_mobile' => $customer->mobile ,
                          'customer_email' => $customer->email ,
                          'workstation_employee_code' => $workstation->code ,
                          'payment_method' => $paymentMethod->payment_name,
                          'state_name' => $state->state_name_np ?? null,
                          'district_name' => $district->district_name_en ?? null,
                          'municipality_name' => $municipality->location_name_en ?? null,
                          'country_name' => $country->title ?? null,
                          'is_foreign_order' => $order->is_foreign_order,
                          'customer_address' => $address->address ?? null,
                          'coupon_code'   => $coupon->offer_code ?? null,
                          'is_mobile_order'=>$order->is_mobile_order,
                          'is_foreign_order'=>$order->is_foreign_order,
                          'subOrder' =>$subOrderArray,
                        );

                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Order Process Data Fetched',
                                    'data' =>$array,
                                );
                return response()->json($result,200);
                }else{

                  return response()->json([
                    'status'     => false,
                    'message'    => 'There is no order to process',
                    'data'       =>null,
                ], 401);

                }
            }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    } 


    public function singleOrderProcess(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array = array();
                $order = ProductOrder::where('id',$id)->first();
                if ($order) {
                $paymentMethod = PaymentMethod::where('id',$order->payment_id)->first();
                $customer = Customer::where('id',$order->user_id)->first();
                $address = BillingAddress::where('id',$order->delivery_address_id)->first();
                $state = State::where('id',$address->state_id)->first();
                $district = District::where('id',$address->district_id)->first();
                $municipality = Municipality::where('id',$address->municipality_id)->first();
                $country = Country::where('id',$address->country_id)->first();
                
                 if ($order->coupon_id) {
                  $coupon = Campaign::where('id',$order->coupon_id)->first();
                }
                 //sub orders
                    $subOrders = ProductOrderList::where('order_code_id',$order->id)->get();
                    $subOrderArray = array();
                        foreach ($subOrders as $key => $subOrder) {
                            
                             if ($subOrder->is_gift==1) {
                            $gift = OrderGift::where('sub_order_id',$subOrder->id)->first();
                            if ($gift->is_wrapping==1) {
                             $wrapPaper = WrapPaper::where('id',$gift->wrapping_paper_id)->first();
                            }
                          }
                            $product = Product::where('id',$subOrder->product_id)->first();

                            $relcategory = RelationProductCategory::where('product_id',$product->id)->where('parent_category_id',0)->first();
                            $category = Category::where('id',$relcategory->category_id)->first();

                            $relsubcategory = RelationProductCategory::where('product_id',$product->id)->where('parent_category_id','!=',0)->first();
                            $subcategory = Category::where('id',$relsubcategory->category_id)->first();

                            $productImage = ProductImage::where('product_id', $product->id)->where('is_desktop_thumbnail',1)->first();
                            if ($productImage) {
                             $productImage = $productImage->image;
                            }else{
                              $productImage = DEFAULT_IMG;
                            }
                            $subOrderArray[]= array(
                              'id' => $subOrder->id ,
                              'order_id' => $order->id ,
                              'sub_order_code' => $subOrder->sub_order_code ,
                              'product_id' =>$product->id,
                              'product_sku' =>$product->sku,
                              'product_name' =>$product->product_name,
                              'product_qty' =>$subOrder->product_qty,
                              'product_price' =>$subOrder->price,
                              'product_image' => $productImage,
                              'order_status'  => $subOrder->order_status,
                              'category_id' => $category->id,
                              'category_name' => $category->title,
                              'sub_category_id' => $subcategory->id,
                              'sub_category_name' => $subcategory->title,
                              'is_gift' => $order->is_gift,
                              'is_wrapping' =>$gift->is_wrapping ?? null,
                              'wrap_paper' => $wrapPaper->title ?? null,
                            );
                        }

                     $array[]= array(
                          'id' => $order->id ,
                          'order_code' => $order->order_code ,
                          'order_date' => Carbon::parse($order->order_datetime)->toDateString(),
                          'order_time' => Carbon::parse($order->order_datetime)->format('H:i A'),
                          'is_mobile_order'=> $order->is_mobile_order,
                          'customer_code' => $customer->user_code ,
                          'customer_name' => $customer->name ,
                          'customer_mobile' => $customer->mobile ,
                          'customer_email' => $customer->email ,
                          'workstation_employee_code' => $workstation->code ,
                          'payment_method' => $paymentMethod->payment_name,
                          'state_name' => $state->state_name_np ?? null,
                          'district_name' => $district->district_name_en ?? null,
                          'municipality_name' => $municipality->location_name_en ?? null,
                          'country_name' => $country->title ?? null,
                          'is_foreign_order' => $order->is_foreign_order,
                          'customer_address' => $address->address ?? null,
                          'coupon_code'   => $coupon->offer_code ?? null,
                          'is_mobile_order'=>$order->is_mobile_order,
                          'is_foreign_order'=>$order->is_foreign_order,
                          'subOrder' =>$subOrderArray,
                        );

                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Single Order Process Data Fetched',
                                    'data'          =>$array,
                                );
                return response()->json($result,200);
                }else{

                  return response()->json([
                    'status'     => false,
                    'message'    => 'There is no order to process',
                    'data'       =>null,
                ], 401);

                }
            }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    }

    

    // public function placeOrderUpdate(Request $request,$id)
    // {
    //     $wsId = $request->wsId;
    //     $workstation=WorkstationUser::find($wsId);
    //     if ($workstation) {
    //     $validator=Validator::make($request->all(), [
    //             'order_status' => 'required',
    //         ]);
    //         if ($validator->passes()) {
    //         $order = ProductOrderList::find($id);

    //         if($order){
    //         $order->order_status = $request->order_status;
    //         $order->ky_cancel_reason = $request->ky_cancel_reason;
    //         $order->save();
            
            
    //         $productOrder = ProductOrderList::where('order_code_id',$order->order_code_id)->get();
    //         foreach ($productOrder as $key => $po) {
    //           if ($po->order_status== 1) {
    //             $productOrderBill = ProductOrderBilling::where('sub_order_code_id',$po->id)->first();
    //             if(!$productOrderBill){
    //               $bill['order_code_id'] = $po->order_code_id;
    //               $bill['sub_order_code_id'] = $po->id;
    //               $bill['billing_status'] = 0;
    //               $bill['transaction_code'] = 'KYT'.time();
    //               // $bill['order_code'] = 'KY-'.time();
    //             //   $bill['bill_no'] = 'KYB'.time();
    //               $bill['billing_request_date'] = now();
    //               $result = ProductOrderBilling::create($bill);
    //             }
    //           }else{

    //           }
    //         }

    //         $orderdata= array(
    //                             'id' =>$order->id ,
    //                             'order_status' =>$order->order_status ,
    //                          );

    //                 $data = array(
    //                     'status'  =>true,
    //                     'message' =>'Order Process Updated Successfully!' ,
    //                     'data'=>$orderdata,
    //                     );
    //                 return response()->json($data,200);
    //         }else{
    //                 return response()->json([
    //                     'message'   =>'Product Order Data Not found !',
    //                     'status'    =>false,
    //                     'data'      =>null,
    //                 ]);
    //             }
    //             }else{

    //             $result = array(
    //             'status'        => false,
    //             'message'       => 'Input Field Required',
    //             'data'    => $validator->errors()
    //             );
    //             return response()->json($result,422);
    //         }
    //     }
    //         else{
    //             return response()->json([
    //                 'status'     => false,
    //                 'message'    => 'Workstation User Not Found',
    //                 'data'       =>null,
    //             ], 404);
    //         }
    // }



public function placeOrderUpdate(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'order_status' => 'required',
            ]);
            if ($validator->passes()) {
            
            $productOrder = ProductOrder::find($id);
            if ($productOrder) {
              $productOrder->order_status = 1;
              $productOrder->save(); 

            foreach ($request->product_sub_order_id as $key => $subOrderId) {
                $subOrder = ProductOrderList::find($subOrderId);

                if ($request->order_status[$key]== 1) {

                  $subOrder->order_status = $request->order_status[$key];
                  $subOrder->ky_cancel_reason = $request->ky_cancel_reason[$key];
                  $subOrder->save();

                  $warehouseOrder = RelationWarehouseOrder::where('sub_order_id',$subOrder->id)->first();
                  if(!$warehouseOrder){
                    $warehouse['order_id'] = $subOrder->order_code_id;
                    $warehouse['sub_order_id'] = $subOrder->id;
                    $warehouse['status'] = 0;
                    $warehouse['product_id'] = $subOrder->product_id;
                    $warehouse['product_qty'] = $subOrder->product_qty;
                    $warehouse['warehouse_id'] = $request->warehouse_id[$key];
                    $result = RelationWarehouseOrder::create($warehouse);
                  }

                  $productOrderBill = ProductOrderBilling::where('sub_order_code_id',$subOrder->id)->first();
                  if(!$productOrderBill){
                    $bill['order_code_id'] = $subOrder->order_code_id;
                    $bill['sub_order_code_id'] = $subOrder->id;
                    $bill['billing_status'] = 0;
                    $bill['transaction_code'] = 'KYT'.time();
                    $bill['billing_request_date'] = now();
                    $result = ProductOrderBilling::create($bill);
                  }
                }else{

                }
            }
            
            $orderdata= array(
                                'id' =>$productOrder->id ,
                                'order_status' =>$productOrder->order_status ,
                             );

                    $data = array(
                        'status'  =>true,
                        'message' =>'Order Process Updated Successfully!' ,
                        'data'=>$orderdata,
                        );
                    return response()->json($data,200);
            }else{
                    return response()->json([
                        'message'   =>'Product Order Data Not found !',
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
