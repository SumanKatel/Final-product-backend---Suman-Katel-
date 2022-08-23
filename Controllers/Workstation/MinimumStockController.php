<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Model\Workstation\Category;
use App\Model\Workstation\MinimumStock;
use App\Model\Workstation\Warehouse;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;

class MinimumStockController extends Controller
{

    public function setupList(Request $request){
    	$wsId = $request->wsId;
	    $workstation=WorkstationUser::find($wsId);
	    if ($workstation) {
			$list = MinimumStock::orderBy('created_at','desc')->get();
			$array = array();
			foreach ($list as $key => $value) {
				$warehouse = Warehouse::where('id',$value->warehouse_id)->first();
				$category = Category::where('id',$value->category_id)->first();
				$subCategory = Category::where('id',$value->sub_category_id)->first();
				$array[] = array(
                        'id' 				=> $value->id ,
                        'warehouse' 		=> $warehouse->name ,
                        'category_name'	    => $category->title,
                        'sub_category_name' => $subCategory->title,
                        'minimum_stock'     => $value->minimum_stock,
                        );
			}
				$data = array(
                        'status'  		=> true,
                        'message' 		=> 'list has Been Listed!' ,
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

    public function addSetup(Request $request){
    	$wsId = $request->wsId;
	    $workstation=WorkstationUser::find($wsId);
	    if ($workstation) {
			$crud = new MinimumStock;
			$crud->warehouse_id = $request->warehouse_id;
			$crud->category_id = $request->category_id;
			$crud->sub_category_id = $request->sub_category_id;
			$crud->minimum_stock = $request->minimum_stock;
			$crud->save();
			if ($crud) {
				$data = array(
                        'status'  		=> true,
                        'message' 		=> 'Created Successfully!' ,
                        );
                    return response()->json($data,200);
			}
		}else{
	        return response()->json([
	            'status'     => false,
	            'message'    => 'Workstation User Not Found',
	            'data'       =>null,
	        ], 401);
	    }
    }


    public function editSetup(Request $request,$id){
    	$wsId = $request->wsId;
	    $workstation=WorkstationUser::find($wsId);
	    if ($workstation) {
			$value = MinimumStock::find($id);
				$warehouse = Warehouse::where('id',$value->warehouse_id)->first();
				$category = Category::where('id',$value->category_id)->first();
				$subCategory = Category::where('id',$value->sub_category_id)->first();
				$stocks = array(
                        'id' 				=> $value->id ,
                        'warehouse_id' 		=> $value->warehouse_id ,
                        'warehouse' 		=> $warehouse->name ,
                        'category_id'	    => $value->category_id,
                        'category_name'	    => $category->title,
                        'sub_category_id'   => $value->sub_category_id,
                        'sub_category_name' => $subCategory->title,
                        'minimum_stock'     => $value->minimum_stock,
                        );
				$data = array(
                        'status'  		=> true,
                        'message' 		=> 'list has Been Listed!' ,
                        'data'			=> $stocks,
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


    public function updateSetup(Request $request,$id){
    	$wsId = $request->wsId;
	    $workstation=WorkstationUser::find($wsId);
	    if ($workstation) {
			$crud = MinimumStock::find($id);
			$crud->warehouse_id = $request->warehouse_id;
			$crud->category_id = $request->category_id;
			$crud->sub_category_id = $request->sub_category_id;
			$crud->minimum_stock = $request->minimum_stock;
			$crud->save();
			if ($crud) {
				$data = array(
                        'status'  		=> true,
                        'message' 		=> 'Updated Successfully!' ,
                        );
                    return response()->json($data,200);
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
