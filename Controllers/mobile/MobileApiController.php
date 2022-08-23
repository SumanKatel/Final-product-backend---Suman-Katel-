<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Model\Front\ProductQuestion;
use App\Model\Front\ProductQuestionReply;
use App\Model\Workstation\Author;
use App\Model\Workstation\BannerSetup;
use App\Model\Workstation\Category;
use App\Model\Workstation\Customer;
use App\Model\Workstation\DeliveryAddressPrice;
use App\Model\Workstation\PaymentMethod;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductImage;
use App\Model\Workstation\RelationCampaignProduct;
use App\Model\admin\AdminFaq;
use App\Model\admin\AdminSetting;
use App\Model\admin\ProductRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MobileApiController extends Controller
{
	 public function paymentMethod()
    {
        $array=array();
        $payment=PaymentMethod::where('status',1)->get();
        foreach ($payment as $key => $value) {
            $array[]= array(
              'id' =>$value->id ,
              'payment_name' =>$value->payment_name ,
               );
        }
         $array = array(
                  'payment_method' => $array,
                  );
        $result = array(
                            'status'        =>true,
                            'message'       => 'Payment Method Fetched Successfully',
                            'data' =>$array,
                        );

        return response()->json($result,200);
    }

    public function getDeliveryCharge(Request $request,$id)
    {
      $deliveryAddressc=DeliveryAddressPrice::where('municipality_id',$id)->first();
      if ($deliveryAddressc) {
          $price = $deliveryAddressc->delivery_price;
          // $result = array(
          //                 'status'        => true,
          //                 'message'       => 'Delivery Price!',
          //                 'price'         => $deliveryAddressc->delivery_price,
          //                 'is_available'  => $deliveryAddressc->is_available,
          //                 'is_available'  => $deliveryAddressc->cod_available,
          //                 'min_day'       => $deliveryAddressc->min_day,
          //                 'max_day'       => $deliveryAddressc->max_day,
          //             );
          //     return response()->json($result,200);
          $result = array(
                                    'status'        =>true,
                                    'price'         =>0,
                                    'free_above_order_value' =>0,
                                    'is_available' =>1,
                                    'min_day'       => 2,
                                    'max_day'       => 3,
                                    'message'       => 'Delivery Price!',
                                );
                        return response()->json($result,200);
      }else{
          // return response()->json([
          //     'status'     => false,
          //     'message'    => 'Sorry the delivery service is not currently available on your area.',
          //     'data'       =>null,
          // ], 401);
        $result = array(
                                    'status'        =>true,
                                    'price'         =>0,
                                    'free_above_order_value' =>0,
                                    'is_available' =>1,
                                    'min_day'       => 2,
                                    'max_day'       => 3,
                                    'message'       => 'Delivery Price!',
                                );
                        return response()->json($result,200);
      }
    }


    public function faq()
    {
        $array=array();
        $list=AdminFaq::where('status',1)->get();
        foreach ($list as $key => $value) {
            $array[]= array(
              'id' =>$value->id ,
              'title' =>$value->title ,
              'description' =>$value->description ,
               );
        }

         $array = array(
                  'faq' => $array,
                  ); 

        $result = array(
                            'status'        =>true,
                            'message'       => 'FAQ Data Fetched',
                            'data' =>$array,
                        );

        return response()->json($result,200);
    }


    public function getAnswer(Request $request,$id)
    {
      $question=ProductQuestion::where('id',$id)->first();
      if ($question) {
        $array = array();
        $answers = ProductQuestionReply::where('question_id',$question->id)->get();
        foreach ($answers as $key => $value) {
          if ($value->customer_id) {
          $customer = Customer::where('id',$value->customer_id)->first();
          $user     = $customer->name; 
          }else{
          $user     = 'Kitab Yatra'; 
          }
            $array[]= array(
              'id'            => $value->id ,
              'question_id'   => $value->question_id ,
              'answer'        => $value->ques_ans ,
              'customer_name' => $user,
              'answer_time'   => $value->created_at->diffForHumans(),
               );
        }
         $array = array(
                  'get_answer' => $array,
                  );

        $result = array(
                            'status'        =>true,
                            'message'       => 'Answer Data Fetched',
                            'data' =>$array,
                        );

        return response()->json($result,200);
      }else{
          return response()->json([
              'status'     => false,
              'message'    => 'Product Not found !!',
              'data'       =>null,
          ], 401);
      }
    }


    public function getQuestion(Request $request,$id)
    {
      $product=Product::where('id',$id)->first();
      if ($product) {
        $array = array();
        $questions = ProductQuestion::where('product_id',$product->id)->get();
        foreach ($questions as $key => $value) {
          $customer = Customer::where('id',$value->customer_id)->first();
            $array[]= array(
              'id'            => $value->id ,
              'question'      => $value->question ,
              'customer_name' => $customer->name,
              'question_time' => $value->created_at->diffForHumans(),
               );
        }

        $array = array(
                  'get_question' => $array,
                  );

        $result = array(
                            'status'        =>true,
                            'message'       => 'Question Data Fetched',
                            'data' =>$array,
                        );

        return response()->json($result,200);
      }else{
          return response()->json([
              'status'     => false,
              'message'    => 'Product Not found !!',
              'data'       =>null,
          ], 401);
      }
    }


    public function getRating(Request $request,$id)
    {
      $product=Product::where('id',$id)->first();
      if ($product) {
        $ratings = ProductRating::where('product_id',$product->id)->get();
        $rating_one = ProductRating::where('product_id',$product->id)->where('rating',1)->get()->count();
        $rating_two = ProductRating::where('product_id',$product->id)->where('rating',2)->get()->count();
        $rating_three = ProductRating::where('product_id',$product->id)->where('rating',3)->get()->count();
        $rating_four = ProductRating::where('product_id',$product->id)->where('rating',4)->get()->count();
        $rating_five = ProductRating::where('product_id',$product->id)->where('rating',5)->get()->count();

        $ratingSum = $ratings->sum('rating'); 
        $array = array();
        foreach ($ratings as $key => $rating) {
            $customer = Customer::where('id',$rating->user_id)->first();
            $array[]= array(
              'id'        => $rating->id ,
              'customer'  => $customer->name ,
              'rating'    => $rating->rating ,
              'reviews'   => $rating->reviews ,
               );
        }

        $array = array(
                  'totalRating'   => $ratingSum,
                  'rating_one'    => $rating_one,
                  'rating_two'    => $rating_two,
                  'rating_three'  => $rating_three,
                  'rating_four'   => $rating_four,
                  'rating_five'   => $rating_five,
                  'get_rating'    => $array,
                  );

        $result = array(
                            'status'        =>  true,
                            
                            'message'       => 'Rating Data Fetched !',
                            'data'          => $array,
                        );
              return response()->json($result,200);
      }else{
          return response()->json([
              'status'     => false,
              'message'    => 'Product Not found !!',
              'data'       =>null,
          ], 401);
      }
    }
    public function setting()
    {
       $value = AdminSetting::findorfail('1');
        $array = array(
            'site_title' =>$value->title,
            'email' =>$value->email,
            'address' =>$value->address,
            'mobile' =>$value->mobile_no,
            'phone' =>$value->phone_no,
            'footer_content' =>$value->footer_content,
            'logo' => $value->logo,
            'meta_description' => $value->meta_descriptions,
            'meta_keywords' => $value->meta_keywords,
            'logo' => $value->logo,
            'meta_title' => $value->meta_title,
            'instagram' => $value->instagram,
            'facebook' => $value->facebook,
            'twitter' => $value->twitter,
          );
        
         $array = array(
                  'setting' => $array,
                 );

        return response()->json($array,200);
    }

    public function search(Request $request)
    {
      if ($request->title != '') {
          $array=array();

          $productTitle = $request->title;
          $productFilter = $request->filter;
          $productList = Product::select('*');
          if($productTitle != null){
            $productList->where('product_name', 'like', '%'.$productTitle.'%');
          }
          if($productFilter != null){
            if($productFilter == 1){ //date
              $productList->orderBy('created_at', 'asc');
            }
            elseif ($productFilter == 2) { //price
              $productList->orderBy('listing_price', 'asc');
            }
          }
          $productList = $productList->get();

          
          foreach ($productList as $key => $product) {
            $product = Product::where('id',$product->id)->first();
            
            $campaign = RelationCampaignProduct::where('product_id',$product->id)->first();
            $campaignProduct = RelationCampaignProduct::where('product_id',$product->id)->first();
            if($campaignProduct){
                if($campaignProduct->discount_type==1){
                    $discountprice = $product->mrp_paper_book-($campaignProduct->discount_amount);
                    $listing_price = $discountprice;
                    
                }else{
                    $per= ($product->mrp_paper_book*$campaignProduct->discount_percentage)/100;
                    $discountprice = $product->mrp_paper_book-$per;
                    $listing_price = $discountprice;
                    
                }
                
            }else{
                $listing_price = $product->listing_price;
            }
            
            $stock=$product->opening_stock;
            
            if($stock==0)
            {
              $iss = 0;
            }else{
              $iss = 1;
            }
            
            $author = Author::where('id',$product->author_id)->first();
            if ($author) {
              $author_name = $author->name;
              $author_slug = $author->slug;
            }else{
              $author_name = '';
              $author_slug = '';
            }
            
            $productImage = ProductImage::where('product_id', $product->id)->where('is_desktop_thumbnail',1)->first();
            if ($productImage) {
             $productImage = $productImage->image;
            }else{
              $productImage = DEFAULT_IMG;
            }
            
            $minStock = minStock();
            $productStock = $minStock>$product->opening_stock;
            if ($productStock==true) {
              $productStock = 'Limited';
            }else{
              $productStock = 'In Stock';
            }
            $array[]= array(
              'id' => $product->id ,
              'slug' => $product->slug ,
              'product_name' => $product->product_name ,
              'sku' => $product->sku ,
              'mrp_paper_book' => $product->mrp_paper_book ,
              'listing_price' => $listing_price ,
              'product_image' => $productImage,
              'productStock'  => $productStock,
              'author_name'  => $author_name,
              'author_slug'  => $author_slug,
              'stock'         => $iss,
               );
            
      }

      $array = array(
                  'search' => $array,
                 );

      $result = array(
                'status'        =>  true,
                'message'       => 'Search Data Fetched',
                'data'          =>  $array,
              );

        return response()->json($result,200);
      }else{
        $nf = array(
                    'status'        =>  false,
                    'message'       => 'Your Search Book not Found',
                    'data'          =>  null,
              );
          return response()->json($nf,401);
      }
    }

    public function frontBanner()
    {
    // dd(DEFAULT_IMG);
      $array=array();
      $productList=BannerSetup::where('banner_id',1)->orderBy('created_at','desc')->get();
      foreach ($productList as $key => $value) {
          if(!$value->product_id){
            $value->product_id =1;
            }
          
        $product = Product::where('id',$value->product_id)->first();
        
        $campaign = RelationCampaignProduct::where('product_id',$product->id)->first();
        $campaignProduct = RelationCampaignProduct::where('product_id',$product->id)->first();
        if($campaignProduct){
            if($campaignProduct->discount_type==1){
                $discountprice = $product->mrp_paper_book-($campaignProduct->discount_amount);
                $listing_price = $discountprice;
                
            }else{
                $per= ($product->mrp_paper_book*$campaignProduct->discount_percentage)/100;
                $discountprice = $product->mrp_paper_book-$per;
                $listing_price = $discountprice;
                
            }
            
        }else{
            $listing_price = $product->listing_price;
        }
        
      
        
        $wordlimit = str_limit(strip_tags($product->product_name),20);
        $author = Author::where('id',$product->author_id)->first();
        if ($author) {
          $author_name = $author->name;
          $author_slug = $author->slug;
        }else{
          $author_name = '';
          $author_slug = '';
        }
        
        $productImage = ProductImage::where('product_id', $product->id)->where('is_desktop_thumbnail',1)->first();
        if ($productImage) {
         $productImage = $productImage->image;
        }else{
          $productImage = DEFAULT_IMG;
        }
        
        $minStock = minStock();
        $productStock = $minStock>$product->opening_stock;
        if ($productStock==true) {
          $productStock = 'Limited';
        }else{
          $productStock = 'In Stock';
        }
        
         $stock=$product->opening_stock;
        if($stock=0)
        {
          $iss = 0;
        }else{
          $iss = 1;
        }
          $array[]= array(
            'id' => $product->id ,
            'banner_type_id' => $value->banner_type_id ,
            'banner_image' => $value->banner_image ,
            'slug' => $product->slug ,
            'product_name' => $wordlimit ,
            'sku' => $product->sku ,
            'mrp_paper_book' => $product->mrp_paper_book ,
            'listing_price' => $listing_price ,
            'product_image' => $productImage,
            'productStock'  => $productStock,
            'author_name'  => $author_name,
            'author_slug'  => $author_slug,
            'stock'         => $iss,
             );
      }

      $array = array(
                  'front_banner' => $array,
                 );

      $result = array(
                          'status'        => true,
                          'message'       => 'Data Fetched',
                          'products'      => $array,
                      );

      return response()->json($result,200);

    }



  public function productFilter(Request $request)
      {
        $validator=Validator::make($request->all(), [
            ]);
            $array=array();
            if ($validator->passes()) {
            $query = Product::where('status',1)->get();
            //for price range
              if($request->min_price && $request->max_price)
              {
                  $query = $query->where('listing_price','>=',$request->min_price);
                  $query = $query->where('listing_price','<=',$request->max_price);
              }
              
              if($request->author_id){
                 foreach($request->author_id as $aid){
                  $query = Product::where('status',1)->where('author_id',$aid); 
                 }
                   
              }
            }else{
              $result = array(
              'status'        => false,
              'message'       => 'Input Field Required',
              'data'        => $validator->errors()
              );
              return response()->json($result,422);
          }
  }
}
