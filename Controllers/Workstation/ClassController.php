<?php

namespace App\Http\Controllers\Workstation;
use App\Http\Controllers\Controller;
use App\Model\Workstation\lClass;
use App\Model\Workstation\University;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;



class ClassController extends Controller
{

    public function classlist(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=lClass::orderBy('id','desc')->where('is_deleted',0)->get();
                foreach ($list as $key => $value) {
                    $university = University::where('id',$value->university_id)->first();
                    $array[]= array(
                      'id' =>$value->id ,
                      'university_name' =>$university->name, 
                      'university_id' =>$value->university_id, 
                      'title' =>$value->title, 
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

    public function classAdd(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'university_id' => 'required',
            ]);
            if ($validator->passes()) {
            $uni['title'] = $request->title;
            $uni['university_id'] = $request->university_id;
            $uni['status'] = $request->status;
            $result = lClass::create($uni);
            if ($result) {
                    $data = array(
                        'status'  =>true,
                        'message' =>'Class added Successfully!' ,
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


    public function classEdit(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $value=lClass::where('id',$id)->first();
                $university = University::where('id',$value->university_id)->first();
                if($value){
                    $array[]= array(
                      'id' =>$value->id ,
                      'title' =>$value->title, 
                      'university_name' =>$university->name, 
                      'university_id' =>$value->university_id, 
                      'status' =>$value->status, 
                       );
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Class Data Fetched',
                                    'data' =>$array,
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'Class Not Found',
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

    public function classUpdate(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'university_id' => 'required',
            ]);
            if ($validator->passes()) {
            $uni = lClass::find($id);
            if($uni){
            $uni->title = $request->title;
            $uni->university_id = $request->university_id;
            $uni->status = $request->status;
            $uni->save();
                    $data = array(
                        'status'  =>true,
                        'message' =>'Class updated Successfully!' ,
                        );
                    return response()->json($data,200);
            }else{
                    return response()->json([
                        'message'   =>'class Not found !',
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

    public function classDetele(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $data=lClass::where('id',$id)->first();
                if($data){
                        $data->is_deleted = 1;
                        $data->save();
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Class Deleted Successfully!',
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'class Not Found',
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