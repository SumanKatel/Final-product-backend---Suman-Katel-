<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Workstation\OfferPlatform;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;

class OfferPlatformController extends Controller
{
   	public function offer_platform_list(Request $request){
   		$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
    	  if ($workstation) {
                $array=array();
                $list=OfferPlatform::get();
                $result = array(
                                    'status'        => true,
                                    'message'       => 'Offer Platform Data Fetched',
                                    'data' 			=>  $list,
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
}
