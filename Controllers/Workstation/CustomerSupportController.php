<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Front\ProductCart;
use App\Model\Workstation\BillingAddress;
use App\Model\Workstation\Customer;
use App\Model\Workstation\District;
use App\Model\Workstation\Municipality;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductOrder;
use App\Model\Workstation\ProductOrderList;
use App\Model\Workstation\State;
use App\Model\Workstation\SupportAction;
use App\Model\Workstation\WorkstationUser;
use App\Model\admin\CreditStore;
use App\Model\admin\CustomerSupport;
use App\Model\admin\CustomerSupportFile;
use App\Model\admin\CustomerSupportReply;
use App\Model\admin\CustomerSupportReplyFile;
use App\Model\admin\Medium;
use App\Model\admin\SupportType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerSupportController extends Controller
{

      public function supportPost(Request $request){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
            $validator=Validator::make($request->all(), [
                'customer_id'               => 'required',
                'customer_support_type_id'  => 'required',
                'product_order_id'          => 'required',
                'product_sub_order_id'      => 'required',
                'medium_id'                 => 'required',
                'remarks'                   => 'required',
                'description'               => 'required',
                ]);
                if ($validator->passes()) {
                    $support['customer_id'] = $request->customer_id;
                    $support['support_code'] = time();
                    $support['customer_support_type_id'] = $request->customer_support_type_id;
                    $support['product_order_id'] = $request->product_order_id;
                    $support['product_sub_order_id'] = $request->product_sub_order_id;
                    $support['medium_id'] = $request->medium_id;
                    $support['remarks'] = $request->remarks;
                    $support['description'] = $request->description;
                    $support['status'] = 0;
                    $support['datetime'] = date('Y-m-d H:i:s');
                    $supportResult = CustomerSupport::create($support);

                    if ($supportResult) {
                        if(!empty($request->file('attachFiles'))){
                            foreach ($request->file('attachFiles') as $key => $file) {
                                if ($file) {
                                    $imagefile = uploadImageGcloud($file,'customer-support-file');
                                    $attachFile['customer_support_id'] = $supportResult->id;
                                    $attachFile['file'] = $imagefile;
                                    $attachResult=CustomerSupportFile::create($attachFile);
                                }
                            }
                        }
                    }

                    $result = array(
                            'status'        =>true,
                            'message'       => 'Customer Support Added Successfully',
                        );
                return response()->json($result,200);
                
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
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }

    }



public function supportList(Request $request){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
                $array = array();
                $customerSupports = CustomerSupport::get();
                    foreach ($customerSupports as $key => $customerSupport) {
                       $customer = Customer::where('id',$customerSupport->customer_id)->first();
                       $attachFiles = CustomerSupportFile::where('customer_support_id',$customerSupport->id)->get();
                       $supportType = SupportType::where('id',$customerSupport->customer_support_type_id)->first();
                       $medium = Medium::where('id',$customerSupport->medium_id)->first();

                          if ($customerSupport->status==0) {
                              $status = 'Open';
                              }else{
                              $status = 'Closed';
                              }

                        $array[]= array(
                          'id' => $customerSupport->id ,
                          'support_code' => $customerSupport->support_code ,
                          'medium' => $medium->title ?? null ,
                          'support_title' => $supportType->title ,
                          'remarks'        => $customerSupport->remarks,
                          'description'        => $customerSupport->description,
                          'status'  => $status,
                          'support_date' => Carbon::parse($customerSupport->datetime)->toDateString() ,
                          'support_time' => date('H:i A', strtotime($customerSupport->datetime)) ,
                          'customer_code' => $customer->user_code,
                          'customer_name' => $customer->name,
                          'customer_mobile' => $customer->mobile,
                          'status'        => $customerSupport->status,
                          'attachFiles'   => $attachFiles,
                           );

                    }
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Customer Support List',
                            'data'          => $array,
                        );
                    return response()->json($result,200);
                     
            }else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 401);
        }
    }

    public function supportActionList(Request $request){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
                $array = array();
                $list = SupportAction::get();
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Customer Support Action List',
                            'data'          => $list,
                        );
                    return response()->json($result,200);
                     
            }else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 401);
        }
    }

    public function SingleSupportProcess(Request $request,$supportId){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
                $array = array();
                $customerSupport = CustomerSupport::find($supportId);
                   $customer = Customer::where('id',$customerSupport->customer_id)->first();
                   $attachFiles = CustomerSupportFile::where('customer_support_id',$customerSupport->id)->get();
                   $supportType = SupportType::where('id',$customerSupport->customer_support_type_id)->first();
                   $medium = Medium::where('id',$customerSupport->medium_id)->first();

                   $address = BillingAddress::where('user_id',$customer->id)->first();
                   $state = State::where('id',$address->state_id)->first();
                   $district = District::where('id',$address->district_id)->first();
                   $municipality = Municipality::where('id',$address->municipality_id)->first();

                   $order = ProductOrder::where('id',$customerSupport->product_order_id)->first();
                   $subOrder = ProductOrderList::where('id',$customerSupport->product_sub_order_id)->first();
                   $replyData =array();

                    $customerSupportsReply = CustomerSupportReply::where('customer_support_id',$customerSupport->id)->get();
                    foreach ($customerSupportsReply as $key => $customerSupport) {
                        $action = SupportAction::where('id',$customerSupport->action_status)->first();
                        $attachFiles = CustomerSupportReplyFile::where('customer_support_reply_id',$customerSupport->id)->get();
                          if ($customerSupport->customer_id!==null) {
                              $reply_by = 'You';
                              }else{
                              $reply_by = 'Kitab Yatra';
                              }

                        $replyData[]= array(
                          'id' => $customerSupport->id ,
                          'reply' => $customerSupport->reply ,
                          'reply_by'  => $reply_by ?? null,
                          'reply_date' => Carbon::parse($customerSupport->reply_datetime)->toDateString() ,
                          'reply_time' => date('H:i A', strtotime($customerSupport->reply_datetime)) ,
                          'action_status' => $action->title,
                          'attachFiles'   => $attachFiles,
                           );
                        }
                          if ($customerSupport->status==0) {
                              $status = 'Open';
                              }else{
                              $status = 'Closed';
                              }

                        $array[]= array(
                          'id' => $customerSupport->id ,
                          'support_code' => $customerSupport->support_code ,
                          'medium' => $medium->title ?? null ,
                          'support_title' => $supportType->title ,
                          'remarks'        => $customerSupport->remarks,
                          'status'  => $status,
                          'support_date' => Carbon::parse($customerSupport->datetime)->toDateString() ,
                          'support_time' => date('H:i A', strtotime($customerSupport->datetime)) ,
                          'customer_code' => $customer->user_code,
                          'customer_name' => $customer->name,
                          'customer_email' => $customer->email,
                          'customer_mobile' => $customer->mobile,
                          'status'        => $customerSupport->status,
                          'state_name' => $state->state_name_np ?? null,
                          'district_name' => $district->district_name_en ?? null,
                          'municipality_name' => $municipality->location_name_en ?? null,
                          'order_sku'   => $order->order_code,
                          'sub_order_sku'   => $subOrder->sub_order_code,
                          'attachFiles'   => $attachFiles,
                          'replyData'      => $replyData,
                           );
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Customer Support List',
                            'data'          => $array,
                        );
                    return response()->json($result,200);
                     
            }else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 401);
        }
    }

    public function supportReplyPost(Request $request){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
            $validator=Validator::make($request->all(), [
                'customer_support_id' => 'required',
                'reply'               => 'required',
                ]);
                if ($validator->passes()) {
                    $support['customer_support_id'] = $request->customer_support_id;
                    $support['reply'] = $request->reply;
                    $support['ky_user_id'] = $workstation->id;
                    $support['status'] = 0;
                    $support['action_status'] = $request->action_status;
                    $support['reply_datetime'] = date('Y-m-d H:i:s');
                    $supportResult = CustomerSupportReply::create($support);

                    if ($supportResult) {
                        if(!empty($request->file('attachFiles'))){
                            foreach ($request->file('attachFiles') as $key => $file) {
                                if ($file) {
                                    $imagefile = uploadImageGcloud($file,'customer-support-file');
                                    $attachFile['customer_support_reply_id'] = $supportResult->id;
                                    $attachFile['file'] = $imagefile;
                                    $attachResult=CustomerSupportReplyFile::create($attachFile);
                                }
                            }
                        }
                    }
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Customer Support Reply Added Successfully',
                        );
                    return response()->json($result,200);
                
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
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 401);
            }

    }


    public function supportReplyList(Request $request,$supportId){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
            if ($customer) {
                $array = array();
                $customerSupportsReply = CustomerSupportReply::where('customer_support_id',$supportId)->get();
                    foreach ($customerSupportsReply as $key => $customerSupport) {
                       $attachFiles = CustomerSupportReplyFile::where('customer_support_reply_id',$customerSupport->id)->get();
                          if ($customerSupport->customer_id!==null) {
                              $reply_by = 'You';
                              }else{
                              $reply_by = 'Kitab Yatra';
                              }

                        $array[]= array(
                          'id' => $customerSupport->id ,
                          'reply' => $customerSupport->reply ,
                          'reply_by'  => $reply_by,
                          'reply_date' => Carbon::parse($customerSupport->reply_datetime)->toDateString() ,
                          'reply_time' => date('H:i A', strtotime($customerSupport->reply_datetime)) ,
                          'attachFiles'   => $attachFiles,
                           );

                    }
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Customer Support Reply List',
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

    


    public function getTotalCredit(Request $request){
      $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
            $credits = CreditStore::get();
            // $totalCost = $credits->sum('balance');
            $array=array();
            foreach ($credits as $key => $credit) {
                $complain = CustomerSupport::where('id',$credit->complain_id)->first();
                $workstationUser = WorkstationUser::where('id',$credit->added_by)->first();
                $customer = Customer::where('id',$credit->customer_id)->first();

             $array[] = array(
                'id'=>$credit->id,
                'balance'=>$credit->balance,
                'description'=>$credit->description,
                'added_date'=>$credit->added_date,
                'complain_code'=>$complain->support_code,
                'complain_date'=>$complain->created_at,
                'added_by'=>$workstationUser->code,
                'credit_status'=>$credit->status,
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
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 401);
            }

    }




}
