<?php

namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use App\Model\Workstation\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Validator;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{

    public function frontSignup(Request $request)
    {
        $validator=Validator::make($request->all(), [
                'name' => 'required',
                'mobile_number' => 'required||unique:customers|digits:10',
                'email'=>'required|unique:customers',
                'password' => 'required|confirmed|min:6',
            ]);
            if ($validator->passes()) {
            $a['name']=$request->name;
            $a['email']=$request->email;
            $a['address']=$request->address;
            $a['mobile_number']=$request->mobile_number;
            $password = $request->password;
            $a['password'] = bcrypt($password);
            $a['status'] = 1;
            $verifytoken = Str::random(20);
            $a['verify_token'] = $verifytoken;
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $verifyCode = mt_rand(10, 99). mt_rand(10, 99). $characters[rand(0, strlen($characters) - 1)];
            $access_token = base64_encode($verifytoken);
            $a['access_token'] = $access_token;

            $a['otp_code'] = $verifyCode;
            $result=Customer::create($a);
                if ($result) {

                    $user= array(
                               /* 'id' =>$result->id ,*/
                                'name' =>$result->name ,
                                'email' =>$result->email ,
                                'mobile_number' =>$result->mobile_number ,
                                'access_token' =>$result->access_token ,
                             );

                    $data = array(
                        'status'  =>true,
                        'message' =>'Customer register Successfully' ,
                        'data'=>$user,
                        );
                    return response()->json($data,200);
                }
            }else{
                $data= array(
                'status'        => false,
                'message'       => 'Input Field Required',
                'data'    => $validator->errors()
                     );
                return response()->json($data,422);
            }

    }


    public function frontLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'mobile_number'        => 'required',
            'password'      => 'required',
        ]);

        if ($validator->passes()) {
            $userdetail = Customer::where('mobile_number',$request->mobile_number )->orwhere('email',$request->mobile_number )->first();
            if (!empty($userdetail)) {
                    if (Hash::check($request->password, $userdetail->password)) {
                if ($userdetail->status == 1) {
                        $userdetail=Customer::find($userdetail->id);
                        $random = Str::random(40);
                        $access_token = base64_encode($random);
                        $userdetail->access_token = $access_token;
                        $userdetail->save();
                        $data= array(
                                'id' => $userdetail->id ,
                                'name' => $userdetail->name ,
                                'mobile_number' => $userdetail->mobile_number ,
                                'access_token' => $userdetail->access_token ,
                             );
                        $result = array(
                            'status'        => true,
                            'message'       => 'Login Successful',
                            'data'          =>$data,
                        );
                        return response()->json($result,200);
                    } else{
                        $result = array(
                            'status'    => false,
                            'message'       => 'You have not been verified, Please verify your account first.',
                        );
                        return response()->json($result,203);
                    }
                } else{
                     $result = array(
                            'status'        => false,
                            'message'   => 'Login Credential, Incorrect !!!',
                        );
                        return response()->json($result,401);
                }
            }else{
                // invalid user login
                $result = array(
                    'status'     => false,
                    'message'   => 'Login Credential, Incorrect !!!',
                );
                return response()->json($result,401);
            }

        } else{
            $result = array(
                'status'     => false,
                'message' => 'Input Field Required',
                'data'    => $validator->errors()
            );
            return response()->json($result,422);
        }
    }
    
    public function customerLogout(Request $request)
    {
        try {
            $customerId = $request->customerId;
            $customer=Customer::find($customerId);
            if ($customer) {
                $data=Customer::where('id', $customer->id)->update(['access_token' => null]);
                $result = array(
                                'status'        =>true,
                                'message'       => 'Logout Successfully',
                            );
                return response()->json($result,200);
            }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Customer Not Found!!',
                ], 401);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong!!'], 500);
        }
        
    }

}
