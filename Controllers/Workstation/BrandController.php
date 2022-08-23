<?php

namespace App\Http\Controllers\Workstation;
use App\Http\Controllers\Controller;
use App\Model\Workstation\Brand;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;



class BrandController extends Controller
{

    public function brandlist(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=Brand::orderBy('id','desc')->where('is_deleted',0)->get();
                foreach ($list as $key => $value) {
                    $array[]= array(
                      'id' =>$value->id ,
                      'title' =>$value->title, 
                      'slug' =>$value->slug, 
                      'description' =>$value->description, 
                      'status' =>$value->status, 
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Brand Data Fetched',
                                    'data' =>$array,
                                );

                return response()->json($result,200);
            
            }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    } 

    public function brandAdd(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'description' => 'required|max:255',
            ]);
            if ($validator->passes()) {
            $uni['title'] = $request->title;
            $uni['description'] = $request->description;
            $uni['status'] = $request->status;
            $result = Brand::create($uni);
            if ($result) {
                    $data = array(
                        'status'  =>true,
                        'message' =>'Brand added Successfully!' ,
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
                'data'    => $validator->errors()
                );
                return response()->json($result,422);
            }
        }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 404);
            }
    }


    public function brandEdit(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $value=Brand::where('id',$id)->first();
                if($value){
                    $array[]= array(
                      'id' =>$value->id ,
                      'title' =>$value->title, 
                      'description' =>$value->description, 
                      'status' =>$value->status, 
                       );
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Brand Data Fetched',
                                    'data' =>$array,
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'Delivery Partner Not Found',
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

    public function brandUpdate(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'description' => 'required|max:255',
            ]);
            if ($validator->passes()) {
            $uni = Brand::find($id);
            if($uni){
            $uni->title = $request->title;
            $uni->description = $request->description;
            $uni->status = $request->status;
            $uni->save();
                    $data = array(
                        'status'  =>true,
                        'message' =>'Brand updated Successfully!' ,
                        );
                    return response()->json($data,200);
            }else{
                    return response()->json([
                        'message'   =>'Brand Not found !',
                        'status'    =>false,
                        'data'      =>null,
                    ]);
                }
                }else{

                $result = array(
                'status'        => false,
                'message'       => 'Input Field Required',
                'data'    => $validator->errors()
                );
                return response()->json($result,422);
            }
        }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 404);
            }
    }

    public function brandDetele(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $data=Brand::where('id',$id)->first();
                if($data){
                        $data->is_deleted = 1;
                        $data->save();
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Brand Deleted Successfully!',
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'Brand Not Found',
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
}