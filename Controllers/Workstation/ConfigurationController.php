<?php

namespace App\Http\Controllers\Workstation;
use App\Http\Controllers\Controller;
use App\Model\Workstation\Author;
use App\Model\Workstation\BillType;
use App\Model\Workstation\Category;
use App\Model\Workstation\CategoryAttributeRelation;
use App\Model\Workstation\DeliveryAddressPrice;
use App\Model\Workstation\Distributor;
use App\Model\Workstation\District;
use App\Model\Workstation\Language;
use App\Model\Workstation\Municipality;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductAttribute;
use App\Model\Workstation\ProductAttributeOption;
use App\Model\Workstation\Publisher;
use App\Model\Workstation\State;
use App\Model\Workstation\Warehouse;
use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;



class ConfigurationController extends Controller
{

    public function passwordCheck(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $validator = Validator::make($request->all(), [
                    'password'      => 'required',
                ]);
                    if ($validator->passes()) {
                        if (Hash::check($request->password, $workstation->password)) {
                            $result = array(
                                            'status'        =>true,
                                            'message'       => 'Password Confirmation',
                                        );

                        return response()->json($result,200);
                        }else{
                        $result = array(
                                            'status'        =>false,
                                            'message'       => 'Password Not match',
                                        );

                        return response()->json($result,401);
                        }
                    
                    }else{
                        $result = array(
                        'status'        => false,
                        'message'       => 'Input Field Required',
                        'data'    => $validator->errors()
                        );
                        return response()->json($result,422);
                    }
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>null,
                ], 401);
            }
    } 



    public function billTypelist(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=BillType::where('status',1)->get();
                foreach ($list as $key => $value) {
                    $array[]= array(
                      'id' =>$value->id ,
                      'title' =>$value->title 
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Bill type Data Fetched',
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

    public function distributorList(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=Distributor::where('status',1)->get();
                foreach ($list as $key => $value) {
                    $array[]= array(
                      'id' =>$value->id ,
                      'title' =>$value->title 
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Distributor Data Fetched',
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


    public function authorlist(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=Author::orderBy('id','desc')->get();
                // $list=Publisher::orderBy('id','asc')->get();

                foreach ($list as $key => $value) {
                    $product = Product::where('author_id',$value->id)->get();
                    $productCount = count($product)+1;
                    $finalProductCount = sprintf("%'03d", $productCount);

                    if ($value->image) {
                     $image = $value->image;
                    }else{
                      $image = DEFAULT_IMG;
                    }

                    if ($value->cover_image) {
                     $cover_image = $value->cover_image;
                    }else{
                      $cover_image = DEFAULT_IMG;
                    }

                    $array[]= array(
                      'id' =>$value->id ,
                      'name' =>$value->name ,
                      'description' =>$value->description ,
                      'author_code' =>$value->author_code ,
                      'book_count' =>$finalProductCount ,
                      'image' => $image,
                      'cover_image' => $cover_image,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Author Data Fetched',
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

    public function authorAdd(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'name' => 'required|max:255',
                'author_code' => 'required|max:2|unique:tbl_author',
                // 'description' => 'required',
                // 'author_image' => 'required',
                // 'author_cover_image' => 'required',
            ]);
            if ($validator->passes()) {

            $count_author_code = Author::where('author_code', 'like', $request->author_code.'%')->count();
            $new_author_code = $request->author_code.sprintf('%02s', $count_author_code);
            $author['name'] = $request->name;
            $author['author_code'] = $new_author_code;
            $author['description'] = $request->description;
            // $author['image'] = $request->image;

            if(!empty($request->file('author_image'))){
            $imagefile = uploadImageGcloud($request->file('author_image'),'author_image');
            $author['image'] = $imagefile;
            }

            if(!empty($request->file('author_cover_image'))){
            $imagefile = uploadImageGcloud($request->file('author_cover_image'),'author_cover_image');
            $author['cover_image'] = $imagefile;
            }



            // $image = $request->file('author_image');
            // $extension = $image->getClientOriginalExtension();
            // $authorName = 'author'. time(). '.' . $extension;
            // $path = base_path() . '/public/images/author/image/';

            // $image->move($path, $authorName);             

            
            // $image = $request->file('author_cover_image');
            // $extension = $image->getClientOriginalExtension();
            // $authorName = 'author_cover'. time(). '.' . $extension;
            // $path = base_path() . '/public/images/author/cover/';

            // $image->move($path, $authorName);               
            // $author['cover_image'] = $authorName;


            $result = Author::create($author);
            if ($result) {
                $user= array(
                                'id' =>$result->id ,
                                'name' =>$result->name ,
                                'author_code' =>$result->author_code ,
                             );

                    $data = array(
                        'status'  =>true,
                        'message' =>'Author added Successfully!' ,
                        'data'=>$user,
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


         public function authorEdit(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $author=Author::where('id',$id)->first();
                if($author){
                    $array[]= array(
                      'id' =>$author->id ,
                      'name' =>$author->name ,
                      'description' =>$author->description ,
                      'author_code' =>$author->author_code ,
                      'image' => $author->image,
                      'cover_image' => $author->cover_image,
                       );
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Author Data Fetched',
                                    'data' =>$array,
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

    public function authorUpdate(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'name' => 'required|max:255',
                'author_code' => 'required|max:2',
                // 'description' => 'required',
            ]);
            if ($validator->passes()) {
            $author = Author::find($id);
            if($author){
            $author->name = $request->name;
            $author->author_code = $request->author_code;
            $author->description = $request->description;

            if($request->author_image_delete == 1)
            {
                $author->image = null;
            }

            if($request->author_cover_image_delete == 1)
            {
                $author->cover_image = null;
            }

            if(!empty($request->file('author_image'))){
            $imagefile = uploadImageGcloud($request->file('author_image'),'author_image');
            $author['image'] = $imagefile;
            }

            if(!empty($request->file('author_cover_image'))){
            $imagefile = uploadImageGcloud($request->file('author_cover_image'),'author_cover_image');
            $author['cover_image'] = $imagefile;
            }


            // if(!empty($request->file('author_image'))){
            // $image = $request->file('author_image');
            // $extension = $image->getClientOriginalExtension();
            // $authorName = 'author'. time(). '.' . $extension;
            // $path = base_path() . '/public/images/author/image/';

            // $image->move($path, $authorName);             
            // $author->image = $authorName;
            // }

            // if(!empty($request->file('author_cover_image'))){
            // $image = $request->file('author_cover_image');
            // $extension = $image->getClientOriginalExtension();
            // $authorName = 'author_cover'. time(). '.' . $extension;
            // $path = base_path() . '/public/images/author/cover/';

            // $image->move($path, $authorName);               
            // $author->cover_image = $authorName;
            // }
            $author->save();
            $authordata= array(
                                'id' =>$author->id ,
                                'name' =>$author->name ,
                                'author_code' =>$author->author_code ,
                             );

                    $data = array(
                        'status'  =>true,
                        'message' =>'Author updated Successfully!' ,
                        'data'=>$authordata,
                        );
                    return response()->json($data,200);
            }else{
                    return response()->json([
                        'message'   =>'Author Not found !',
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

    public function authorDetele(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $publisher=Author::where('id',$id)->first();
                if($publisher){
                        $publisher->delete();
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Author Deleted Successfully!',
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

    
        public function publisherAdd(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'name' => 'required|max:255',
                'publisher_code' => 'required|max:2|unique:tbl_publisher',
                // 'description' => 'required',
                // 'publisher_image' => 'required',
                // 'publisher_cover_image' => 'required',
            ]);
            if ($validator->passes()) {
            $count_publisher_code = Publisher::where('publisher_code', 'like', $request->publisher_code.'%')->count();
            $new_publisher_code = $request->publisher_code.sprintf('%01s', $count_publisher_code);

            $publisher['name'] = $request->name;
            $publisher['publisher_code'] = $new_publisher_code;
            $publisher['description'] = $request->description;
            // $publisher['image'] = $request->image;

            if(!empty($request->file('publisher_image'))){
            $imagefile = uploadImageGcloud($request->file('publisher_image'),'publisher_image');
            $publisher['image'] = $imagefile;
            }
            
            if(!empty($request->file('publisher_cover_image'))){
            $imagefile = uploadImageGcloud($request->file('publisher_cover_image'),'publisher_cover_image');
            $publisher['cover_image'] = $imagefile;
            }

            // if(!empty($request->file('publisher_image'))){
            // $image = $request->file('publisher_image');
            // $extension = $image->getClientOriginalExtension();
            // $publisherName = 'publisher'. time(). '.' . $extension;
            // $path = base_path() . '/public/images/publisher/image/';

            // $image->move($path, $publisherName);             
            // $publisher['image'] = $publisherName;
            // }

            // if(!empty($request->file('publisher_cover_image'))){
            // $image = $request->file('publisher_cover_image');
            // $extension = $image->getClientOriginalExtension();
            // $publisherName = 'publisher_cover'. time(). '.' . $extension;
            // $path = base_path() . '/public/images/publisher/cover/';

            // $image->move($path, $publisherName);               
            // $publisher['cover_image'] = $publisherName;
            // }


            $result = Publisher::create($publisher);
            if ($result) {
                $user= array(
                                'id' =>$result->id ,
                                'name' =>$result->name ,
                                'publisher_code' =>$result->publisher_code ,
                             );

                    $data = array(
                        'status'  =>true,
                        'message' =>'publisher added Successfully!' ,
                        'data'=>$user,
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



    public function publisherlist(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=Publisher::orderBy('id','desc')->get();
                foreach ($list as $key => $value) {

                    if ($value->image) {
                     $image = $value->image;
                    }else{
                      $image = DEFAULT_IMG;
                    }

                    if ($value->cover_image) {
                     $cover_image = $value->cover_image;
                    }else{
                      $cover_image = DEFAULT_IMG;
                    }


                    $array[]= array(
                      'id' =>$value->id ,
                      'name' =>$value->name ,
                      'description' =>$value->description ,
                      'publisher_code' =>$value->publisher_code ,
                      'image' => $value->image,
                      'cover_image' => $cover_image,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Publisher Data Fetched',
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


      public function publisherEdit(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $publisher=Publisher::where('id',$id)->first();
                if($publisher){
                    $array[]= array(
                      'id' =>$publisher->id ,
                      'name' =>$publisher->name ,
                      'description' =>$publisher->description ,
                      'publisher_code' =>$publisher->publisher_code ,
                      'image' => $publisher->image,
                      'cover_image' => $publisher->cover_image,
                       );
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Publisher Data Fetched',
                                    'data' =>$array,
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

    public function publisherUpdate(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'name' => 'required|max:255',
                'publisher_code' => 'required|max:2',
                // 'description' => 'required',
            ]);
            if ($validator->passes()) {
            $publisher = Publisher::find($id);
            if($publisher){
            $publisher->name = $request->name;
            $publisher->publisher_code = $request->publisher_code;
            $publisher->description = $request->description;

            if($request->publisher_image_delete == 1)
            {
                $publisher->image = null;
            }

            if($request->publisher_cover_image_delete == 1)
            {
                $publisher->cover_image = null;
            }

            if(!empty($request->file('publisher_image'))){
            $imagefile = uploadImageGcloud($request->file('publisher_image'),'publisher_image');
            $publisher['image'] = $imagefile;
            }
            
            if(!empty($request->file('publisher_cover_image'))){
            $imagefile = uploadImageGcloud($request->file('publisher_cover_image'),'publisher_cover_image');
            $publisher['cover_image'] = $imagefile;
            }
            $publisher->save();
            $publisherdata= array(
                                'id' =>$publisher->id ,
                                'name' =>$publisher->name ,
                                'publisher_code' =>$publisher->publisher_code ,
                             );

                    $data = array(
                        'status'  =>true,
                        'message' =>'Author updated Successfully!' ,
                        'data'=>$publisherdata,
                        );
                    return response()->json($data,200);
            }else{
                    return response()->json([
                        'message'   =>'Author Not found !',
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

    public function publisherDetele(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $publisher=Publisher::where('id',$id)->first();
                if($publisher){
                        $publisher->delete();
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Publisher Deleted Successfully!',
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'Publisher Not Found',
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


     public function languagelist(Request $request)
    {
        // $wsId = $request->wsId;
        // $workstation=WorkstationUser::find($wsId);
        //     if ($workstation) {
                $array=array();
                $list=Language::where('status',1)->get();
                foreach ($list as $key => $value) {
                    $array[]= array(
                      'id' =>$value->id ,
                      'title' =>$value->title ,
                      'language_code' =>$value->language_code ,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Language Data Fetched',
                                    'data' =>$array,
                                );

                return response()->json($result,200);
            
            // }
            // else{
            //     return response()->json([
            //         'status'     => false,
            //         'message'    => 'Workstation User Not Found',
            //         'data'       =>null,
            //     ], 401);
            // }
    }

    public function categorylist(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=Category::where('status',1)->where('parent_id',0)->get();
                foreach ($list as $key => $value) {
                    $array[]= array(
                      'id' =>$value->id ,
                      'slug' =>$value->slug ,
                      'title' =>$value->title ,
                      'category_code' =>$value->category_code ,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Category Data Fetched',
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

    public function subCategorylist(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=Category::where('status',1)->where('parent_id','!=',0)->get();
                foreach ($list as $key => $value) {
                    $array[]= array(
                      'id' =>$value->id ,
                      'slug' =>$value->slug ,
                      'title' =>$value->title ,
                      'category_id' =>$value->parent_id ,
                      'category_code' =>$value->category_code ,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Category Data Fetched',
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


    public function getCategoryWishAttribute(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=CategoryAttributeRelation::where('category_id',$id)->get();
                foreach ($list as $key => $value) {
                    $attribute=ProductAttribute::where('id',$value->attribute_id)->first();
                    $attributeOption = ProductAttributeOption::where('product_attr_id',$attribute->id)->get();
                    $attribute->option = $attributeOption;
                    $array[]= array(
                      'attribute_id' =>$attribute->id ,
                      'name' =>$attribute->name ,
                      'input_type_id' =>$attribute->input_type_id ,
                      'option' =>$attribute->option,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Product Attribute Data Fetched',
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


    public function getDeliveryAddressPrice(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=DeliveryAddressPrice::get();
                foreach ($list as $key => $value) {
                    $state = State::where('id',$value->state_id)->first();
                    $district = District::where('id',$value->district_id)->first();
                    $municipality = Municipality::where('id',$value->municipality_id)->first();

                    $category = Category::where('id',$value->category_id)->first();
                    $subCategory = Category::where('id',$value->sub_category_id)->first();
                    $product = Product::where('id',$value->product_id)->first();
                    $array[]= array(
                      'id' => $value->id ,
                      'state_id' => $value->state_id ,
                      'category_id' => $value->category_id ?? "" ,
                      'category' => $category->title ?? "" ,
                      'product_id' => $value->product_id ?? "",
                      'product_name' => $product->product_name ?? "" ,
                      'sub_category_id' => $value->sub_category_id ?? "" ,
                      'sub_category' => $subCategory->title ?? "" ,
                      'product_id' => $value->product_id ?? "" ,
                      'state_name' => $state->state_name_np ,
                      'district_id' => $value->district_id ,
                      'municipality_id' => $value->municipality_id,
                      'district_name' => $district->district_name_en,
                      'municipality_name' => $municipality->location_name_en,
                      'is_available' => $value->is_available,
                      'free_above_order_value' => $value->free_above_order_value,
                      'delivery_price' => $value->delivery_price,
                      'max_day' => $value->max_day,
                      'min_day' => $value->min_day,
                      'cod_available' => $value->cod_available,
                       );
                }
                $result = array(
                                    'status'        => true,
                                    'message'       => 'Delivery Address Data Fetched',
                                    'data' => $array,
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


    public function postDeliveryAvailable(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {

            $address = DeliveryAddressPrice::where('id',$id)->first();
            if ($address) {
                $address->is_available = $request->is_available;
                $address->save();
            }
            $result = array(
                                'status'        => true,
                                'message'       => 'Availability Updated !',
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

    public function postCodAvailable(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {

            $address = DeliveryAddressPrice::where('id',$id)->first();
            if ($address) {
                $address->cod_available = $request->cod_available;
                $address->save();
            }
            $result = array(
                                'status'        => true,
                                'message'       => 'Availability Updated !',
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



    public function workstationLogout(Request $request)
    {
            $wsId = $request->wsId;
            $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
            $data=WorkstationUser::where('id', $workstation->id)->update(['access_token' => null]);
            $result = array(
                            'status'        =>true,
                            'message'       => 'Logout Successfully',
                            'data'          => null,
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


    //for warehouse


    public function warehouseAdd(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'name' => 'required|max:255',
                'state_id' => 'required',
                'district_id' => 'required',
                'municipality_id' => 'required',
                'ward' => 'required',
                'area' => 'required',
                'street' => 'required',
            ],
                [
                    'required'       => 'This field is required.',
                ]);

            if ($validator->passes()) {
            $warehouse['name'] = $request->name;
            $warehouse['state_id'] = $request->state_id;
            $warehouse['district_id'] = $request->district_id;
            $warehouse['municipality_id'] = $request->municipality_id;
            $warehouse['ward'] = $request->ward;
            $warehouse['area'] = $request->area;
            $warehouse['street'] = $request->street;
            $warehouse['land_mark'] = $request->land_mark;
            $warehouse['status'] = $request->status;
            $result = Warehouse::create($warehouse);
            if ($result) {
                    $data = array(
                        'status'  =>true,
                        'message' =>'Warehouse added Successfully!' ,
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



    public function warehouselist(Request $request)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $array=array();
                $list=Warehouse::orderBy('id','desc')->get();
                foreach ($list as $key => $value) {
                    $state = State::where('id',$value->state_id)->first();
                    $district = District::where('id',$value->district_id)->first();
                    $municipality = Municipality::where('id',$value->municipality_id)->first();
                    $array[]= array(
                      'id' =>$value->id ,
                      'name' =>$value->name ,
                      'ward' =>$value->ward ,
                      'area' => $value->area,
                      'street' => $value->street,
                      'land_mark' => $value->land_mark,
                      'state_name' => $state->state_name_np ,
                      'district_name' => $district->district_name_en,
                      'municipality_name' => $municipality->location_name_en,
                       );
                }
                $result = array(
                                    'status'        =>true,
                                    'message'       => 'Warehouse Data Fetched',
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


      public function warehouseEdit(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $warehouse=Warehouse::where('id',$id)->first();
                if($warehouse){
                    $state = State::where('id',$warehouse->state_id)->first();
                    $district = District::where('id',$warehouse->district_id)->first();
                    $municipality = Municipality::where('id',$warehouse->municipality_id)->first();
                    $array[]= array(
                      'id' =>$warehouse->id ,
                      'name' =>$warehouse->name ,
                      'ward' =>$warehouse->ward ,
                      'area' => $warehouse->area,
                      'street' => $warehouse->street,
                      'land_mark' => $warehouse->land_mark,
                      'state_name' => $state->state_name_np ,
                      'district_name' => $district->district_name_en,
                      'municipality_name' => $municipality->location_name_en,
                      'state_id' =>$warehouse->state_id ,
                      'district_id' =>$warehouse->district_id ,
                      'municipality_id' =>$warehouse->municipality_id ,
                       );
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Warehouse Data Fetched',
                                    'data' =>$array,
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'Warehouse Not Found',
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

    public function warehouseUpdate(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
        if ($workstation) {
        $validator=Validator::make($request->all(), [
                'name' => 'required|max:255',
                'state_id' => 'required',
                'district_id' => 'required',
                'municipality_id' => 'required',
                'ward' => 'required',
                'area' => 'required',
                'street' => 'required',
            ],
                [
                    'required'       => 'This field is required.',
                ]);
            if ($validator->passes()) {
            $warehouse = Warehouse::find($id);
            if($warehouse){
            $warehouse->name = $request->name;
            $warehouse->state_id = $request->state_id;
            $warehouse->district_id = $request->district_id;
            $warehouse->municipality_id = $request->municipality_id;
            $warehouse->ward = $request->ward;
            $warehouse->area = $request->area;
            $warehouse->street = $request->street;
            $warehouse->land_mark = $request->land_mark;
            $warehouse->status = $request->status;
            $warehouse->save();

            $data = array(
                'status'  =>true,
                'message' =>'Warehouse updated Successfully!' ,
                );
            return response()->json($data,200);
            }else{
                    return response()->json([
                        'message'   =>'Warehouse Not found !',
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

    public function warehouseDetele(Request $request,$id)
    {
        $wsId = $request->wsId;
        $workstation=WorkstationUser::find($wsId);
            if ($workstation) {
                $warehouse=Warehouse::where('id',$id)->first();
                if($warehouse){
                        $warehouse->delete();
                        $result = array(
                                    'status'        =>true,
                                    'message'       => 'Warehouse Deleted Successfully!',
                                );
                        return response()->json($result,200);
                }else{
                    return response()->json([
                        'status'     => false,
                        'message'    => 'Warehouse Not Found',
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

    

}
