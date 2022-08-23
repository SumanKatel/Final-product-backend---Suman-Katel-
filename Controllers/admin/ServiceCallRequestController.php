<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Model\admin\RequestCallService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PrintHelper;

class ServiceCallRequestController extends AdminController {

    private $title = 'Service Call Request';
    private $sort_by = 'created_at';
    private $sort_order = 'asc';
    private $index_link = 'service-request.index';
    private $list_page = 'admin.service-request.list';
    private $create_form = 'admin.service-request.add';
    private $update_form = 'admin.service-request.edit';
    private $link = 'service-request';
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $list = RequestCallService::orderBy($this->sort_by, $this->sort_order)->get();
        $result = array(
            'list'              => $list,
            'page_header'       => 'List of '.$this->title,
            'link'              => $this->link,
        );
        return view($this->list_page, $result);
    }
}