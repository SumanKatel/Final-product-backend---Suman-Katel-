<?php

namespace App\Http\Controllers\Workstation;
use App\Http\Controllers\Controller;
use App\Model\Workstation\Distributor;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;



class DistributorController extends Controller
{

    public function distributorList(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=Distributor::orderBy('id','desc')->get();
                foreach ($list as $key => $value) {
                    $array[]= array(
                      'id' =>$value->id ,
                      'title' =>$value->title, 
                      'phone' =>$value->phone, 
                      'email' =>$value->email, 
                      'address' =>$value->address, 
                      'vat_no' =>$value->vat_no, 
                      'k_person_name' =>$value->k_person_name, 
                      'k_person_designation' =>$value->k_person_designation, 
                      'k_person_phone' =>$value->k_person_phone, 
                      'k_person_email' =>$value->k_person_email, 
                      'slug' =>$value->slug, 
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Distributor Data Fetched',
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

    public function distributorAdd(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'phone' => 'required|max:255',
                'email' => 'required|max:255',
                'address' => 'required|max:255',
                'status' => 'required|max:255',
            ]);
            if ($validator->passes()) {
            $distributor['title'] = $request->title;
            $distributor['phone'] = $request->phone;
            $distributor['email'] = $request->email;
            $distributor['address'] = $request->address;
            $distributor['vat_no'] = $request->vat_no;
            $distributor['k_person_name'] = $request->k_person_name;
            $distributor['k_person_designation'] = $request->k_person_designation;
            $distributor['k_person_phone'] = $request->k_person_phone;
            $distributor['k_person_email'] = $request->k_person_email;
            $distributor['status'] = $request->status;
            $result = Distributor::create($distributor);
            if ($result) {
                    $data = array(
                        'status'  =>true,
                        'message' =>'Distributor added Successfully!' ,
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


         public function distributorEdit(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $value=Distributor::where('id',$id)->first();
                if($value){
                    $array[]= array(
                      'id' =>$value->id ,
                      'title' =>$value->title, 
                      'phone' =>$value->phone, 
                      'email' =>$value->email, 
                      'address' =>$value->address, 
                      'vat_no' =>$value->vat_no, 
                      'k_person_name' =>$value->k_person_name, 
                      'k_person_designation' =>$value->k_person_designation, 
                      'k_person_phone' =>$value->k_person_phone, 
                      'k_person_email' =>$value->k_person_email, 
                      'slug' =>$value->slug, 
                       );
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Distributor Data Fetched',
                                    'data' =>$array,
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'Distributor Not Found',
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

    public function distributorUpdate(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'phone' => 'required|max:255',
                'email' => 'required|max:255',
                'address' => 'required|max:255',
                'status' => 'required|max:255',
            ]);
            if ($validator->passes()) {
            $distributor = Distributor::find($id);
            if($distributor){
            $distributor->title = $request->title;
            $distributor->phone = $request->phone;
            $distributor->email = $request->email;
            $distributor->address = $request->address;
            $distributor->vat_no = $request->vat_no;
            $distributor->k_person_name = $request->k_person_name;
            $distributor->k_person_designation = $request->k_person_designation;
            $distributor->k_person_phone = $request->k_person_phone;
            $distributor->k_person_email = $request->k_person_email;
            $distributor->save();
                    $data = array(
                        'status'  =>true,
                        'message' =>'Distributor updated Successfully!' ,
                        );
                    return response()->json($data,200);
            }else{
                    return response()->json([
                        'message'   =>'Distributor Not found !',
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

    public function distributorDetele(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $data=Distributor::where('id',$id)->first();
                if($data){
                        $data->delete();
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Distributor Deleted Successfully!',
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'Distributor Not Found',
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