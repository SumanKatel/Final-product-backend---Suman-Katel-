<?php

namespace App\Http\Controllers\Workstation;
use App\Http\Controllers\Controller;
use App\Imports\ProductImport;
use App\Model\Workstation\Author;
use App\Model\Workstation\Brand;
use App\Model\Workstation\Category;
use App\Model\Workstation\CategoryAttributeRelation;
use App\Model\Workstation\Employer;
use App\Model\Workstation\Language;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductAttribute;
use App\Model\Workstation\ProductImage;
use App\Model\Workstation\Publisher;
use App\Model\Workstation\RelationProductAttribute;
use App\Model\Workstation\RelationProductCategory;
use App\Model\Workstation\WorkstationUser;
use App\Model\Workstation\Level;
use App\Model\Workstation\Subject;
use App\Model\admin\Class;
use App\Model\admin\University;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;


class ProductController extends Controller
{

 public function academicConfList(Request $request)
{
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
            $array = array();
            $universities = University::where('status',1)->orderBy('created_at','desc')->get();
                foreach ($universities as $key => $university) {
                    $levels = Level::where('university_id',$university->id)->where('status',1)->get();
                    $levelsArray = array();
                        foreach ($levels as $key => $level) { 
                            $classes = Class::where('level_id',$level->id)->where('status',1)->get();
                            $classArray = array();
                            foreach ($classes as $key => $class) {

                                    $subjectArray = array();
                                    $subjects = Subject::where('class_id',$class->id)->where('status',1)->get();
                                    foreach ($subjects as $key => $subject) {
                                       $subjectArray[]= array(
                                          'id'          => $subject->id ,
                                          'title'       => $subject->title ,
                                          'slug'        => $subject->slug ,
                                        );
                                    }
                                $classArray[]= array(
                                  'id'          => $class->id ,
                                  'title'       => $class->title ,
                                  'slug'        => $class->slug ,
                                  'subjectList'   =>$subjectArray,
                                );
                            }
                            $levelsArray[]= array(
                              'id'          => $level->id ,
                              'title'       => $level->title ,
                              'slug'        => $level->slug ,
                              'classList'   =>$classArray,
                            );
                        }
                    $array[]= array(
                          'id' => $university->id ,
                          'name' => $university->name ,
                          'slug' => $university->slug ,
                          'acronym' => $university->acronym ,
                          'levelList' =>$levelsArray,
                        );
                }
                    $result = array(
                                    'status'        => true,
                                    'message'       => 'Data Fetched !',
                                    'universities'  => $array,
                                );

                return response()->json($result,200);
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    }



