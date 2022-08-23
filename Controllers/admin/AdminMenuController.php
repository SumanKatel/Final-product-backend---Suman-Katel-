<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use Illuminate\Http\Request;

class AdminMenuController extends AdminController {
    
    public function index(){
    	return view('admin.menu.index');
    }

}
