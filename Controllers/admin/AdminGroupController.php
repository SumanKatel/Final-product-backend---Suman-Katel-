<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Model\admin\AdminGroup;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class AdminGroupController extends AdminController{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $link = "usergroup";
    private $index_link = "usergroup.index";

    public function index()
    {
        $group = AdminGroup::all();
        $result = array(
            'list'          => $group,
            'page_header'   => 'List of User Group',
            'link'          => $this->link,
        );
        return view('admin.usergroup.list', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
        return redirect(route($this->index_link));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'             => 'required',
            'description'       => 'required',
        ]);
        $group = new AdminGroup;
        $group->title = $request->title;
        $group->description = $request->description;
        $group->status = $request->status;
        $group->save();
        Session::flash('success_message', CREATED);
        return redirect(route($this->index_link));
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
        $grouplist = AdminGroup::all();
        $group = AdminGroup::findOrFail($id);
        $result = array(
            'list'          => $grouplist,
            'page_header'   => 'List of User Group',
            'record'        => $group,
            'link'          => $this->link,
        );
        return view('admin.usergroup.edit', $result);
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
        $this->validate($request, [
            'title'             => 'required',
            'description'       => 'required',
        ]);
        //$user_id = AdminLoginController::id();
        $group = AdminGroup::findOrFail($id);
        $group->title = $request->title;
        $group->description = $request->description;
        $group->status = $request->status;
        $group->save();
        Session::flash('success_message', UPDATED);
        return redirect(route($this->index_link));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group = AdminGroup::findOrFail($id);
        $group->delete();
        Session::flash('success_message', DELETED);
        return redirect(route($this->index_link));
    }
}
