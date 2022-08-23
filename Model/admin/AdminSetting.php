<?php

namespace App\Model\admin;

use App\Http\Controllers\admin\AdminLoginController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminSetting extends Model {

	protected $table = 'tbl_site_setting';
    protected $guarded = ['id'];

	public static function getUserDesignLayout(){
		$userid = AdminLoginController::id();
		$data = DB::table('tbl_admin_users')
					->where('id',$userid)
					->select('ui_skin')
					->first();

		return $data;
	}

	public static function getMenuList($id){
        $query = DB::table('tbl_menu_items')
        		->where('menu',$id)
        		->where('parent','0');
        		if(\App::getLocale() == 'np'){
        			$query->select('id','label_np as label','link','newtab','class');
        		}else{
        			$query->select('id','label','link','newtab','class');
        		}
        $data = $query->orderBy('sort','asc')
                ->get();
        return $data;
    }

    public static function getChildMenu($id){
	    $query = DB::table('tbl_menu_items')
	    		->where('parent',$id);
	    		if(\App::getLocale() == 'np'){
        			$query->select('id','label_np as label','link','newtab');
        		}else{
        			$query->select('id','label','link','newtab');
        		}
	    $data = $query->orderBy('sort','asc')
	            ->get();
	    return $data;
	}

	public static function getMenuNameById($menuid){
        $data = DB::table('tbl_menus')
                // ->where('status','1')
                ->where('id',$menuid)
                ->select('id','name')
                ->first();
        return $data;
    }
	
}
