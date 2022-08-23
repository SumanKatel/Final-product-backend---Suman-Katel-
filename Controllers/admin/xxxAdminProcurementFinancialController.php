<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Model\admin\AdminProcurementFinancial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminProcurementFinancialController extends AdminController {

    private $sort_by = 'title_en';
    private $sort_order = 'asc';
    private $index_link = 'procurementfinancial.index';
    private $list_page = 'admin.procurementfinancial.list';
    private $create_form = 'admin.procurementfinancial.add';
    private $update_form = 'admin.procurementfinancial.edit';
    private $link = 'procurementfinancial';
    private $title = 'Procurement Financial';
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
        $list = AdminProcurementFinancial::all();
        // $list = AdminProcurementFinancial::orderBy($this->sort_by, $this->sort_order)->paginate(PAGES);
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
    public function store(Request $request){
        $this->validate($request, [
            'title_en'              => 'required',
            'title_np'              => 'required',
            'description_en'        => 'required',
            'description_np'        => 'required',
        ]);
        $crud = new AdminProcurementFinancial;
        $crud->title_en = $request->title_en;
        $crud->title_np = $request->title_np;
        $crud->description_en = $request->description_en;
        $crud->description_np = $request->description_np;
        $crud->published_date = $request->published_date;
        $crud->image = ($request->image != '')?chunkfullurl($request->image):null;
        $crud->file = ($request->file != '')?chunkfullurl($request->file):null;
        $crud->meta_title = $request->meta_title;
        $crud->meta_keywords = $request->meta_keywords;
        $crud->meta_description = $request->meta_description;
        $crud->slug = str_slug($request->title_en, '-');
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
        $pages = AdminProcurementFinancial::findOrFail($id);
        $result = array(
            'page_header'       => 'Edit '.$this->title.' Detail',
            'record'            => $pages,
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
    public function update(Request $request, $id){
        $this->validate($request, [
            'title_en'              => 'required',
            'title_np'              => 'required',
            'description_en'        => 'required',
            'description_np'        => 'required',
        ]);

        $crud = AdminProcurementFinancial::findOrFail($id);
        $crud->title_en = $request->title_en;
        $crud->title_np = $request->title_np;
        $crud->description_en = $request->description_en;
        $crud->description_np = $request->description_np;
        $crud->published_date = $request->published_date;
        $crud->meta_title = $request->meta_title;
        $crud->meta_keywords = $request->meta_keywords;
        $crud->meta_description = $request->meta_description;
        $crud->image = ($request->image != '')?chunkfullurl($request->image):null;
        $crud->file = ($request->file != '')?chunkfullurl($request->file):null;
        $crud->slug = str_slug($request->slug,'-');
        $crud->updated_by = $this->user_id;
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
    public function destroy($id){
        $crud = AdminProcurementFinancial::findOrFail($id);
        $crud->delete();
        Session::flash('success_message', DELETED);
        return redirect(route($this->index_link));
    }
}