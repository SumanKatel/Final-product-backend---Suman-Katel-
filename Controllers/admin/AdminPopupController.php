<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\admin\AdminLoginController;
use App\Model\admin\AdminPopup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminPopupController extends Controller {

    private $title = 'Popup';
    private $sort_by = 'published_date';
    private $sort_order = 'desc';
    private $index_link = 'popup.index';
    private $list_page = 'admin.popup.list';
    private $create_form = 'admin.popup.add';
    private $update_form = 'admin.popup.edit';
    private $link = 'popup';
    private $user_id;

    public function __construct(){
        $this->user_id = AdminLoginController::id();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = AdminPopup::orderBy($this->sort_by, $this->sort_order)->get();
        $result = array(
            'list'              => $list,
            'link'              => $this->link,
            'page_header'       => 'List of '.$this->title,
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
        $result = array(
            'page_header'       => 'Add '.$this->title.' Detail',
            'link'              => $this->link,
        );
        return view($this->create_form, $result);
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
            'title'         => 'required',
            'image'         => 'required',
        ]);

        $crud = new AdminPopup;
        $crud->title = $request->title;
        $crud->link = $request->link;
        $crud->image = $request->image;
        $crud->published_date = $request->published_date;
        $crud->created_by = $this->user_id;
        $crud->status = $request->status;
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
        $record = AdminPopup::findOrFail($id);
        $result = array(
            'page_header'       => 'Edit '.$this->title.' Detail',
            'record'            => $record,
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
            'title'     => 'required',
            'image'      => 'required',
        ]);

        $user_id = AdminLoginController::id();
        $crud = AdminPopup::findOrFail($id);
        $crud->title = $request->title;
        $crud->link = $request->link;
        $crud->image = $request->image;
        $crud->published_date = $request->published_date;
        $crud->updated_by = $user_id;
        $crud->status = $request->status;
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
        $crud = AdminPopup::findOrFail($id);
        $crud->delete();
        Session::flash('success_message', DELETED);
        return redirect(route($this->index_link));
    }
}