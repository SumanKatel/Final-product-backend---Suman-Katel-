<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Model\Front\HomepageLayout;
use App\Model\Front\RelationProductHomepage;
use App\Model\Workstation\Author;
use App\Model\Workstation\Campaign;
use App\Model\Workstation\Category;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductImage;
use App\Model\Workstation\RelationCampaignProduct;
use App\Model\Workstation\RelationHomepageCollection;
use Illuminate\Http\Request;

class ProductSectionApiController extends Controller
{
    public function todayOffer()
      {
        // dd(DEFAULT_IMG);
          $todayOffer = HomepageLayout::find(1);
          $array=array();
          $productList=RelationProductHomepage::where('homepage_layout_id',$todayOffer->id)->get();
          foreach ($productList as $key => $value) {
              
            $product = Product::where('id',$value->product_id)->first();
            
            $campaignProduct = RelationCampaignProduct::where('product_id',$product->id)->first();
            

            if($campaignProduct ){
              $offer_platform = Campaign::where('id', $campaignProduct->campaign_id)->whereNot('offer_platfrom_id',1)->get();
                if($campaignProduct->discount_type==1 && $offer_platform){
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
            
            $stock=$product->opening_stock;
            if($stock==0)
            {
              $iss = 0;
            }else{
              $iss = 1;
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
                'list' => $array,
                );
        
          $result = array(
                              'status'        =>true,
                              'message'       => 'Data Fetched',
                              'layoutTitle'   => $todayOffer->title,
                              'layoutId'      => $todayOffer->id,
                              'layoutSlug'    => $todayOffer->slug,
                              'products'      => $array,
                          );

          return response()->json($result,200);

    }
    public function newRelease()
      {
        $newRelease = HomepageLayout::find(2);
          $array=array();
          $productList=RelationProductHomepage::where('homepage_layout_id',$newRelease->id)->get();
          foreach ($productList as $key => $value) {
            $product = Product::where('id',$value->product_id)->first();
            
            
            $campaignProduct = RelationCampaignProduct::where('product_id',$product->id)->first();
            
            if($campaignProduct ){
              $offer_platform = Campaign::where('id', $campaignProduct->campaign_id)->whereNot('offer_platfrom_id',1)->get();
                if($campaignProduct->discount_type==1 && $offer_platform){
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
                'list' => $array,
                );
          $result = array(
                              'status'        =>true,
                              'layoutSlug'    => $newRelease->slug,
                              'layoutId'      => $newRelease->id,
                              'message'       => 'Data Fetched',
                              'layoutTitle'   => $newRelease->title,
                              'products'      =>$array,
                          );

          return response()->json($result,200);

    }

      public function bestSeller()
      {
        $bestSeller = HomepageLayout::find(3);
          $array=array();
          $productList=RelationProductHomepage::where('homepage_layout_id',$bestSeller->id)->get();
          foreach ($productList as $key => $value) {
            $product = Product::where('id',$value->product_id)->first();
            
            
            $campaignProduct = RelationCampaignProduct::where('product_id',$product->id)->first();

            if($campaignProduct){
                $offer_platform = Campaign::where('id', $campaignProduct->campaign_id)->whereNot('offer_platfrom_id',1)->get();
                if($campaignProduct->discount_type==1 && $offer_platform){
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
                'list' => $array,
                );
          $result = array(
                              'status'        =>true,
                              'message'       => 'Data Fetched',
                              'layoutSlug'    => $bestSeller->slug,
                              'layoutTitle'   => $bestSeller->title,
                              'layoutId'      => $bestSeller->id,
                              'products'      =>$array,
                          );

          return response()->json($result,200);

    }

      public function awardWinning()
      {
        $awardWinning = HomepageLayout::find(4);
          $array=array();
          $productList=RelationProductHomepage::where('homepage_layout_id',$awardWinning->id)->get();
          foreach ($productList as $key => $value) {
            $product = Product::where('id',$value->product_id)->first();
            
            
            $campaignProduct = RelationCampaignProduct::where('product_id',$product->id)->first();
            if($campaignProduct){
              $offer_platform = Campaign::where('id', $campaignProduct->campaign_id)->whereNot('offer_platfrom_id',1)->get();
                if($campaignProduct->discount_type==1 && $offer_platform){
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
                'list' => $array,
                );
          $result = array(
                              'status'        =>true,
                              'message'       => 'Data Fetched',
                              'layoutSlug'    => $awardWinning->slug,
                              'layoutTitle'   => $awardWinning->title,
                              'layoutId'      => $awardWinning->id,
                              'products'      =>$array,
                          );

          return response()->json($result,200);

    }

      public function literaryGift()
      {
        $literaryGift = HomepageLayout::find(5);
          $array=array();
          $productList=RelationProductHomepage::where('homepage_layout_id',$literaryGift->id)->get();
          foreach ($productList as $key => $value) {
            $product = Product::where('id',$value->product_id)->first();
            
            
            $campaignProduct = RelationCampaignProduct::where('product_id',$product->id)->first();
            if($campaignProduct){
                $offer_platform = Campaign::where('id', $campaignProduct->campaign_id)->whereNot('offer_platfrom_id',1)->get();
                if($campaignProduct->discount_type==1 && $offer_platform){
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
            $wordlimit = str_limit(strip_tags($product->product_name),15);
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
                'list' => $array,
                );
          $result = array(
                              'status'        =>true,
                              'message'       => 'Data Fetched',
                              'layoutSlug'    => $literaryGift->slug,
                              'layoutTitle'   => $literaryGift->title,
                              'layoutId'      => $literaryGift->id,
                              'products'      =>$array,
                          );

          return response()->json($result,200);

    }

    public function children()
      {
        $children = HomepageLayout::find(6);
          $array=array();
          $productList=RelationProductHomepage::where('homepage_layout_id',$children->id)->get();
          foreach ($productList as $key => $value) {
            $product = Product::where('id',$value->product_id)->first();
            
            $campaign = RelationCampaignProduct::where('product_id',$product->id)->first();
            $campaignProduct = RelationCampaignProduct::where('product_id',$product->id)->first();
            if($campaignProduct){
                $offer_platform = Campaign::where('id', $campaignProduct->campaign_id)->whereNot('offer_platfrom_id',1)->get();
                if($campaignProduct->discount_type==1 && $offer_platform){
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
                'list' => $array,
                );

          $result = array(
                              'status'        =>true,
                              'message'       => 'Data Fetched',
                              'layoutTitle'   => $children->title,
                              'layoutSlug'    => $children->slug,
                              'layoutId'      => $children->id,
                              'products'      =>$array,
                          );

          return response()->json($result,200);

    }


     public function collection()
      {
        $collection = HomepageLayout::find(7);
        
          $array=array();
          $productList=RelationHomepageCollection::where('homepage_layout_id',$collection->id)->get();
          foreach ($productList as $key => $value) {
            $cat = Category::where('id',$value->category_id)->first();
              $array[]= array(
                'id' => $cat->id ,
                'slug' => $cat->slug ,
                'cat_name' => $cat->title ,
                'image' => $value->image ,
                 );
          }

          $array = array(
                'list' => $array,
                );
          $result = array(
                              'status'        =>true,
                              'message'       => 'Data Fetched',
                              'layoutTitle'   => $collection->title,
                              'layoutSlug'    => $collection->slug,
                              'layoutId'      => $collection->id,
                              'products'      =>$array,
                          );

          return response()->json($result,200);

    }
}
