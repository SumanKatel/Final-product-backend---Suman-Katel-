<?php
namespace App\Http\Middleware;
use App\Model\admin\AdminMenu;
use Closure;
class RolesMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $user_group_id = session('admin')['user_group_id'];
        /*
         * Retrieves the action from request and gets the Controller Name and Method Name
         */
        $action = app('request')->route()->getAction();
        /*
         * Splits the controller and store in array
         */
        $controllers = explode("@", class_basename($action['controller']));
        // pe($controllers);
        /*
         * Checks the existence of controller and method
         */
        $controller_name = isset($controllers[0]) ? $controllers[0] : '';
        $method_name = isset($controllers[1]) ? $controllers[1] : '';
        /*
         *List of controller where permissions are not necessary
         */
        $publicController = [
            'AdminDashboardController',
            'AdminLoginController',
            'AdminProfileController',
            'AdminAjaxController',
            'AdminSiteSettingController',
            'AdminGalleryController',
            'AdminUIComponentController'
        ];
        /*
         * checks the controller in array where permission are not allowed
         */
        if (!in_array($controller_name, $publicController)) {
            // p($controller_name);
            // pe($user_group_id);
            // $user_group_id = Auth::user()->user_group_id;
            /*
             * Joins User Roles and Admin_menus on the basis of user_group_id from tbl_admin_roles and menu_controller from tbl_admin_menus
             */
            $res = AdminMenu::join('tbl_admin_roles', 'tbl_admin_menus.id', '=', 'tbl_admin_roles.menu_id')
                    ->select('tbl_admin_roles.*', 'tbl_admin_menus.*')
                    ->where('user_group_id', '=', $user_group_id)
                    ->where('menu_controller', '=', $controller_name)
                    ->first();
            if (empty($res)) {
                $this->noPermission();
            } else {
                /*
                 * List of method where permissions are checked
                 */
                $arr = ['index', 'create', 'edit', 'destroy', 'show'];
                /*
                 * Search whether the method name exist in the array
                 */
                if (in_array($method_name, $arr)) {
                    $isView = $res->allow_view;
                    $isAdd = $res->allow_add;
                    $isEdit = $res->allow_edit;
                    $isDelete = $res->allow_delete;
                    switch ($method_name) {
                        case  'index':
                        if ($isView != 1) {
                            $this->noPermission();
                        }
                        break;
                        case  'create':
                        if ($isAdd != 1) {
                            $this->noPermission();
                        }
                        break;
                        case  'edit':
                        if ($isEdit != 1) {
                            $this->noPermission();
                        }
                        break;
                        case  'destroy':
                        if ($isDelete != 1) {
                            $this->noPermission();
                        }
                        break;
                        case  'show':
                        if ($isView != 1) {
                            $this->noPermission();
                        }
                        break;
                    }
                }
            }
        }
        return $next($request);
    }
    function noPermission() {
        echo "<h2>Sorry you do not have permission</h2>";
        echo "This action has been logged and notified to administrator";
        echo "<br /><a href='".route('dashboard')."'>click here to go back</a>";
        dd();
    }
}
