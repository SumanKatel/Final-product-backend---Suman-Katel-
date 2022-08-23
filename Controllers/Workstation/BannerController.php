<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Workstation\Banner;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function list(Request $request){
    	$wsId = $request->wsId;
	    $workstation=WorkstationUser::find($wsId);
	    if ($workstation) {
			$banner = Banner::all();
			if($banner){
				$data = array(
                        'status'  		=> true,
                        'message' 		=> 'Banner has Been Listed!' ,
                        'data'			=> $banner,
                        );
                    return response()->json($data,200);
			}else{
				$result = array(
                'status'        => false,
                'message'       => 'Banner not Found',
                'data'    		=> null
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
}
