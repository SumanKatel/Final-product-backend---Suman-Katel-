<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Model\Workstation\District;
use App\Model\Workstation\Municipality;
use App\Model\Workstation\State;
use App\Model\admin\AdminCountry;
use Illuminate\Http\Request;

class MobileApiAddressController extends Controller
{
  public function country()
    {
        $array=array();
        $state=AdminCountry::get();
        foreach ($state as $key => $value) {
            $array[]= array(
              'id' =>$value->id ,
              'title' =>$value->title ,
               );
        }

        $array = array(
                  'country' => $array,
                  );

        $result = array(
                            'status'        => true,
                            'message'       => 'Country Data Fetched',
                            'data'          => $array,
                        );

        return response()->json($result,200);
    }
    public function state()
    {
        $array=array();
        $state=State::get();
        foreach ($state as $key => $value) {
            $array[]= array(
              'id' =>$value->id ,
              'province_name' =>$value->state_name_np ,
               );
        }

        $array = array(
                  'state' => $array,
                  );

        $result = array(
                            'status'        => true,
                            'message'       => 'Province Data Fetched',
                            'data'          => $array,
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
         $array = array(
                  'district' => $array,
                  );
        $result = array(
                            'status'        =>true,
                            'message'       => 'District Data Fetched',
                            'data' =>$array,
                        );

        return response()->json($result,200);
    }

    public function get_district($state_id)
    {
        $array=array();
        $district=District::where('state_id', $state_id)->get();
        foreach ($district as $key => $value) {
            $array[]= array(
              'id' =>$value->id ,
              'state_id' =>$value->state_id ,
              'district_name' =>$value->district_name_en ,
               );
        }
         $array = array(
                  'district' => $array,
                  );
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
         $array = array(
                  'municipality' => $array,
                  );
        $result = array(
                            'status'        =>true,
                            'message'       => 'Municipality Data Fetched',
                            'data' =>$array,
                        );

        return response()->json($result,200);
    }

    public function get_municipality($district_id)
    {
        $array=array();
        $list=Municipality::where('district_id', $district_id)->get();
        foreach ($list as $key => $value) {
            $array[]= array(
              'id' =>$value->id ,
              'district_id' =>$value->district_id ,
              'municipality_name' =>$value->location_name_en ,
               );
        }
         $array = array(
                  'municipality' => $array,
                  );
        $result = array(
                            'status'        =>true,
                            'message'       => 'Municipality Data Fetched',
                            'data' =>$array,
                        );

        return response()->json($result,200);
    }

}
