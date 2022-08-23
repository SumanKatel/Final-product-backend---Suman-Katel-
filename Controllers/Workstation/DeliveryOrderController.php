<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Workstation\BillingAddress;
use App\Model\Workstation\DeliveryGroup;
use App\Model\Workstation\DeliveryGuyOrderAssign;
use App\Model\Workstation\DeliveryGuys;
use App\Model\Workstation\DeliveryPartner;
use App\Model\Workstation\Municipality;
use App\Model\Workstation\OrderDelivery;
use App\Model\Workstation\OrderPackage;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductOrder;
use App\Model\Workstation\ProductOrderList;
use App\Model\Workstation\RelationGroupOrderDelivery;
use App\Model\Workstation\RelationWarehouseOrder;
use App\Model\Workstation\Warehouse;
use App\Model\Workstation\WarehouseOrderProcess;
use App\Model\Workstation\WarehouseOrderProcessProduct;
use App\Model\Workstation\WarehouseProduct;
use App\Model\Workstation\WorkstationUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;


class DeliveryOrderController extends Controller
{

    public function list(Request $request)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=Municipality::get();
                foreach ($list as $key => $value) {
                  $orderDelivery = OrderDelivery::where('status',0)->where('municipality_id',$value->id)->get();
                  if (count($orderDelivery)>0) {
                    $orderStatus = 1;
                  }else{
                    $orderStatus = 0;
                  }
                if ($orderDelivery) {
                  $deliveryArray = array();
                  foreach ($orderDelivery as $key => $delivery) {
                    $productOrder  = ProductOrder::where('id',$delivery->order_id)->first();
                    $orderPackage  = OrderPackage::where('id',$delivery->order_package_id)->first();
                    $subOrderList = ProductOrderList::where('order_code_id',$productOrder->id)->get();
                    $totalProduct = $subOrderList->sum('product_qty');

                    $deliveryArray[]= array(
                      'order_delivery_id'  => $delivery->id ,
                      'package_code'       => $orderPackage->package_code ,
                      'product_order_date' => Carbon::parse($delivery->order_datetime)->toDateString() ,
                      'package_date'       => Carbon::parse($orderPackage->updated_at)->toDateString() ,
                      'totalProduct'       => $totalProduct,
                       );
                    }
                  }
                    $array[]= array(
                      'id' =>$value->id ,
                      'district_id' =>$value->district_id ,
                      'municipality_name' =>$value->location_name_en ,
                      'order_delivery_status'=>$orderStatus,
                      'orderDelivery'=>$deliveryArray,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Municipality Data Fetched',
                                    'data' => $array,
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

    public function makeGroup(Request $request){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
            	if(!empty($request->order_delivery_id))
            	{
            		$crud['group_code'] = time();
            		$crud['status'] = 1;
            		$group = DeliveryGroup::create($crud);
            		if ($group) {
            			foreach ($request->order_delivery_id as $key => $singleId) {
            				$data = OrderDelivery::where('id',$singleId)->first();
            				$orderDelivery['delivery_group_id'] = $group->id;
            				$orderDelivery['order_delivery_id'] = $data->id;
            				$orderDelivery['order_id'] = $data->order_id;
            				$orderDelivery['municipality_id'] = $data->municipality_id;
            				$orderDelivery['status'] = 0;
		            		$final = RelationGroupOrderDelivery::create($orderDelivery);
            			}
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Group Package Created!',
                        );
                return response()->json($result,200);
            		}
            }
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
   }


     public function readyAssignList(Request $request)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array = array();
                $data = RelationGroupOrderDelivery::where('status',0)->get(); 
                foreach ($data as $key => $value) {
                  $group = DeliveryGroup::where('id',$value->delivery_group_id)->first();
                  $municipality = Municipality::where('id',$value->municipality_id)->first();
                    $array[]= array(
                      'id' => $value->id ,
                      'group_code' => $group->group_code ,
                      'municipality_name' =>$municipality->location_name_en ,
                      'group_date' => Carbon::parse($group->created_at)->toDateString() ,
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

  public function getDeliveryPartner(Request $request)
    {
        $wsId = $request->wsId;
        $workstation = WorkstationUser::find($wsId);
            if ($workstation) {
                $array = array();
                $data = DeliveryPartner::where('status',1)->get(); 

                foreach ($data as $key => $value) {

                  $deliveryGuysArray = array();
                  $deliveryGuys = DeliveryGuys::where('delivery_partner_id',$value->id)->get();
                  foreach ($deliveryGuys as $key => $guys) {
                    $guy = DeliveryGuys::where('id',$guys->id)->first();
                    $deliveryGuysArray[]= array(
                      'delivery_guy_id' => $guy->delivery_guy_id ,
                      'name'            => $guy->fullname ,
                      'mobile'          => $guy->mobileno ,
                       );
                  }
                    $array[]= array(
                      'id' => $value->id ,
                      'delivery_partner' => $value->title ,
                      'deliveryGuys' => $deliveryGuysArray ,
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

     public function assignToDelivery(Request $request){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
              if(!empty($request->rel_group_order_delivery_id))
              {
                  foreach ($request->rel_group_order_delivery_id as $key => $singleId) {
                    $data = RelationGroupOrderDelivery::where('id',$singleId)->first();
                    $assigned = DeliveryGuyOrderAssign::where('rel_group_order_delivery_id',$data->id)->first();
                    if (!$assigned) {
                    $assign['delivery_guy_id'] = $request->delivery_guy_id;
                    $assign['rel_group_order_delivery_id'] = $data->id;
                    $assign['order_id'] = $data->order_id;
                    $assign['status'] = 0;
                    $final = DeliveryGuyOrderAssign::create($assign);
                    if ($final) {
                      $data->status = 1;
                      $data->save();
                    }
                    }
                  }
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Delivery Partner Assign Successfully !',
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

}
