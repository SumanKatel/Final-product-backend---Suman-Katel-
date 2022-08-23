<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Workstation\BillingAddress;
use App\Model\Workstation\Isle;
use App\Model\Workstation\OrderDelivery;
use App\Model\Workstation\OrderPackage;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductOrder;
use App\Model\Workstation\ProductOrderList;
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


class OrderPackagingController extends Controller
{

    public function acknowledgeList(Request $request,$warehouseId)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $warehouse = Warehouse::where('id',$warehouseId)->first();
                if ($warehouse) {
                $array = array();
                $orderPackages = OrderPackage::where('warehouse_id',$warehouse->id)->where('status',0)->get(); 
                foreach ($orderPackages as $key => $value) {
                  $processData = WarehouseOrderProcess::where('id',$value->process_id)->first();
                  $employee = WorkstationUser::where('id',$processData->process_by)->first();
                  $subOrderList = ProductOrderList::where('order_code_id',$value->order_id)->get();

                  $totalProduct = $subOrderList->sum('product_qty');

                  $productArray = array();
                  foreach ($subOrderList as $key => $subOrder) {
                    $product = Product::where('id',$subOrder->product_id)->first();
                    $productArray[]= array(
                      'product_id' => $product->id ,
                      'product_sku' => $product->sku ,
                      'product_name' => $product->product_name ,
                      'product_qty' => $subOrder->product_qty ,
                      'date' => Carbon::parse($value->created_at)->toDateString() ,
                      'time' => date('H:i A', strtotime($value->created_at)) ,
                       );
                  }
                    $array[]= array(
                      'id' => $value->id ,
                      'status' => $value->status ,
                      'process_code' => $processData->process_code ,
                      'total_product' => $totalProduct,
                      'date' => Carbon::parse($value->created_at)->toDateString() ,
                      'time' => date('H:i A', strtotime($value->created_at)) ,
                      'employee_code' => $employee->code ,
                      'employee_name' => $employee->name ,
                      'products'=> $productArray,
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


  public function processAcknowledgeList(Request $request,$warehouseId)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $warehouse = Warehouse::where('id',$warehouseId)->first();
                if ($warehouse) {
                $array = array();
                $orderPackages = OrderPackage::where('warehouse_id',$warehouse->id)->where('status',1)->get(); 
                foreach ($orderPackages as $key => $value) {
                  $processData = WarehouseOrderProcess::where('id',$value->process_id)->first();
                  $employee = WorkstationUser::where('id',$processData->process_by)->first();
                  $subOrderList = ProductOrderList::where('order_code_id',$value->order_id)->get();
                  $totalProduct = $subOrderList->sum('product_qty');

                  $productArray = array();
                  foreach ($subOrderList as $key => $subOrder) {
                    $product = Product::where('id',$subOrder->product_id)->first();
                    $productArray[]= array(
                      'product_id' => $product->id ,
                      'product_sku' => $product->sku ,
                      'product_name' => $product->product_name ,
                      'product_qty' => $subOrder->product_qty ,
                      'date' => Carbon::parse($value->created_at)->toDateString() ,
                      'time' => date('H:i A', strtotime($value->created_at)) ,
                       );
                  }
                    $array[]= array(
                      'id' => $value->id ,
                      'status' => $value->status ,
                      'package_code' => $value->package_code ,
                      'total_product' => $totalProduct,
                      'date' => Carbon::parse($value->created_at)->toDateString() ,
                      'time' => date('H:i A', strtotime($value->created_at)) ,
                      'employee_code' => $employee->code ,
                      'employee_name' => $employee->name ,
                      'products'=> $productArray,
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

public function orderPackageProcess(Request $request,$orderPackageId){
      $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $data = OrderPackage::where('id',$orderPackageId)->first();
                if ($data) {
                $data->status = 1;
                $data->is_bill_attached = $request->is_bill_attached;
                $data->is_qa_checked = $request->is_qa_checked;
                $data->is_delivery_note_fixed = $request->is_delivery_note_fixed;
                $data->save();
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Package Process Successfully!',
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


   public function orderTransferToDelivery(Request $request){
      $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
              foreach ($request->orderPackageId as $key => $orderPackageId) {
                $data = OrderPackage::where('id',$orderPackageId)->where('status',1)->first();
                if ($data) {
                $productOrder  = ProductOrder::where('id',$data->order_id)->first();
                $address       = BillingAddress::where('id',$productOrder->delivery_address_id)->first();
                $crud['warehouse_id'] = $data->warehouse_id;
                $crud['process_id'] = $data->process_id;
                $crud['order_id'] = $data->order_id;
                $crud['order_package_id'] = $data->id;
                $crud['municipality_id'] = $address->municipality_id;
                $crud['status'] = 0;
                $delivery = OrderDelivery::create($crud);
                if ($delivery) {
                $data->status = 2;
                $data->save();
                }
              }
            }
            $result = array(
                    'status'        =>true,
                    'message'       => 'Transfer to delivery Successfully!',
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

}
