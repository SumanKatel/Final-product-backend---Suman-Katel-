<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use App\Model\Front\ProductWishList;
use App\Model\Workstation\Author;
use App\Model\Workstation\Customer;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishController extends Controller
{
    public function wish_add(Request $request){

        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
        $validator=Validator::make($request->all(), [
                'product_id'                => 'required',
            ]);
            if ($validator->passes()) {
                $wishProduct = ProductWishList::where('customer_id', $customer->id)->where('product_id',$request->product_id)->first();
                if($wishProduct)
                {
                    $data = array(
                        'status'        => false,
                        'message'       => 'Already Added' ,
                        );
                    return response()->json($data,401);
                    
                }else{
                $card = new ProductWishList();
                $card->product_id = $request->product_id;
                $card->customer_id = $customerId;
                $card->save();

            if ($card) {
                    $data = array(
                        'status'        => true,
                        'message'       => 'This product added to Wish List!' ,
                        'data'          => $card,
                        );
                    return response()->json($data,200);
                }else{
                    return response()->json([
                        'message'   =>'Something went wrong !',
                        'status'    =>false,
                        'data'      =>null,
                    ]);
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
        }else
        {
            return response()->json([
                'status'     => false,
                'message'    => 'User Not Found',
                'data'       =>null,
            ], 404);
        }
    }

    public function wish_list(Request $request){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            $array = array();
            $list = ProductWishList::where('customer_id', $customer->id)->get();

            foreach ($list as $key => $cart) {
            $product = Product::where('id',$cart->product_id)->first();
            
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
                'wish_id' => $cart->id ,
                'id' => $product->id ,
                'slug' => $product->slug ,
                'product_name' => $product->product_name ,
                'sku' => $product->sku ,
                'mrp_paper_book' => $product->mrp_paper_book ,
                'product_image' => $productImage,
                'productStock'  => $productStock,
                'author_name'  => $author_name,
                'author_slug'  => $author_slug,
                'stock'         => $iss,
                 );
              
        }

            $result = array(
                    'status'        => true,
                    'message'       => 'Wishlist Data Fetched',
                    'data'          =>  $array,
                );

            return response()->json($result,200);
        }
        else{
            return response()->json([
                'status'     => false,
                'message'    => 'User Not Found',
                'data'       => $this->wsId,
            ], 401);
        }
    }

    public function wish_delete(Request $request, $id){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            $cart=ProductWishList::where('id', $id)->first();
            if($cart){
                $cart->delete();
                $result = array(
                            'status'        =>true,
                            'message'       => 'Wish list data Deleted Successfully!',
                        );
                return response()->json($result,200);
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Cart Not Found',
                    'data'       =>null,
                ], 401);
            }
        }else{
            return response()->json([
                'status'     => false,
                'message'    => 'User Not Found',
                'data'       =>null,
            ], 401);
        }
    }


    public function wishCheck(Request $request,$productId){
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
        if ($customer) {
            $wishProduct = ProductWishList::where('customer_id', $customer->id)->where('product_id',$productId)->first();
            if ($wishProduct) {
                    $is_wish = 1;
                    $wish_id = $wishProduct->id;
                }else{
                    $is_wish = 0;
                    $wish_id = null;
                }
            $result = array(
                    'status'        => true,
                    'is_wish'       => $is_wish,
                    'wish_id'       => $wish_id,
                );
            return response()->json($result,200);
        }
        else{
            return response()->json([
                'status'     => false,
                'message'    => 'User Not Found',
                'data'       => $this->wsId,
            ], 401);
        }
    }
}
