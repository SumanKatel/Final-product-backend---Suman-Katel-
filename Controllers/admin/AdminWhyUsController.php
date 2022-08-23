<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\admin\AdminLoginController;
use App\Model\admin\AdminWhyUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PrintHelper;


class AdminWhyUsController extends Controller {

    private $title = 'Why Us';
    private $sort_by = 'title';
    private $sort_order = 'asc';
    private $index_link = 'why-us.index';
    private $list_page = 'admin.why-us.list';
    private $create_form = 'admin.why-us.add';
    private $update_form = 'admin.why-us.edit';
    private $link = 'why-us';
    private $user_id;

    public function __construct(){
        $this->user_id = session('admin')['userid']; //AdminLoginController::id();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $list = AdminWhyUs::orderBy($this->sort_by, $this->sort_order)->get();
        $result = array(
            'list'              => $list,
            'page_header'       => $this->title,
            'link'              => $this->link,
        );
        return view($this->list_page, $result);
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
            'title' => 'required',
        ]);
        $crud = new AdminWhyUs;
        $crud->title = $request->title;
        $crud->description = $request->description;
        $crud->status = $request->status;
        $crud['sort_order'] = PrintHelper::nextSortOrder('tbl_why_us');
        $crud->created_by = session('admin')['userid'];
        $crud->icon = $request->icon;
        $crud->save();
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
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $record = AdminWhyUs::findOrFail($id);
        $list = AdminWhyUs::orderBy($this->sort_by, $this->sort_order)->get();
        $result = array(
            'page_header'       => 'Edit '.$this->title.' Detail',
            'record'            => $record,
            'list'              => $list,
            'link'              => $this->link,
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
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title'      => 'required',
        ]);
        $crud = AdminWhyUs::findOrFail($id);
        $crud->title = $request->title;
        $crud->description = $request->description;
        $crud->status = $request->status;
        $crud->icon = $request->icon;
        $crud->created_by = session('admin')['userid'];
        $crud->save();
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
        $crud = AdminWhyUs::findOrFail($id);
        $crud->delete();
        Session::flash('success_message', DELETED);
        return redirect(route($this->index_link));
    }
}