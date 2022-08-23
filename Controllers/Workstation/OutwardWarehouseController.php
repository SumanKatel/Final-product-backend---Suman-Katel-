<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Workstation\Isle;
use App\Model\Workstation\OrderPackage;
use App\Model\Workstation\Product;
use App\Model\Workstation\RelationWarehouseOrder;
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


class OutwardWarehouseController extends Controller
{

    public function getList(Request $request,$warehouseId)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $warehouse = Warehouse::where('id',$warehouseId)->first();
                if ($warehouse) {
                $array = array();
                $list = RelationWarehouseOrder::where('warehouse_id',$warehouse->id)->where('status',0)->get();
                foreach ($list as $key => $value) {
                $warehouseProduct = WarehouseProduct::where('warehouse_id',$value->warehouse_id)->where('product_id',$value->product_id)->first();
                if ($warehouseProduct) {
                $shelve = Shelves::where('id',$warehouseProduct->shelves_id)->first();
                $isle = Isle::where('id',$warehouseProduct->isle_id)->first();
                $section = Section::where('id',$warehouseProduct->section_id)->first();
                }
                $product = Product::where('id',$value->product_id)->first();
                    $array[]= array(
                      'id' => $value->id ,
                      'shelve' => $shelve->title ?? null ,
                      'isle' => $isle->title ?? null,
                      'section' => $section->title ?? null,
                      'product_name' => $product->product_name ,
                      'product_sku' => $product->sku ,
                      'product_qty' => $value->product_qty,
                      'date' => Carbon::parse($value->created_at)->toDateString() ,
                      'time' => date('H:i A', strtotime($value->created_at)) ,
                       );
                }
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
// The Social Dilemma

public function processOrder(Request $request,$warehouseId){
      $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
              $warehouse = Warehouse::where('id',$warehouseId)->first();
                if ($warehouse) {
                    $data['warehouse_id'] = $warehouse->id;
                    $data['process_code'] = time();
                    $data['status'] = 0;
                    $data['process_by'] = $workstation->id;
                    $data['collected_datetime'] = date('Y-m-d H:i:s');
                    $warehouseOrderProcess = WarehouseOrderProcess::create($data);
                      if ($warehouseOrderProcess) 
                        {
                          foreach ($request->rel_warehouse_order_id as $key => $relwarehouseId) {
                            $process_data = WarehouseOrderProcessProduct::where('rel_warehouse_order_id',$relwarehouseId->id)->first();
                            if (!$process_data) {
                            $relWarehousOrder = RelationWarehouseOrder::where('id',$relwarehouseId)->first();
                            $relWarehousOrder->status = 1;
                            $relWarehousOrder->save();
                              if ($relWarehousOrder) 
                                {
                                  $crud['warehouse_order_process_id'] = $warehouseOrderProcess->id;
                                  $crud['rel_warehouse_order_id'] = $relWarehousOrder->id;
                                  $crud['order_id'] = $relWarehousOrder->order_id;
                                  $crud['status'] = 0;
                                  $finalProcess = WarehouseOrderProcessProduct::create($crud);
                                }
                            }
                            }
                  }
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Process to Warehouse Successfully!',
                        );
                return response()->json($result,200);
              }


            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
   }


