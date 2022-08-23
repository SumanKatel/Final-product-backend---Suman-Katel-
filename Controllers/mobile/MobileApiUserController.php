<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Model\Workstation\BillingAddress;
use App\Model\Workstation\Customer;
use App\Model\Workstation\DeliveryAddressPrice;
use App\Model\Workstation\District;
use App\Model\Workstation\Municipality;
use App\Model\Workstation\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MobileApiUserController extends Controller
{
    public function userProfile(Request $request)
    {
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
        	$data= array(
                            'id' =>$customer->id ,
                            'name' =>$customer->name ,
                            'email' =>$customer->email ,
                            'mobile' =>$customer->mobile ,
                            'member_since' =>$customer->created_at ,
                         );
        	$data = array(
		        		'user_detail' => $data,
		        	);
            $result = array(
                'status'        => true,
                'message'       => 'Profile Data Fetched',
                'data'          => $data,
            );
        return response()->json($result,200);
        }
        else{
            return response()->json([
                'status'     => false,
                'message'    => 'Customer User Not Found',
                'data'       =>null,
            ], 401);
        }
        
    }
    public function getDeliveryCharge(Request $request,$id){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            if ($customer) {
                $deliveryAddressc=DeliveryAddressPrice::where('municipality_id',$id)->first();
                if ($deliveryAddressc) {
                    
                if ($deliveryAddressc) {
                    $price = $deliveryAddressc->delivery_price;
                }else{
                    $price = '100';
                }
                       $result = array(
                                    'status'        =>true,
                                    'price'         =>0,
                                    'free_above_order_value' =>0,
                                    'is_available' =>1,
                                    'min_day'       => 2,
                                    'max_day'       => 3,
                                    'message'       => 'Delivery Price!',
                                );
                        return response()->json($result,200);
                }else{
                   $result = array(
                                    'status'        =>true,
                                    'price'         =>0,
                                    'free_above_order_value' =>0,
                                    'is_available' =>1,
                                    'min_day'       => 2,
                                    'max_day'       => 3,
                                    'message'       => 'Delivery Price!',
                                );
                        return response()->json($result,200);
                }
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Customer User Not Found',
                    'data'       =>null,
                ], 401);
            }
    	}

    }
    public function addressList(Request $request)
    {
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {

        $array=array();
        $list = BillingAddress::where('user_id', $customer->id)->get();
            foreach ($list as $key => $value) {
                $state = State::where('id',$value->state_id)->first();
                $district = District::where('id',$value->district_id)->first();
                $municipality = Municipality::where('id',$value->municipality_id)->first();
                $array[]= array(
                  'id' => $value->id ,
                  'state_id' => $value->state_id ,
                  'title' => $value->title ,
                  'state_name' => $state->state_name_np ,
                  'district_id' => $value->district_id ,
                  'district_name' => $district->district_name_en,
                  'municipality_id' => $value->municipality_id,
                  'municipality_name' => $municipality->location_name_en,
                  'customer_name' => $customer->name,
                  'customer_address' => $value->address,
                  'customer_mobile' => $customer->mobile,
                );
            }
            $array =  array(
		            	'address_list' => $array
		            );
            $result = array(
                                'status'        => true,
                                'message'       => 'Delivery Address Data Fetched',
                                'data' 			=> $array,
                            );

            return response()->json($result,200);
        }
        else{
            return response()->json([
                'status'     => false,
                'message'    => 'Customer User Not Found',
                'data'       =>null,
            ], 401);
        }
        
    }
    public function addressAdd(Request $request)
    {
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'state_id' => 'required',
                'municipality_id' => 'required',
                'district_id' => 'required',
                'address' => 'required',
            ]);
            if ($validator->passes()) {
            $address['title'] = $request->title;
            $address['state_id'] = $request->state_id;
            $address['municipality_id'] = $request->municipality_id;
            $address['district_id'] = $request->district_id;
            $address['address'] = $request->address;
            $address['user_id'] = $customer->id;

            $result = BillingAddress::create($address);
            if ($result) {
                    $data = array(
                        'status'  =>true,
                        'message' =>'Address added Successfully!' ,
                        'data'=> null,
                        );
                    return response()->json($data,200);
                }
                else{
                    return response()->json([
                        'message'   =>'Something went wrong !',
                        'status'    =>false,
                        'data'      =>null,
                    ], 401);
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
                'message'    => 'Customer User Not Found',
                'data'       =>null,
            ], 401);
        }
    }
    public function addressDelete(Request $request,$id)
    {
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            if ($customer) {
                $address=BillingAddress::where('id',$id)->first();
                if($address){
                        $address->delete();
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Address Deleted Successfully!',
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'Billing Address Not Found',
                        'data'       =>null,
                    ], 401);
                }
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Customer User Not Found',
                    'data'       =>null,
                ], 401);
            }
    	}

	}
	public function addressUpdate(Request $request,$id)
    {
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            if ($customer) {
                $address=BillingAddress::where('id',$id)->first();
                $address->title = $request->title;
                $address->state_id = $request->state_id;
                $address->municipality_id = $request->municipality_id;
                $address->district_id = $request->district_id;
                $address->address = $request->address;
                $address->save();
                if($address){
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Address Update Successfully!',
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'Billing Address Not Found',
                        'data'       =>null,
                    ], 401);
                }
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Customer User Not Found',
                    'data'       =>null,
                ], 401);
            }
	    }
	}

	 public function updatePassword(Request $request)
    {
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
                $validator = Validator::make($request->all(), [
                  'password' => 'required|confirmed|min:6',
                  'old_password' => 'required',
                ]);
                if ($validator->passes()) {
                    if(Hash::check($request->old_password, $customer->password))
                    {
                            $customer->password = bcrypt($request->password);
                            $customer->save();

                            $result = array(
                            'status'        =>true,
                            'message'       => 'Password Updated !',
                        );
                        return response()->json($result,200);
                    }else{
                         $result = array(
                            'message'       => 'Old Password Has Not Matched',
                            'status'        => false,
                            // 'data'    => $validator->errors()
                        );
                        return response()->json($result,422);
                    }
             } else{
                $result = array(
                    'message'     => 'Input Field Required',
                    'error'     => true,
                    'data'    => $validator->errors()
                );
                return response()->json($result,422);
            }
        }
        else{
            return response()->json([
                'status'     => false,
                'message'    => 'Customer User Not Found',
                'data'       =>null,
            ], 401);
        }
        
    }
    public function updateProfile(Request $request)
    {
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
            if ($customer) {
        $validator = Validator::make($request->all(), [
                'name'    => 'required|max:255',
                // 'mobile' => 'required|digits:10',
        ]);

        if ($validator->passes()) {
        
            if($request->has('name')){ 
                $customer->name = $request->name;
            }
            
            if($request->has('spin')){
                $customer->spin = $request->spin;
            }
            $customer->save();
            return response()->json([
                'status'  => true,
                'message' => 'Profile Updated'],200);

             } else{
            $result = array(
                'status'     => false,
                'message'     => 'Input Field Required',
                'error'     => true,
                'data'    => $validator->errors()
            );
            return response()->json($result,422);
        }

         }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Customer Not Found',
                ], 401);
            }

    }
}
