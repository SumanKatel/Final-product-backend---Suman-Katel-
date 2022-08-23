<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Model\admin\AdminFailLoginLogs;
use App\Model\admin\AdminSetting;
use App\Model\admin\AdminSuccessLoginLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminSiteSettingController extends AdminController {
    
    public function successLogin(){
        $user_id = AdminLoginController::id();
    	$loginlist = AdminSuccessLoginLogs::getSuccessLoginList($user_id);
    	$result = array(
    		'page_header' 		=> 'Login Logs', 
    		'list' 				=> $loginlist, 
    	);
        return view('admin.logs.successlogin', $result);
    }

    public function failLogin(){
    	$failLogins = AdminFailLoginLogs::orderBy('created_at','desc')->paginate(20);
    	$result = array(
    		'page_header' 		=> 'Fail Login Logs', 
    		'list' 				=> $failLogins, 
    	);
        return view('admin.logs.faillogin', $result);
    }

    function setting(){
        $settingdata = AdminSetting::find(1);
        $result = array(
            'page_header'       => 'Site Setting Management',
            'settingdata'       => $settingdata,
        );
        return view('admin.setting', $result);
    }

    function updateSetting(Request $request){
        $inputs = $request->all();
        $user_id = AdminLoginController::id();
        $data = AdminSetting::findOrFail(1);
        $data->fill($inputs);

         if(!empty($request->file('chairman_image'))){
            $image = $request->file('chairman_image');
            $extension = $image->getClientOriginalExtension();
            $bannerName = 'chairman'. time(). '.' . $extension;
            $image->move(public_path('images/chairman/image'), $bannerName);
            $data->chairman_image=$bannerName;
            }

        $inputs['updated_by'] = $user_id;
        $data->save();
        Session::flash('success_message', "Successfully Updated !!!");
        return redirect(route('setting'));
    }
}
