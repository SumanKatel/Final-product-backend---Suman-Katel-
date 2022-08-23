<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Model\admin\RequestCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PrintHelper;

class CourseCallRequestController extends AdminController {

    private $title = 'Course Call Request';
    private $sort_by = 'created_at';
    private $sort_order = 'asc';
    private $index_link = 'course-request.index';
    private $list_page = 'admin.course-request.list';
    private $create_form = 'admin.course-request.add';
    private $update_form = 'admin.course-request.edit';
    private $link = 'course-request';
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $list = RequestCall::orderBy($this->sort_by, $this->sort_order)->get();
        $result = array(
            'list'              => $list,
            'page_header'       => 'List of '.$this->title,
            'link'              => $this->link,
        );
        return view($this->list_page, $result);
    }
}