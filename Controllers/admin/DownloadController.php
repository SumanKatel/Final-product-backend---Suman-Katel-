<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Model\admin\Download;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DownloadController extends AdminController {

    private $title = 'Download';
    private $sort_order = 'asc';
    private $index_link = 'download.index';
    private $list_page = 'admin.download.list';
    private $create_form = 'admin.download.add';
    private $update_form = 'admin.download.edit';
    private $link = 'download';
    private $user_id;

   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $list = Download::all();
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
            'page_header'       => 'Create '.$this->title.' Detail',
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
            'files'        => 'required',
        ]);
        if(!empty($request->file('files')))
        {
            $image = $request->file('files');
            $extension = $image->getClientOriginalExtension();
            $imageName = 'file'. time(). '.' . $extension;
            $image->move('public/images/download/file/', $imageName);             
            $request['file'] = $imageName;
        }
            $result=Download::create($request->all());


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
        $pages = Download::findOrFail($id);
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
            'title'              => 'required',
        ]);

        $crud = Download::findOrFail($id);
        if(!empty($request->file('files')))
        {
            $image = $request->file('files');
            $extension = $image->getClientOriginalExtension();
            $imageName = 'file'. time(). '.' . $extension;
            $image->move('public/images/download/file/', $imageName);             
            $request['file'] = $imageName;
        }

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
        $crud = Download::findOrFail($id);
        $crud->delete();
        Session::flash('success_message', DELETED);
        return redirect(route($this->index_link));
    }

}