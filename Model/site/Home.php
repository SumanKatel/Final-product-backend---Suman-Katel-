<?php
namespace App\Model\site;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Home extends Model {

    public static function incrementViewCount(){
        DB::table('tbl_site_setting')->where('id', '1')->increment('total_views', 1);
    }

    public static function updatePostsViewCount($postid){
        DB::table('tbl_posts')->where('id', $postid)->increment('viewcount', 1);
    }

    public static function updatePageViewCount($pageid){
        DB::table('tbl_pages')->where('id', $pageid)->increment('viewcount', 1);
    }

    public static function getSliderData(){
        $data = DB::table('tbl_slider')
                ->where('status',1)
                ->get();
        return $data;
    }

    public static function getShowOnHomePage(){
        $data = DB::table('tbl_pages')
                ->where('status','1')
                ->where('show_on_homepage','1')
                ->select('title','description','slug','image')
                ->first();
        return $data;
    }

    public static function getCategoryDetail($slug){
        $data = DB::table('tbl_category')
                ->where('status','1')
                ->where('slug',$slug)
                ->select('id','title','slug','description','sub_title','show_date')
                ->first();
        return $data;
    }

    public static function getRecentPost($limit=10){
        $data = DB::table('tbl_posts as P')
                ->join('rel_post_category as PC','PC.post_id','=','P.id')
                ->join('tbl_category as C','PC.category_id','=','C.id')
                ->where('C.slug', 'news')
                ->orWhere('C.slug', 'insights')
                ->where('P.status',1)
                ->select('P.title','P.slug','P.image','P.published_date')
                ->orderBy('P.published_date','desc')
                ->limit($limit)
                ->get();
        return $data;
    }

    public static function getCategoryIdPostId($postid){
        $data = DB::table('tbl_category as C')
                ->join('rel_post_category as PC','PC.category_id','=','C.id')
                ->where('PC.post_id',$postid)
                ->where('C.status',1)
                ->select('C.id','C.title','C.slug','C.show_date')
                ->first();
        return $data;
    }

    public static function getRelatedPostListByCategoryId($categoryid,$postid,$limit=10){
        $fields = array(
            'P.id',
            'P.title',
            'P.slug',
            'P.image',
            'P.published_date',
        );
        $data = DB::table('tbl_posts AS P')
                ->join('rel_post_category as PC','PC.post_id','=','P.id')
                ->where('P.id','!=',$postid)
                ->where('P.status','1')
                ->where('PC.category_id',$categoryid)
                ->orderBy('P.published_date','desc')
                ->select($fields)
                ->limit($limit)
                ->get();
        return $data;
    }

    public static function getPostListByCategoryId($categoryid,$limit=10){
        $fields = array(
            'P.id',
            'P.title',
            'P.slug',
            'P.description',
            'P.image',
            'P.published_date',
            'P.author_id',
            'P.file',
        );
        $data = DB::table('tbl_posts AS P')
                ->join('rel_post_category as PC','PC.post_id','=','P.id')
                ->where('P.status','1')
                ->where('PC.category_id',$categoryid)
                ->orderBy('P.published_date','desc')
                ->select($fields)
                ->limit($limit)
                ->get();
        return $data;
    }

    public static function getTestimonialList(){
        $data = DB::table('tbl_testimonial')
                ->where('status',1)
                ->select('name','designation','company_name','description','image','url')
                ->get();
        return $data;
    }

    public static function getPartnerList($type,$limit=6){
        $data = DB::table('tbl_partner')
                ->select('title','url','image')
                ->where('partner_type_id',$type)
                ->where('status',1)
                ->orderBy('sort_order','asc')
                ->limit($limit)
                ->get();
        return $data;
    }

    public static function getCategoryList($categoryid){
        $fields = array(
            'P.id',
            'P.title',
            'P.slug',
            'P.description',
            'P.image',
            'P.file',
            'P.published_date',
            'P.author_id',
            // 'P.video_link',
            'P.file',
        );
        $data = DB::table('tbl_posts AS P')
                ->join('rel_post_category as PC','PC.post_id','=','P.id')
                ->where('P.status','1')
                ->where('PC.category_id',$categoryid)
                ->orderBy('P.published_date','desc')
                ->select($fields)
                ->paginate(9);
        return $data;
    }

    public static function getPostDetail($slug){
        $fields = array(
            'P.id',
            // 'P.sub_title',
            'P.title',
            'P.description',
            'P.image',
            'P.file',
            'P.slug',
            'P.author_id',
            // 'P.video_link',
            'P.meta_title',
            'P.meta_keywords',
            'P.meta_description',
            // 'P.image_slideshow_text',
            'P.file',
            'P.published_date',
        );
        $data = DB::table('tbl_posts as P')
                ->where('P.status','1')
                ->where('P.slug',$slug)
                ->orderBy('P.published_date','desc')
                ->first($fields);
        return $data;
    }

