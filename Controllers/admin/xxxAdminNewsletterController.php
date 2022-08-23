<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\site\MailController;
use App\Model\admin\AdminNewsletter;
use App\Model\admin\NewsLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminNewsletterController extends AdminController {

    private $title = 'Newsletter';
    private $sort_by = 'inserted_date';
    private $sort_order = 'desc';
    private $index_link = 'newsletter.index';
    private $list_page = 'admin.newsletter.list';
    private $create_form = 'admin.newsletter.add';
    private $update_form = 'admin.newsletter.edit';
    private $show_form = 'admin.newsletter.show';
    private $link = 'newsletter';
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
        $list = AdminNewsletter::orderBy($this->sort_by, $this->sort_order)->paginate(PAGES);
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
            'page_header'       => 'Create'.$this->title,
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
            'subject'            => 'required',
            'description'        => 'required',
        ]);
        $crud = new AdminNewsletter;
        $crud->title = $request->title;
        $crud->subject = $request->subject;
        $crud->description = $request->description;
        $crud->slug = uniqid();
        $crud->created_by = $this->user_id;
        $crud->inserted_date = date('Y-m-d H:i:s');
        $crud->status = '1';
        $crud->save();
        // email send to newsletter list
        MailController::sendNewsLetterMail($crud->id);
        Session::flash('success_message', CREATED);
        return redirect(route($this->index_link));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug){
        $record = AdminNewsletter::where('slug',$slug)->first();
        if (!empty($record)) {
            $result = array(
                'page_header'       => 'View '.$this->title.' Detail',
                'record'            => $record,
                'link'              => $this->link,
            );
            return view($this->show_form, $result);
        }else{
            return view('errors.404');            
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        $record = AdminNewsletter::findOrFail($id);
        $result = array(
            'page_header'       => 'Edit '.$this->title.' Detail',
            'record'            => $record,
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
            'subject'            => 'required',
            'description'        => 'required',
        ]);
        $crud = AdminNewsletter::findOrFail($id);
        $crud->title = $request->title;
        $crud->subject = $request->subject;
        $crud->description = $request->description;
        $crud->created_by = $this->user_id;
        $crud->status = '1';
        $crud->save();
        // email send to newsletter list
        MailController::sendNewsLetterMail($id);
        Session::flash('success_message', CREATED);
        return redirect(route($this->index_link));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
    }

     public function resendMail($id){
        $record = AdminNewsletter::findOrFail($id);
        MailController::sendNewsLetterMail($id);
        return redirect(route($this->index_link));
    }

    public function emailList(){
        $list = NewsLetter::orderBy('email','asc')->get();
        $result = array(
            'list'          => $list,
            'page_header'   => 'NewsLetter Subscriber List',
            'link'          => $this->link,
        );
        return view('admin.newsletter.emaillist',$result);
    }
}