<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Model\admin\AdminGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;

class AdminGalleryController extends AdminController {

    private $title = 'Gallery';
    private $sort_by = 'sort_order';
    private $sort_order = 'asc';
    private $index_link = 'gallery.index';
    private $list_page = 'admin.gallery.list';
    private $create_form = 'admin.gallery.add';
    private $update_form = 'admin.gallery.edit';
    private $remember_page = 'gallery_parent_id';

    public function album($id) {
        Cache::forever($this->remember_page, $id);
        $list = AdminGallery::where('parent_id', $id)->orderBy($this->sort_by, $this->sort_order)->paginate(PAGES);
        $result = array(
            'list'              => $list,
            'page_header'       => 'List of '.$this->title,
        );
        return view($this->list_page, $result);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = AdminGallery::orderBy($this->sort_by, $this->sort_order)->paginate(PAGES);
        $result = array(
            'list'              => $list,
            'page_header'       => 'List of '.$this->title,
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
        $result = array(
            'remember_page'     => $this->remember_page,
            'page_header'       => 'Add '.$this->title.' Detail',
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
        foreach (Input::file('image') as $file) {
            $rules = array(
                'image' => 'required|mimes:jpeg,gif,png'
            );
            $validator = validator(array('image' => $file), $rules);
            if ($validator->passes()) {
                AdminGallery::createModel($file);
            } else {
                return redirect()->back()->withInput()->withErrors($validator);
            }
        }
        Session::flash('success_message', CREATED);
        return redirect(route('admin.gallery.album',Cache::get($this->remember_page)));
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
        $record = AdminGallery::findOrFail($id);
        $result = array(
            'page_header'       => 'Edit '.$this->title.' Detail',
            'record'            => $record,
            'remember_page'     => $this->remember_page,
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
        $rules = array(
            'title' => 'required',
            'image' => 'mimes:jpeg,gif,png',
        );

        $validator = validator(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        AdminGallery::updateModel();
        Session::flash('success_message', UPDATED);
        return redirect(route('admin.gallery.album',Cache::get($this->remember_page)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        AdminGallery::deleteModel($id);
        Session::flash('success_message', DELETED);
        return redirect(route('admin.gallery.album',Cache::get($this->remember_page)));
    }
}