<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Imports\ProductPriceImport;
use App\Model\Workstation\InventoryAction;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductImage;
use App\Model\Workstation\ProductInventory;
use App\Model\Workstation\ProductPrice;
use App\Model\Workstation\Warehouse;
use App\Model\Workstation\WorkstationUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class ProductInventoryController extends Controller
{

	public function productInventory(Request $request){
    	$wsId = $request->wsId;
	    $workstation=WorkstationUser::find($wsId);
	    if ($workstation) {
	    	$validator=Validator::make($request->all(), [
                'sku' => 'required',
            ]);
            if ($validator->passes()) {
			$product = Product::where('sku',$request->sku)->first();
			if ($product) {
				$productImage = ProductImage::where('product_id', $product->id)->where('is_desktop_thumbnail',1)->first();
	            if ($productImage) {
	             $productImage = $productImage->image;
	            }else{
	              $productImage = DEFAULT_IMG;
	            }
				$array =array();
				$inventory = ProductInventory::where('product_id',$product->id)->get();
				$outInventory = ProductInventory::where('product_id',$product->id)->where('is_increase',0)->get();
				$inInventory = ProductInventory::where('product_id',$product->id)->where('is_increase',1)->get();

				$totalInventory= $inInventory->sum('inventory')-$outInventory->sum('inventory'); 

				foreach ($inventory as $key => $invento) {
					$warehouse = Warehouse::where('id',$invento->warehouse_id)->first();
					$action = InventoryAction::where('id',$invento->action_id)->first();
					if ($invento->is_increase==1) {
						$sign = '+';
					}else{
						$sign = '-';
					}
					$array[] = array(
                    'id' => $invento->id,
                    'warehouse' => $warehouse->name,
                    'action' => $action->title,
                    'inventory_sign' => $sign,
                    'inventory' => $invento->inventory,
                    'date' => Carbon::parse($invento->created_at)->toDateString(),
                  );
				}

				
				$data = array(
                        'status'  		 	=> true,
                        'message' 		 	=> 'Product found!' ,
                        'totalInventory' 	=> $totalInventory,
                        'product_name'		=> $product->product_name,
                        'product_sku'	    => $product->sku,
                        'product_image'	    => $productImage,
                        'data'				=> $array,

                        );
                    return response()->json($data,200);
			}else{
				$data = array(
                        'status'  		=> false,
                        'message' 		=> 'Product not found!' ,
                        );
                    return response()->json($data,401);
			}
			}else{

                $result = array(
                'status'        => false,
                'message'       => 'Input Field Required',
                'data'    => $validator->errors()
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

    public function singleProductPriceList(Request $request,$productId){
    	$wsId = $request->wsId;
	    $workstation=WorkstationUser::find($wsId);
	    if ($workstation) {
			$price = ProductPrice::where('product_id',$productId)->get();
	        $array = array();
			foreach ($price as $key => $item) {
			$employee = WorkstationUser::where('id',$item->employee_id)->first();
				$array[] = array(
                    'id' => $item->id,
                    'mrp' => $item->mrp,
                    'listing_price' => $item->listing_price,
                    'user_code' => $employee->code,
                    'date' => Carbon::parse($item->created_at)->toDateString(),
                  );
			}

				$data = array(
                        'status'  		=> true,
                        'message' 		=> 'price has Been Listed!' ,
                        'data'			=> $array,
                        );
                    return response()->json($data,200);
		}else{
	        return response()->json([
	            'status'     => false,
	            'message'    => 'Workstation User Not Found',
	            'data'       =>null,
	        ], 401);
	    }
    }


     public function updateProductPrice(Request $request,$productId)
     {
    	$wsId = $request->wsId;
	    $workstation=WorkstationUser::find($wsId);
	    if ($workstation) {
	    	$validator=Validator::make($request->all(), [
                'listing_price' => 'required',
                'mrp' 			=> 'required',
            ]);
            if ($validator->passes()) {

			$product = Product::find($productId);
			if ($product) {
				$product->mrp_paper_book = $request->mrp;
				$product->listing_price = $request->listing_price;
				$product->save();

					//record for product price
				$price['product_id'] = $product->id;
				$price['employee_id'] = $workstation->id;
				$price['mrp'] = $product->mrp_paper_book;
				$price['listing_price'] = $product->listing_price;
	            $recordPrice = ProductPrice::create($price);
	            if ($recordPrice) {
	            $data = array(
                        'status'  =>true,
                        'message' =>'Price Updated Successfully!' ,
                        );
                    return response()->json($data,200);
	            }
			}
			}else{

                $result = array(
                'status'        => false,
                'message'       => 'Input Field Required',
                'data'    => $validator->errors()
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



    public function csvUpload(Request $request)
     {
    	$wsId = $request->wsId;
	    $workstation=WorkstationUser::find($wsId);
	    if ($workstation) {
	    	$validator=Validator::make($request->all(), [
                'price_file' => 'required',
            ]);
            if ($validator->passes()) {

			$path = $request->price_file;
		        	$data =Excel::import(new ProductPriceImport, $path);
		        	if ($path) {
	                    $data = array(
	                        'status'  		=> true,
	                        'message' 		=> 'Price List added Successfully!' ,
	                        'data'			=> $path,
	                        );
	                    return response()->json($data,200);
	                }
			}else{

                $result = array(
                'status'        => false,
                'message'       => 'Input Field Required',
                'data'    => $validator->errors()
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
