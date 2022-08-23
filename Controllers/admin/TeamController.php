<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Model\admin\Team;
use App\Model\admin\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TeamController extends AdminController {

    private $title = 'Teams';
    private $sort_by = 'sort_order';
    private $sort_order = 'asc';
    private $index_link = 'teams.index';
    private $list_page = 'admin.team.list';
    private $create_form = 'admin.team.add';
    private $update_form = 'admin.team.edit';
    private $link = 'teams';
    private $user_id;

    public function __construct(){
        $this->user_id = AdminLoginController::id();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $list = Team::all();
        $result = array(
            'list'              => $list,
            'page_header'       => 'List of '.$this->title,
            'link'              => $this->link,
        );
        return view($this->list_page, $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $departments=Department::where('status','1')->get();
        $result = array(
            'page_header'       => 'Create '.$this->title.' Detail',
            'link'              => $this->link,
            'departments'      =>$departments
        );
        return view($this->create_form, $result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $this->validate($request, [
            'name'              => 'required',
            'info'        => 'required',
            'image'=>'required',
        ]);
        $request->image = ($request->image != '')?chunkfullurl($request->image):null;
        $request=Team::create($request->all());
        Session::flash('success_message', CREATED);
        return redirect(route($this->index_link));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        $pages = Team::findOrFail($id);
        $departments=Department::where('status','1')->get();
        $result = array(
            'page_header'       => 'Edit '.$this->title.' Detail',
            'record'            => $pages,
            'link'              => $this->link,
            'departments'      =>$departments

        );
        return view($this->update_form, $result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $this->validate($request, [
            'name'              => 'required',
            'info'        => 'required',
        ]);


        $crud = Team::findOrFail($id);

        $crud->image = ($request->image != '')?chunkfullurl($request->image):null;
        $crud->update($request->all());
        Session::flash('success_message', UPDATED);
        return redirect(route($this->index_link));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $crud = Team::findOrFail($id);
        $crud->delete();
        Session::flash('success_message', DELETED);
        return redirect(route($this->index_link));
    }
}