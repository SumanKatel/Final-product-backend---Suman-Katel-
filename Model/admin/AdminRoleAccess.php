<?php
namespace App\Model\admin;
use App\Model\admin\AdminMenu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class AdminRoleAccess extends Model {
    protected $table = 'tbl_admin_roles';
    protected $guarded = ['id'];
    public static function getAccessMenu($id, $group_id) {
        $result = DB::table('tbl_admin_menus')
                    ->join('tbl_admin_roles', 'tbl_admin_menus.id', '=', 'tbl_admin_roles.menu_id')
                    ->where('parent_id', $id)
                    ->where('status', 1)
                    ->where('user_group_id', $group_id)
                    ->select( 'tbl_admin_roles.id as group_role_id', 'user_group_id', 'menu_id', 'allow_view', 'allow_add', 'allow_edit', 'allow_delete', 'tbl_admin_menus.*' )
                    ->orderBy('menu_order', 'ASC')
                    ->get();
        return  $result;
    }

    public static function copyMenu($group_id) {
        if($group_id != 0) {
            $menus = AdminMenu::all();
            foreach ($menus as $menu) {
                if(Self::checkMenu($group_id, $menu->id) == 0) {
                    AdminRoleAccess::insert([
                                'user_group_id' => $group_id,
                                'menu_id' => $menu->id,
                                'allow_view' => '0',
                                'allow_add' => '0',
                                'allow_edit' => '0',
                                'allow_delete' => '0'
                            ]);
                }
            }
        }
    }

    public static function checkMenu($group_id, $menu_id) {
        return DB::table('tbl_admin_roles')
                    ->where('user_group_id', '=', $group_id)
                    ->where('menu_id', '=', $menu_id)
                    ->count();
    }

    public static function changePermission($allowId, $id) {
        if($allowId == 1) {
            $value =DB::table('tbl_admin_roles')
                ->where('id', '=', $id)
                ->select('allow_view')->first();
           DB::table('tbl_admin_roles')
                ->where('id', '=', $id)
                ->update(['allow_view' => ($value->allow_view == '1')?'0':'1']);
        }
        elseif($allowId == 2) {
            $value =DB::table('tbl_admin_roles')
                ->where('id', '=', $id)
                ->select('allow_add')->first();
           DB::table('tbl_admin_roles')
                ->where('id', '=', $id)
                ->update(['allow_add' => ($value->allow_add == '1')?'0':'1']);
        }
        elseif($allowId == 3) {
            $value =DB::table('tbl_admin_roles')
                ->where('id', '=', $id)
                ->select('allow_edit')->first();
           DB::table('tbl_admin_roles')
                ->where('id', '=', $id)
                ->update(['allow_edit' => ($value->allow_edit == '1')?'0':'1']);
        }
        else {
            $value =DB::table('tbl_admin_roles')
                ->where('id', '=', $id)
                ->select('allow_delete')->first();
           DB::table('tbl_admin_roles')
                ->where('id', '=', $id)
                ->update(['allow_delete' => ($value->allow_delete == '1')?'0':'1']);
        }
    }
}
