<?php

namespace App\Http\Controllers\Workstation;
use App\Http\Controllers\Controller;
use App\Model\Workstation\Author;
use App\Model\Workstation\BillType;
use App\Model\Workstation\Category;
use App\Model\Workstation\CategoryAttributeRelation;
use App\Model\Workstation\DeliveryAddressPrice;
use App\Model\Workstation\Distributor;
use App\Model\Workstation\District;
use App\Model\Workstation\Language;
use App\Model\Workstation\Municipality;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductAttribute;
use App\Model\Workstation\ProductAttributeOption;
use App\Model\Workstation\Publisher;
use App\Model\Workstation\State;
use App\Model\Workstation\Warehouse;
use App\Model\Workstation\WorkstationUser;
use App\Model\admin\BuyTwoGetOne;
use App\Model\admin\ComboPackageProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;



class BuyTwoGetOneController extends Controller
{
    public function buyOneList(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=BuyTwoGetOne::orderBy('id','desc')->get();
                foreach ($list as $key => $value) {
                    $array[]= array(
                      'id' =>$value->id ,
                      'title' =>$value->title ,
                      'description' =>$value->description ,
                      'image' =>$value->image ,
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

    public function buyOneAdd(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'date_from' => 'required',
                'date_to' => 'required',
                'title' => 'required',
                'image' => 'required',
                'description' => 'required',
                'cost' => 'required',
            ]);
            if ($validator->passes()) {
            $combo['date_from'] = $request->date_from;
            $combo['date_to'] = $request->date_to;
            $combo['title'] = $request->date_from;
            $combo['status'] = $request->status;
            $combo['cost'] = $request->cost;
            $combo['description'] = $request->description;

            if(!empty($request->file('buytwo_image'))){
            $imagefile = uploadImageGcloud($request->file('buytwo_image'),'buytwo_image');
            $combo['image'] = $imagefile;
            }

            $comboResult = BuyTwoGetOne::create($combo);
                if ($comboResult) {
                foreach ($request->product_id as $key => $singleProduct) {
                    $comboProduct['product_id'] = $singleProduct;
                    $comboProduct['buy_two_id'] = $comboResult;
                    $comboProduct['is_free'] = $request->is_free[$key];
                    BuyTwoGetOneProduct::create($comboProduct);
                }
            if ($result) {
                    $data = array(
                        'status'  =>true,
                        'message' =>'Offer added Successfully!' ,
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
}