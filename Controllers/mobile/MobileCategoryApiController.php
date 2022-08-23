<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Model\Workstation\Author;
use App\Model\Workstation\Category;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductImage;
use App\Model\Workstation\Publisher;
use App\Model\Workstation\RelationCampaignProduct;
use App\Model\Workstation\RelationProductCategory;
use Illuminate\Http\Request;

class MobileCategoryApiController extends Controller
{
    public function categorylist(Request $request)
    {
        $array=array();
        $list=Category::where('status',1)->where('parent_id',0)->get();
        foreach ($list as $key => $value) {
            $array[]= array(
              'id' =>$value->id ,
              'title' =>$value->title ,
              'category_code' =>$value->category_code ,
               );
        }
        $array = array(
                'category_list' => $array,
                );
        $result = array(
                            'status'        => true,
                            'message'       => 'Category Data Fetched',
                            'data'          => $array,
                        );

        return response()->json($result,200);

    }

    public function subCategorylist(Request $request)
    {
        $array=array();
        $list=Category::where('status',1)->where('parent_id','!=',0)->get();
        foreach ($list as $key => $value) {
            $array[]= array(
              'id'              => $value->id ,
              'title'           => $value->title ,
              'category_id'     => $value->parent_id ,
              'category_code'   => $value->category_code ,
               );
        }
        $array = array(
                'subCategorylist' => $array,
                );
        $result = array(
                            'status'        => true,
                            'message'       => 'Category Data Fetched',
                            'data'          => $array,
                        );

        return response()->json($result,200);
            
    }

    

  public function categoryProduct($id)
   {
      $category = Category::where('id',$id)->first();
      if ($category) {
        $array=array();
      $relcategory = RelationProductCategory::where('category_id',$category->id)->get();
      foreach ($relcategory as $key => $cat) {
        $product = Product::where('id',$cat->product_id)->first();
        
        
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
        
        $stock=$product->opening_stock;
            if($stock==0)
            {
              $iss = 0;
            }else{
              $iss = 1;
            }
            
        if ($author) {
          $author_name = $author->name;
          $author_slug = $author->slug;
        }else{
          $author_name = '';
          $author_slug = '';
        }
        $publisher = Publisher::where('id',$product->publisher_id)->first();
        $productImage = ProductImage::where('product_id',$product->id)->where('is_desktop_thumbnail',1)->first();
        if ($productImage) {
             $productImage = $productImage->image;
            }else{
              $productImage = DEFAULT_IMG;
            }

         $array[]= array(
              'id' =>$product->id ,
              'category_id' =>$category->id ,
              'product_name' =>$wordlimit ,
              'slug' =>$product->slug ,
              'sku' => $product->sku ,
              'mrp_paper_book' => $product->mrp_paper_book ,
              'listing_price' => $listing_price ,
              'meta_title' => $product->meta_title ,
              'meta_keywords' => $product->meta_keywords ,
              'meta_description' => $product->meta_description ,
              'author_name' => $author_name ,
              'author_slug' => $author_slug ,
              'productImage' => $productImage,
              'stock'         => $iss,
               );
      }
      $array = array(
                'category_product' => $array,
                );

        $result = array(
                            'status'        => true,
                            'message'       => 'Product Data Fetched',
                            'data'          => $array,
                        );

        return response()->json($result,200);

      }
        # code...
      else{
        return response()->json([
            'status'     => false,
            'message'    => 'Category Not Found',
            'data'       =>null,
        ], 401);
    }
  }
}
