<?php

namespace App\Http\Controllers\Workstation;
use App\Http\Controllers\Controller;
use App\Model\Workstation\Employer;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;



class EmployerController extends Controller
{

    public function employerlist(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=Employer::orderBy('id','desc')->where('is_deleted',0)->get();
                foreach ($list as $key => $value) {
                    $array[]= array(
                      'id' =>$value->id ,
                      'organization_type' =>$value->organization_type, 
                      'slug' =>$value->slug, 
                      'name' =>$value->name, 
                      'website' =>$value->website, 
                      'status' =>$value->status, 
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Employer Data Fetched',
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

    public function employerAdd(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'organization_type' => 'required|max:255',
                'name' => 'required|max:255',
            ]);
            if ($validator->passes()) {
            $uni['organization_type'] = $request->organization_type;
            $uni['name'] = $request->name;
            $uni['website'] = $request->website;
            $uni['status'] = $request->status;
            $result = Employer::create($uni);
            if ($result) {
                    $data = array(
                        'status'  =>true,
                        'message' =>'Employer added Successfully!' ,
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


    public function employerEdit(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $value=Employer::where('id',$id)->first();
                if($value){
                    $array[]= array(
                      'id' =>$value->id ,
                      'organization_type' =>$value->organization_type, 
                      'slug' =>$value->slug, 
                      'name' =>$value->name, 
                      'website' =>$value->website, 
                      'status' =>$value->status, 
                       );
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Employer Data Fetched',
                                    'data' =>$array,
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'Employer Not Found',
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

    public function EmployerUpdate(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'organization_type' => 'required|max:255',
                'name' => 'required|max:255',
            ]);
            if ($validator->passes()) {
            $uni = Employer::find($id);
            if($uni){
            $uni->organization_type = $request->organization_type;
            $uni->name = $request->name;
            $uni->website = $request->website;
            $uni->status = $request->status;
            $uni->save();
                    $data = array(
                        'status'  =>true,
                        'message' =>'Employer updated Successfully!' ,
                        );
                    return response()->json($data,200);
            }else{
                    return response()->json([
                        'message'   =>'Employer Not found !',
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

    public function EmployerDetele(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $data=Employer::where('id',$id)->first();
                if($data){
                        $data->is_deleted = 1;
                        $data->save();
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Employer Deleted Successfully!',
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'Employer Not Found',
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