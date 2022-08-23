<?php

namespace App\Http\Controllers\Workstation;
use App\Http\Controllers\Controller;
use App\Model\Workstation\DeliveryGuys;
use App\Model\Workstation\DeliveryPartner;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;



class DeliveryPartnerController extends Controller
{

    public function deliveryPartnerlist(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=DeliveryPartner::orderBy('id','desc')->where('is_deleted',0)->get();
                foreach ($list as $key => $value) {
                    $deliveryGuyArray = array();
                    $deliveryGuy = DeliveryGuys::where('delivery_partner_id',$value->id)->get();
                    foreach ($deliveryGuy as $key => $guy) {
                        $deliveryGuyArray[]= array(
                      'id' =>$guy->id ,
                      'fullname' =>$guy->fullname, 
                      'email' =>$guy->email, 
                      'phoneno' =>$guy->phoneno, 
                      'address' =>$guy->address, 
                      'designation' =>$guy->designation, 
                       );
                    }
                    $array[]= array(
                      'id' =>$value->id ,
                      'title' =>$value->title, 
                      'phone' =>$value->phone, 
                      'email' =>$value->email, 
                      'address' =>$value->address, 
                      'vat_no' =>$value->vat_no, 
                      'slug' =>$value->slug, 
                      'deliveryGuys'=>$deliveryGuyArray,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Deliery Partner Data Fetched',
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

    public function deliveryPartnerAdd(Request $request)
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
            $distributor['status'] = $request->status;
            $result = DeliveryPartner::create($distributor);
            if ($result) {
                foreach ($request->guy_name as $key => $guyName) {
                    $guy['delivery_partner_id'] = $result->id;
                    $guy['fullname'] = $guyName;
                    $guy['email'] = $request->guy_email[$key];
                    $guy['designation'] = $request->guy_designation[$key];
                    $guy['mobileno'] = $request->guy_phone[$key];
                    $guy['status'] = 1;
                    DeliveryGuys::create($guy);
                }
            }
            if ($result) {
                    $data = array(
                        'status'  =>true,
                        'message' =>'Deliery Partner added Successfully!' ,
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


         public function deliveryPartnerEdit(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $value=DeliveryPartner::where('id',$id)->first();
                if($value){

                    $deliveryGuyArray = array();
                    $deliveryGuy = DeliveryGuys::where('delivery_partner_id',$value->id)->get();
                    foreach ($deliveryGuy as $key => $guy) {
                        $deliveryGuyArray[]= array(
                      'id' =>$guy->id ,
                      'fullname' =>$guy->fullname, 
                      'email' =>$guy->email, 
                      'phoneno' =>$guy->phoneno, 
                      'address' =>$guy->address, 
                      'designation' =>$guy->designation, 
                       );
                    }
                    $array[]= array(
                      'id' =>$value->id ,
                      'title' =>$value->title, 
                      'phone' =>$value->phone, 
                      'email' =>$value->email, 
                      'address' =>$value->address, 
                      'vat_no' =>$value->vat_no, 
                      'slug' =>$value->slug, 
                      'deliveryGuys'=>$deliveryGuyArray,
                       );
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Delivery Partner Data Fetched',
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

    public function deliveryPartnerUpdate(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'phone' => 'required|max:255',
                'email' => 'required|max:255',
                'address' => 'required|max:255',
            ]);
            if ($validator->passes()) {
            $distributor = DeliveryPartner::find($id);
            if($distributor){
            $distributor->title = $request->title;
            $distributor->phone = $request->phone;
            $distributor->email = $request->email;
            $distributor->address = $request->address;
            $distributor->vat_no = $request->vat_no;
            $distributor->save();
                    $data = array(
                        'status'  =>true,
                        'message' =>'Delivery Partner updated Successfully!' ,
                        );
                    return response()->json($data,200);
            }else{
                    return response()->json([
                        'message'   =>'Delivery Partner Not found !',
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

    public function deliveryPartnerDetele(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $data=DeliveryPartner::where('id',$id)->first();
                if($data){
                        $data->is_deleted = 1;
                        $data->save();
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Delivery Partner Deleted Successfully!',
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