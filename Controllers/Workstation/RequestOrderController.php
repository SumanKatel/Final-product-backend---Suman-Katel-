<?php

namespace App\Http\Controllers\Workstation;
use App\Http\Controllers\Controller;
use App\Model\Workstation\Author;
use App\Model\Workstation\BillingAddress;
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
use App\Model\Workstation\State;
use App\Model\Workstation\WorkstationUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;


class RequestOrderController extends Controller
{


  public function fetcCustomerInfo(Request $request)
  {

    $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $customer = Customer::where('mobile',$request->mobile)->orwhere('email',$request->email)->first();
                if ($customer) {
                $addressArray = array();
                $addressList = BillingAddress::where('user_id', $customer->id)->get();
                foreach ($addressList as $key => $address) {
                $country = Country::where('id',$address->country_id)->first();
                $state = State::where('id',$address->state_id)->first();
                $district = District::where('id',$address->district_id)->first();
                $municipality = Municipality::where('id',$address->municipality_id)->first();
                
                $addressArray[]= array(
                  'billing_address_id' => $address->id ?? null,
                  'is_foreign' => $address->is_foreign ?? null,
                  'country_id' => $address->country_id ?? null,
                  'country_name' => $country->title ?? null,
                  'title' => $address->title ?? null,
                  'address' => $address->address ?? null,
                  'state_id' => $address->state_id ?? null,
                  'district_id' => $address->district_id ?? null,
                  'municipality_id' => $address->municipality_id ?? null,
                  'state_name' => $state->state_name_np ?? null,
                  'district_name' => $district->district_name_en ?? null,
                  'municipality_name' => $municipality->location_name_en ?? null,
                   );
                }
                $data= array(
                  'id' => $customer->id ,
                  'name' => $customer->name ,
                  'email' => $customer->email ,
                  'mobile' => $customer->mobile ,
                  'addressList' => $addressArray

                   );
                  $result = array(
                                    'status'        =>true,
                                    'message'       => 'Customer data Fetched !',
                                    'data' =>$data,
                                );
                return response()->json($result,200);
                }else{
                  return response()->json([
                    'status'     => false,
                    'message'    => 'Customer Not found',
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


  public function addressDelete(Request $request,$id)
  {
    $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $address = BillingAddress::where('id',$id)->first();
                if ($address) {
                  $address->status = 0;
                  $address->save();
                  $result = array(
                                    'status'        =>true,
                                    'message'       => 'Address Deleted Successfully !',
                                    'data' =>$data,
                                );
                return response()->json($result,200);
                }else{
                  return response()->json([
                    'status'     => false,
                    'message'    => 'Address Not Found !!',
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


    public function fetcCustomerOrderInfo(Request $request)
  {
    $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $customer = Customer::where('mobile',$request->mobile)->first();
                if ($customer) {
                $orderList = array();
                $subOrderArray = array();
                $orders = ProductOrder::where('user_id',$customer->id)->where('status',0)->get();
                foreach ($orders as $key => $order) {
                    $subOrders = ProductOrderList::where('id',$order->id)->get();
                        foreach ($subOrders as $key => $subOrder) {
                            $product = Product::where('id',$subOrder->product_id)->first();
                            $subOrderArray[]= array(
                              'id' => $subOrder->id ,
                              'sub_order_code' => $subOrder->sub_order_code ,
                            );
                        }
                    $orderList[]= array(
                          'id' => $order->id ,
                          'order_code' => $order->order_code ,
                          'subOrderList' =>$subOrderArray,
                        );
                }
                $address = BillingAddress::where('user_id', $customer->id)->first();
                if ($address) {
                $state = State::where('id',$address->state_id)->first();
                $district = District::where('id',$address->district_id)->first();
                $municipality = Municipality::where('id',$address->municipality_id)->first();
                }
                $data= array(
                  'id' => $customer->id ,
                  'name' => $customer->name ,
                  'email' => $customer->email ,
                  'mobile' => $customer->mobile ,
                  'billing_address_id' => $address->id ?? null,
                  'state_id' => $address->state_id ?? null,
                  'district_id' => $address->district_id ?? null,
                  'municipality_id' => $address->municipality_id ?? null,
                  'state_name' => $state->state_name_np ?? null,
                  'district_name' => $district->district_name_en ?? null,
                  'municipality_name' => $municipality->location_name_en ?? null,
                  'orderList' => $orderList,

                   );
                  $result = array(
                                    'status'        =>true,
                                    'message'       => 'Customer data Fetched !',
                                    'data' =>$data,
                                );
                return response()->json($result,200);
                }else{
                  return response()->json([
                    'status'     => false,
                    'message'    => 'Customer Not found',
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

    public function fetchProduct(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=Product::orderBy('id','desc')->where('status',1)->get();
                foreach ($list as $key => $value) {
                $productImage = ProductImage::where('product_id',$value->id)->where('is_desktop_thumbnail',1)->first();
                if ($productImage) {
                 $finalImage = $productImage->image;
                }else{
                  $finalImage = DEFAULT_IMG;
                }
                    $array[]= array(
                      'id' =>$value->id ,
                      'product_name' =>$value->product_name ,
                      'sku' =>$value->sku ,
                      'listing_price' =>$value->listing_price ,
                      'opening_stock' =>$value->opening_stock ,
                      'status' =>$value->status ,
                      'author_id' => $value->author_id,
                      'publisher_id' => $value->publisher_id,
                      'image' => $finalImage,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Product Data Fetched',
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


    public function offlineRequest(Request $request)
  {
    $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
              $validator=Validator::make($request->all(), [
                // 'name' => 'required_unless:customer_id,',
                // 'email' => 'required_unless:customer_id,integer,email|max:255|unique:users',
                 // 'name' => 'required_unless:customer_id.email',
                 // 'email' => 'required_unless:customer_id,email',
                 // 'mobile' => 'required_unless:customer_id,integer',
            ]);
            if ($validator->passes()) {
              if ($request->customer_id!==null) {
                $customerID = $request->customer_id;
              }else{
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $verifyCode = mt_rand(100, 999). mt_rand(100, 999). $characters[rand(0, strlen($characters) - 1)];
                $customer['name'] = $request->name;
                $customer['email'] = $request->email;
                $customer['country_id'] = $request->country_id;
                $customer['mobile'] = $request->mobile;
                $customer['user_code'] = $verifyCode;
                $customer['status'] = 0;
                $customerResult = Customer::create($customer);
                if ($customerResult) {
                $customerID = $customerResult->id;
                  $address['state_id'] = $request->state_id;
                  $address['municipality_id'] = $request->municipality_id;
                  $address['district_id'] = $request->district_id;
                  $address['address'] = $request->address;
                  $address['user_id'] = $customerID;
                  $billingAddress = BillingAddress::create($address);
                }
              }

               if ($billingAddress) {
                  $billingAddressId = $billingAddress->id;
                  }else{
                  $billingAddressId = $request->billing_address_id;
                  }

            if ($customerID) {
                              // $cartList = array();
                                if (!empty($request->cartList)){
                                $order = new ProductOrder;
                                $order->order_code = 'KY-'.date('y').'-'.date('n').'-'.date('j').'-'.time();
                                $order->user_id = $customerID;
                                $order->status = 0; 
                                if($request->payment_id==1){
                                 $order->order_status = 0;    
                                }else{
                                 $order->order_status = 4;     
                                }

                                //for payment screenshot file
                              if(!empty($request->payment_screenshot_file)){
                                $paymentFile = uploadImageGcloud($request->payment_screenshot_file,'payment-screenshot');
                                $order->payment_screenshot_file = $imagefile;
                              }

                                $order->order_datetime = date('Y-m-d H:i:s');
                                $order->payment_id = $request->payment_id;
                                $order->payment_status = 0;
                                $order->is_foreign_order = $request->is_foreign_order;
                                $order->price = $request->price;
                                $order->delivery_address_id = $billingAddressId;
                                $order->billing_address_id = $billingAddressId;
                                $order->discount = $request->discount;
                                $order->delivery_charge_amt = $request->delivery_charge_amt;
                                $order->final_price = $request->final_price;
                                $order->save();
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
                                            $data->user_id = $customerID;
                                            $data->product_qty = $cart['product_qty'];
                                            $itemamt=$cart['product_qty']*$product->cost;
                                            $data->price = $cart['product_price'];
                                            $data->order_datetime = date('Y-m-d H:i:s');
                                            // $data->order_status = 0;
                                            if($order->payment_id==1){
                                             $data->order_status = 0;    
                                            }else{
                                             $data->order_status = 4;     
                                            }
                                            $data->is_foreign_order = $request->is_foreign_order;
                                            $data->save();
                                            if($data){
                                                $product->decrement('opening_stock', $data->product_qty);
                                            }
                                
                                            // $cart->delete();
                                    }
                                    
                                    $subOrder = ProductOrderList::where('id',$data->id)->first();
                                                if($subOrder){
                                                $data->delivery_charge_amt = $order->delivery_charge_amt;
                                                $data->save();
                                                }
                                                
                                     if ($order->payment_id==1) {
                                       // sendOrderToMobile($customer->mobile,$order->order_code);
                                    }
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
                ], 401);
            }
  }








    public function addressAdd(Request $request)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'address' => 'required',
            ]);
            if ($validator->passes()) {
            $address['title'] = $request->title;
            $address['state_id'] = $request->state_id;
            $address['country_id'] = $request->country_id;
            if ($request->country_id) {
                $address['is_foreign'] = 1;
            }else{
                $address['is_foreign'] = 0;
            }
            $address['municipality_id'] = $request->municipality_id;
            $address['district_id'] = $request->district_id;
            $address['address'] = $request->address;
            $address['user_id'] = $request->customer_id;

            $result = BillingAddress::create($address);
            if ($result) {
                    $data = array(
                        'status'  =>true,
                        'message' =>'Address added Successfully!' ,
                        'data'=> null,
                        );
                    return response()->json($data,200);
                }
                else{
                    return response()->json([
                        'message'   =>'Something went wrong !',
                        'status'    =>false,
                        'data'      =>null,
                    ], 401);
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
                ], 401);
            }
    }


















  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




    public function getOrderList(Request $request)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array = array();
                $list = ProductOrderList::orderBy('id','desc')->get();
                foreach ($list as $key => $value) {
                $productOrder = ProductOrder::where('id',$value->order_code_id)->first();
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
                      'product_id' => $product->id ,
                      'product_sku' => $product->sku ,
                      'product_name' => $product->product_name ,
                      'category_id' => $category->id,
                      'category_name' => $category->title,
                      'sub_category_id' => $subcategory->id,
                      'sub_category_name' => $subcategory->title,
                      'is_gift' => $value->is_gift,
                      'order_status' => $value->order_status,
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


    public function newOrderProcess(Request $request)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array = array();
                $value = ProductOrderList::where('order_status','0')->orderBy('order_datetime','desc')->first();
                if ($value) {
               
                $productOrder = ProductOrder::where('id',$value->order_code_id)->first();
                $paymentMethod = PaymentMethod::where('id',$productOrder->payment_id)->first();
                $customer = Customer::where('id',$value->user_id)->first();
                $product = Product::where('id',$value->product_id)->first();
                $productImage = ProductImage::where('product_id',$product->id)->where('is_desktop_thumbnail',1)->first();
                if ($productImage) {
                 $finalImage = $productImage->image;
                }else{
                  $finalImage = DEFAULT_IMG;
                }

                $relcategory = RelationProductCategory::where('product_id',$product->id)->where('parent_category_id',0)->first();
                $category = Category::where('id',$relcategory->category_id)->first();

                $relsubcategory = RelationProductCategory::where('product_id',$product->id)->where('parent_category_id','!=',0)->first();
                $subcategory = Category::where('id',$relsubcategory->category_id)->first();
                $now = now();

                    $array[]= array(
                      'id' => $value->id ,
                      'order_code' => $productOrder->order_code ,
                      'sub_order_code' => $value->sub_order_code ,
                      'workstation_employee_code' => $workstation->code ,
                      'order_date' => Carbon::parse($value->order_datetime)->toDateString(),
                      'order_time' => Carbon::parse($value->order_datetime)->format('H:i A'),

                      'processing_date' => Carbon::parse($now)->toDateString(),
                      'processing_time' => Carbon::parse($now)->format('H:i A'),

                      'customer_id' => $value->user_id ,
                      'customer_name' => $customer->name ,
                      'customer_mobile' => $customer->mobile ,
                      'product_id' => $product->id ,
                      'product_sku' => $product->sku ,
                      'product_name' => $product->product_name ,
                      'category_id' => $category->id,
                      'category_name' => $category->title,
                      'sub_category_id' => $subcategory->id,
                      'sub_category_name' => $subcategory->title,
                      'is_gift' => $value->is_gift,

                      'payment_method' => $paymentMethod->payment_name,
                      'product_qty' => $value->product_qty,
                      'product_image' => $finalImage,
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
                $value = ProductOrderList::where('id',$id)->first();
                if ($value) {
                $productOrder = ProductOrder::where('id',$value->order_code_id)->first();
                $paymentMethod = PaymentMethod::where('id',$productOrder->payment_id)->first();
                $customer = Customer::where('id',$value->user_id)->first();
                $product = Product::where('id',$value->product_id)->first();
                $productImage = ProductImage::where('product_id',$product->id)->where('is_desktop_thumbnail',1)->first();
                if ($productImage) {
                 $finalImage = $productImage->image;
                }else{
                  $finalImage = DEFAULT_IMG;
                }

                $relcategory = RelationProductCategory::where('product_id',$product->id)->where('parent_category_id',0)->first();
                $category = Category::where('id',$relcategory->category_id)->first();

                $relsubcategory = RelationProductCategory::where('product_id',$product->id)->where('parent_category_id','!=',0)->first();
                $subcategory = Category::where('id',$relsubcategory->category_id)->first();
                $now = now();

                    $array[]= array(
                      'id' => $value->id ,
                      'order_code' => $productOrder->order_code ,
                      'sub_order_code' => $value->sub_order_code ,
                      'workstation_employee_code' => $workstation->code ,
                      'order_date' => Carbon::parse($value->order_datetime)->toDateString(),
                      'order_time' => Carbon::parse($value->order_datetime)->format('H:i A'),

                      'processing_date' => Carbon::parse($now)->toDateString(),
                      'processing_time' => Carbon::parse($now)->format('H:i A'),

                      'customer_id' => $value->user_id ,
                      'customer_name' => $customer->name ,
                      'customer_mobile' => $customer->mobile ,
                      'product_id' => $product->id ,
                      'product_sku' => $product->sku ,
                      'product_name' => $product->product_name ,
                      'category_id' => $category->id,
                      'category_name' => $category->title,
                      'sub_category_id' => $subcategory->id,
                      'sub_category_name' => $subcategory->title,
                      'is_gift' => $value->is_gift,

                      'payment_method' => $paymentMethod->payment_name,
                      'product_qty' => $value->product_qty,
                      'product_image' => $finalImage,
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


    

    public function placeOrderUpdate(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'order_status' => 'required',
            ]);
            if ($validator->passes()) {
            $order = ProductOrderList::find($id);

            if($order){
            $order->order_status = $request->order_status;
            $order->ky_cancel_reason = $request->ky_cancel_reason;
            $order->save();
            
            
            $productOrder = ProductOrderList::where('order_code_id',$order->order_code_id)->get();
            foreach ($productOrder as $key => $po) {
              if ($po->order_status== 1) {
                $productOrderBill = ProductOrderBilling::where('sub_order_code_id',$po->id)->first();
                if(!$productOrderBill){
                  $bill['order_code_id'] = $po->order_code_id;
                  $bill['sub_order_code_id'] = $po->id;
                  $bill['billing_status'] = 0;
                  $bill['transaction_code'] = 'KYT'.time();
                  // $bill['order_code'] = 'KY-'.time();
                //   $bill['bill_no'] = 'KYB'.time();
                  $bill['billing_request_date'] = now();
                  $result = ProductOrderBilling::create($bill);
                }
              }else{

              }
            }
            
            
            // $productOrder = ProductOrderList::where('order_code_id',$order->order_code_id)->get();
            // foreach ($productOrder as $key => $po) {
            //   if ($po->order_status== 1) {
            //     $productOrderBill = ProductOrderBilling::where('sub_order_code_id',$po->id)->first();
            //     if(!$productOrderBill){
            //       $bill['sub_order_code_id'] = $po->id;
            //       $bill['order_code_id'] = $po->order_code_id;
            //       $bill['billing_status'] = 0;
            //       $bill['transaction_code'] = 'KYT'.time();
            //       // $bill['order_code'] = 'KY-'.time();
            //     //   $bill['bill_no'] = 'KYB'.time();
            //       $bill['billing_request_date'] = now();
            //       $result = ProductOrderBilling::create($bill);
            //     }
            //   }else{

            //   }
            // }


            $orderdata= array(
                                'id' =>$order->id ,
                                'order_status' =>$order->order_status ,
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
