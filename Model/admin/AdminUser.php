<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminUser extends Model{
	
	protected $table = 'tbl_admin_users';
    protected $guarded = ['id'];

    public static function getUserPassword($username) {
    	$data = DB::table('tbl_admin_users')
                -> select('id', 'name', 'password', 'username', 'email','user_group_id')
                -> where('username', $username)
                -> where('status', '1')
                -> first();
        return $data;
    }

   /* public static function getAdminUserList() {
        $data = DB::table('tbl_admin_users')
                ->get();
        return $data;
    }*/

    public static function updateUserSkinLayout($skin,$userid) {
        $data = DB::table('tbl_admin_users')
                ->where('id', $userid)
                ->update(['ui_skin' => $skin]);
        return $data;
    }

    public function admingroup(){
        return $this->belongsTo(AdminGroup::class,'user_group_id','id');
    }


}
