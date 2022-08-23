<?php

namespace App\Http\Controllers\Workstation;
use App\Http\Controllers\Controller;
use App\Model\Workstation\University;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;



class UniversityController extends Controller
{

    public function universitylist(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=University::orderBy('id','desc')->where('is_deleted',0)->get();
                foreach ($list as $key => $value) {
                    $array[]= array(
                      'id' =>$value->id ,
                      'name' =>$value->name, 
                      'acronym' =>$value->acronym, 
                      'status' =>$value->status, 
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'University Data Fetched',
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

    public function universityAdd(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'name' => 'required|max:255',
                'acronym' => 'required|max:255',
            ]);
            if ($validator->passes()) {
            $uni['name'] = $request->name;
            $uni['acronym'] = $request->acronym;
            $uni['status'] = $request->status;
            $result = University::create($uni);
            if ($result) {
                    $data = array(
                        'status'  =>true,
                        'message' =>'University added Successfully!' ,
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


    public function universityEdit(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $value=University::where('id',$id)->first();
                if($value){
                    $array[]= array(
                      'id' =>$value->id ,
                      'name' =>$value->name, 
                      'acronym' =>$value->acronym, 
                      'status' =>$value->status, 
                       );
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'University Data Fetched',
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

    public function universityUpdate(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'name' => 'required|max:255',
                'acronym' => 'required|max:255',
            ]);
            if ($validator->passes()) {
            $uni = University::find($id);
            if($uni){
            $uni->name = $request->name;
            $uni->acronym = $request->acronym;
            $uni->status = $request->status;
            $uni->save();
                    $data = array(
                        'status'  =>true,
                        'message' =>'University updated Successfully!' ,
                        );
                    return response()->json($data,200);
            }else{
                    return response()->json([
                        'message'   =>'University Not found !',
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

    public function universityDetele(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $data=University::where('id',$id)->first();
                if($data){
                        $data->is_deleted = 1;
                        $data->save();
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'University Deleted Successfully!',
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'University Not Found',
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