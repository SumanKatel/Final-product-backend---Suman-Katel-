<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Model\admin\AdminGroup;
use App\Model\admin\AdminRoleAccess;
use Illuminate\Http\Request;

class AdminRoleAccessController extends AdminController {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = AdminRoleAccess::all();
        $grouplist = AdminGroup::all();
        if ($request->group_id) {
            $group_id = $request->get('group_id');
            AdminRoleAccess::copyMenu($group_id);
            $menulist = AdminRoleAccess::getAccessMenu($id=0,$group_id);
        }else {
            $menulist = 0;
        }

        $result = array(
            'grouplist'             => $grouplist,
            'list'                  => $menulist,
            'page_header'           => 'Assign Roles to Group',
        );
        return view('admin.roleaccess.list', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {  }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = new AdminRoleAccess;
        $role->title = $request->title;
        $role->description = $request->description;
        $role->status = $request->status;
        $role->save();
        return redirect(route('usergroup.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {  }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rolelist = AdminRoleAccess::all();
        $role = AdminRoleAccess::findOrFail($id);
        $result = array(
            'list'          => $rolelist,
            'page_header'   => 'List of Group',
            'record'        => $role,
        );
        return view('admin.roleaccess.edit', $result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //$user_id = AdminLoginController::id();
        $role = AdminRoleAccess::findOrFail($id);
        $role->title = $request->title;
        $role->description = $request->description;
        $role->status = $request->status;
        $role->save();
        return redirect(route('usergroup.index'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = AdminRoleAccess::findOrFail($id);
        $role->delete();
        return redirect(route('usergroup.index'));
    }

    public function changeAccess($allowId, $id){
        return AdminRoleAccess::changePermission($allowId, $id);
    }
}
