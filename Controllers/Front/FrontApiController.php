<?php

namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use App\Model\admin\District;
use App\Model\admin\Municipality;
use App\Model\admin\State;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;


class FrontApiController extends Controller
{

      public function state()
    {
        $array=array();
        $states=State::get();
        foreach ($states as $key => $value) {
            $array[]= array(
              'id' =>$value->id ,
              'state_name' =>$value->state_name_en ,
               );
        }
        $result = array(
                            'status'        =>true,
                            'message'       => 'State Data Fetched',
                            'data' =>$array,
                        );

        return response()->json($result,200);
    }

    public function district()
    {
        $array=array();
        $district=District::get();
        foreach ($district as $key => $value) {
            $array[]= array(
              'id' =>$value->id ,
              'state_id' =>$value->state_id ,
              'district_name' =>$value->district_name_en ,
               );
        }
        $result = array(
                            'status'        =>true,
                            'message'       => 'District Data Fetched',
                            'data' =>$array,
                        );

        return response()->json($result,200);
    }
     public function municipality()
    {
        $array=array();
        $list=Municipality::get();
        foreach ($list as $key => $value) {
            $array[]= array(
              'id' =>$value->id ,
              'district_id' =>$value->district_id ,
              'municipality_name' =>$value->location_name_en ,
               );
        }
        $result = array(
                            'status'        =>true,
                            'message'       => 'Municipality Data Fetched',
                            'data' =>$array,
                        );

        return response()->json($result,200);
    }
}