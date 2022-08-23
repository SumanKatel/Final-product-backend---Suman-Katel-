<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\admin\AdminLoginController;
use App\Model\admin\AdminCategory;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminCategoryController extends Controller {

    private $title = 'Category';
    private $sort_by = 'title';
    private $sort_order = 'asc';
    private $index_link = 'category.index';
    private $list_page = 'admin.category.list';
    private $create_form = 'admin.category.add';
    private $update_form = 'admin.category.edit';
    private $link = 'category';
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

        $list = AdminCategory::orderBy($this->sort_by, $this->sort_order)->get();
        // $list = AdminCategory::orderBy($this->sort_by, $this->sort_order)->paginate(PAGES);
        $result = array(
            'list'              => $list,
            'page_header'       => 'Add '.$this->title,
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
        $crud = new AdminCategory;
        $crud->title = $request->title;
        $crud->description = $request->description;
        $crud->status = $request->status;
        $crud->created_by = session('admin')['userid'];
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
        $record = AdminCategory::findOrFail($id);
        $list = AdminCategory::orderBy($this->sort_by, $this->sort_order)->get();
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
            'slug'       => 'required',
        ]);
        $crud = AdminCategory::findOrFail($id);
        $crud->title = $request->title;
        $crud->description = $request->description;
        $crud->status = $request->status;
        $crud->slug = str_slug($request->slug,'-');
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
        $crud = AdminCategory::findOrFail($id);
        if ($crud) {
            $data=DB::table('rel_post_category')->where('category_id',$crud->id)->get();
            if(count($data)>0)
                {
                    Session::flash('success_message', 'This category is in use. Unable to delete!');
                    return back();
                }
        }
        $crud->delete();
        Session::flash('success_message', DELETED);
        return redirect(route($this->index_link));
    }
}