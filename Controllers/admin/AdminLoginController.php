<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Model\admin\AdminFailLoginLogs;
use App\Model\admin\AdminGroup;
use App\Model\admin\AdminSuccessLoginLogs;
use App\Model\admin\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminLoginController extends AdminController {

    private $link = 'user';

    public function login(Request $request){
        $value = $request->session()->get('admin');
        if ($value) {
            return redirect(route('dashboard'));
        }
    	return view('admin.login');
    }

    public function loginCheck(Request $request){
        $this->validate($request, [
                'username'              => 'required|string|min:6',
                'password'              => 'required|string|min:6',
            ]);
        $userdetail = AdminUser::getUserPassword($request->username);
    	if (!empty($userdetail)) {
            if (Hash::check($request->password, $userdetail->password)) {
                // update login time
                $user = AdminUser::findOrFail($userdetail->id);
                $user->lastlogin = date('Y-m-d H:i:s');
                $user->save();

                $succeslogs = new AdminSuccessLoginLogs;
                $succeslogs->user_id = $userdetail->id;
                $succeslogs->login_date = date('Y-m-d H:i:s');
                $succeslogs->login_device = Self::get_browser_name($_SERVER['HTTP_USER_AGENT']);
                $succeslogs->ip_address = $request->ip();
                $succeslogs->save();

                $request->session()->put('admin', array(
                    'userid'                => $userdetail->id,
                    'username'              => $userdetail->name,
                    'user_group_id'         => $userdetail->user_group_id,
                ));
                return redirect(route('dashboard'));
            }
            else {
                $faillogs = new AdminFailLoginLogs;
                $faillogs->user_name = $request->username;
                $faillogs->fail_password = $request->password;
                $faillogs->ip_address = $request->ip();
                $faillogs->login_device = Self::get_browser_name($_SERVER['HTTP_USER_AGENT']);
                $faillogs->save();
                Session::flash('message', "Login Credential, Incorrect !!! ");
                return redirect(route('login.page'));
            }
        } else {
            $faillogs = new AdminFailLoginLogs;
            $faillogs->user_name = $request->username;
            $faillogs->fail_password = $request->password;
            $faillogs->ip_address = $request->ip();
            $faillogs->login_device = Self::get_browser_name($_SERVER['HTTP_USER_AGENT']);
            $faillogs->save();
            Session::flash('message', "Login Credential, Incorrect !!! ");
            return redirect(route('login.page'));
        }
    }

    public function userRegister(){
        $value = self::id();
        if ($value != '1') {
          return redirect(route('dashboard'));
        } else{
            $admingroup = AdminGroup::orderBy('title','asc')->get();
            // $admingroup = AdminGroup::all();
            $result = array(
                'page_header'       => 'User Registration',
                'admingroup'        => $admingroup,
                'link'              => $this->link,
            );
    	   return view('admin.adminuser.register', $result);
        }
    }

    public function userRegisterData(Request $request){
    	$adminuser = new AdminUser;
    	$this->validate($request, [
                'name'          		=> 'required|string|min:6',
                'email'          		=> 'required|string|min:6|email',
                'mobileno'				=> 'required|string|min:6',
                'username'				=> 'required|string|min:6',
                'password'				=> 'required|string|min:6|confirmed',
            ]);
    	$adminuser->name = $request->name;
        $adminuser->email = $request->email;
        $adminuser->mobileno = $request->mobileno;
        $adminuser->username = $request->username;
        $adminuser->user_group_id = $request->user_group_id;
        $adminuser->password = bcrypt($request->password);
        $adminuser->status = $request->status;
        $adminuser->save();
        return redirect(route('dashboard'));
    }

    public function dashboard(){
        return view('admin.home');
    }

    public function adminUserList(){
        $value = self::id();
        if ($value != '1') {
          return redirect(route('dashboard'));
        } else{
            $userlist = AdminUser::with('admingroup')->get();
            // return $userlist;
            $result = array(
                'list'              => $userlist,
                'page_header'       => 'User List',
                'link'              => $this->link,
            );
            return view('admin.adminuser.list', $result);
        }
    }

     public static function id(){
        $value = isset(session('admin')['userid']) ? isset(session('admin')['userid']) : 0;
        return $value;
    }

    public static function deleteUser($id) {
        $value = self::id();
        if ($value != '1') {
          return redirect(route('dashboard'));
        } else{
            $user = AdminUser::findOrFail($id);
            $user->delete();
            return redirect(route('user.list'));
        }
    }

    public static function editUser($id){ // when admin want to edit other user profile
        $value = self::id();
        if ($value != '1') {
          return redirect(route('dashboard'));
        } else{
            $userlist = AdminUser::findOrFail($id);
            $admingroup = AdminGroup::orderBy('title','asc')->get();
            $result = array(
                'record'            => $userlist,
                'page_header'       => 'Edit User Registration',
                'admingroup'        => $admingroup,
                'link'              => 'user',
            );
            return view('admin.adminuser.edit', $result);
        }
    }

    public function updateuser(Request $request, $id){
        $adminuser = AdminUser::findOrFail($id);
        if (isset($request->changepwd) && $request->changepwd == 'on') {
            $this->validate($request, [
                'name'                  => 'required|string|min:6',
                'email'                 => 'required|string|min:6|email',
                'mobileno'              => 'required|string|min:6',
                'username'              => 'required|string|min:6',
                'password'              => 'required|string|min:6|confirmed',
            ]);
            $adminuser->password = bcrypt($request->password);
            $adminuser->name = $request->name;
            $adminuser->email = $request->email;
            $adminuser->mobileno = $request->mobileno;
            $adminuser->user_group_id = $request->user_group_id;
            $adminuser->username = $request->username;
            $adminuser->status = isset($request->status)?$request->status:'1';
            $adminuser->save();
        } else{
             $this->validate($request, [
                'name'                  => 'required|string|min:6',
                'email'                 => 'required|string|min:6|email',
                'mobileno'              => 'required|string|min:6',
                'username'              => 'required|string|min:6',
                'username'              => 'required|string|min:6',
            ]);
            $adminuser->name = $request->name;
            $adminuser->email = $request->email;
            $adminuser->mobileno = $request->mobileno;
            $adminuser->user_group_id = $request->user_group_id;
            $adminuser->username = $request->username;
            $adminuser->status = isset($request->status)?$request->status:'1';
            $adminuser->save();
        }
        Session::flash('success_message',UPDATED);
        return redirect(route('user.list'));
    }

    public function logout(Request $request){
    	/*$request->session()->flush();*/
    	$request->session()->forget('admin');
    	return redirect(route('login.page'));
    }

    public function get_browser_name($user_agent) {
        if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
        elseif (strpos($user_agent, 'Edge')) return 'Edge';
        elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
        elseif (strpos($user_agent, 'Safari')) return 'Safari';
        elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
        elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
        return 'Other';
    }

    /*public static function is_superadmin(){
        $value = self::id();
        if ($value != '1') {
          return redirect(route('dashboard'));
        }
    }*/

}