<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Workstation\Quiz;
use App\Model\Workstation\QuizAnswer;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
  private $wsId;
  private $workstation;

    public function quiz_list(Request $request){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
            $list=Quiz::all();
            $result = array(
                                'status'        => true,
                                'message'       => 'Quiz Data Fetched',
                                'data'      	=>  $list,
                            );

            return response()->json($result,200);
        
        }
        else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       => $this->wsId,
            ], 401);
        }
    }

    public function quiz_add_post(Request $request){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'from_date' 		=> 'required',
                'from_time' 		=> 'required',
                'to_date' 			=> 'required',
                'to_time' 			=> 'required',
                'question'			=> 'required',
                'answer' 			=> 'required',
                'is_correct' 		=> 'required',
            ]);

            if ($validator->passes()) {
            $quiz = new Quiz;
            $quiz->from_date = $request->from_date;
            $quiz->from_time = $request->from_time;
            $quiz->to_date = $request->to_date;
            $quiz->to_time = $request->to_time;
            $quiz->question = $request->question;
            $quiz->status = $request->status;
            $quiz->save();

            
            $is_correct = $request->is_correct;
            $answers = $request->answer;

            for($i=0; $i<count($is_correct); $i++){
            	$answer = new QuizAnswer;
            	$answer->quiz_id = $quiz->id;
            	$answer->is_correct = $is_correct[$i];
            	$answer->answer = $answers[$i];
            	$answer->status = 1;
            	$answer->save();
            }

            if ($quiz && $answer) {
                $quiz = array(
                                'id'      		=> $quiz->id ,
                                'question'     	=> $quiz->question ,
                             );

                    $data = array(
                        'status'      	=> true,
                        'message'     	=> 'Quiz added Successfully!' ,
                        'data'      	=> $quiz,
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
        }else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 404);
        }
    }

    public function quiz_edit(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
          $quiz_detail = Quiz::where('id', $id)->first();
          $answer_detail = QuizAnswer::where('quiz_id', $id)->get();
            if($quiz_detail){
                $result = array(
                            'status'                  	=> true,
                            'message'               	=> 'Quiz Data Fetched For Edit',
                            'quiz_detail'       		=> $quiz_detail,
                            'answer_detail'      		=> $answer_detail,
                        );
                return response()->json($result,200);
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Quiz Not Found',
                    'data'       =>null,
                ], 401);
            }
        }else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 401);
        }
    }

    public function quiz_update(Request $request, $id){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'from_date' 		=> 'required',
                'from_time' 		=> 'required',
                'to_date' 			=> 'required',
                'to_time' 			=> 'required',
                'question'			=> 'required',
                'answer' 			=> 'required',
                'is_correct' 		=> 'required',
            ]);

            if ($validator->passes()) {
            $quiz =  Quiz::findOrFail($id);

            if($quiz){
            	QuizAnswer::where('quiz_id', $id)->delete();
            }

            $quiz->from_date = $request->from_date;
            $quiz->from_time = $request->from_time;
            $quiz->to_date = $request->to_date;
            $quiz->to_time = $request->to_time;
            $quiz->question = $request->question;
            $quiz->status = $request->status;
            $quiz->save();
            
            $is_correct = $request->is_correct;
            $answers = $request->answer;

            for($i=0; $i<count($is_correct); $i++){
            	$answer = new QuizAnswer;
            	$answer->quiz_id = $quiz->id;
            	$answer->is_correct = $is_correct[$i];
            	$answer->answer = $answers[$i];
            	$answer->status = 1;
            	$answer->save();
            }

            if ($quiz && $answer) {
                $quiz = array(
                                'id'      		=> $quiz->id ,
                                'question'     	=> $quiz->question ,
                             );

                    $data = array(
                        'status'      	=> true,
                        'message'     	=> 'Quiz Updated Successfully!' ,
                        'data'      	=> $quiz,
                        );
                    return response()->json($data,200);
            }else{
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
        }else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 404);
        }
    }

    public function quiz_delete(Request $request, $id){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        	$quiz = Quiz::where('id', $id)->first();
        	if($quiz){
        		Quiz::where('id', $id)->update(['status' => 0]);
	        	QuizAnswer::where('quiz_id', $id)->update(['status' => 0]);

	        	 $data = array(
	                        'status'      	=> true,
	                        'message'     	=> 'Quiz Deleted Successfully!' ,
	                        // 'data'      	=> $quiz,
	                        );
	            return response()->json($data,200);
        	}else{
                    return response()->json([
                        'message'   =>'Something went wrong !',
                        'status'    =>false,
                        'data'      =>null,
                    ]);
              }
        }else{
        	 return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 404);
        }
    }
}
