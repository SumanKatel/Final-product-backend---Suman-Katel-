<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminSuccessLoginLogs extends Model {

    protected $table = 'tbl_user_login_logs';
    protected $guarded = ['id'];
    public $timestamps = false;

    public static function getSuccessLoginList($userid){
    	$data = DB::table('tbl_user_login_logs AS L')
                    ->where('L.user_id',$userid)
                    ->leftjoin('tbl_admin_users AS A','L.user_id','A.id')
					->select('L.*','A.username')
                    ->orderBy('id','desc')
					->paginate(20);
		return $data;
    }

}

