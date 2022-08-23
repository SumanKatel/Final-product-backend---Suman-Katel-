<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Model\Front\HomepageLayout;
use App\Model\Workstation\Author;
use App\Model\Workstation\Category;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductImage;
use App\Model\Workstation\Publisher;
use App\Model\Workstation\RelationCampaignProduct;
use App\Model\Workstation\RelationHomepageProduct;
use App\Model\Workstation\RelationProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileProductApiController extends Controller
{
    public function product_detail($slug){
      $product = Product::where('slug',$slug)->first();
      if ($product) {
          
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
            
            
        $productCategories=array();
            $categories = RelationProductCategory::where('product_id',$product->id)->get();
            foreach ($categories as $key => $cat) {
              $category = Category::where('id',$cat->category_id)->first();
              $productCategories[]= array(
              'id' =>$category->id ,
              'category_title' =>$category->title ,
              'slug' =>$category->slug ,
               );
            }

          $productImage = ProductImage::where('product_id',$product->id)->get();
          
          $stock=$product->opening_stock;
            if($stock==0)
            {
              $iss = 0;
            }else{
              $iss = 1;
            }

            $author = Author::where('id',$product->author_id)->first();
            $publisher = Publisher::where('id',$product->publisher_id)->first();
            $language = Publisher::where('id',$product->language_id)->first();
            $product_attribute = DB::table('product_attribute as pa')
                                ->join('tbl_product_attributes as tpa', 'tpa.id', 'pa.product_attribute_id')
                                ->select('tpa.name', 'tpa.description','pa.attribute_value', 'tpa.id')
                                ->where('pa.product_id', $product->id)
                                ->get();
              $minStock = minStock();
              $productStock = $minStock>$product->opening_stock;
              if ($productStock==true) {
                $productStock = 'Limited';
              }else{
                $productStock = 'In Stock';
              }
            $array= array(
              'id' =>$product->id ,
              'product_name' =>$product->product_name ,
              'slug' =>$product->slug ,
              'sku' => $product->sku ,
              'mrp_paper_book' => $product->mrp_paper_book ,
              'product_description' => $product->product_description ,
              'short_description' => $product->short_description ,
              'listing_price' => $listing_price ,
              'productStock'  => $productStock,
              'author_name' => $author->name,
              'author_slug' => $author->slug,
              'publisher_name' => $publisher->name,
              'publisher_slug' => $publisher->slug,
              'language' => (!empty($language) ? $language->title :''),
              'meta_title' => $product->meta_title ,
              'meta_keywords' => $product->meta_keywords ,
              'meta_description' => $product->meta_description ,
              'productCategories'=> $productCategories,
              'productImage' => $productImage,
              'product_attribute' => $product_attribute,
              'stock'         => $iss,
               );

        $array = array(
                'product_detail' => $array,
                );

        $result = array(
                            'status'        =>true,
                            'message'       => 'Product Data Fetched',
                            'data' =>$array,
                        );

        return response()->json($result,200);

      }else{
        return response()->json([
            'status'     => false,
            'message'    => 'Product Not Found',
            'data'       =>null,
        ], 401);
    }
  }

  public function homepageProduct($id)
   {
      $category = HomepageLayout::where('id',$id)->first();
      if ($category) {
        $array=array();
      $relcategory = RelationHomepageProduct::where('homepage_layout_id',$category->id)->get();
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
        $wordlimit = str_limit(strip_tags($product->product_name),20);
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
              'productImage' => $productImage,
              'stock'         => $iss,
              'author_name' => $author_name ,
              'author_slug' => $author_slug ,
               );
      }
      $array = array(
                'homepage_product' => $array,
                );
        

        $result = array(
                            'status'        =>true,
                            'message'       => 'Product Data Fetched',
                            'data' =>$array,
                        );

        return response()->json($result,200);

      }
        # code...
      else{
        return response()->json([
            'status'     => false,
            'message'    => 'Product Not Found',
            'data'       =>null,
        ], 401);
    }
  }
  
}
