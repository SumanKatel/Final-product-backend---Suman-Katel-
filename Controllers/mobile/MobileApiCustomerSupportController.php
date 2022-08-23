<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Model\Workstation\Customer;
use App\Model\Workstation\WorkstationUser;
use App\Model\admin\CreditStore;
use App\Model\admin\CustomerSupport;
use App\Model\admin\CustomerSupportFile;
use App\Model\admin\CustomerSupportReply;
use App\Model\admin\CustomerSupportReplyFile;
use App\Model\admin\Medium;
use App\Model\admin\SupportType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class MobileApiCustomerSupportController extends Controller
{
    public function getSupportType(Request $request)
    {
         $array=array();
         $teams=SupportType::where('status',1)->orderBy('created_at','asc')->get();
         foreach ($teams as $key => $value) {
         $path = asset($value->icon);
         $array[] = array(
            'id'=>$value->id,
            'imagepath' =>$path,
            'title'=>$value->title,
            'slug'=>$value->slug,
            'description'=>$value->description,
          );
         }

         $array = array(
         			'support_type' => $array,
         		);

         $result = array(
                            'status'        =>true,
                            'message'       => 'Data Fetched',
                            'data' =>$array,
                        );
        return response()->json($result,200);
    }



    public function getMedium()
    {
        $array=array();
        $list=Medium::where('status',1)->get();
        foreach ($list as $key => $value) {
            $array[]= array(
              'id' =>$value->id ,
              'title' =>$value->title ,
               );
        }
        $array = array(
        			'get_medium' => $array,
        		);
        $result = array(
                            'status'        =>true,
                            'message'       => 'Data Fetched',
                            'data' =>$array,
                        );

        return response()->json($result,200);
    }


    public function supportPost(Request $request){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
            if ($customer) {
            $validator=Validator::make($request->all(), [
                'customer_support_type_id'  => 'required',
                'product_order_id'          => 'required',
                'product_sub_order_id'      => 'required',
                'remarks'                   => 'required',
                ]);
                if ($validator->passes()) {
                    $support['customer_id'] = $customer->id;
                    $support['support_code'] = time();
                    $support['customer_support_type_id'] = $request->customer_support_type_id;
                    $support['product_order_id'] = $request->product_order_id;
                    $support['product_sub_order_id'] = $request->product_sub_order_id;
                    // $support['medium_id'] = $request->medium_id;
                    $support['remarks'] = $request->remarks;
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
            }else
            {
                return response()->json([
                    'status'     => false,
                    'message'    => 'User Not Found',
                    'data'       =>null,
                ], 404);
            }

    }

    public function supportReplyPost(Request $request){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
            if ($customer) {
            $validator=Validator::make($request->all(), [
                'customer_support_id' => 'required',
                'reply'               => 'required',
                ]);
                if ($validator->passes()) {
                    $support['customer_id'] = $customer->id;
                    $support['customer_support_id'] = $request->customer_support_id;
                    $support['reply'] = $request->reply;
                    $support['status'] = 0;
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
                    'message'    => 'User Not Found',
                    'data'       =>null,
                ], 404);
            }

    }


    public function supportList(Request $request){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
            if ($customer) {
                $array = array();
                $customerSupports = CustomerSupport::where('customer_id',$customer->id)->get();
                    foreach ($customerSupports as $key => $customerSupport) {
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
                          'status'  => $status,
                          'support_date' => Carbon::parse($customerSupport->datetime)->toDateString() ,
                          'support_time' => date('H:i A', strtotime($customerSupport->datetime)) ,
                          'attachFiles'   => $attachFiles,
                           );

                    }
                    $array = array(
                                'support_list' => $array
                            );
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Customer Support List',
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
                    $array = array(
                                'support_reply_list' => $array,
                            );
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
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            $credits = CreditStore::where('customer_id',$customer->id)->get();
            // $credits = CreditStore::where('customer_id',65)->get();
            $totalCost = $credits->sum('balance');
            $array=array();
            foreach ($credits as $key => $credit) {
                $complain = CustomerSupport::where('id',$credit->complain_id)->first();
                $workstationUser = WorkstationUser::where('id',$credit->added_by)->first();

                $array[] = array(
                    'id'=>$credit->id,
                    'balance'=>$credit->balance,
                    'description'=>$credit->description,
                    'added_date'=>$credit->added_date,
                    'complain_code'=>$complain->support_code,
                    'added_by'=>$workstationUser->code,
                );
             }
             $array = array(
                        'store_credit' => $array,
                        );
             $result = array(
                                'status'        => true,
                                'message'       => 'Data Fetched',
                                'totalCost'     => $totalCost,
                                'data'          => $array,
                            );
            return response()->json($result,200);
        }else{
            return response()->json([
                'status'     => false,
                'message'    => 'User Not Found',
                'data'       =>null,
            ], 404);
        }
    }
}
