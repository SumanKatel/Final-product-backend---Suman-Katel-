<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Workstation\PhotoSliderPost;
use App\Model\Workstation\PhotoSliderPostDetail;
use App\Model\Workstation\Product;
use App\Model\Workstation\VideoPost;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PhotoSliderPostDetailController extends Controller
{
    private $wsId;
  	private $workstation;

    // static value
    // is_title = 1 -> title and subtitle
    // is_title = 0 -> product_sku
    // is_no_end_date_time = 0 -> value need
    // is_no_end_date_time = 1 -> null value
    // is_title = 0 -> sku
    // is_title = 1 -> title and sub_title
    // post_type = 0 -> image
    // post_type = 1 -> video


  	public function photo_slider_post_list(Request $request){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
            $list = PhotoSliderPostDetail::all();
            foreach($list as $list_key => $list_value){
            	$product_detail = Product::where('id', 3)->first();
            	$list[$list_key]->product_name = $product_detail->product_name;
            	$list[$list_key]->sku = $product_detail->sku;
            }
            $result = array(
                                'status'        => true,
                                'message'       => 'Photo Slider Post Detail Data Fetched',
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

    public function photo_slider_post_add_post(Request $request){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
            'from_date' 		=> 'required',
            'from_time' 		=> 'required',
            'to_date' 			=> 'required_if:is_no_end_date_time,==,0',
            'to_time' 			=> 'required_if:is_no_end_date_time,==,0',
            'is_title'          => 'required',
            'post_type'         => 'required',
            // 'desciption'		=> 'required',
            'product_sku' 		=> 'required_if:is_title,==,0',
            'title'             => 'required_if:is_title,==,1',
            'sub_title'         => 'required_if:is_title,==,1',
            'image' 			=> 'required_if:post_type,==,0',
            'video'             => 'required_if:post_type,==,1',
            'video_thumnail'    => 'required_if:post_type,==,1',
            
        ]);

            if ($validator->passes()) {
            $photo_slider_post_detail = new PhotoSliderPostDetail;
            $product_sku = $request->product_sku;
            $product_detail = Product::where('sku', $product_sku)->first();
            $photo_slider_post_detail->from_date = $request->from_date;
            $photo_slider_post_detail->from_time = $request->from_time;
            $photo_slider_post_detail->to_date = $request->to_date;
            $photo_slider_post_detail->to_time = $request->to_time;
            $photo_slider_post_detail->is_no_end_date_time = $request->is_no_end_date_time;
            $photo_slider_post_detail->description = $request->description;
            $photo_slider_post_detail->is_title = $request->is_title;
            $photo_slider_post_detail->post_type = $request->post_type;
            if($product_detail){
                $photo_slider_post_detail->product_id = $product_detail->id;
            }else{
                $photo_slider_post_detail->product_id = null;
            }
            $photo_slider_post_detail->title = $request->title;
            $photo_slider_post_detail->sub_title = $request->sub_title;
            $photo_slider_post_detail->status = $request->status;
            $photo_slider_post_detail->save();
            $image = '';
    	   if($request->post_type == 0){
                 $image_sort = $request->image_sort;
                if(!empty($request->file('image'))){
                    $sort_order=1;
                    foreach ($request->file('image') as $key => $image_post) {
                        $image = new PhotoSliderPost;
                        $image->photo_slider_post_detail_id = $photo_slider_post_detail->id;
                        $imagefile = uploadImageGcloud($image_post,'photo_slider_post');
                        $image->image = $imagefile;
                        $image->status = 1;
                        $image->image_sort = $image_sort[$key];
                        $image->save();
                    }
                }
           }else{
                $video_post = new VideoPost;
                $video_post->photo_slider_post_detail_id = $photo_slider_post_detail->id;
                $video_thumnail = uploadImageGcloud($request->file('video_thumnail'),'video_post');
                $video_post->video_thumnail = $video_thumnail;
                $video = uploadImageGcloud($request->file('video'),'video_post');
                $video_post->video = $video;
                $video_post->youtube_url = $request->youtube_url;
                $video_post->status = 1;
                $video_post->save();
           }
           

            if ($photo_slider_post_detail) {
                if($image != null){
                    $message = 'Image Slider';
                }else{
                    $message = 'video';
                }
                $photo_slider_post_detail = array(
                                'id'      			=> $photo_slider_post_detail->id ,
                                'desciption'     	=> $photo_slider_post_detail->desciption ,
                                'product_sku'     	=> $product_sku ,
                             );

                    $data = array(
                        'status'      	=> true,
                        'message'     	=> $message.'Post Detail added Successfully!' ,
                        'data'      	=> $photo_slider_post_detail,
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

    public function photo_slider_post_edit(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
          $photo_slider_post_detail = PhotoSliderPostDetail::where('id', $id)->first();
          $photo_slider_post = PhotoSliderPost::where('photo_slider_post_detail_id', $id)->get();
            if($photo_slider_post_detail){
                $result = array(
                            'status'                  		=> true,
                            'message'               		=> 'Photo Slider Post Detail Data Fetched For Edit',
                            'photo_slider_post_detail'      => $photo_slider_post_detail,
                            'photo_slider_post'      		=> $photo_slider_post,
                        );
                return response()->json($result,200);
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Photo Slider Post Detail Data Not Found',
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

    public function photo_slider_post_update(Request $request, $id){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'from_date' 		=> 'required',
                'from_time' 		=> 'required',
                'to_date'           => 'required_if:is_no_end_date_time,==,0',
                'to_time'           => 'required_if:is_no_end_date_time,==,0',
                // 'desciption'		=> 'required',
                'is_title'          => 'required',
                'post_type'          => 'required',
                'product_sku'       => 'required_if:is_title,==,0',
                'title'             => 'required_if:is_title,==,1',
                'sub_title'         => 'required_if:is_title,==,1',
                'image'             => 'required_if:post_type,==,0',
                'video'             => 'required_if:post_type,==,1',
                'video_thumnail'    => 'required_if:post_type,==,1',
            ]);

            if ($validator->passes()) {

            $photo_slider_post_detail = PhotoSliderPostDetail::where('id', $id)->first();
            if($photo_slider_post_detail){
                if($photo_slider_post_detail->post_type == 0){
            	   $photo_slider_post = PhotoSliderPost::where('photo_slider_post_detail_id', $id)
            						  ->delete();
                }elseif($photo_slider_post_detail->post_type == 1){
                    $photo_slider_post = VideoPost::where('photo_slider_post_detail_id', $id)
                                      ->delete();
                }
            }
            $product_sku = $request->product_sku;
            $product_detail = Product::where('sku', $product_sku)->first();
            $photo_slider_post_detail->from_date = $request->from_date;
            $photo_slider_post_detail->from_time = $request->from_time;
            $photo_slider_post_detail->to_date = $request->to_date;
            $photo_slider_post_detail->to_time = $request->to_time;
            $photo_slider_post_detail->description = $request->description;
            if($product_detail){
                $photo_slider_post_detail->product_id = $product_detail->id;
            }else{
                $photo_slider_post_detail->product_id = null;
            }
            $photo_slider_post_detail->is_title = $request->is_title;
            $photo_slider_post_detail->post_type = $request->post_type;
            $photo_slider_post_detail->title = $request->title;
            $photo_slider_post_detail->sub_title = $request->sub_title;
            $photo_slider_post_detail->status = $request->status;
            $photo_slider_post_detail->save();
            $image = '';
            
            if($request->post_type == 0){
              $image_sort = $request->image_sort;
                if(!empty($request->file('image'))){
                    $sort_order=1;
                    foreach ($request->file('image') as $key => $image_post) {

                        $image = new PhotoSliderPost;
                        $image->photo_slider_post_detail_id = $photo_slider_post_detail->id;
                        $imagefile = uploadImageGcloud($image_post,'photo_slider_post');
                        $image->image = $imagefile;
                        $image->status = 1;
                        if(isset($image_sort[$key])){
                            $image->image_sort = $image_sort[$key];
                        }
                        $image->save();
                    }
                }
            }else{
                $video_post = new VideoPost;
                $video_thumnail = uploadImageGcloud($request->file('video_thumnail'),'video_post');
                $video_post->video_thumnail = $video_thumnail;
                $video = uploadImageGcloud($request->file('video'),'video_post');
                $video_post->video = $video;
                $video_post->status = 1;
                $video_post->save();
            }
          
            if ($photo_slider_post_detail ) {
                if($image != null){
                    $message = 'Image Slider';
                }else{
                    $message = 'Video';
                }
                $photo_slider_post_detail = array(
                                'id'      			=> $photo_slider_post_detail->id ,
                                'desciption'     	=> $photo_slider_post_detail->desciption ,
                                'product_sku'     	=> $product_sku ,
                             );

                    $data = array(
                        'status'      	=> true,
                        'message'     	=> $message . 'Post Detail updated Successfully!' ,
                        'data'      	=> $photo_slider_post_detail,
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

    public function photo_slider_post_delete(Request $request, $id){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        	$entered_password = $request->entered_password;
        	if(Hash::check($entered_password, $workstation->password)){
        		$photo_slider_post_detail = PhotoSliderPostDetail::where('id', $id)->first();
        		if($photo_slider_post_detail){
	        		PhotoSliderPostDetail::where('id', $id)->delete();
		        	PhotoSliderPost::where('photo_slider_post_detail_id', $id)->delete();

		        	 $data = array(
		                        'status'      	=> true,
		                        'message'     	=> 'Photo Slider Post Deleted Successfully!' ,
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
