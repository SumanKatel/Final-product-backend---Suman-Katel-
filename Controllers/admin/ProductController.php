<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Model\admin\Product;
use App\Model\admin\ProductCategory;
use App\Model\admin\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProductController extends AdminController {

    private $title = 'Products';
    private $sort_by = 'sort_order';
    private $sort_order = 'asc';
    private $index_link = 'product.index';
    private $list_page = 'admin.product.list';
    private $create_form = 'admin.product.add';
    private $update_form = 'admin.product.edit';
    private $link = 'product';
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
        $list = Product::all();
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
        $pc=ProductCategory::where('status','1')->get();
        $result = array(
            'page_header'       => 'Create '.$this->title.' Detail',
            'link'              => $this->link,
            'pc'              => $pc,
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
            'title'              => 'required',
            'product_category_id'     => 'required',
            'description'              => 'required',
        ],
        ['product_category_id.required' => 'The product category field is required']
    );
        $request->image = ($request->image != '')?chunkfullurl($request->image):null;
        $request=Product::create($request->all());
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
        $pages = Product::findOrFail($id);
        $pc=ProductCategory::where('status','1')->get();
        $result = array(
            'page_header'       => 'Edit '.$this->title.' Detail',
            'record'            => $pages,
            'link'              => $this->link,
            'pc'       =>$pc,
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
            'title'              => 'required',
            'description'        => 'required',
        ]);

        $crud = Product::findOrFail($id);
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
        $crud = Product::findOrFail($id);
        $crud->delete();
        Session::flash('success_message', DELETED);
        return redirect(route($this->index_link));
    }

    public function enrolBy($slug)
    {
        $data=Product::where('slug',$slug)->first();
        $productEnrol=OrderProduct::where('product_id',$data->id)->get();
        $result = array(
            'list'              => $productEnrol,
            'page_header'       => 'Apply List of '.$data->title,
            'link'              => $this->link,
        );
        return view('admin.product.productEnroll', $result);
    }
}