        public function addProduct(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'product_name' => 'required|max:255',
                
            ]);
            if ($validator->passes()) {

            $author = Author::where('id',$request->author_id)->first();
            $language = language::where('id',$request->language_id)->first();
            $publisher = Publisher::where('id',$request->publisher_id)->first();

            $category= Category::where('id', 1)->first();
            $parent_category_code = $category->category_code;
            $sub_category = Category::where('id', 228)->first();
            $sub_category_code = $sub_category->category_code;
            

            $product = Product::where('author_id',$request->author_id)->get();
            $productCount = count($product)+1;
            $finalProductCount = sprintf("%'03d", $productCount);


            $sku = strtoupper($parent_category_code.$sub_category_code.$language->language_code.$publisher->publisher_code.$author->author_code.$finalProductCount);

            $crud['product_name'] = $request->product_name;
            $crud['product_description'] = $request->product_description;
            $crud['publisher_id'] = $request->publisher_id;
            $crud['author_id'] = $request->author_id;
            $crud['language_id'] = $request->language_id;
            $crud['is_ebook'] = $request->is_ebook;
            $crud['sku'] = $sku;
            $crud['sku_ebook'] = $request->sku_ebook;
            $crud['mrp_paper_book'] = $request->mrp_paper_book;
            $crud['listing_price'] = $request->listing_price;
            $crud['opening_stock'] = $request->opening_stock;
            $crud['mrp_e_book'] = $request->mrp_e_book;
            $crud['meta_title'] = $request->meta_title;
            $crud['meta_keywords'] = $request->meta_keywords;
            $crud['meta_description'] = $request->meta_description;

            $crud['brand_id'] = $request->brand_id;
            $crud['employer_id'] = $request->employer_id;
            $crud['level_id'] = $request->level_id;

            $crud['status'] = $request->status;
            $product = Product::create($crud);

            if ($product) {
                //for relation product category

                if (!empty($request->category_id)) {
                    foreach ($request->category_id as $key => $cat_id) {
                        $parentCategory = Category::where('id',$cat_id)->first();
                            $crud = new RelationProductCategory;
                            $crud->product_id = $product->id;
                            $crud->parent_category_id = $parentCategory->parent_id;
                            $crud->category_id = $parentCategory->id;
                            $crud->save();
                    }
                }


                //for product Attribute

                if ($request->input_type_1 == '1'){
                foreach ($request->product_attribute_id_1 as $k => $attributeId) {

                    // add new data
                    $crud = new RelationProductAttribute;
                    $crud->product_id = $product->id;
                    $crud->product_attribute_id = $attributeId;
                    $crud->attribute_value = $request->product_attribute_value_1[$k];
                    $crud->save();
                    }
                }

                if ($request->input_type_2 == '2') {
                    // $request->product_attribute_id_2 = array_unique($request->product_attribute_id_2);
                    foreach ( $request->product_attribute_id_2 as $kl => $attributeId) {
                        $crud = new RelationProductAttribute;
                        $crud->product_id = $product->id;
                        $crud->product_attribute_id = $attributeId;
                        $crud->attribute_value = $request->product_attribute_value_2[$kl];
                        $crud->save();
                    }
                }


                //for Image upload
                if(!empty($request->file('desktopImages'))){
                    $sort_order=1;
                        foreach ($request->file('desktopImages') as $key => $desktopImage) {
                            if ($desktopImage) {

                                $imagefile = uploadImageGcloud($desktopImage,'product-desktop');
                                $img['image'] = $imagefile;



                                // $extension = $desktopImage->getClientOriginalExtension();
                                // $ran=random_int(100, 999);
                                // $productName = 'product'. time().$ran. '.' . $extension;
                                // $path = base_path() . '/public/images/product/desktop/';
                                // $desktopImage->move($path, $productName);

                                // $img['image'] = $productName;
                                $img['product_id'] = $product->id;

                                if($key==0){
                                $img['is_desktop_thumbnail'] = 1;
                                }else{
                                $img['is_desktop_thumbnail'] = 2;
                                }
                                // $img['sort_order'] = $request->desktop_sort_order[$key];
                                $img['sort_order'] = $sort_order++;
                                $img['status'] = 1;
                                $imageResult=ProductImage::create($img);
                            }
                        }
                    }


                    if(!empty($request->file('mobileImages'))){
                    $sort_order=1;
                        foreach ($request->file('mobileImages') as $key => $mobileImage) {
                            if ($mobileImage) {
                                $imagefile = uploadImageGcloud($mobileImage,'product-mobile');
                                $img['image'] = $imagefile;
                                $img['product_id'] = $product->id;
                                if($key==0){
                                $img['is_mobile_thumbnail'] = 1;
                                }else{
                                $img['is_mobile_thumbnail'] = 2;
                                }
                                $img['sort_order'] = $sort_order++;
                                $img['status'] = 1;
                                $imageResult=ProductImage::create($img);
                            }
                        }
                    }
                }
            if ($product) {
                $list= array(
                                'all' =>$product,
                             );

                    $data = array(
                        'status'  =>true,
                        'message' =>'Product added Successfully!' ,
                        'data'=>$list,
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



    public function getProduct(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=Product::orderBy('id','desc')->get();
                foreach ($list as $key => $value) {
                   
                $relcategory = RelationProductCategory::where('product_id',$value->id)->where('parent_category_id',0)->first();
                $category = Category::where('id',$relcategory->category_id)->first();

                $relsubcategory = RelationProductCategory::where('product_id',$value->id)->where('parent_category_id','!=',0)->first();
                $subcategory = Category::where('id',$relsubcategory->category_id)->first();
                    $array[]= array(
                      'id' =>$value->id ,
                      'product_name' =>$value->product_name ,
                      'sku' =>$value->sku ,
                      'listing_price' =>$value->listing_price ,
                      'opening_stock' =>$value->opening_stock ,
                      'status' =>$value->status ,
                      'category_id' => $category->id,
                      'author_id' => $value->author_id,
                      'publisher_id' => $value->publisher_id,
                      'category_name' => $category->title,
                      'sub_category_id' => $subcategory->id,
                      'sub_category_name' => $subcategory->title,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Product Data Fetched',
                                    'data' =>$array,
                                );

                return response()->json($result,200);
            
            }
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    } 


    public function editProduct(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $product=Product::find($id);
                if ($product) {
                $desktopImages = ProductImage::where('product_id',$product->id)->where('is_desktop_thumbnail','!=','')->get();
                $mobileImages = ProductImage::where('product_id',$product->id)->where('is_mobile_thumbnail','!=','')->get();

                $relcategory = RelationProductCategory::where('product_id',$product->id)->where('parent_category_id',0)->first();
                $category = Category::where('id',$relcategory->category_id)->first();

                $relsubcategory = RelationProductCategory::where('product_id',$product->id)->where('parent_category_id','!=',0)->first();
                $subcategory = Category::where('id',$relsubcategory->category_id)->first();

                $level = Level::where('id',$product->level_id)->first();
                $brand = Brand::where('id',$product->brand_id)->first();
                $LoksewaEmployer = Employer::where('id',$product->employer_id)->first();

                $allcategory = RelationProductCategory::where('product_id',$product->id)->get();
                $allcategoryArray = array();
                foreach ($allcategory as $key => $categoryData) {
                    $category = Category::where('id',$categoryData->category_id)->first();

                    $allcategoryArray[]= array(
                              'category_id' => $category->id ,
                              'category_name' => $category->category_name ,
                            );
                }

                    $array[]= array(
                      'id' =>$product->id ,
                      'product_name' =>$product->product_name ,
                      'publisher_id' =>$product->publisher_id ,
                      'author_id' =>$product->author_id ,
                      'language_id' =>$product->language_id ,
                      'meta_title' =>$product->meta_title ,
                      'meta_keywords' =>$product->meta_keywords ,
                      'meta_description' =>$product->meta_description ,
                      'sku' =>$product->sku ,
                      'listing_price' =>$product->listing_price ,
                      'mrp_paper_book' =>$product->mrp_paper_book ,
                      'opening_stock' =>$product->opening_stock ,
                      'product_description' => $product->product_description,
                      'status' =>$product->status ,
                      'category_id' => $category->id,
                      'category_name' => $category->title,
                      'sub_category_id' => $subcategory->id,
                      'sub_category_name' => $subcategory->title,
                      'desktopImages' => $desktopImages,
                      'mobileImages' => $mobileImages,

                      'level_id'=>$level->id ?? null,
                      'level_title'=>$level->title ?? null,
                      'brand_id'=>$brand->id ?? null,
                      'brand_title'=>$brand->title ?? null,
                      'loksewa_employer_id'=>$LoksewaEmployer->id ?? null,
                      'loksewa_employer_name'=>$LoksewaEmployer->name ?? null,

                      'allcategory'    => $allcategoryArray,
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
            else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    }

public function updateProduct(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'product_name' => 'required|max:255',
                
            ]);
            if ($validator->passes()) {
            $product=Product::find($id);
            $product->product_name = $request->product_name;
            $product->product_description = $request->product_description;
            $product->publisher_id = $request->publisher_id;
            $product->author_id = $request->author_id;
            $product->language_id = $request->language_id;
            $product->is_ebook = $request->is_ebook;
            // $product->sku = $request->sku;
            // $product->sku_ebook = $request->sku_ebook;
            $product->mrp_paper_book = $request->mrp_paper_book;
            $product->listing_price = $request->listing_price;
            $product->opening_stock = $request->opening_stock;
            $product->mrp_e_book = $request->mrp_e_book;
            $product->meta_title = $request->meta_title;
            $product->meta_keywords = $request->meta_keywords;
            $product->meta_description = $request->meta_description;
            $product->status = $request->status;
            $product->save();

            if ($product) {
                //for relation product category

                // if (!empty($request->category_id)) {
                //     foreach ($request->category_id as $key => $cat_id) {
                //         $parentCategory = Category::where('id',$cat_id)->first();
                //             $crud = new RelationProductCategory;
                //             $crud->product_id = $product->id;
                //             $crud->parent_category_id = $parentCategory->parent_id;
                //             $crud->category_id = $parentCategory->id;
                //             $crud->save();
                //     }
                // }


                //for product Attribute

                // if ($request->input_type_1 == '1'){
                // foreach ($request->product_attribute_id_1 as $k => $attributeId) {

                //     // add new data
                //     $crud = new RelationProductAttribute;
                //     $crud->product_id = $product->id;
                //     $crud->product_attribute_id = $attributeId;
                //     $crud->attribute_value = $request->product_attribute_value_1[$k];
                //     $crud->save();
                //     }
                // }

                // if ($request->input_type_2 == '2') {
                //     // $request->product_attribute_id_2 = array_unique($request->product_attribute_id_2);
                //     foreach ( $request->product_attribute_id_2 as $kl => $attributeId) {
                //         $crud = new RelationProductAttribute;
                //         $crud->product_id = $product->id;
                //         $crud->product_attribute_id = $attributeId;
                //         $crud->attribute_value = $request->product_attribute_value_2[$kl];
                //         $crud->save();
                //     }
                // }

                //image delete

                 if(!empty($request->delete_img)){
                        foreach ($request->delete_img as $key => $img) {
                            if ($img) {
                            $productImage = ProductImage::where('id',$img)->first();
                            $productImage->delete();
                            }
                        }
                    }



                //for Image upload
                if(!empty($request->file('desktopImages'))){
                    $sort_order=1;
                        foreach ($request->file('desktopImages') as $key => $desktopImage) {
                            if ($desktopImage) {

                                $imagefile = uploadImageGcloud($desktopImage,'product-desktop');
                                $img['image'] = $imagefile;



                                // $extension = $desktopImage->getClientOriginalExtension();
                                // $ran=random_int(100, 999);
                                // $productName = 'product'. time().$ran. '.' . $extension;
                                // $path = base_path() . '/public/images/product/desktop/';
                                // $desktopImage->move($path, $productName);

                                // $img['image'] = $productName;
                                $img['product_id'] = $product->id;

                                if($key==0){
                                $img['is_desktop_thumbnail'] = 1;
                                }else{
                                $img['is_desktop_thumbnail'] = 2;
                                }
                                // $img['sort_order'] = $request->desktop_sort_order[$key];
                                $img['sort_order'] = $sort_order++;
                                $img['status'] = 1;
                                $imageResult=ProductImage::create($img);
                            }
                        }
                    }


                    if(!empty($request->file('mobileImages'))){
                    $sort_order=1;
                        foreach ($request->file('mobileImages') as $key => $mobileImage) {
                            if ($mobileImage) {
                                $imagefile = uploadImageGcloud($mobileImage,'product-mobile');
                                $img['image'] = $imagefile;
                                $img['product_id'] = $product->id;
                                if($key==0){
                                $img['is_mobile_thumbnail'] = 1;
                                }else{
                                $img['is_mobile_thumbnail'] = 2;
                                }
                                $img['sort_order'] = $sort_order++;
                                $img['status'] = 1;
                                $imageResult=ProductImage::create($img);
                            }
                        }
                    }
                }
            if ($product) {
                $list= array(
                                'all' =>$product,
                             );

                    $data = array(
                        'status'  =>true,
                        'message' =>'Product added Successfully!' ,
                        'data'=>$list,
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

    public function fileimportProduct(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'product_file' => 'required|max:255',
                
            ]);
            if ($validator->passes()) {
            $result =Excel::import(new ProductImport, $path);
            if ($result) {
            $data = array(
                        'status'  =>true,
                        'message' =>'Product uploaded Successfully!' ,
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
