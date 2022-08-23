<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Workstation\VideoPost;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class VideoPostController extends Controller
{
	public function video_post_list(Request $request){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
            $list=VideoPost::all();
            $result = array(
                                'status'        => true,
                                'message'       => 'Video Post Data Fetched',
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

    public function video_post_add_post(Request $request){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
            'from_date' 		=> 'required',
            'from_time' 		=> 'required',
            'to_date'           => 'required_if:is_no_end_date_time,==,0',
            'to_time'           => 'required_if:is_no_end_date_time,==,0',
            'video_thumnail'	=> 'required',
            'video' 			=> 'required',
            'title' 			=> 'required',
            'sub_title' 		=> 'required',
            'description' 		=> 'required',
        ]);
            if ($validator->passes() ) {
            $video_post = new VideoPost;
            $video_post->from_date = $request->from_date;
            $video_post->from_time = $request->from_time;
            if($request->is_no_end_date_time == 0){
                $video_post->to_date = $request->to_date;
                $video_post->to_time = $request->to_time;
            }else{
                $video_post->to_date = null;
                $video_post->to_time = null;
            }
            $video_post->is_no_end_date_time = $request->is_no_end_date_time;
            $video_thumnail = uploadImageGcloud($request->file('video_thumnail'),'video_post');
            $video_post->video_thumnail = $video_thumnail;
            $video = uploadImageGcloud($request->file('video'),'video_post');
            $video_post->video = $video;
            $video_post->title = $request->title;
            $video_post->sub_title = $request->sub_title;
            $video_post->description = $request->description;
            $video_post->status = $request->status;
            $video_post->save();

            if ($video_post) {
                $video_post = array(
                                'id'      		=> $video_post->id ,
                                'title'     	=> $video_post->title ,
                             );

                    $data = array(
                        'status'      	=> true,
                        'message'     	=> 'Video Post added Successfully!' ,
                        'data'      	=> $video_post,
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
                'status'        				=> false,
                'message'       				=> 'Input Field Required',
                'data'        					=> $validator->errors(),
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

     public function video_post_edit(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
          	$video_post_detail = VideoPost::where('id', $id)->first();
            if($video_post_detail){
                $result = array(
                            'status'                  	=> true,
                            'message'               	=> 'Video Post Data Fetched For Edit',
                            'video_post_detail'       	=> $video_post_detail,
                        );
                return response()->json($result,200);
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Video Post Not Found',
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

    public function video_post_update(Request $request, $id){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
            'from_date' 		=> 'required',
            'from_time' 		=> 'required',
            'to_date'           => 'required_if:is_no_end_date_time,==,0',
            'to_time'           => 'required_if:is_no_end_date_time,==,0',
            'video_thumnail'	=> 'required',
            'video' 			=> 'required',
            'title' 			=> 'required',
            'sub_title' 		=> 'required',
            'description' 		=> 'required',
        ]);
        

            if ($validator->passes() ) {
            $video_post = VideoPost::where('id', $id)->first();
            if ($video_post) {
            	$video_post->from_date = $request->from_date;
	            $video_post->from_time = $request->from_time;
	            if($request->is_no_end_date_time == 0){
	            	$video_post->to_date = $request->to_date;
	            	$video_post->to_time = $request->to_time;
	            }else{
	            	$video_post->to_date = null;
	            	$video_post->to_time = null;
	            }
                $video_post->is_no_end_date_time = $request->is_no_end_date_time;
	            $video_thumnail = uploadImageGcloud($request->file('video_thumnail'),'video_post');
	            $video_post->video_thumnail = $video_thumnail;
	            $video = uploadImageGcloud($request->file('video'),'video_post');
	            $video_post->video = $video;
	            $video_post->title = $request->title;
	            $video_post->sub_title = $request->sub_title;
	            $video_post->description = $request->description;
	            $video_post->status = $request->status;
	            $video_post->save();
                $video_post = array(
                                'id'      		=> $video_post->id ,
                                'title'     	=> $video_post->title ,
                             );

                    $data = array(
                        'status'      	=> true,
                        'message'     	=> 'Video Post updated Successfully!' ,
                        'data'      	=> $video_post,
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
                'status'        			=> false,
                'message'       			=> 'Input Field Required',
                'data'        				=> $validator->errors(),
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

    public function video_post_delete(Request $request, $id){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        	$entered_password = $request->entered_password;
        	if(Hash::check($entered_password, $workstation->password)){
        		$video_post = VideoPost::where('id', $id)->first();
	        	if($video_post){
	        		VideoPost::where('id', $id)->delete();

		        	 $data = array(
		                        'status'      	=> true,
		                        'message'     	=> 'Video Post Deleted Successfully!' ,
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
                    'message'   =>'Wrong Password !',
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
