<?php

namespace App\Http\Controllers\Site;
use App\Http\Controllers\Controller;
use App\Model\admin\AdminContact;
use App\Model\admin\TrainingParticipant;
use App\Model\admin\NewsLetter;
use App\Model\site\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller {

    public function index(){
        return view('admin.login');
        
    //     $slider = Home::getSliderData();
    //     $showhomepage = Home::getShowOnHomePage();
    //     $servicelist = Home::getCategoryDetail('consulting');
    //     if (!empty($servicelist)) {
    //         $list = Home::getPostListByCategoryId($servicelist->id,$limit=3);
    //         $servicelist->list = $list;
    //     }

    //     $newslist = Home::getCategoryDetail('news');
    //     if (!empty($newslist)) {
    //         $list = Home::getPostListByCategoryId($newslist->id,$limit=6);
    //         $newslist->list = $list;
    //     }

    //     $insightslist = Home::getCategoryDetail('insights');
    //     if (!empty($insightslist)) {
    //         $list = Home::getPostListByCategoryId($insightslist->id,$limit=6);
    //         $insightslist->list = $list;
    //     }

    //     $partnerlist = Home::getPartnerList('2'); // 2 for client
    //     $testimonial = Home::getTestimonialList();
    //     $popupimage = Home::getPopUpImage();

    //     $result = array(
    //         'page_header'       => 'Home',
    //         'slider'            => $slider,
    //         'showhomepage'      => $showhomepage,
    //         'servicelist'       => $servicelist,
    //         'newslist'          => $newslist,
    //         'insightslist'      => $insightslist,
    //         'partnerlist'       => $partnerlist,
    //         'testimonial'       => $testimonial,
    //         'popupimage'        => $popupimage,
    //     );
    //     // return $testimonial;
    //     return view('site.home',$result);
    // }

    // public function categoryList($slug){
    //     $category = Home::getCategoryDetail($slug);
    //     if (!empty($category)) {
    //         $list = Home::getCategoryList($category->id);
    //         $result = array(
    //             'list'            => $list,
    //             'category'        => $category,
    //         );
    //         // return $result;
    //         return view('site.single.categorylist', $result);
    //     } else {
    //         return view('errors.404');
    //     }
    }

    public function authorPostList($slug){
        $author = Home::getAuthorNameBySlug($slug);
        if (!empty($author)) {
            $list = Home::getPostListByAuthorId($author->id);
            $result = array(
                'list'          => $list,
                'author'        => $author,
            );
            return view('site.authorlist', $result);
        } else {
            return view('errors.404');
        }
    }

    public function postDetail($slug){
        $relatedpost = array();
        $detail = Home::getPostDetail($slug);
        if (!empty($detail)) {
            Home::updatePostsViewCount($detail->id);
            $author = Home::getAuthorNameByID($detail->author_id);
            $category = Home::getCategoryIdPostId($detail->id);
            if (!empty($category)) {
                $relatedpost = Home::getRelatedPostListByCategoryId($category->id,$detail->id,5);
            }
            $result = array(
                'detail'        => $detail,
                'author'        => $author,
                'category'      => $category,
                'relatedpost'   => $relatedpost,
            );
            // return $result;
            return view('site.single.postdetail', $result);
        } else{
            return view('errors.404');
        }
    }

    public function pageDetail($slug){
        $relatedpost = array();
        $detail = Home::getPageDetail($slug);
        if (!empty($detail)) {
            Home::updatePageViewCount($detail->id);
            $result = array(
                'detail'        => $detail,
            );
            // return $result;
            return view('site.single.pagedetail', $result);
        } else{
            return view('errors.404');
        }
    }

    public function aboutUs(){
        $showhomepage = Home::getPageDetail('about-us');
        $showhomepage_1 = Home::getPageDetail('who-we-are');
        $showhomepage_2 = Home::getPageDetail('how-we-work');
        // $servicelist = Home::getCategoryDetail('consulting');
        $partnerlist = Home::getPartnerList('1'); // 1 for partners
        $valueslist = Home::getOurCoreValues();
        // if (!empty($servicelist)) {
        //     $list = Home::getPostListByCategoryId($servicelist->id,$limit=3);
        //     $servicelist->list = $list;
        // }
        // $teamlist = Home::getOurTeamListByCategoryId($id=1);

        $result = array(
            'page_header'           => 'About Us',
            'showhomepage'          => $showhomepage,
            'showhomepage_1'        => $showhomepage_1,
            'showhomepage_2'        => $showhomepage_2,
            'partnerlist'           => $partnerlist,
            // 'servicelist'           => $servicelist,
            // 'teamlist'              => $teamlist,
            'valueslist'            => $valueslist,
        );
        // return $teamlist;
        return view('site.single.aboutus', $result);
    }

    public function videoList(){
        $list = Home::getVideoList();
        $result = array(
            'page_header'   => 'Video List',
            'list'          => $list,
        );
        return view('site.videolist', $result);
    }

    public function faqList(){
        $list = Home::getFaqList();
        $result = array(
            'page_header'   => 'FAQ List',
            'list'          => $list,
        );
        // return $list;
        return view('site.faq', $result);
    }

    public function ourTeam(){
        $categorylist = Home::getStaffCategory();
        if (!empty($categorylist)) {
            foreach ($categorylist as $k => $val) {
                $stafflist = Home::getOurTeamListByCategoryId($val->id);
                $categorylist[$k]->stafflist = $stafflist;
            }
        }
        $result = array(
            'page_header'   => 'Our Team List',
            'list'          => $categorylist,
        );
        return view('site.single.ourteam', $result);
    }

    public function teamDetail($slug){
        $detail = Home::getTeamDetail($slug);
        if (!empty($detail)) {
            $stafflist = Home::getOurTeamListByCategoryId(1);
            $result = array(
                'detail'          => $detail,
                'stafflist'       => $stafflist,
            );
            return view('site.single.teamdetail', $result);
        }else{
            return view('errors.404');
        }
    }

    public function trainingSeminar(){
        $detail = Home::getTrainingSeminar();
        if (!empty($detail)) {
            $resourceperson = Home::getResourcePersonListById($detail->id);
            $cordinator = Home::getCoordinatorListById($detail->id);
            $upcominglist = Home::getUpcomingTrainingList();
            $pasttrainingist = Home::getPastTrainingList();

            $result = array(
                'detail'            => $detail,
                'resourceperson'    => $resourceperson,
                'cordinator'        => $cordinator,
                'upcominglist'      => $upcominglist,
                'pasttrainingist'   => $pasttrainingist,
            );
            // return $traininglist;
            return view('site.single.training', $result);
        }else{
            return view('errors.404');
        }
    }

    public function trainingDetail($slug){
        $detail = Home::getTrainingDetail($slug);
        if (!empty($detail)) {
            $resourceperson = Home::getResourcePersonListById($detail->id);
            $cordinator = Home::getCoordinatorListById($detail->id);
            $upcominglist = Home::getUpcomingTrainingList();
            $pasttrainingist = Home::getPastTrainingList();

            $result = array(
                'detail'            => $detail,
                'resourceperson'    => $resourceperson,
                'cordinator'        => $cordinator,
                'upcominglist'      => $upcominglist,
                'pasttrainingist'   => $pasttrainingist,
            );
            // return $traininglist;
            return view('site.single.training', $result);
        }else{
            return view('errors.404');
        }
    }

    public function galleryList($slug){
        $album = Home::getAlbumDetail($slug);
        if (!empty($album)) {
            $list = Home::getGalleryListByAlbumId($album->id);
            $result = array(
                'page_header'   => 'Album List',
                'album'         => $album,
                'list'          => $list,
            );
            // return $result;
            return view('site.gallery', $result);
        }else{
            return view('errors.404');
        }
    }

    public function contactUs(){
        $result = array(
            'page_header'   => 'Contact Us',
        );
        return view('site.single.contactus', $result);
    }

    public function newsLetter(Request $request){
        if (!empty($request->all())) {
            $validator = Validator::make($request->all(),
                [
                    'newslettermail'             => 'required|email|unique:tbl_newsletter_list,email',
                ],
                [
                    'newslettermail.unique'       => 'Email has already been registered',
                ]
            );
            if ($validator->passes()) {
                $newsletter = new NewsLetter;
                $newsletter->email = $request->newslettermail;
                $newsletter->status = '1';
                $newsletter->save();
                // Store your user in database
                $result = array(
                    'error'     => false,
                    'message'   => "We've received your email. Thank You !!!",
                );

                return response()->json($result,200);
            }else{
                $result = array(
                    'error'     => true,
                    'errors'    => $validator->errors()->first()
                );
                return response()->json($result,200);
            }
        }else{
            $result = array(
                'error'     => true,
                'errors'    => 'Unauthorized Access',
            );
            return response()->json($result,200);
        }
    }

    public function postContact(Request $request){
        if (!empty($request->all())) {
            $validator = Validator::make($request->all(),[
                'name'          => 'required',
                'address'       => 'required',
                'email'         => 'required',
                'subject'       => 'required',
                'message'       => 'required',
                'phoneno'       => 'required',
            ]);
            if ($validator->passes()) {
                $crud = new AdminContact;
                $crud->name = $request->name;
                $crud->email = $request->email;
                $crud->address = $request->address;
                $crud->phoneno = $request->phoneno;
                $crud->ip_address = $request->ip();
                $crud->message = $request->message;
                $crud->inserted_date = date('Y-m-d H:i:s');
                $crud->viewed = '0';
                $crud->status = '1';
                $crud->save();
                // Store your user in database
                $result = array(
                    'error'     => false,
                    'message'   => "Thank You for Contacting with Us !!!",
                );

                return response()->json($result,200);
            }else{
                $result = array(
                    'error'     => true,
                    'errors'    => $validator->errors()
                );
                return response()->json($result,200);
            }
        }else{
            $result = array(
                'error'     => true,
                'errors'    => 'Unauthorized Access',
            );
            return response()->json($result,200);
        }
    }

    public function postTrainingEnrollment(Request $request){
        if (!empty($request->all())) {
            $validator = Validator::make($request->all(),[
                'fullname'          => 'required',
                'emailaddress'      => 'required|email',
                'mobileno'          => 'required',
                'message_data'      => 'required',
                'training_id'       => 'required',
            ]);
            if ($validator->passes()) {
                $crud = new TrainingParticipant;
                $crud->fullname = $request->fullname;
                $crud->emailaddress = $request->emailaddress;
                $crud->mobileno = $request->mobileno;
                $crud->training_id = $request->training_id;
                $crud->ip_address = $request->ip();
                $crud->message_data = $request->message_data;
                $crud->requested_date = date('Y-m-d H:i:s');
                $crud->viewed = '0';
                $crud->status = '1';
                $crud->save();
                // Store your user in database

                // send mail as well

                $result = array(
                    'error'     => false,
                    'message'   => "Thank You for Participating !!!",
                );

                return response()->json($result,200);
            }else{
                $result = array(
                    'error'     => true,
                    'errors'    => $validator->errors()
                );
                return response()->json($result,200);
            }
        }else{
            $result = array(
                'error'     => true,
                'errors'    => 'Unauthorized Access',
            );
            return response()->json($result,200);
        }
    }

}