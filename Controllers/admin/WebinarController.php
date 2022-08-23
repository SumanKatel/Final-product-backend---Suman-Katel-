<?php

namespace App\Http\Controllers\admin;

use App\Exports\WebinarAllRequestExport;
use App\Exports\WebinarRequestExport;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Model\admin\CourseCategory;
use App\Model\admin\RequestCallWebinar;
use App\Model\admin\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class WebinarController extends AdminController {

    private $title = 'Webinar';
    private $sort_by = 'created_at';
    private $sort_order = 'asc';
    private $index_link = 'webinar.index';
    private $list_page = 'admin.webinar.list';
    private $create_form = 'admin.webinar.add';
    private $update_form = 'admin.webinar.edit';
    private $show_page = 'admin.webinar.show';
    private $link = 'webinar';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $list = Webinar::orderBy('created_at','desc')->get();
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
     
    public function requestList($slug){
        $webinar = Webinar::where('slug',$slug)->first();
        if($webinar){
        $requests = RequestCallWebinar::where('webinar_id',$webinar->id)->get();
        $result = array(
            'page_header'       => 'Create '.$this->title.' Detail',
            'link'              => $this->link,
            'webinar'           => $webinar,
            'requests'          => $requests,
            'page_header'       => 'Enroll of '.$webinar->title,
        );
        return view($this->show_page, $result);
        }else{
            
        }
        
    }
    public function create(){
        $pc=CourseCategory::where('status','1')->get();
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
            'course_category_id'     => 'required',
            'description'              => 'required',
            'cover_image'=>'required',
            'meta_keywords'=>'required',
            'meta_description'=>'required',
        ],
        ['course_category_id.required' => 'The Course Category field is required']
    );
        $request->image = $request->image;
        $request->cover_image = $request->cover_image;
        $request=Webinar::create($request->all());
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
        $pages = Webinar::findOrFail($id);
        $pc=CourseCategory::where('status','1')->get();
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
            'meta_keywords'=>'required',
            'meta_description'=>'required',
        ]);

        $crud = Webinar::findOrFail($id);
        $crud->image = $request->image;
              $crud->cover_image = $request->cover_image;
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
        $crud = Webinar::findOrFail($id);
        $crud->delete();
        Session::flash('success_message', DELETED);
        return redirect(route($this->index_link));
    }
    
    public function export(Request $request){
        $crud = Webinar::findOrFail($request->id);
        return Excel::download(new WebinarRequestExport($request->id), $crud->title.'.xlsx');
    }

    public function exportAllWebinar(){
        return Excel::download(new WebinarAllRequestExport(), 'webinar-requests.xlsx');
    }
}