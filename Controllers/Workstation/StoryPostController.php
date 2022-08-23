<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Workstation\StoryPost;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StoryPostController extends Controller
{
	 public function story_post_list(Request $request){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
            $list=StoryPost::all();
            $result = array(
                                'status'        => true,
                                'message'       => 'Story Post Data Fetched',
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

    public function story_post_add_post(Request $request){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'from_date' 		=> 'required',
                'from_time' 		=> 'required',
                'image'				=> 'required',
                'title' 			=> 'required',
                'description' 		=> 'required',
            ]);

            if ($validator->passes()) {
            $story_post = new StoryPost;
            $story_post->from_date = $request->from_date;
            $story_post->from_time = $request->from_time;
            $end_datetime = date('Y-m-d H:i:s', strtotime($request->from_date.$request->from_time. ' + 23 hours 59 minutes'));
            $story_post->to_date = date('Y-m-d', strtotime($end_datetime));
            $story_post->to_time = date('H:i:s', strtotime($end_datetime));
            // $story_post->to_date = '2021-12-30';
            // $story_post->to_time = '24:00:00';
            $imagefile = uploadImageGcloud($request->file('image'),'story_post');
            $story_post->image = $imagefile;
            $story_post->title = $request->title;
            $story_post->description = $request->description;
            $story_post->status = $request->status;
            $story_post->save();

            if ($story_post) {
                $story_post = array(
                                'id'      		=> $story_post->id ,
                                'title'     	=> $story_post->title ,
                             );

                    $data = array(
                        'status'      	=> true,
                        'message'     	=> 'Story Post added Successfully!' ,
                        'data'      	=> $story_post,
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
                'data'        	=> $validator->errors()
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

    public function story_post_edit(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
          $story_post_detail = StoryPost::where('id', $id)->first();
            if($story_post_detail){
                $result = array(
                            'status'                  	=> true,
                            'message'               	=> 'Story Post Data Fetched For Edit',
                            'story_post_detail'       	=> $story_post_detail,
                        );
                return response()->json($result,200);
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Story Post Not Found',
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
    public function story_post_update(Request $request, $id){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'from_date' 		=> 'required',
                'from_time' 		=> 'required',
                'image'				=> 'required',
                'title' 			=> 'required',
                'description' 		=> 'required',
            ]);

            if ($validator->passes()) {
            $story_post = StoryPost::where('id', $id)->first();
            if($story_post)
            {
                $story_post->from_date = $request->from_date;
                $story_post->from_time = $request->from_time;
                $end_datetime = date('Y-m-d H:i:s', strtotime($request->from_date.$request->from_time. ' + 23 hours 59 minutes'));
                $story_post->to_date = date('Y-m-d', strtotime($end_datetime));
                $story_post->to_time = date('H:i:s', strtotime($end_datetime));
                $image = $request->file('image')->getClientOriginalName();
                $imagefile = uploadImageGcloud($request->file('image'),'story_post');
                $story_post->image = $imagefile;
                $story_post->title = $request->title;
                $story_post->description = $request->description;
                $story_post->status = $request->status;
                $story_post->save();
           
                $story_post = array(
                                'id'      		=> $story_post->id ,
                                'title'     	=> $story_post->title ,
                             );

                    $data = array(
                        'status'      	=> true,
                        'message'     	=> 'Story Post updated Successfully!' ,
                        'data'      	=> $story_post,
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
                'data'        	=> $validator->errors()
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
    public function story_post_delete(Request $request, $id){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        	$entered_password = $request->password;
        	if(Hash::check($entered_password, $workstation->password)){
        		$story_post = StoryPost::where('id', $id)->first();
	        	if($story_post){
	        		StoryPost::where('id', $id)->delete();

		        	 $data = array(
		                        'status'      	=> true,
		                        'message'     	=> 'Story Post Deleted Successfully!' ,
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
