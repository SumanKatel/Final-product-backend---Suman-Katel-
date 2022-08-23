<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\AdminController;
use Illuminate\Http\Request;
use App\Model\admin\Product;
use App\Model\admin\Booked;

class AdminDashboardController extends AdminController {

    public function dashboard(){
        $products = Product::select('id', 'title')->where('status', 1)->get();
        $product_chart_data = [];
        foreach($products as $product){
            $booked_product_count = Booked::where('product_id', $product->id)->count();
            $array = array(
                $product->title,
                $booked_product_count
            );
            array_push($product_chart_data, $array);
        }
        $result = array(
            'page_header' => 'Dashboard',
            'product_chart_data' => json_encode($product_chart_data)
        );
        return view('admin.home',$result);
    }

    public function mediaLibrary(){
    	$result = array(
            'page_header' => 'Media Library'
        );
        return view('admin.medialibrary', $result);
    }

}