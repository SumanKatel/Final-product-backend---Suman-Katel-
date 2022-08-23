<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Model\admin\Course;
use App\Model\admin\RequestCallWebinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PrintHelper;

class WebinarCallRequestController extends AdminController {

    private $title = 'Webinar Call Request';
    private $sort_by = 'created_at';
    private $sort_order = 'desc';
    private $index_link = 'webinar-request.index';
    private $list_page = 'admin.webinar-request.list';
    private $create_form = 'admin.webinar-request.add';
    private $update_form = 'admin.webinar-request.edit';
    private $link = 'webinar-request';
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $list = RequestCallWebinar::orderBy($this->sort_by, $this->sort_order)->get();
        $result = array(
            'list'              => $list,
            'page_header'       => 'List of '.$this->title,
            'link'              => $this->link,
        );
        return view($this->list_page, $result);
    }
}