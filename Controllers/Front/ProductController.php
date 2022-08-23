<?php

namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use App\Model\Front\HomepageLayout;
use App\Model\admin\Customer;
use App\Model\admin\Product;
use App\Model\admin\AdminFaq;
use App\Model\admin\ProductQuestion;
use App\Model\admin\ProductQuestionAnswer;
use App\Model\admin\AdminContact;
use App\Model\admin\Review;
use App\Model\admin\Valuation;
use App\Model\admin\BannerDownload;
use App\Model\admin\Vendor;
use App\Model\admin\Rating;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;
use Mail;


class ProductController extends Controller
{
    
    
     public function faq()
  {
    $array = array();
    $products = AdminFaq::orderBy('created_at','desc')->get();

    foreach ($products as $key => $product) {
            $array[]= array(
              'title'      => $product->title ,
              'description'      => $product->description ,
              
               );
          }
      $result = array(
                        'status'        =>true,
                        'message'       => 'FAQ Data Fetched',
                        'data'          => $array,
                    );
        return response()->json($result,200);
  }

  public function allProduct()
  {
    $array = array();
    $products = Product::orderBy('created_at','desc')->get();

    foreach ($products as $key => $product) {
        
      $reviewArray = array();
      $reviews = Rating::where('product_id',$product->id)->orderBy('created_at','DESC')->get();
      foreach ($reviews as $key => $review) {
              $customer = Customer::where('id',$review->customer_id)->first();
              $reviewArray[] = array(
                'customer_name' => $customer->name ,
                'rating_value'   => $review->rating_value ,
                'review'       => $review->review ,
                'review_date'   => Carbon::parse($review->created_at)->toFormattedDateString(),
                 );
      }
      
      
            $array[]= array(
              'product_id'      => $product->id ,
              'product_name'      => $product->title ,
              'product_thumbnail' => asset($product->image) ,
              'slug'              => $product->slug ,
              'description'       => $product->description ,
              'price'             => $product->price ,
              'ratings'      => $reviewArray,
               );
          }
      $result = array(
                        'status'        =>true,
                        'message'       => 'Product Data Fetched',
                        'data'          => $array,
                    );
        return response()->json($result,200);
  }


  public function compare(Request $request)
  {
    $array = array();
    foreach ($request->product_ids as $key => $product) {
            $product = Product::find($product);
            
            $content = str_replace("&nbsp;", "", $product->description);
            $info=strip_tags(htmlspecialchars_decode($content));
            
            $array[]= array(
              'product_id'        => $product->id ,
              'product_name'      => $product->title ,
              'product_thumbnail' => asset($product->image) ,
              'slug'              => $product->slug ,
              'description'       => $product->description,
              'price'             => $product->price ,
               );
          }
      $result = array(
                        'status'        =>true,
                        'message'       => 'Compare Data Fetched',
                        'data'          => $array,
                    );
        return response()->json($result,200);
  }


    public function suggest(Request $request)
  {
      
    $products = Product::where('price', '>=', $request->min_price)->where('price', '<=', $request->max_price)->get();
    $array = array();
    foreach ($products as $key => $product) {
            $array[]= array(
              'product_id'      => $product->id ,
              'product_name'      => $product->title ,
              'product_thumbnail' => asset($product->image) ,
              'slug'              => $product->slug ,
              'description'       => $product->description ,
              'price'             => $product->price ,
               );
          }
      $result = array(
                        'status'        =>true,
                        'message'       => 'Suggest Data Fetched',
                        'data'          => $array,
                    );
        return response()->json($result,200);
  }

      public function valuation(Request $request)
    { 
        $now = date('Y-m-d');
        $y = \Carbon\Carbon::now()->format('Y');
        $differentYear = $y-$request->make_year;
        $product = Product::find($request->model);
        $kmPrice = ($request->kms)*25;
        $yearPrice = $differentYear*100000;
        $reductPrice = $kmPrice+$yearPrice;

        $finalValue = ($product->price) - $reductPrice;

      $crud = new Valuation();
      $crud->name = $request->name;
      $crud->model = $request->model;
      $crud->kms = $request->kms;
      $crud->make_year = $request->make_year;
      $crud->person_name = $request->person_name;
      $crud->person_mobile = $request->person_mobile;
      $crud->save();
      $car_value = $finalValue;
      if ($crud) {
            $result = array(
                    'status'        =>true,
                    'message'       => 'Request sent Successful!!',
                    'car_value'     => $car_value,
                    'model'         => $crud->model,
                    'kms'           => $crud->kms,
                    'make_year'     => $crud->make_year,
                );
            return response()->json($result,200);
      }
    }
    
        public function brochureDownload(Request $request)
    {
        $validator=Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required',
                'phone' => 'required|digits:10',
            ]);
            if ($validator->passes()) {
            $banner['name'] = $request->name;
            $banner['email'] = $request->email;
            $banner['phone'] = $request->phone;
            $banner['address'] = $request->address;
            $result = BannerDownload::create($banner);

            if ($result) {
                    $arrayName = array(
                        'name'              => $result->name,
                        'email'             => $result->email,
                        'subject'           => 'Paramandu Motors e-Brochure',
                        );
                    Mail::send('email.brochure-sent', $arrayName, function ($m) use ($arrayName) {
                    $mail_from = env('MAIL_USERNAME', 'bajracharyarakshak@gmail.com');
                    $m->from($mail_from, 'Paramandu Motors');
                    $m->to($arrayName['email'])
                    ->subject($arrayName['subject']);
                    });           
            }

            if ($result) {
                    $data = array(
                        'status'  =>true,
                        'message' =>'Dear '. $result->name.',<br>We have sent our brochure to your email address. <h3>'.$result->email.'</h3>. <br> Please check your email. <br> If you do not see in your inbox please, check your spam folder <br>Thank You',
                        );
                    return response()->json($data);
                }
                else{
                    return response()->json([
                        'message'   =>'Something went wrong !',
                        'status'    =>false,
                        'data'      =>null,
                    ]);
                }
                    
                }else{

                $data= array(
                    'error' => true,
                    'errors'=>$validator->errors()
                     );
                return response()->json($data,422);
            }
    }
    
       public function feedback(Request $request)
    { 
      $crud = new AdminContact();
      $crud->name = $request->name;
      $crud->address = $request->address;
      $crud->email = $request->email;
      $crud->phoneno = $request->phoneno;
      $crud->message = $request->message;
      $crud->save();
      if ($crud) {
            $result = array(
                    'status'        =>true,
                    'message'       => 'Feedback sent Successful!!',
                );
            return response()->json($result,200);
      }
    }

}