<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminMenu extends Model {
	protected $table = 'tbl_admin_menus';
    protected $guarded = ['id'];

	public static function getMenu($id){
		$user_group_id = session('admin')['user_group_id'];
		// if ($user_group_id == '1') {
  //           $result = DB::table('tbl_admin_menus')
  //               ->where('parent_id', $id)->where('status', '1')
  //               ->orderBy('menu_order', 'ASC')
  //               ->get();
  //       } else {
            $result = DB::table('tbl_admin_menus')->select('tbl_admin_menus.*')
                ->join('tbl_admin_roles', 'tbl_admin_menus.id', '=', 'tbl_admin_roles.menu_id')
                ->where('parent_id', $id)
                ->where('status', '1')
                ->where('allow_view', '1')
                ->where('user_group_id', $user_group_id)
                ->orderBy('menu_order', 'ASC')
                ->get();
        //}
        return $result;
	}

	public static function getAllMenus() {
		$user_group_id = session('admin')['user_group_id'];
		$result = DB::table('tbl_admin_menus')->select('tbl_admin_menus.*')
            ->join('tbl_admin_roles', 'tbl_admin_menus.id', '=', 'tbl_admin_roles.menu_id')
            ->where('parent_id', 0)
            ->where('status', 1)
            ->where('allow_view', 1)
            ->where('user_group_id', $user_group_id)
            ->orderBy('menu_order', 'ASC')
            ->get();
        return $result;
	}

   public static function getpms($id){
    $result=DB::table('tbl_menu_items')->where('parent',$id)->get();
    return $result;
  }
}
