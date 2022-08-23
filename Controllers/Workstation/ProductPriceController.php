<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Imports\ProductPriceImport;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductImage;
use App\Model\Workstation\ProductPrice;
use App\Model\Workstation\WorkstationUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class ProductPriceController extends Controller
{

	public function productSearch(Request $request){
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

				$data = array(
                        'status'  		=> true,
                        'message' 		=> 'Product found!' ,
                        'product_name'  => $product->product_name,
                        'product_sku'   => $product->sku,
                        'product_image' => $productImage,
                        'mrp'			=> $product->mrp_paper_book,
                        'listing_price' => $product->listing_price,
                        'product_id' 	=> $product->id,
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
			$price = ProductPrice::where('product_id',$productId)->orderBy('created_at','desc')->get();
	        $array = array();
			foreach ($price as $key => $item) {
			$employee = WorkstationUser::where('id',$item->employee_id)->first();
				$array[] = array(
                    'id' => $item->id,
                    'mrp' => $item->mrp,
                    'listing_price' => $item->listing_price,
                    'user_code' => $employee->code ?? null,
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
		        	$list = Excel::toArray(new ProductPriceImport(), $path);
		        	foreach ($list as $key => $value) {
		        		foreach ($value as $finalKey => $finalData) {
		        		  $sku =  $finalData[0];
					      $mrp =  $finalData[1];
					      $listing_price = $finalData[2];
					      if($finalData !== 0)
					      {
					        $product = Product::where('sku', $sku)->first();
					        if($product){

					          $finalArray[] = array(
					                    'prdouct_id' => $product->id,
					                    'product_name' => $product->product_name,
					                    'product_sku' => $product->sku,
					                    'mrp' => $mrp,
					                    'listing_price' => $listing_price,
					                  );
					        }
					      }
		        		}
		        	}
		        	if ($path) {
	                    $data = array(
	                        'status'  		=> true,
	                        'message' 		=> 'Price List added Successfully!' ,
	                        'data'			=> $finalArray,
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



    public function uploadConfirm(Request $request)
     {
    	$wsId = $request->wsId;
	    $workstation=WorkstationUser::find($wsId);
	    if ($workstation) {

	    	foreach ($request->product as $key => $requestProduct) 
	    	{
		    $product = Product::where('id', $requestProduct)->first();
		        if($product){
		        $product->mrp_paper_book = $request->mrp[$key];
		        $product->listing_price = $request->listing_price[$key];
		        $product->save();

		          //record for product price
		        $price['product_id'] = $product->id;
		        $price['employee_id'] = $workstation->id;
		        $price['mrp'] = $product->mrp_paper_book;
		        $price['listing_price'] = $product->listing_price;
		        $recordPrice = ProductPrice::create($price);
	        	}
	    	}
			
			$data = array(
                        'status'  =>true,
                        'message' =>'Price Updated Successfully!' ,
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
}
