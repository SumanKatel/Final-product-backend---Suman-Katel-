<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\admin\AdminLoginController;
use App\Model\admin\AdminPosts;
use App\Model\admin\AdminCategory;
use App\Model\admin\OrderProduct;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminPostsController extends Controller {

    private $title = 'Posts';
    private $sort_by = 'published_date';
    private $sort_order = 'desc';
    private $index_link = 'posts.index';
    private $list_page = 'admin.posts.list';
    private $create_form = 'admin.posts.add';
    private $update_form = 'admin.posts.edit';
    private $link = 'posts';
    private $user_id;

    public function __construct(){
        $this->user_id = AdminLoginController::id();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categorylist = AdminCategory::orderBy('title', 'asc')->get();
        if(!empty($_GET)){
            $title = $request->input('title');
            $category = $request->input('category');
            $date = $request->input('published_date');
            // $list = AdminPosts::getFilterDataSearch($title,$date);
            $query = AdminPosts::with('category');
            if ($title !='') {
                $query->where('title','like', '%'.$title.'%');
            }
            if ($date !='') {
                $query->where('published_date', $date);
            }
            if ($category !='') {
                $query->whereHas('category', function($q) use ($category) {
                        $q->where('category_id',$category);
                    });
            }
            $list = $query->select('id','title','slug','published_date','status')
                    ->orderBy('published_date', 'desc')
                    ->paginate(PAGES);
        }else{
            $list = AdminPosts::with('category')
                    ->select('id','title','slug','published_date','status')
                    ->orderBy($this->sort_by, $this->sort_order)
                    ->paginate(PAGES);
        }
        // return $list;
        $result = array(
            'categorylist'      => $categorylist,
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
    public function create()
    {
        $categorylist = AdminCategory::orderBy('title', 'asc')->get();
        $result = array(
            'categorylist'      => $categorylist,
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
            'title'              => 'required',
            'description'        => 'required',
            'category'           => 'required',
        ]);

        $crud = new AdminPosts;
        $crud->title = $request->title;
        $crud->description = $request->description;
        $crud->meta_title = $request->meta_title;
        $crud->meta_keywords = $request->meta_keywords;
        $crud->meta_description = $request->meta_description;
        $crud->image = $request->image;
        $crud->file = ($request->file != '')?chunkfullurl($request->file):null;
        $crud->published_date = $request->published_date;
        $crud->created_by = session('admin')['userid'];
        $crud->status = $request->status;
        $crud->save();
        $crud->category()->sync($request->category);
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
        $categorylist = AdminCategory::orderBy('title', 'asc')->get();
        $record = AdminPosts::with('category')->where('id',$id)->first();
        $result = array(
            'page_header'       => 'Edit '.$this->title.' Detail',
            'record'            => $record,
            'categorylist'      => $categorylist,
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
            'title'              => 'required',
            'description'        => 'required',
        ]);

        $crud = AdminPosts::findOrFail($id);
        $crud->title = $request->title;
        $crud->description = $request->description;
        $crud->meta_title = $request->meta_title;
        $crud->meta_keywords = $request->meta_keywords;
        $crud->meta_description = $request->meta_description;
        $crud->image = $request->image;
        $crud->file = ($request->file != '')?chunkfullurl($request->file):null;
        $crud->published_date = $request->published_date;
        $crud->slug = str_slug($request->title, '-');
        $crud->updated_by = session('admin')['userid'];
        $crud->status = $request->status;
        $crud->save();
        $crud->category()->sync($request->category);
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
        $crud = AdminPosts::findOrFail($id);
        $crud->delete();
        AdminPosts::deleteNewsCategoryList($id);
        Session::flash('success_message', DELETED);
        return redirect(route($this->index_link));
    }
    
}