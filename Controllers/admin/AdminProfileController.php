<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Model\admin\AdminUser;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminProfileController extends AdminController {
    
    public static function editUserProfile($editid){ // when user wants to change their profile
        try {
            $id = decrypt($editid);
            $userlist = AdminUser::findOrFail($id);
            $result = array(
                'record'      => $userlist,
                'page_header'   => 'Edit Profile Detail',
            );
            return view('admin.profile.edit', $result);
        } catch (DecryptException $e) {
            return redirect(route('dashboard'));
        }
    }

    public function updateuser(Request $request, $id){        
        $user_group_id = session('admin')['user_group_id'];
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
            $adminuser->username = $request->username;
            $adminuser->status = '1';
            $adminuser->save();
        } else{
             $this->validate($request, [
                'name'                  => 'required|string|min:6',
                'email'                 => 'required|string|min:6|email',
                'mobileno'              => 'required|string|min:6',
                'username'              => 'required|string|min:6',
            ]);
            $adminuser->name = $request->name;
            $adminuser->email = $request->email;
            $adminuser->mobileno = $request->mobileno;
            $adminuser->username = $request->username;
            $adminuser->status = '1';
            $adminuser->save();
        }
        $request->session()->forget('admin')['username'];
        $request->session()->put('admin', array(
        			'userid'                => $id,
                    'username'              => $adminuser->name,
                    'user_group_id'         => $user_group_id,
                ));
        
        Session::flash('success_message', UPDATED);
        return redirect(route('dashboard'));
    }
}
