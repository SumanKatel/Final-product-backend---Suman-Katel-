<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Workstation\OfferPlatform;
use App\Model\Workstation\PurchaseOrder;
use App\Model\Workstation\PurchaseOrderProduct;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;
use Validator;

class PurchaseOrderController extends Controller
{

   	public function purchaseOrderPost(Request $request)
    {
   		$wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
    	  if ($workstation) {
                $validator=Validator::make($request->all(), [
                'distributor_id'             => 'required',
                'product_id'                 => 'required',
                'quantity'                   => 'required',
                ]);
                if ($validator->passes()) {
                    $purchase['purchase_order_code'] = time();
                    $purchase['status'] = 0;
                    $purchaseResult = PurchaseOrder::create($purchase);
                    if ($purchaseResult) {
                        foreach ($request->product_id as $key => $productID) {
                            $purchaseProduct['purchase_order_id'] = $purchaseResult->id;
                            $purchaseProduct['product_id'] = $productID;
                            $purchaseProduct['distributor_id'] = $request->distributor_id[$key];
                            $purchaseProduct['quantity'] = $request->quantity[$key];
                            $purchaseProduct['total_price'] = $request->total_price[$key];
                            $purchaseProduct['status'] = 0;
                            PurchaseOrderProduct::create($purchaseProduct);
                        }
                    }
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Purchase Order Created Successfully!',
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
            }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       => $this->wsId,
                ], 401);
            }
   }
}
