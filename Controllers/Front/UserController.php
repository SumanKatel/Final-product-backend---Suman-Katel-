<?php

namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use App\Model\Front\CustomerFollowAuthor;
use App\Model\Workstation\BillingAddress;
use App\Model\Workstation\Country;
use App\Model\Workstation\Customer;
use App\Model\Workstation\DeliveryAddressPrice;
use App\Model\Workstation\District;
use App\Model\Workstation\Municipality;
use App\Model\Workstation\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Validator;


class UserController extends Controller
{

    public function isfollowAuthor(Request $request)
    {
            $customerId = $request->customerId;
            $customer=Customer::find($customerId);
            if ($customer) {
                $customerFollowAuthor = CustomerFollowAuthor::where('customer_id',$customer->id)->where('author_id',$request->author_id)->first();
                if ($customerFollowAuthor) {
                    $is_follow = 1;
                }else{
                    $is_follow = 0;
                }
                    $result = array(
                            'status'        => true,
                            'is_follow'       => $is_follow,
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

    public function followAuthor(Request $request)
    {
            $customerId = $request->customerId;
            $customer=Customer::find($customerId);
            if ($customer) {
                $customerFollowAuthor = CustomerFollowAuthor::where('customer_id',$customer->id)->where('author_id',$request->author_id)->first();
                if ($customerFollowAuthor) {
                   $customerFollowAuthor->delete();
                   $result = array(
                            'status'        => true,
                            'message'       => 'UnFollow',
                        );
                    return response()->json($result,200);
                }else{
                    $follow['customer_id'] = $customer->id;
                    $follow['author_id'] = $request->author_id;
                    $data = CustomerFollowAuthor::create($follow);
                    $result = array(
                            'status'        => true,
                            'message'       => 'Follow',
                        );
                    return response()->json($result,200);
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
                        $result = array(
                            'status'        => true,
                            'message'       => 'Profile Data Fetched',
                            'data'          =>$data,
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

     public function addressList(Request $request)
    {
            $customerId = $request->customerId;
            $customer=Customer::find($customerId);
            if ($customer) {

            $array=array();
            $list = BillingAddress::where('user_id', $customer->id)->get();
                foreach ($list as $key => $value) {
                    $country = Country::where('id',$value->country_id)->first();
                    $state = State::where('id',$value->state_id)->first();
                    $district = District::where('id',$value->district_id)->first();
                    $municipality = Municipality::where('id',$value->municipality_id)->first();
                    $array[]= array(
                      'id' => $value->id ,
                      'state_id' => $value->state_id ?? null,
                      'country_id' => $value->country_id ?? null,
                      'country_name' => $country->title ?? null,
                      'title' => $value->title ,
                      'state_name' => $state->state_name_np ?? null,
                      'district_id' => $value->district_id ?? null,
                      'district_name' => $district->district_name_en ?? null,
                      'municipality_id' => $value->municipality_id ?? null,
                      'municipality_name' => $municipality->location_name_en ?? null,
                      'customer_name' => $customer->name,
                      'customer_address' => $value->address,
                      'customer_mobile' => $customer->mobile,
                      'is_foreign' => $value->is_foreign,
                       );
                }
                $result = array(
                                    'status'        => true,
                                    'message'       => 'Delivery Address Data Fetched',
                                    'data' => $array,
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
                'address' => 'required',
            ]);
            if ($validator->passes()) {
            $address['title'] = $request->title;
            $address['state_id'] = $request->state_id;
            $address['country_id'] = $request->country_id;
            if ($request->country_id) {
                $address['is_foreign'] = 1;
            }else{
                $address['is_foreign'] = 0;
            }
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
                    'message'    => 'Workstation User Not Found',
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
                $address->country_id = $request->country_id;
                if ($request->country_id) {
                $address['is_foreign'] = 1;
                }else{
                $address['is_foreign'] = 0;
                }
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
                        // $result = array(
                        //             'status'        =>true,
                        //             'price'         =>$price,
                        //             'free_above_order_value' =>$deliveryAddressc->free_above_order_value,
                        //             'is_available' =>$deliveryAddressc->is_available,
                        //             'min_day'       => $deliveryAddressc->min_day,
                        //             'max_day'       => $deliveryAddressc->max_day,
                        //             'message'       => 'Delivery Price!',
                        //         );
                        // return response()->json($result,200);

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
                    // return response()->json([
                    //     'status'     => false,
                    //     'message'    => 'Sorry the delivery service is not currently available on this location.',
                    //     'data'       =>null,
                    // ], 401);


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
                'mobile' => 'required|digits:10',
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
