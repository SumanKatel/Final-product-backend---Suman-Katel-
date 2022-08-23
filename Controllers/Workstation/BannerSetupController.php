<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Workstation\Banner;
use App\Model\Workstation\BannerSetup;
use App\Model\Workstation\Product;
use App\Model\Workstation\WorkstationUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BannerSetupController extends Controller
{

    public function bannerSetupList(Request $request,$id){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
            $banner = Banner::where('id',$id)->first();
            if($banner){
                $array = array();
                $bannerSetups = BannerSetup::where('banner_id',$banner->id)->get();
                foreach ($bannerSetups as $key => $value) {
                     $array[]= array(
                      'id'        => $value->id ,
                      'title' => $value->title ,
                      'banner_id' => $value->banner_id ,
                      'banner_type_id' => $value->banner_type_id ,
                      'banner_id' => $value->banner_id ,
                      'date_from'  => Carbon::parse($value->date_from)->toDateString() ,
                      'date_to'  => Carbon::parse($value->date_to)->toDateString() ,
                      'product_id'    => $value->product_id ,
                      'banner_image'    => $value->banner_image ,
                      'background_image'    => $value->background_image ,
                       );
                }
                $data = array(
                        'status'        => true,
                        'message'       => 'Banner has Been Listed!' ,
                        'data'          => $array,
                        );
                    return response()->json($data,200);
            }else{
                $result = array(
                'status'        => false,
                'message'       => 'Banner not Found',
                'data'          => null
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


    public function bannerSetupDelete(Request $request,$id){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
            $banner = BannerSetup::where('id',$id)->first();
            if($banner){
            $banner->delete();
                $data = array(
                        'status'        => true,
                        'message'       => 'Banner has Been Deleted!' ,
                        );
                    return response()->json($data,200);
            }else{
                $result = array(
                'status'        => false,
                'message'       => 'Banner not Found',
                'data'          => null
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

    public function add(Request $request, $id){
    	$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'banner_type_id' => 'required',
                'date_from' => 'required',
                'date_to' => 'required',
            ]);

            if ($validator->passes()) {
            	$banner_setup =new BannerSetup;
            	$banner_setup->banner_id = $id;
            	$banner_setup->title = $request->title;
            	$banner_setup->date_from = $request->date_from;
            	$banner_setup->date_to = $request->date_to;
            	$banner_setup->banner_type_id = $request->banner_type_id;

            	// product banner
            	if($request->banner_type_id == 0){
            		 $validator=Validator::make($request->all(), [
		                'sku' => 'required',
		            ]);
            		 if ($validator->passes()) {
	            		$product_detail = Product::where('sku', $request->sku)->first();
	            		$banner_setup->product_id = $product_detail->id;
	            		$banner_setup->background_color = $request->background_color;
	            		 if(!empty($request->file('background_image'))){
			            	$banner_setup->background_image = uploadImageGcloud($request->file('background_image'),'banner_setup');
			        	}
		        	}else{
		        		 $result = array(
			                'status'        => false,
			                'message'       => 'Input Field Required',
			                'data'    		=> $validator->errors()
			             );
			             return response()->json($result,422);
		        	}
            	}
            	// general banner
            	if($request->banner_type_id == 1){
            		 $validator=Validator::make($request->all(), [
		            ]);
            		if ($validator->passes()) {
	            		if(!empty($request->file('banner_image'))){
			            	$banner_setup->banner_image = uploadImageGcloud($request->file('banner_image'),'banner_setup');
			        	}
			        	$banner_setup->button_text = $request->button_text;
			        	$banner_setup->button_color = $request->button_color;
			        	$banner_setup->button_link = $request->button_link;
			        }else{
			        	$result = array(
			                'status'        => false,
			                'message'       => 'Input Field Required',
			                'data'    		=> $validator->errors()
			             );
			             return response()->json($result,422);
			        }

            	}
            	$banner_setup->save();
            
            if ($banner_setup) {
                $data = array(
                    'status'  		=> true,
                    'message' 		=> 'Banner Setup added Successfully!' ,
                    'data'			=> $banner_setup,
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
                'data'    		=> $validator->errors()
                );
                return response()->json($result,422);
            }
        }else
        {
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 404);
        }
    }



        public function edit(Request $request, $bannerId, $bannerSetupId)
        {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'banner_type_id' => 'required',
                'date_from' => 'required',
                'date_to' => 'required',
            ]);

            if ($validator->passes()) {
                $banner_setup = BannerSetup::where('id',$bannerSetupId)->first();
                if ($banner_setup) {
                $banner_setup->banner_id = $bannerId;
                $banner_setup->title = $request->title;
                $banner_setup->date_from = $request->date_from;
                $banner_setup->date_to = $request->date_to;
                $banner_setup->banner_type_id = $request->banner_type_id;

                // product banner
                if($request->banner_type_id == 0){
                     $validator=Validator::make($request->all(), [
                        'sku' => 'required',
                    ]);
                     if ($validator->passes()) {
                        $product_detail = product::where('sku', $request->sku)->first();
                        $banner_setup->product_id = $product_detail->id;
                        $banner_setup->background_color = $request->background_color;
                         if(!empty($request->file('background_image'))){
                            $banner_setup->background_image = uploadImageGcloud($request->file('background_image'),'banner_setup');
                        }
                    }else{
                         $result = array(
                            'status'        => false,
                            'message'       => 'Input Field Required',
                            'data'          => $validator->errors()
                         );
                         return response()->json($result,422);
                    }
                }
                // general banner
                if($request->banner_type_id == 1){
                     $validator=Validator::make($request->all(), [
                    ]);
                    if ($validator->passes()) {
                        if(!empty($request->file('banner_image'))){
                            $banner_setup->banner_image = uploadImageGcloud($request->file('banner_image'),'banner_setup');
                        }
                        $banner_setup->button_text = $request->button_text;
                        $banner_setup->button_color = $request->button_color;
                        $banner_setup->button_link = $request->button_link;
                    }else{
                        $result = array(
                            'status'        => false,
                            'message'       => 'Input Field Required',
                            'data'          => $validator->errors()
                         );
                         return response()->json($result,422);
                    }

                }
                $banner_setup->save();
            
            if ($banner_setup) {
                $data = array(
                    'status'        => true,
                    'message'       => 'Banner Setup updated Successfully!' ,
                    'data'          => $banner_setup,
                    );
                return response()->json($data,200);
                }
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
                'data'          => $validator->errors()
                );
                return response()->json($result,422);
            }
        }else
        {
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 404);
        }
    }
}
