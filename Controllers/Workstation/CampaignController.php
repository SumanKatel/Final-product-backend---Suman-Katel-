<?php

namespace App\Http\Controllers\Workstation;

use App\Http\Controllers\Controller;
use App\Imports\CampaignProductImport;
use App\Imports\PublisherCampaignImport;
use App\Model\Workstation\Author;
use App\Model\Workstation\Campaign;
use App\Model\Workstation\Category;
use App\Model\Workstation\Customer;
use App\Model\Workstation\OfferPlatform;
use App\Model\Workstation\Product;
use App\Model\Workstation\Publisher;
use App\Model\Workstation\RelationCampaignAuthor;
use App\Model\Workstation\RelationCampaignCategory;
use App\Model\Workstation\RelationCampaignIndividual;
use App\Model\Workstation\RelationCampaignOfferPlatform;
use App\Model\Workstation\RelationCampaignProduct;
use App\Model\Workstation\RelationCampaignPublisher;
use App\Model\Workstation\WorkstationUser;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class CampaignController extends Controller
{

  private $wsId;
  private $workstation;

  

    public function campaign_list(Request $request){
    $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
                $array=array();
                $list=Campaign::get();
                // foreach ($list as $key => $value) {
                   
                    // $array[]= array(
                    //   'id' => $value->id ,
                    //   'name' => $value->name ,
                    //   'description' => $value->description ,
                    //   'author_code' => $value->author_code ,
                    //   'book_count' => $finalProductCount ,
                    //   'image' => $value->image,
                    //   'cover_image' => $value->cover_image,
                    //    );
                // }
                $result = array(
                                    'status'        => true,
                                    'message'       => 'Campaign Data Fetched',
                                    'data'      =>  $list,
                                );

                return response()->json($result,200);
            
            }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       => $this->wsId,
                ], 401);
            }

    }


    public function campaignProductList(Request $request,$id){
      $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
                $array=array();
                $campaign=Campaign::find($id);
                $productList = RelationCampaignProduct::where('campaign_id',$campaign->id)->get();
                foreach ($productList as $key => $pro) {
                  if($pro->discount_type==1){
                      $type = 'Discount Amount';
                      $discount_value = $pro->discount_amount;
                  }else{
                      $type = 'Discount Percentage';
                      $discount_value = $pro->discount_percentage;
                  }
                  $product = Product::where('id',$pro->product_id)->first();
                   $array[]= array(
                      'id' =>$pro->id ,
                      'campaign_id' =>$campaign->id ,
                      'product_id' =>$product->id ,
                      'product_sku' =>$product->sku ,
                      'offer_from' =>$pro->offer_from ,
                      'offer_to' =>$pro->offer_to ,
                      'discount_type' =>$type ,
                      'discount_value' =>$discount_value ,
                       );

                  # code...
                }
                $result = array(
                                    'status'        => true,
                                    'message'       => 'Campaign Product Data Fetched',
                                    'data'      =>  $array,
                                );

                return response()->json($result,200);
            
            }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       => $this->wsId,
                ], 401);
            }

    }
    public function campaign_add(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'date_from' => 'required',
                'date_to' => 'required',
            ]);

            if ($validator->passes()) {
            $campaign['title'] = $request->title;
            $campaign['date_from'] = $request->date_from;
            $campaign['date_to'] = $request->date_to;
            $campaign['is_replace'] = $request->is_replace;
            $campaign['image'] = $request->image;
            // $campaign['slug'] = $request->slug;
            $campaign['description'] = $request->description;
            $campaign['short_description'] = $request->short_description;
            // $campaign['offer_platform_id'] = $request->offer_platform_id;
            $campaign['individual_order_limit'] = $request->individual_order_limit;
            $campaign['max_discount_percentage'] = $request->max_discount_percentage;
            $campaign['max_discount_amount'] = $request->max_discount_amount;
            $campaign['max_discount_amount_limit'] = $request->max_discount_amount_limit;
            $campaign['offer_code'] = $request->offer_code;
            $campaign['campaign_total_discount'] = $request->campaign_total_discount;
            $campaign['campaign_total_item'] = $request->campaign_total_item;
            $campaign['campaign_total_sales_value'] = $request->campaign_total_sales_value;
            $campaign['campaign_type_status'] = $request->campaign_type_status;
            $campaign['status'] = $request->status;

            if(!empty($request->file('image'))){
              $imagefile = uploadImageGcloud($request->file('image'),'campaign');
              $campaign['image'] = $imagefile;
          }
      $result = Campaign::create($campaign);
       if ($campaign) {

                //for relation product category
        if($request->campaign_type_status == 0){
          if (!empty($request->category_id)) {
                      foreach ($request->category_id as $key => $cat_id) {
                          $parentCategory = Category::where('id',$cat_id)->first();
                            $crud = new RelationCampaignCategory;
                            $crud->campaign_id = $result->id;
                            $crud->author_id = $parentCategory->parent_id;
                            $crud->category_id = $parentCategory->id;
                            $crud->save();
                      }
          }
                }
                // campaign product
                if($request->campaign_type_status == 1){
           $path = $request->product_file;
              if($path != null)
              {

                  $data =Excel::import(new CampaignProductImport($result->id), $path);
            
                }
              }

                // offer_plaform
                if(!empty($request->offer_platform_id)){
                  foreach ($request->offer_platform_id as $key => $offer_platform_id) {
                        $crud = new RelationCampaignOfferPlatform;
                        $crud->campaign_id = $result->id;
                        $crud->offer_platform_id = $offer_platform_id;
                        $crud->save();
                  }
                }

                // marketplace
                // if(!empty($request->marketplace_id)){
                //  foreach ($request->offer_platform_id as $key => $offer_platform_id) {
                //         $crud = new RelationCampaignOfferPlatform;
                //         $crud->campaign_id = $result->id;
                //         $crud->offer_platform_id = $offer_platform_id;
                //         $crud->save();
                 //  }
                // }

            }
            if ($result) {
                $campaign= array(
                                'id'      => $result->id ,
                                'title'     => $result->title ,
                                'description'   => $result->description ,
                             );

                    $data = array(
                        'status'      => true,
                        'message'     => 'Campaign added Successfully!' ,
                        'data'      => $campaign,
                        );
                    return response()->json($data,200);
                }
            else{
                    return response()->json([
                        'message'   =>'Something went wrong !',
                        'status'    =>false,
                        'data'      =>null,
                    ]);
              }
          }else{

                $result = array(
                'status'        => false,
                'message'       => 'Input Field Required',
                'data'        => $validator->errors()
                );
                return response()->json($result,422);
            }
        }else
        {
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 404);
        }
    }

    public function campaign_edit(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
          $campaign_detail = Campaign::where('id', $id)->first();
          $offer_plaform_detail = OfferPlatform::all();
          $campaign_offer_platform = RelationCampaignOfferPlatform::where('campaign_id', $id)->get();
          $campaign_type_status = $campaign_detail->campaign_type_status;
          $campaign_type_detail = '';
          $campaign_category = '';
          $campaign_product_list = '';
          if($campaign_type_status == 0){
            $campaign_type_detail = RelationCampaignCategory::where('campaign_id', $id)->get();
            $campaign_category = Category::all();
          }
          if($campaign_type_status == 1){
            $campaign_type_detail = RelationCampaignProduct::where('campaign_id', $id)->get();
            $campaign_product_list = DB::table('rel_campaign_product as rcp')
                        ->join('tbl_product as p', 'p.id', 'rcp.product_id')
                        ->select('p.id as product_id', 'p.product_name', 'p.sku')
                        ->where('rcp.campaign_id', $id)
                        ->get();
          }
            if($campaign_detail){
                $result = array(
                            'status'                  => true,
                            'message'               => 'Campaign Data Fetched',
                            'campaign_detail'       => $campaign_detail,
                            'offer_plaform_detail'      => $offer_plaform_detail,
                            'campaign_offer_platform'   => $campaign_offer_platform,
                            'campaign_type_detail'      => $campaign_type_detail,
                            'campaign_category'       => $campaign_category,
                            'campaign_product_list'     => $campaign_product_list,
                        );
                return response()->json($result,200);
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Author Not Found',
                    'data'       =>null,
                ], 401);
            }
        }else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 401);
        }
    }

    public function campaign_update (Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
            $validator=Validator::make($request->all(), [
              'title' => 'required|max:255',
              'date_from' => 'required',
              'date_to' => 'required',
          ]);
            if ($validator->passes()) {
              $campaign = Campaign::where('id', $id)->first();
              if($campaign){
                $campaign->title = $request->title;
                $campaign->date_from = $request->date_from;
                $campaign->date_to = $request->date_to;
                $campaign->is_replace = $request->is_replace;
                $campaign->image = $request->image;
                // $campaign->slug = $request->slug;
                $campaign->description = $request->description;
                $campaign->short_description = $request->short_description;
                // $campaign->offer_platform_id = $request->offer_platform_id;
                $campaign->individual_order_limit = $request->individual_order_limit;
                $campaign->max_discount_percentage = $request->max_discount_percentage;
                $campaign->max_discount_amount = $request->max_discount_amount;
                $campaign->max_discount_amount_limit = $request->max_discount_amount_limit;
                $campaign->offer_code = $request->offer_code;
                $campaign->campaign_total_discount = $request->campaign_total_discount;
                $campaign->campaign_total_item = $request->campaign_total_item;
                $campaign->campaign_total_sales_value = $request->campaign_total_sales_value;
                $campaign->campaign_type_status = $request->campaign_type_status;
                $campaign->status = $request->status;

                if(!empty($request->file('image'))){
                  $imagefile = uploadImageGcloud($request->file('image'),'campaign');
                  $campaign->image = $imagefile;
              }
              $campaign->save();
          if ($campaign) {
              $campaign_offer =  RelationCampaignOfferPlatform::where('campaign_id', $id)->get();
              $campaign_category = RelationCampaignCategory::where('campaign_id', $id)->get();
              $campaign_product = RelationCampaignProduct::where('campaign_id', $id)->get();
              if($campaign_category){
                RelationCampaignCategory::where('campaign_id', $id)->delete();
              }
              if($campaign_product){
                RelationCampaignProduct::where('campaign_id', $id)->delete();
              }
              if($campaign_offer){
                RelationCampaignOfferPlatform::where('campaign_id', $id)->delete();
              }

                    //for relation product category
            if($request->campaign_type_status == 0){

              if (!empty($request->category_id)) {
                          foreach ($request->category_id as $key => $cat_id) {
                              $parentCategory = Category::where('id',$cat_id)->first();
                                $crud = new RelationCampaignCategory;
                                $crud->campaign = $result->id;
                                $crud->parent_category_id = $parentCategory->parent_id;
                                $crud->category_id = $parentCategory->id;
                                $crud->save();
                          }
              }
                    }
                    // campaign product
                    if($request->campaign_type_status == 1){
               $path = $request->product_file;
                  if($path != null)
                  {
                      $this->validate($request, [
                          'product_file'        => 'mimes:xlsx,csv'
                      ]);

                      $data =Excel::import(new CampaignProductImport($result->id), $path);
                
                    }
                  }

                    // offer_plaform
                    if(!empty($request->offer_platform_id)){
                      foreach ($request->offer_platform_id as $key => $offer_platform_id) {
                            $crud = new RelationCampaignOfferPlatform;
                            $crud->campaign = $result->id;
                            $crud->offer_platform_id = $offer_platform_id;
                            $crud->save();
                      }
                    }

                    // marketplace
                    // if(!empty($request->marketplace_id)){
                    //  foreach ($request->offer_platform_id as $key => $offer_platform_id) {
                    //         $crud = new RelationCampaignOfferPlatform;
                    //         $crud->campaign = $result->id;
                    //         $crud->offer_platform_id = $offer_platform_id;
                    //         $crud->save();
                     //  }
                    // }

                }

                if ($campaign) {
                    $campaign= array(
                                    'id'      => $campaign->id ,
                                    'title'     => $campaign->title ,
                                    'description'   => $campaign->description ,
                                 );

                        $data = array(
                            'status'      => true,
                            'message'     => 'Campaign updated Successfully!' ,
                            'data'      => $campaign,
                            );
                        return response()->json($data,200);
                    }
                else{
                        return response()->json([
                            'message'   =>'Something went wrong !',
                            'status'    =>false,
                            'data'      =>null,
                        ]);
                  }
          
              }else{

                  return response()->json([
                          'message'   =>'Campaign Not found !',
                          'status'    =>false,
                          'data'      =>null,
                      ]);
              }
          }else{

                  $result = array(
                  'status'        => false,
                  'message'       => 'Input Field Required',
                  'data'    => $validator->errors()
                  );
                  return response()->json($result,422);
              }
          }
        else{
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 404);
        }
    }

       public function campaign_delete(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $campaign_detail=Campaign::where('id',$id)->first();
                if($campaign_detail){
              $campaign_offer =  RelationCampaignOfferPlatform::where('campaign_id', $id)->get();
          $campaign_category = RelationCampaignCategory::where('campaign_id', $id)->get();
          $campaign_product = RelationCampaignProduct::where('campaign_id', $id)->get();
          if($campaign_category){
            RelationCampaignCategory::where('campaign_id', $id)->delete();
          }
          if($campaign_product){
            RelationCampaignProduct::where('campaign_id', $id)->delete();
          }
          if($campaign_offer){
            RelationCampaignOfferPlatform::where('campaign_id', $id)->delete();
          }

                    $campaign_detail->delete();
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Campaign Deleted Successfully!',
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'Campaign Not Found',
                        'data'       =>null,
                    ], 401);
                }
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    }



    public function authorCampaignAdd(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'date_from' => 'required',
                'date_to' => 'required',
                'author_id' => 'required',
            ]);

            if ($validator->passes()) {
            $campaign['title'] = $request->title;
            $campaign['date_from'] = $request->date_from;
            $campaign['date_to'] = $request->date_to;
            $campaign['is_replace'] = $request->is_replace;
            $campaign['description'] = $request->description;
            $campaign['short_description'] = $request->short_description;
            $campaign['individual_order_limit'] = $request->individual_order_limit;
            $campaign['max_discount_percentage'] = $request->max_discount_percentage;
            $campaign['max_discount_amount'] = $request->max_discount_amount;
            $campaign['max_discount_amount_limit'] = $request->max_discount_amount_limit;
            $campaign['offer_code'] = $request->offer_code;
            $campaign['campaign_total_discount'] = $request->campaign_total_discount;
            $campaign['campaign_total_item'] = $request->campaign_total_item;
            $campaign['campaign_total_sales_value'] = $request->campaign_total_sales_value;
            $campaign['campaign_type_status'] = $request->campaign_type_status;
            $campaign['discount_type'] = $request->discount_type;
            $campaign['discount_amount'] = $request->discount_amount;
            $campaign['discount_percentage'] = $request->discount_percentage;
            $campaign['status'] = $request->status;

            if(!empty($request->file('image'))){
              $imagefile = uploadImageGcloud($request->file('image'),'campaign');
              $campaign['image'] = $imagefile;
          }
      $result = Campaign::create($campaign);
       if ($campaign) {
                //for relation campaign and author
        // if($request->campaign_type_status == 0){
          if (!empty($request->author_id)) {
                      $author = Author::where('id',$request->author_id)->first();
                      if($author)
                      {
                      $crud = new RelationCampaignAuthor;
                      $crud->campaign_id = $result->id;
                      $crud->author_id = $author->id;
                      $crud->save();
                      }
                    }
                // }
            }
            if ($result) {
                $campaign= array(
                                'id'      => $result->id ,
                                'title'     => $result->title ,
                                'description'   => $result->description ,
                             );

                    $data = array(
                        'status'      => true,
                        'message'     => 'Campaign added Successfully!' ,
                        'data'      => $campaign,
                        );
                    return response()->json($data,200);
                }
            else{
                    return response()->json([
                        'message'   =>'Something went wrong !',
                        'status'    =>false,
                        'data'      =>null,
                    ]);
              }
          }else{

                $result = array(
                'status'        => false,
                'message'       => 'Input Field Required',
                'data'        => $validator->errors()
                );
                return response()->json($result,422);
            }
        }else
        {
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 404);
        }
    }




    public function publisherCampaignAdd(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'date_from' => 'required',
                'date_to' => 'required',
                'publisher_id' => 'required',
            ]);

            if ($validator->passes()) {
            $campaign['title'] = $request->title;
            $campaign['date_from'] = $request->date_from;
            $campaign['date_to'] = $request->date_to;
            $campaign['is_replace'] = $request->is_replace;
            $campaign['description'] = $request->description;
            $campaign['short_description'] = $request->short_description;
            $campaign['individual_order_limit'] = $request->individual_order_limit;
            $campaign['max_discount_percentage'] = $request->max_discount_percentage;
            $campaign['max_discount_amount'] = $request->max_discount_amount;
            $campaign['max_discount_amount_limit'] = $request->max_discount_amount_limit;
            $campaign['offer_code'] = $request->offer_code;
            $campaign['campaign_total_discount'] = $request->campaign_total_discount;
            $campaign['campaign_total_item'] = $request->campaign_total_item;
            $campaign['campaign_total_sales_value'] = $request->campaign_total_sales_value;
            $campaign['campaign_type_status'] = $request->campaign_type_status;
            $campaign['status'] = $request->status;

            if(!empty($request->file('image'))){
              $imagefile = uploadImageGcloud($request->file('image'),'campaign');
              $campaign['image'] = $imagefile;
          }
      $result = Campaign::create($campaign);
       if ($campaign) {
                //for relation campaign and author
        // if($request->campaign_type_status == 0){
          if (!empty($request->publisher_id)) {
                      $publisher = Publisher::where('id',$request->publisher_id)->first();
                      if($publisher)
                      {
                      $crud = new RelationCampaignPublisher;
                      $crud->campaign_id = $result->id;
                      $crud->publisher_id = $publisher->id;
                      $crud->save();
                      }
                    }
                // }
            }
            if ($result) {
                $campaign= array(
                                'id'      => $result->id ,
                                'title'     => $result->title ,
                                'description'   => $result->description ,
                             );

                    $data = array(
                        'status'      => true,
                        'message'     => 'Campaign added Successfully!' ,
                        'data'      => $campaign,
                        );
                    return response()->json($data,200);
                }
            else{
                    return response()->json([
                        'message'   =>'Something went wrong !',
                        'status'    =>false,
                        'data'      =>null,
                    ]);
              }
          }else{

                $result = array(
                'status'        => false,
                'message'       => 'Input Field Required',
                'data'        => $validator->errors()
                );
                return response()->json($result,422);
            }
        }else
        {
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 404);
        }
    }


     public function individualCampaignAdd(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'title' => 'required|max:255',
                'date_from' => 'required',
                'date_to' => 'required',
                'customer_id' => 'required',
            ]);

            if ($validator->passes()) {
            $campaign['title'] = $request->title;
            $campaign['date_from'] = $request->date_from;
            $campaign['date_to'] = $request->date_to;
            $campaign['is_replace'] = $request->is_replace;
            $campaign['description'] = $request->description;
            $campaign['short_description'] = $request->short_description;
            $campaign['individual_order_limit'] = $request->individual_order_limit;
            $campaign['max_discount_percentage'] = $request->max_discount_percentage;
            $campaign['max_discount_amount'] = $request->max_discount_amount;
            $campaign['max_discount_amount_limit'] = $request->max_discount_amount_limit;
            $campaign['offer_code'] = $request->offer_code;
            $campaign['campaign_total_discount'] = $request->campaign_total_discount;
            $campaign['campaign_total_item'] = $request->campaign_total_item;
            $campaign['campaign_total_sales_value'] = $request->campaign_total_sales_value;
            $campaign['campaign_type_status'] = $request->campaign_type_status;
            $campaign['status'] = $request->status;

            if(!empty($request->file('image'))){
              $imagefile = uploadImageGcloud($request->file('image'),'campaign');
              $campaign['image'] = $imagefile;
          }
      $result = Campaign::create($campaign);
       if ($campaign) {
                //for relation campaign and author
        // if($request->campaign_type_status == 0){
          if (!empty($request->customer_id)) {
                      $author = Customer::where('id',$request->customer_id)->first();
                      if($author)
                      {
                      $crud = new RelationCampaignIndividual;
                      $crud->campaign_id = $result->id;
                      $crud->customer_id = $author->id;
                      $crud->save();
                      }
                    }
                // }
            }
            if ($result) {
                $campaign= array(
                                'id'      => $result->id ,
                                'title'     => $result->title ,
                                'description'   => $result->description ,
                             );

                    $data = array(
                        'status'      => true,
                        'message'     => 'Campaign added Successfully!' ,
                        'data'      => $campaign,
                        );
                    return response()->json($data,200);
                }
            else{
                    return response()->json([
                        'message'   =>'Something went wrong !',
                        'status'    =>false,
                        'data'      =>null,
                    ]);
              }
          }else{

                $result = array(
                'status'        => false,
                'message'       => 'Input Field Required',
                'data'        => $validator->errors()
                );
                return response()->json($result,422);
            }
        }else
        {
            return response()->json([
                'status'     => false,
                'message'    => 'Workstation User Not Found',
                'data'       =>null,
            ], 404);
        }
    }



  public function publisherCampaignCsvUpload(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'publisher_campaign_file' => 'required|max:255',
                
            ]);
            if ($validator->passes()) {
              $path = $request->publisher_campaign_file;
            $result =Excel::import(new PublisherCampaignImport, $path);
            if ($result) {
            $data = array(
                        'status'  =>true,
                        'message' =>'Publisher Campaign Run Successfully!' ,
                        );
                    return response()->json($data,200);
            }
            }else{

                $result = array(
                'status'        => false,
                'message'       => 'Input Field Required',
                'data'    => $validator->errors()
                );
                return response()->json($result,422);
            }
        }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 404);
            }
    }
}
