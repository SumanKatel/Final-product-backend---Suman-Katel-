<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Workstation\Isle;
use App\Model\Workstation\Product;
use App\Model\Workstation\Section;
use App\Model\Workstation\Shelves;
use App\Model\Workstation\Warehouse;
use App\Model\Workstation\WarehouseOrderProcess;
use App\Model\Workstation\WarehouseOrderProcessProduct;
use App\Model\Workstation\WarehouseProduct;
use App\Model\Workstation\WorkstationUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;


class WarehouseOrderProcessController extends Controller
{
    public function isleList(Request $request){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
                $array = array();
                $list = Isle::get();
                    $result = array(
                            'status'        =>true,
                            'message'       => 'List Fetched !',
                            'data'          => $list,
                        );
                    return response()->json($result,200);
                     
            }else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 401);
        }
    }

    public function sectionList(Request $request){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
                $array = array();
                $list = Section::get();
                    $result = array(
                            'status'        =>true,
                            'message'       => 'List Fetched !',
                            'data'          => $list,
                        );
                    return response()->json($result,200);
                     
            }else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 401);
        }
    }

    public function shelvesList(Request $request){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
                $array = array();
                $list = Shelves::get();
                    $result = array(
                            'status'        =>true,
                            'message'       => 'List Fetched !',
                            'data'          => $list,
                        );
                    return response()->json($result,200);
                     
            }else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 401);
        }
    }

    public function addWarehouseProduct(Request $request){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
            $validator=Validator::make($request->all(), [
                'warehouse_id'               => 'required',
                'product_id'                 => 'required',
                'quantity'                   => 'required',
                'isle_id'                    => 'required',
                'section_id'                 => 'required',
                'shelves_id'                 => 'required',
                ]);
                if ($validator->passes()) {
                    $warehouse['warehouse_id'] = $request->warehouse_id;
                    $warehouse['product_id'] = $request->product_id;
                    $warehouse['quantity'] = $request->quantity;
                    $warehouse['isle_id'] = $request->isle_id;
                    $warehouse['section_id'] = $request->section_id;
                    $warehouse['shelves_id'] = $request->shelves_id;
                    $warehouse['emplyee_id'] = $workstation->id;
                    $warehouse['status'] = $request->status;
                    $warehouseResult = WarehouseProduct::create($warehouse);
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Product add on warehouse Successfully',
                        );
                return response()->json($result,200);
                
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
                ], 401);
            }

    }


        public function listWarehouseProduct(Request $request)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array = array();
                $list = WarehouseProduct::orderBy('id','desc')->get();
                foreach ($list as $key => $value) {
                $shelve = Shelves::where('id',$value->shelves_id)->first();
                $isle = Isle::where('id',$value->isle_id)->first();
                $section = Section::where('id',$value->section_id)->first();
                $product = Product::where('id',$value->product_id)->first();
                $warehouse = Warehouse::where('id',$value->warehouse_id)->first();
                $emplyee = WorkstationUser::where('id',$value->emplyee_id)->first();
                    $array[]= array(
                      'id' => $value->id ,
                      'status' => $value->status ,
                      'shelve' => $shelve->title ,
                      'isle' => $isle->title ,
                      'section' => $section->title ,
                      'product_name' => $product->product_name ,
                      'product_sku' => $product->sku ,
                      'quantity' => $value->quantity ,
                      'emplyee_code' => $emplyee->code ,
                      'emplyee_name' => $emplyee->name ,
                      'date' => Carbon::parse($value->created_at)->toDateString() ,
                      'time' => date('H:i A', strtotime($value->created_at)) ,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Data Fetched',
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


   	public function orderProcessPost(Request $request){
   		$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
            $validator=Validator::make($request->all(), [
                 'warehouse_id'               => 'required',
                // 'product_id'                 => 'required',
                // 'quantity'                   => 'required',
                // 'isle_id'                    => 'required',
                // 'section_id'                 => 'required',
                // 'shelves_id'                 => 'required',
                ]);
                if ($validator->passes()) {
                    $data['warehouse_id'] = $request->warehouse_id;
                    $data['process_code'] = time();
                    $data['status'] = 0;
                    $data['process_by'] = $workstation->id;
                    $data['collected_datetime'] = date('Y-m-d H:i:s');
                    $warehouseOrderProcess = WarehouseOrderProcess::create($data);
                    if ($warehouseOrderProcess) {
                        foreach ($request->sub_order_id as $key => $subOrderId) {
                        $subOrder = ProductOrderList::where('id',$subOrderId)->first();
                        $item['sub_order_id'] = $subOrderId;
                        $item['product_id'] = $subOrder->product_id;
                        $item['status'] = 0;
                        $item['warehouse_order_process_id'] = $warehouseOrderProcess->id;
                        $result = WarehouseOrderProcessProduct::create($item);
                        }
                    }
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Data Added Successfully',
                        );
                return response()->json($result,200);
                
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
                ], 401);
            }
   }
}
