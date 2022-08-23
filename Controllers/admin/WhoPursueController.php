<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\admin\AdminLoginController;
use App\Model\admin\Course;
use App\Model\admin\WhoPursue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PrintHelper;


class WhoPursueController extends Controller {

    private $title = 'Who Pursue';
    private $sort_by = 'sort_order';
    private $sort_order = 'asc';
    private $index_link = 'who-pursue.index';
    private $list_page = 'admin.who-pursue.list';
    private $create_form = 'admin.who-pursue.add';
    private $update_form = 'admin.who-pursue.edit';
    private $link = 'who-pursue';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $list = WhoPursue::orderBy($this->sort_by, $this->sort_order)->get();
        $courses=Course::where('status','1')->get();
        $result = array(
            'list'              => $list,
            'courses'           => $courses,
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
            'description' => 'required',
        ]);
        $crud = new WhoPursue;
        $crud->description = $request->description;
        $crud->status = $request->status;
        $crud->for_who = $request->for_who;
        $crud['sort_order'] = PrintHelper::nextSortOrder('tbl_why_us');
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
        $record = WhoPursue::findOrFail($id);
        $courses=Course::where('status','1')->get();
        $list = WhoPursue::orderBy($this->sort_by, $this->sort_order)->get();
        $result = array(
            'page_header'       => 'Edit '.$this->title.' Detail',
            'record'            => $record,
            'list'              => $list,
            'courses'           => $courses,
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
            'description'      => 'required',
        ]);
        $crud = WhoPursue::findOrFail($id);
        $crud->description = $request->description;
        $crud->status = $request->status;
        $crud->for_who = $request->for_who;
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
        $crud = WhoPursue::findOrFail($id);
        $crud->delete();
        Session::flash('success_message', DELETED);
        return redirect(route($this->index_link));
    }
}