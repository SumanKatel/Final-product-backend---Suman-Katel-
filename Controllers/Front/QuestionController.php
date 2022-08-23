<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Model\Front\Coupon;
use App\Model\Front\ProductCart;
use App\Model\Front\ProductQuestion;
use App\Model\Front\ProductQuestionReply;
use App\Model\Workstation\Customer;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductOrder;
use App\Model\Workstation\ProductOrderList;
use App\Model\Workstation\WorkstationUser;
use App\Model\admin\CreditStore;
use App\Model\admin\CustomerSupport;
use App\Model\admin\CustomerSupportFile;
use App\Model\admin\Medium;
use App\Model\admin\SupportType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{

    public function addQuestion(Request $request)
    {
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
            if ($customer) {
            $validator=Validator::make($request->all(), [
                'question'                  => 'required',
                'product_id'                => 'required',
            ]);

            if ($validator->passes()) {
                $question = new ProductQuestion();
                $question->product_id = $request->product_id;
                $question->customer_id = $customer->id;
                $question->question = $request->question;
                $question->status = 1;
                $question->save();

            if ($question) {
                    $data = array(
                        'status'        => true,
                        'message'       => 'Question added Successfully!' ,
                        );
                    return response()->json($data,200);
                }
            else{
                    return response()->json([
                        'message'   =>'Something went wrong !',
                        'status'    =>false,
                        'data'      =>null,
                    ]);
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


    public function customerQuestionReply(Request $request,$id)
    {

        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
            if ($customer) {
            $validator=Validator::make($request->all(), [
                'ques_ans'                  => 'required',
            ]);

            if ($validator->passes()) {
                $question = ProductQuestion::where('id',$id)->first();
                $reply = new ProductQuestionReply();
                $reply->customer_id = $customer->id;
                $reply->question_id = $question->id;
                $reply->ques_ans = $request->ques_ans;
                $reply->status = 0;
                $reply->instered_datetime = date('Y-m-d H:i:s');
                $reply->save();

            if ($reply) {
                    $data = array(
                        'status'        => true,
                        'message'       => 'Question Reply added Successfully!' ,
                        );
                    return response()->json($data,200);
                }
            else{
                    return response()->json([
                        'message'   =>'Something went wrong !',
                        'status'    =>false,
                        'data'      =>null,
                    ]);
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
