<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Model\admin\Customer;
use Illuminate\Http\Request;
use App\Model\admin\Product;
use Illuminate\Support\Facades\Validator;
use App\Model\admin\Booked;


class ProfileController extends Controller
{

    public function getProfile(Request $request){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
                $array= array(
                      'customer_name'     => $customer->name ,
                      'customer_slug'     => $customer->slug ,
                      'customer_phone'    => $customer->mobile_number ,
                      'customer_email'    => $customer->email ,
                       );
                    
            $result = array(
                    'status'        => true,
                    'message'       => 'User Profile Data Fetched',
                    'data'          =>  $array,
                );

            return response()->json($result,200);
        }
        else{
            return response()->json([
                    'status'     => false,
                    'message'    => 'Customer Not Found!!',
            ], 401);
        }
    }
    
    public function updateProfile(Request $request){
        $customerId = $request->customerId;
        $customer   = Customer::find($customerId);
        if ($customer) {
        $validator=Validator::make($request->all(), [
                'name' => 'required',
                'mobile_number' => 'required||unique:customers|digits:10',
                'email'=>'required|unique:customers',
            ]);

            if ($validator->passes()) {
                     $customer->name = $request->name;
                     $customer->mobile_number = $request->mobile_number;
                     $customer->email = $request->email;
                     $cart->save();
                    $data = array(
                        'status'        => true,
                        'message'       => 'Cart Update' ,
                        );
                    return response()->json($data,200);
                }else{

                        $result = array(
                        'status'        => false,
                        'message'       => 'Input Field Required',
                        'data'        => $validator->errors()
                        );
                        return response()->json($result,422);
                    }
                }else
                {
                    return response()->json([
                            'status'     => false,
                            'message'    => 'Customer Not Found!!',
                        ], 401);
                }
            }

    public function updatePassword(Request $request)
    {
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
                $validator = Validator::make($request->all(), [
                  'password'     => 'required|confirmed|min:6',
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
    
    public function book(Request $request)
    {
            $customerId = $request->customerId;
            $customer   = Customer::find($customerId);
            if ($customer) {
            $validator=Validator::make($request->all(), [
                    'product_id' => 'required',
                ]);

            if ($validator->passes()) {
              $crud = new Booked();
              $crud->product_id = $request->product_id;
              $crud->customer_id = $request->customer_id;
              $crud->address = $request->address;
              $crud->save();
                    $result = array(
                            'status'        =>true,
                            'message'       => 'Book Successful!!',
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
                }else
                {
                    return response()->json([
                            'status'     => false,
                            'message'    => 'Customer Not Found!!',
                        ], 401);
                }
            }
     public function bookList(Request $request)
    {
            $customerId = $request->customerId;
            $customer   = Customer::find($customerId);
            if ($customer) {
                $array=array();
                $bookList = Booked::where('customer_id',$customer->id)->get();
                foreach ($bookList as $key => $value) {
                $product = Product::find($value->product_id);
                $array[]= array(
                  'book_id' =>$value->id ,
                  'customer_name' => $customer->name ,
                  'customer_mobile_number' => $customer->mobile_number ,
                  'product_id'      => $product->id ,
                  'product_name'      => $product->title ,
                  'product_thumbnail' => asset($product->image) ,
                  'slug'              => $product->slug ,
                  'price'             => $product->price ,
                   );
                }
                
                 $result = array(
                            'status'        =>true,
                            'message'       => 'Booked Data Fetched',
                            'data' =>$array,
                        );

        return response()->json($result,200);
        
            }else
                {
                    return response()->json([
                            'status'     => false,
                            'message'    => 'Customer Not Found!!',
                        ], 401);
                }
    }
    
}
