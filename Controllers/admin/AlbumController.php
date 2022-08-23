<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Model\admin\Album;
use App\Model\admin\Gallery;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AlbumController extends AdminController {

    private $title = 'Album';
    private $sort_order = 'asc';
    private $index_link = 'album.index';
    private $list_page = 'admin.album.list';
    private $create_form = 'admin.album.add';
    private $update_form = 'admin.album.edit';
    private $link = 'album';
    private $user_id;

   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $list = Album::all();
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
    public function store(Request $request){
        $this->validate($request, [
            'title'              => 'required',
            'image'        => 'required',
        ]);
        $request->image = ($request->image != '')?chunkfullurl($request->image):null;
        $request=Album::create($request->all());
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
        $pages = Album::findOrFail($id);
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
            'image'        => 'required',
        ]);

        $crud = Album::findOrFail($id);
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
        $crud = Album::findOrFail($id);
        $crud->delete();
        Session::flash('success_message', DELETED);
        return redirect(route($this->index_link));
    }
     public function albumGallery($id){
        $list = Album::where('id',$id)->first();
        $images=Gallery::where('album_id',$list->id)->get();
        $result = array(
            'list'              => $list,
            'images'              => $images,
            'page_header'       => 'List of Gallery',
            'link'              => 'gallery',
        );
        return view('admin.album.gallery',$result);
    }

    public function galleryStore(Request $request,$id)
    {
        $album=Album::where('id',$id)->first();
        $request->image = $request->image;
        $request['album_id']=$album->id;
        Gallery::create($request->all());
        return redirect(route('albumGallery',$album->id));
        Session::flash('success_message', CREATED);
    }

    public function albumGalleryStatus($id){
        $data=Gallery::find($id);
        if($data->status=='1'){
            $data->status='0';
            $data->save();
        }
        else{
            $data->status='1';
            $data->save();
        }
        return back();
        Session::flash('success_message', STATUS);

    }

    public function albumGalleryDelete($id){
        $data=Gallery::find($id);
        $data->delete();
        return back();
        Session::flash('success_message', STATUS);
    }

    public function albumGalleryEdit($id)
    {
        $record = Gallery::findOrFail($id);
        $images = Gallery::get();
        $result = array(
            'page_header'       => 'List of Gallery',
            'record'            => $record,
            'images'              => $images,
            'link'              => $this->link,
        );
        return view('admin.album.galleryEdit', $result);
    }

    public function albumGalleryUpdate(Request $request,$id){
        $data=Gallery::find($id);
        $album=Album::where('id',$data->album_id)->first();
        $data->image = ($request->image != '')?chunkfullurl($request->image):null;
        $data->update($request->all());
        // return back();
        // Session::flash('success_message', STATUS);
        return redirect(route('albumGallery',$album->id));
        

    }


    // public function store(Request $request)
    // {
    //     foreach (Input::file('image') as $file) {
    //         $rules = array(
    //             'image' => 'required|mimes:jpeg,gif,png'
    //         );
    //         $validator = validator(array('image' => $file), $rules);
    //         if ($validator->passes()) {
    //             AdminGallery::createModel($file);
    //         } else {
    //             return redirect()->back()->withInput()->withErrors($validator);
    //         }
    //     }
    //     Session::flash('success_message', CREATED);
    //     return redirect(route('admin.gallery.album',Cache::get($this->remember_page)));
    // }
}