   public function getProcessList(Request $request,$warehouseId)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $warehouse = Warehouse::where('id',$warehouseId)->first();
                if ($warehouse) {
                $array = array();
                $list = WarehouseOrderProcess::where('warehouse_id',$warehouse->id)->get();
                foreach ($list as $key => $value) {
                $employee = WorkstationUser::where('id',$value->process_by)->first();
                $warehouseOrderProduct = WarehouseOrderProcessProduct::where('warehouse_order_process_id',$value->id)->get();
                if ($warehouseOrderProduct) {
                  $warehouseOrderProductArray = array();
                  foreach ($warehouseOrderProduct as $key => $singleProcessData) {
                    $relData = RelationWarehouseOrder::where('id',$singleProcessData->rel_warehouse_order_id)->first();
                    $product = Product::where('id',$relData->product_id)->first();

                    $warehouseProduct = WarehouseProduct::where('warehouse_id',$relData->warehouse_id)->where('product_id',$relData->product_id)->first();
                    if ($warehouseProduct) {
                    $shelve = Shelves::where('id',$warehouseProduct->shelves_id)->first();
                    $isle = Isle::where('id',$warehouseProduct->isle_id)->first();
                    $section = Section::where('id',$warehouseProduct->section_id)->first();
                    }
                    $warehouseOrderProductArray[]= array(
                      'id' => $singleProcessData->id ,
                      'singleProcessData_status' => $singleProcessData->status,
                      'shelve' => $shelve->title ?? null ,
                      'isle' => $isle->title ?? null,
                      'section' => $section->title ?? null,
                      'product_name' => $product->product_name ,
                      'product_sku' => $product->sku ,
                      'product_qty' => $relData->product_qty,
                      'date' => Carbon::parse($value->created_at)->toDateString() ,
                      'time' => date('H:i A', strtotime($value->created_at)) ,
                       );
                  }
                 $array[]= array(
                      'id' => $value->id ,
                      'process_code' => $value->process_code,
                      'process_status' => $value->status,
                      'employee_code' => $employee->code ,
                      'employee_name' => $employee->name ,
                      'date' => Carbon::parse($value->collected_datetime)->toDateString() ,
                      'list' => $warehouseOrderProductArray ,
                       );
                }
              }

                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Data Fetched',
                                    'data' =>$array,
                                );

                return response()->json($result,200);
            
            }
          }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    }
    public function confirmTransfer(Request $request,$warehouseOrderProductId){
      $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
              $warehouseOrderProductData = WarehouseOrderProcess::where('id',$warehouseOrderProductId)->first();
                if ($warehouseOrderProductData) {
                   $warehouseOrderProductData->status = 1;
                   $warehouseOrderProductData->save();
                      if ($warehouseOrderProductData) 
                        {
                          $list = WarehouseOrderProcessProduct::where('warehouse_order_process_id',$warehouseOrderProductData->id)->get();
                          foreach ($list as $key => $single) {
                            $data = WarehouseOrderProcessProduct::where('id',$single->id)->first();
                            $data->status = 1;
                            $data->save();
                            }
                  }
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Transfer Confirmation!',
                        );
                return response()->json($result,200);
              }


            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
   }




      public function transferToPackage(Request $request,$warehouseProcessId){
      $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
              $warehouseProcess = WarehouseOrderProcess::where('id',$warehouseProcessId)->first();
                if ($warehouseProcess) {
                    $warehouseProcessProduct = WarehouseOrderProcessProduct::where('warehouse_order_process_id',$warehouseProcess->id)->get();
                    $uniqueOrder = array();
                      if(!empty($warehouseProcessProduct)){
                          foreach($warehouseProcessProduct as $val){
                              $date = WarehouseOrderProcessProduct::where('order_id',$val->order_id)->first();
                              $date->status = 2;
                              $date->save();
                              array_push($uniqueOrder,$date);
                          }
                      }
                      $uniqueOrder = array_unique($uniqueOrder);

                    $ran = 1;
                    foreach ($uniqueOrder as $key => $value) {
                      $dd = RelationWarehouseOrder::where('id',$value->rel_warehouse_order_id)->first();
                      $orderPackage = OrderPackage::where('order_id',$dd->order_id)->where('process_id',$warehouseProcess->id)->first();
                      if (!$orderPackage) {
                      $data['process_id'] = $warehouseProcess->id;
                      $data['package_code'] = time().$ran++;
                      $data['status'] = 0;
                      $data['order_id'] = $dd->order_id;
                      $data['warehouse_id'] = $dd->warehouse_id;
                      $data['collected_datetime'] = date('Y-m-d H:i:s');
                      $final = OrderPackage::create($data);
                      }
                    }
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Process to Warehouse Successfully!',
                        );
                return response()->json($result,200);
              }
              else{
                $result = array(
                            'status'        =>false,
                            'message'       => 'Processing Data Not Found',
                        );
                return response()->json($result,401);
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