    public static function getAuthorNameByID($authorid){
        $data = DB::table('tbl_author')
                ->select('title','slug','description','image')
                ->where('id',$authorid)
                ->where('status',1)
                ->first();
        return $data;
    }

    public static function getPageDetail($slug){
        $data = DB::table('tbl_pages')
                ->select('id','slug','image','published_date','title','description')
                ->where('slug',$slug)
                ->where('status','1')
                ->first();
        return $data;
    }

    public static function getAuthorNameBySlug($slug){
        $data = DB::table('tbl_author')
                ->select('id','name','slug','description','image')
                ->where('slug',$slug)
                ->where('status',1)
                ->first();
        return $data;
    }

    public static function getPostListByAuthorId($authorid){
        $fields = array(
            'P.id',
            'P.title',
            'P.slug',
            'P.description',
            'P.image',
            'P.published_date',
        );
        $data = DB::table('tbl_posts AS P')
                ->where('P.author_id',$authorid)
                ->where('P.status',1)
                ->orderBy('P.published_date','desc')
                ->select($fields)
                ->paginate(10);
        return $data;
    }

    public static function getVideoList(){
        $data= Db::table('tbl_videos')
                ->where('status','1')
                ->select('title','embed','image','published_date')
                ->orderBy('sort_order')
                ->paginate(12);
        return $data;
    }

    public static function getFaqList(){
        $data = Db::table('tbl_faq')
                ->where('status','1')
                ->select('title','description')
                ->orderBy('sort_order')
                ->get();
        return $data;
    }

    public static function getStaffCategory(){
        $data = DB::table('tbl_department')
                ->where('status',1)
                ->select('id','title')
                ->orderBy('sort_order','asc')
                ->get();
        return $data;
    }


    public static function getOurTeamListByCategoryId($deptid){
        $data = DB::table('tbl_teams')
                ->where('status','1')
                ->where('department_id',$deptid)
                ->select('name','slug','designation','email','image','info','facebook_link','twitter_link','linkedin_link','phone')
                ->orderBy('sort_order','asc')
                ->get();
        return $data;
    }

    public static function getTeamDetail($slug){
        $data = DB::table('tbl_teams')
                ->where('status',1)
                ->where('slug',$slug)
                ->select('name','slug','designation','email','image','info','facebook_link','twitter_link','linkedin_link','phone')
                ->first();
        return $data;
    }

    public static function getTrainingSeminar(){
        $data = DB::table('tbl_training_seminar')
                ->where('status',1)
                ->orderBy('inserted_date','desc')
                ->limit(1)
                ->first();
        return $data;
    }

    public static function getTrainingDetail($slug){
        $data = DB::table('tbl_training_seminar')
                ->where('status',1)
                ->where('slug',1)
                ->first();
        return $data;
    }

    public static function getUpcomingTrainingList(){
        $today = date('Y-m-d');
        $data = DB::table('tbl_training_seminar')
                ->where('status',1)
                ->where('inserted_date','>',$today)
                ->orderBy('inserted_date','desc')
                ->get();
        return $data;
    }

    public static function getPastTrainingList(){
        $today = date('Y-m-d');
        $data = DB::table('tbl_training_seminar')
                ->where('status',1)
                ->where('inserted_date','<' ,$today)
                ->orderBy('inserted_date','desc')
                ->get();
        return $data;
    }

    public static function getResourcePersonListById($trainingid){
        $data = DB::table('tbl_resource_person as R')
                ->join('rel_training_resource_person as TR','TR.resouce_person_id','=','R.id')
                ->where('R.status',1)
                ->where('TR.training_id',$trainingid)
                ->orderBy('R.sort_order','asc')
                ->get();
        return $data;
    }

    public static function getCoordinatorListById($trainingid){
        $data = DB::table('tbl_training_coordinator as R')
                ->join('rel_training_coordinator as TR','TR.coordinator_id','=','R.id')
                ->where('R.status',1)
                ->where('TR.training_id',$trainingid)
                ->orderBy('R.title','asc')
                ->get();
        return $data;
    }

    public static function getOurCoreValues(){
        $data = DB::table('tbl_core_value')
                ->where('status',1)
                ->orderBy('title','asc')
                ->get();
        return $data;
    }

    public static function getPopupImage(){
        $data = DB::table('tbl_popup')
                ->where('status', '1')
                ->select('title','link','image')
                ->orderBy('published_date','desc')
                ->get();
        return $data;
    }

    // upto here

    public static function getSearch($tablename,$title){
         $fields = array(
            'slug',
            'image',
            // 'description_LANG as description',
            'title_LANG as title',
            'published_date',
        );
        $fields = str_replace('LANG', \App::getLocale(), $fields);
        $data = DB::table($tablename)
                ->where('title_en', 'like', '%'.$title.'%')
                ->orWhere('title_np', 'like', '%'.$title.'%')
                ->where('status', '1')
                ->orderBy('created_at','desc')
                ->limit(15)
                ->get($fields);
        return $data;
    }

}