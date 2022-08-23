<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Imports\HomepageProductImport;
use App\Model\Workstation\HomepageLayout;
use App\Model\Workstation\Product;
use App\Model\Workstation\RelationHomepageCollection;
use App\Model\Workstation\RelationHomepageProduct;
use App\Model\Workstation\WorkstationUser;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class HomepageLayoutController extends Controller
{

	public function homepage_layout_list(Request $request){
		$wsId = $request->wsId;
	    $workstation=WorkstationUser::find($wsId);
	    if ($workstation) {
			$home_layout_detail = HomepageLayout::all();
			if($home_layout_detail){
				$data = array(
                        'status'  		=> true,
                        'message' 		=> 'Home Layout has Been Listed!' ,
                        'data'			=> $home_layout_detail,
                        );
                    return response()->json($data,200);
			}else{
				$result = array(
                'status'        => false,
                'message'       => 'Home Layout not Found',
                'data'    		=> null
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

	public function homepage_layout_update_title(Request $request, $id){
		$wsId = $request->wsId;
	    $workstation=WorkstationUser::find($wsId);
	    if ($workstation) {
			$validator=Validator::make($request->all(), [
            'title'	=> 'required',
       		 ]);
        	if ($validator->passes()) {
	        	$home_layout = HomepageLayout::where('id',$id)->first();
        		if($home_layout){
		        	$home_layout->title = $request->title;
		        	$home_layout->save();	
					$data = array(
	                        'status'  		=> true,
	                        'message' 		=> 'Home Layout has Been Listed!' ,
	                        'data'			=> $home_layout,
	                        );
                    return response()->json($data,200);
				}else{
					$result = array(
	                'status'        => false,
	                'message'       => 'Home Layout not Found',
	                'data'    		=> null
	                );
	                return response()->json($result,422);
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

   public function homepage_layout_add(Request $request, $id){
   	$wsId = $request->wsId;
    $workstation=WorkstationUser::find($wsId);
    if ($workstation) {
    	if($id == 7){

    		$validator=Validator::make($request->all(), [
	            'image'			=> 'required',
	            'category_id'	=> 'required',
	        ]);
	        if ($validator->passes()) {
	        		$homepage_collection = new RelationHomepageCollection;
		        	$homepage_collection->category_id = $request->category_id;
		        	$homepage_collection->homepage_layout_id = $id;
		        	$homepage_collection->status = 1;
		        	 if(!empty($request->file('image'))){
			            $imagefile = uploadImageGcloud($request->file('image'),'homepage_collection');
			            $homepage_collection->image = $imagefile;
			        }
			        $homepage_collection->save();
		        	if ($homepage_collection) {
	                    $data = array(
	                        'status'  		=> true,
	                        'message' 		=> 'Product List added Successfully in Todays Offer!' ,
	                        'data'			=> $homepage_collection,
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


	    	$validator=Validator::make($request->all(), [
	            'product_file'	=> 'required',
	        ]);
	        if ($validator->passes()) {
		        
		        	$path = $request->product_file;
		        	$data =Excel::import(new HomepageProductImport($id), $path);
		        	if ($path) {
	                    $data = array(
	                        'status'  		=> true,
	                        'message' 		=> 'Product List added Successfully in Todays Offer!' ,
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
	        }

	   	}else{
	        return response()->json([
	            'status'     => false,
	            'message'    => 'Workstation User Not Found',
	            'data'       =>null,
	        ], 401);
	    }
	}

	public function homepage_collection_delete(Request $request, $id){
		 $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $homepage_collection=RelationHomepageCollection::where('id',$id)->first();
                if($homepage_collection){
                        $homepage_collection->delete();
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Collection Deleted Successfully!',
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'Homepage COllection Not Found',
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

	public function homepage_collection_list(Request $request){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                
            $list=DB::table('rel_homepage_collection as rhc')
            		->join('tbl_product_category as tpc', 'tpc.id', 'rhc.category_id')
            		->select('tpc.title', 'rhc.image', 'tpc.id as category_id', 'rhc.homepage_layout_id', 'rhc.id')
            		->get();
            $result = array(
                                'status'        => true,
                                'message'       => 'Homepage Collection Data Fetched',
                                'data' 			=> $list,
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


	public function homepage_layout_product_list(Request $request,$id){
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
		        $array = array();
	        	$home_layout = HomepageLayout::where('id',$id)->first();
	        	if ($home_layout) {
	        	$homelayoutProdut = RelationHomepageProduct::where('homepage_layout_id',$home_layout->id)->get();
	        	foreach ($homelayoutProdut as $key => $value) {
	        		$product = Product::where('id',$value->product_id)->first();
		            $array[]= array(
		              'id'            => $value->id ,
		              'product_name'   => $product->product_name ,
		              'sku'        => $product->sku ,
		              'date_from' => $value->date_from,
		              'date_to'   => $value->date_to,
		               );
		        }
		        $result = array(
		                            'status'        =>true,
		                            'message'       => 'Product List Fetched!',
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

}
