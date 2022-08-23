<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Model\Workstation\Customer;
use App\Model\Workstation\PhotoSliderPost;
use App\Model\Workstation\PhotoSliderPostDetail;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductImage;
use App\Model\Workstation\Quiz;
use App\Model\Workstation\QuizAnswer;
use App\Model\Workstation\StoryPost;
use App\Model\Workstation\VideoPost;
use App\Model\mobile\UserQuizRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
	// for quiz
    public function user_quiz_create(Request $request){
    	$customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
        	$today = date('Y-m-d');
            
            
            $already_answer_quiz = UserQuizRecord::select('quiz_id')->where('user_id', $customerId)->get();
           
            $list = Quiz::whereNotIn('id', $already_answer_quiz)
                    ->where('to_date','>=',$today)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
            foreach($list as $list_key => $list_value){
                $answer = QuizAnswer::where('quiz_id', $list_value->id)->get();
                $list[$list_key]->answer = $answer;
            }
            $list = array(
                        'quiz_question_answer'          =>  $list,
                    );
            	
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
                'message'    => 'Customer User Not Found',
                'data'       => null,
            ], 401);
        }
    }

    public function user_quiz_save(Request $request, $quiz_id){
    	$customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
        	$validator=Validator::make($request->all(), [
                'answer_id' 		=> 'required',
            ]);

            $correct_answer = QuizAnswer::where('quiz_id', $quiz_id)
            				->where('id', $request->answer_id)
            				->first();

            if ($validator->passes()) {
            $quized = UserQuizRecord::where('user_id',$customerId)->where('quiz_id'->quiz_id)->first();
            if (!$quized) {
            $quiz = new UserQuizRecord;
            $quiz->user_id = $customerId;
            $quiz->quiz_id = $quiz_id;
            $quiz->answer_id = $request->answer_id;
            $quiz->is_correct = $correct_answer->is_correct; 
            $quiz->point = ($correct_answer->is_correct == 1 ? 10 : 0);
            $quiz->status = 1;
            $quiz->save();

            if ($correct_answer && $quiz) {
                $quiz = array(
                                'id'      		=> $quiz->id,
                                'quiz_id'     	=> $quiz_id ,
                                'answer_id'     => $quiz->answer_id ,
                                'is_correct'    => ($correct_answer->is_correct == 1 ? 'Correct Answer' : 'Wrong Answer') ,
                             );
                 $quiz = array(
                        'quiz'          =>  $quiz,
                    );

                    $data = array(
                        'status'      	=> true,
                        'message'     	=> 'User Quiz Record added Successfully!' ,
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
             return response()->json([
                    'message'   => 'Already Submit Your Answer',
                    'status'    => false,
                    'data'      => null,
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
                'message'    => 'Customer User Not Found',
                'data'       =>null,
            ], 404);
        }

    }

    public function story_list(Request $request){
    	// $customerId = $request->customerId;
     //    $customer=Customer::find($customerId);
     //    if ($customer) {
        	$today = date('Y-m-d H:i:s');
        	// return $current_time = date('H:i:s');
            $list = StoryPost::where(DB::raw("CONCAT(from_date,' ',from_time)"), '<=', $today )
            		->where(DB::raw("CONCAT(to_date,' ',to_time)"), '>=', $today )
            		->orderBy('created_at', 'asc')
            		->take(5)
            		->get();
            $list = array(
                'story_list'          =>  $list,
            );
            $result = array(
                                'status'        => true,
                                'message'       => 'Story Post Data Fetched',
                                'data'      	=>  $list,
                            );

            return response()->json($result,200);
        
        // }
        // else{
        //     return response()->json([
        //         'status'     => false,
        //         'message'    => 'Customer User Not Found',
        //         'data'       => $this->customerId,
        //     ], 401);
        // }
    }


    public function photo_slide_and_video_list(Request $request){
    	// $customerId = $request->customerId;
     //    $customer=Customer::find($customerId);
     // if ($customer) {
        	$today = date('Y-m-d H:i:s');
            $product_image = null;
        	// return $current_time = date('H:i:s');
            $list = DB::table('tbl_photo_slider_post_detail as tpspd')
	            		->where(DB::raw("CONCAT(tpspd.from_date,' ',tpspd.from_time)"), '<=', $today )
	            		->where(DB::raw("CONCAT(tpspd.to_date,' ',tpspd.to_time)"), '>=', $today )
	            		->orderBy('created_at', 'asc')
	            		->get();

	        foreach($list as $key => $value){
                if($value->post_type == 0){
                    $post_image_video = PhotoSliderPost::where('photo_slider_post_detail_id', $value->id)->get();
                    // if($value->is_title == 0){
                        if($value->product_id != null){
                            $product_detail = Product::select('product_name', 'sku','listing_price', 'mrp_paper_book')->where('id', $value->product_id)->first();
                            $product_image = ProductImage::where('product_id',$value->product_id)->first();
                        }else{
                            $product_detail = null;
                        }
                    // }
                }elseif ($value->post_type == 1) {
                    $product_detail = null;
                    $post_image_video = VideoPost::select('id', 'video as image', 'video_thumnail', 'photo_slider_post_detail_id','status')->where('photo_slider_post_detail_id', $value->id)->get();
                }else{
                    $product_detail = null;
                    $post_image_video = VideoPost::select('id', 'youtube_url as image', 'video_thumnail', 'photo_slider_post_detail_id','status')->where('photo_slider_post_detail_id', $value->id)->get();
                }



                /*else{
                    $product_detail = null;
                    $post_image_video = VideoPost::select('id', 'video as image', 'video_thumnail', 'photo_slider_post_detail_id','status')->where('photo_slider_post_detail_id', $value->id)->get();
                }*/
	        	$list[$key]->post_image_video = $post_image_video;
	        	$list[$key]->product_detail = $product_detail;
                $list[$key]->product_image = $product_image;
	        }
            $list = array(
                        'slider_image_video_list'          =>  $list,
                    ); 

            $result = array(
                                'status'        => true,
                                'message'       => 'Photo Slider Post Data Fetched',
                                'data'      	=>  $list,
                            );

            return response()->json($result,200);
        
        // }
        // else{
        //     return response()->json([
        //         'status'     => false,
        //         'message'    => 'Customer User Not Found',
        //         'data'       => $this->customerId,
        //     ], 401);
        // }
    }
}
