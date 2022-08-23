<?php

namespace App\Http\Controllers\Workstation;
use App\Http\Controllers\Controller;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    public function workstationLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'mobile'        => 'required',
            'password'      => 'required',
        ]);

        if ($validator->passes()) {
            $userdetail = WorkstationUser::where('mobile',$request->mobile)->first();

            if (!empty($userdetail)) {
                    if (Hash::check($request->password, $userdetail->password)) {
                if ($userdetail->status == 1) {
                        $userdetail=WorkstationUser::find($userdetail->id);
                        $random = Str::random(40);
                        $access_token = base64_encode($random);
                        $userdetail->access_token = $access_token;
                        $userdetail->save();
                        $data= array(
                                'id' => $userdetail->id ,
                                'name' => $userdetail->name ,
                                'mobile' => $userdetail->mobile ,
                                'access_token' => $userdetail->access_token ,
                             );
                        $result = array(
                            'status'        => 200,
                            'message'       => 'Login Successful',
                            'data'          =>$data,
                        );
                        return response()->json($result,200);
                    } else{
                        $result = array(
                            'status'    => 401,
                            'message'       => 'You have been not verified, Please contact to admin.',
                        );
                        return response()->json($result,200);
                    }
                } else{
                     $result = array(
                            'status'        => 401,
                            'message'   => 'Login Credential, Incorrect !!!',
                        );
                        return response()->json($result,200);
                }
            }else{
                // invalid user login
                $result = array(
                    'status'     => 401,
                    'message'   => 'Login Credential, Incorrect !!!',
                );
                return response()->json($result,200);
            }

        } else{
            $result = array(
                'status'     => 422,
                'message' => 'Input Field Required',
                'data'    => $validator->errors()
            );
            return response()->json($result,200);
        }
    }

}
