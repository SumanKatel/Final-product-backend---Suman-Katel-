<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Model\admin\AdminContact;
use App\Model\admin\Customer;
use App\Model\admin\Booked;
use App\Model\admin\BannerDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminContactController extends Controller {

    private $title = 'Contact Us';
    private $sort_by = 'inserted_date';
    private $sort_order = 'desc';
    private $index_link = 'contact.index';
    private $list_page = 'admin.contact.list';
    private $create_form = 'admin.contact.add';
    private $update_form = 'admin.contact.edit';
    private $link = 'contact';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = AdminContact::orderBy($this->sort_by,$this->sort_order)->paginate(PAGES);
        $result = array(
            'list'              => $list,
            'page_header'       => 'List of '.$this->title,
        );
        return view($this->list_page, $result);
    }
    
     public function adminCustomerList()
    {
        $list = Customer::paginate(100);
        $result = array(
            'list'              => $list,
            'page_header'       => 'Customer List',
        );
        return view('admin.contact.customer', $result);
    }
    
    public function adminCustomerBookList()
    {
        $list = Booked::paginate(100);
        $result = array(
            'list'              => $list,
            'page_header'       => 'Car Booked List',
        );
        return view('admin.contact.book', $result);
    }
    
    public function adminCustomerBrochureList()
    {
        $list = BannerDownload::paginate(100);
        $result = array(
            'list'              => $list,
            'page_header'       => 'Brochure Download List',
        );
        return view('admin.contact.brochure', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect(route($this->index_link));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect(route($this->index_link));
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
        $record = AdminContact::findOrFail($id);
        $record->viewed = '1';
        $record->save();
        $result = array(
            'page_header'       => 'View '.$this->title.' Detail',
            'record'            => $record,
        );
        return back();
        
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
        return redirect(route($this->index_link));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $crud = AdminContact::findOrFail($id);
        $crud->delete();
        Session::flash('success_message', DELETED);
        return redirect(route($this->index_link));
    }
}