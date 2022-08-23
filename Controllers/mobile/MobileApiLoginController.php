<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Model\Workstation\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MobileApiLoginController extends Controller
{


    // $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    // $register_code = $state->id.mt_rand(10, 99). mt_rand(10, 99). $characters[rand(0, strlen($characters) - 1)];





    public function frontSignup(Request $request)
    {
        $validator=Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'mobile' => 'required|unique:users|digits:10',
                'password' => 'required|confirmed|min:6',
            ]);
            if ($validator->passes()) {

            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $verifyCode = mt_rand(100, 999). mt_rand(100, 999). $characters[rand(0, strlen($characters) - 1)];
            $random = Str::random(40);
            $access_token = base64_encode($random);
            $customer['name'] = $request->name;
            $customer['email'] = $request->email;
            $customer['mobile'] = $request->mobile;
            $customer['user_code'] = $verifyCode;
            $otpcode = mt_rand(100, 999). mt_rand(100, 999);
            $customer['otp_code'] = $otpcode;
            $customer['status'] = 0;
            $customer['mobile_access_token'] = $access_token;
            $customer['password'] = bcrypt($request->password);
            $result = Customer::create($customer);
            if ($result) {
                
            //     $textmsg = "Dear user, your OTP is ".$otpcode.'. kitabyatra.com';
            //     $api_url = "http://api.sparrowsms.com/v2/sms/?".
            //     http_build_query(array(
            //     'token' => 'smbmlduE5JuZSCpz4BWd',
            //     'from'  => 'InfoSMS',
            //     'to'    => $result->mobile,
            //     'text'  => $textmsg,
            // ));
            //     $response = file_get_contents($api_url);
                
            sendCodeToMobile($result->mobile,$result->otp_code);

            // sendCodeToMobile($result->mobile,$result->otp_code);
                $user = array(
                                'id' =>$result->id ,
                                'name' =>$result->name ,
                                'email' =>$result->email ,
                                'mobile' =>$result->mobile ,
                                'access_token' =>$result->mobile_access_token ,
                             );
                $user = array(
                			'user_data' => $user,
                		);

                    $data = array(
                        'status'  =>true,
                        'message' =>'We have sent you an OTP code in your mobile number. Please verify with code.' ,
                        'data'=>$user,
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
    public function frontLogin(Request $request){
        $validator = Validator::make($request->all(), [
                'mobile'              => 'required',
                'password'            => 'required',
            ],
            [
              'mobile.required'=> 'The mobile or email is Required', // custom message
            ]
        );

        if ($validator->passes()) {
            $userdetail = Customer::where('mobile',$request->mobile)->orWhere('email', $request->mobile)->first();

            if (!empty($userdetail)) {
                    if (Hash::check($request->password, $userdetail->password)) {
                if ($userdetail->status == 1) {
                        $userdetail=Customer::find($userdetail->id);
                        $random = Str::random(40);
                        $access_token = base64_encode($random);
                        $userdetail->mobile_access_token = $access_token;
                        $userdetail->save();
                        $data= array(
                                'id' => $userdetail->id ,
                                'name' => $userdetail->name ,
                                'mobile' => $userdetail->mobile ,
                                'access_token' => $userdetail->mobile_access_token ,
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
                            'message'       => 'You have been not verified, Please verify your account first.',
                        );
                        return response()->json($result,401);
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

    public function signupOtpVerify(Request $request)
    {

        $validator = Validator::make($request->all(), [
          'mobile'       => 'required',
          'otp_code'       => 'required',
        ]);

        if ($validator->passes()) {
            $customer = Customer::where('mobile',$request->mobile)->first();
            if($customer){
                $final = Customer::where('mobile',$request->mobile)->where('otp_code',$request->otp_code)->first();
                if($final)
                {
                    $final->status = 1;
                    $final->mobile_verify = 1;
                    $final->save();

                    $result = array(
                        'status'        => true,
                        'message'       => 'Your OTP verified!',
                    );
                    return response()->json($result,200);
                }
                else{
                     $result = array(
                            'status'        => false,
                            'message'       => 'OTP not match, Please enter right otp number.',
                        );
                        return response()->json($result,401);
                }
             } else{
                $result = array(
                    'status'  => false,
                    'message' => 'Customer not found !'
                );
                return response()->json($result,401);
            }

            } else{
                $result = array(
                    'status'  => false,
                    'message' => 'Input Field Required',
                    'data'    => $validator->errors()
                );
                return response()->json($result,422);
            }
        }

        public function forsentOtp(Request $request)
    	{

	        $validator = Validator::make($request->all(), [
	          'mobile'       => 'required',
	        ]);

	        if ($validator->passes()) {
	            $customer = Customer::where('mobile',$request->mobile)->first();
	            if($customer){

	            $otpcode = mt_rand(100, 999). mt_rand(100, 999);
	            //here is otp sent to mbl
	            $customer->otp_code=$otpcode;
	            $customer->save();
	            sendCodeToMobile($customer->mobile,$customer->otp_code);
	            $result = array(
	                            'status'        => true,
	                            'message'       => 'Otp sent Successful, Please check your mobile.',
	                        );
	                        return response()->json($result,200);
	             } else{
	                $result = array(
	                    'status'  => false,
	                    'message' => 'Customer not found !'
	                );
	                return response()->json($result,401);
	            }

            } else{
                $result = array(
                    'status'  => false,
                    'message' => 'Input Field Required',
                    'data'    => $validator->errors()
                );
                return response()->json($result,422);
            }
        }

        public function forverifyOtp(Request $request)
    	{

	        $validator = Validator::make($request->all(), [
	          'mobile'       => 'required',
	          'otp_code'       => 'required',
	        ]);

        	if ($validator->passes()) {
	            $customer = Customer::where('mobile',$request->mobile)->first();
	            if($customer){
	                $final = Customer::where('mobile',$request->mobile)->where('otp_code',$request->otp_code)->first();
	                if($final){
	                    $result = array(
	                        'status'        => true,
	                        'message'       => 'Your OTP verified!',
	                    );
	                    return response()->json($result,200);
	                }
	                else{
	                     $result = array(
	                            'status'        => false,
	                            'message'       => 'OTP not match, Please enter right otp number.',
	                        );
	                        return response()->json($result,401);
	                }
	             } else{
		                $result = array(
		                    'status'  => false,
		                    'message' => 'Customer not found !'
		                );
	                	return response()->json($result,401);
	            	}

        	} else{
                $result = array(
                    'status'  => false,
                    'message' => 'Input Field Required',
                    'data'    => $validator->errors()
                );
                return response()->json($result,422);
            }
        }

    public function postForgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
          'mobile'                    => 'required',
          'password'                  => 'required|confirmed',
        ]);

        if ($validator->passes()) {
            $customer = Customer::where('mobile',$request->mobile)->first();
            if($customer){
                    $customer->password = bcrypt($request->password);
                    $customer->otp_code = null;
                    $customer->status = 1;
                    $customer->mobile_verify = 1;
                    $customer->save();
                        $result = array(
                            'status'        => true,
                            'message'       => 'Password Updated Successful, Please login with new password.',
                        );
                        return response()->json($result,200);
             } else{
                $result = array(
                    'status'  => false,
                    'message' => 'Customer not found !'
                );
                return response()->json($result,401);
            }

            } else{
                $result = array(
                    'status'  => false,
                    'message' => 'Input Field Required',
                    'data'    => $validator->errors()
                );
                return response()->json($result,422);
            }
        }

    public function userLogout(Request $request)
    {
        try {
            $customerId = $request->customerId;
            $customer=Customer::find($customerId);
            if ($customer) {
                $data=Customer::where('id', $customer->id)->update(['mobile_access_token' => null]);
                $result = array(
                                'status'        =>true,
                                'message'       => 'Logout Successfully',
                                'data'          => null,
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
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong!!'], 500);
        }
        
    }

}
