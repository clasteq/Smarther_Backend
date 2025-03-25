<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\CommonController;
use DB;
use App\Models\BackgroundTheme;

class CommunicationPostStaff extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $table = 'communication_posts_staff';

    protected $appends = [ 'posted_user', 'is_created_ago', 'is_liked', 'like_count', 'post_category', 'post_theme', 
         'is_attachment', 'is_image_attachment', 'is_video_attachment', 'is_files_attachment', 'is_notify_datetime', 
         'is_youtube_link', 'is_read_count', 'is_post_receivers'];

     public function getIsReadCountAttribute() {
        $is_read_count = DB::table('staff_notifications')->where('post_id', $this->id)->where('type_no', 4)
            ->where('read_status', 1)->select('id')->count();
        return $is_read_count;
     }

     public function getIsYoutubeLinkAttribute() {
        $is_youtube_link = '';
        $youtube_link = $this->youtube_link;
        if(!empty($youtube_link)) {
            $re = '/watch\?v=(.*)/mi'; 

            preg_match_all($re, $youtube_link, $matches, PREG_SET_ORDER, 0);
            if(isset($matches) && is_array($matches) && count($matches)>0) {
                if(isset($matches[0]) && is_array($matches[0]) && count($matches[0])>=1) {
                    $videoid = $matches[0][1];
                    $videoid = trim($videoid);
                    $is_youtube_link = 'https://www.youtube.com/embed/'.$videoid;
                }
            }
            if(empty($is_youtube_link)) {
                $re = '/youtu.be\/(.*)/mi';  
                preg_match_all($re, $youtube_link, $matches, PREG_SET_ORDER, 0); 
                if(isset($matches) && is_array($matches) && count($matches)>0) {  
                    if(isset($matches[0]) && is_array($matches[0]) && count($matches[0])>=1) {
                        $videoid = $matches[0][1];
                        if(!empty($videoid)) {
                            $arr = explode('?', $videoid);
                            $videoid = $arr[0];
                        }
                        $videoid = trim($videoid);
                        if(!empty($videoid)) {
                            $is_youtube_link = 'https://www.youtube.com/embed/'.$videoid;
                        }
                    }
                }
            }
            if(empty($is_youtube_link)) {
                $re = '/shorts\/(.*)/mi';
                preg_match_all($re, $youtube_link, $matches, PREG_SET_ORDER, 0);  
                if(isset($matches) && is_array($matches) && count($matches)>0) {
                    if(isset($matches[0]) && is_array($matches[0]) && count($matches[0])>=1) {
                        $videoid = $matches[0][1];
                        $videoid = trim($videoid);
                        $is_youtube_link = 'https://www.youtube.com/embed/'.$videoid;
                    }
                }
            }
        }
        return $is_youtube_link;
     }

     public function getIsPostReceiversAttribute() {

        $post_id = $this->id;
        $post  = DB::table('communication_posts_staff')->where('id', $post_id)->first();

        $is_receivers = '';
        if(!empty($post)) {
            $post_type = $post->post_type;
            $receiver_end = $post->receiver_end;
            if($post_type == 1) { // section ids
                $section_ids = $post->receiver_end;
                if(!empty($section_ids)) {
                    $section_ids = explode(',', $section_ids);
                    $section_ids = array_unique($section_ids);
                    $section_ids = array_filter($section_ids);
                    if(count($section_ids) > 0) {
                        $is_receivers = DB::table('sections')
                            ->leftjoin('classes', 'classes.id', 'sections.class_id')
                            ->where('sections.status','ACTIVE')->whereIn('sections.id',$section_ids)
                            ->select('section_name as name1', 'classes.class_name as name')->orderby('name', 'asc')->get(); 
                    }
                }
            }   else if($post_type == 2) { // role ids 
                $role_ids = $post->receiver_end;
                if(!empty($role_ids)) {
                     
                    $role_ids = explode(',', $role_ids);
                    $role_ids = array_unique($role_ids);
                    $role_ids = array_filter($role_ids);
                    if(count($role_ids) > 0) { 
                        $is_receivers = DB::table('userroles')//->where('status','ACTIVE')
                            ->whereIn('id',$role_ids)
                            ->select('user_role as name', DB::RAW('"" as name1'))->orderby('name', 'asc')->get();  
                    } 
                }
            }   else if($post_type == 6) { // user ids
                $user_ids = $post->receiver_end;
                if(!empty($user_ids)) {
                    $user_ids = explode(',', $user_ids);
                    $user_ids = array_unique($user_ids);
                    $user_ids = array_filter($user_ids);
                    if(count($user_ids) > 0) {
                        $is_receivers = DB::table('users')->leftjoin('userroles', 'userroles.ref_code', 'users.user_type')
                            ->where('users.school_college_id',$post->posted_by)->where('userroles.school_id',$post->posted_by)
                            ->whereIn('users.id',$user_ids)
                            ->select('name', 'userroles.user_role as name1')->orderby('name', 'asc')->get(); 
                    }
                }
            }   else if($post_type == 3) { // all user ids 

            }   else if($post_type == 4) { // group ids 
                $group_ids = $post->receiver_end;
                if(!empty($group_ids)) {
                     
                    $group_ids = explode(',', $group_ids);
                    $group_ids = array_unique($group_ids);
                    $group_ids = array_filter($group_ids);
                    if(count($group_ids) > 0) { 
                        $is_receivers = DB::table('communication_groups')->where('status','ACTIVE')->whereIn('id',$group_ids)
                            ->select('group_name as name', DB::RAW('"" as name1'))->orderby('name', 'asc')->get();  
                    } 
                }
            }   else if($post_type == 5) { // department ids 
                $department_ids = $post->receiver_end;
                if(!empty($department_ids)) {
                     
                    $department_ids = explode(',', $department_ids);
                    $department_ids = array_unique($department_ids);
                    $department_ids = array_filter($department_ids);
                    if(count($department_ids) > 0) { 
                        $is_receivers = DB::table('departments')->where('status','ACTIVE')->whereIn('id',$department_ids)
                            ->select('department_name as name', DB::RAW('"" as name1'))->orderby('name', 'asc')->get();  
                    } 
                }
            }  
        }
        if(!empty($is_receivers) && $is_receivers->isNotEmpty()) {
            $is_receivers = $is_receivers->toArray();
        }
        return  $is_receivers;
    }

    public static function getIsReceiversAttribute($post_id) {

        $post  = DB::table('communication_posts_staff')->where('id', $post_id)->first();

        $is_receivers = '';
        if(!empty($post)) {
            $post_type = $post->post_type;
            $receiver_end = $post->receiver_end;
            if($post_type == 1) { // section ids
                $section_ids = $post->receiver_end;
                if(!empty($section_ids)) {
                    $section_ids = explode(',', $section_ids);
                    $section_ids = array_unique($section_ids);
                    $section_ids = array_filter($section_ids);
                    if(count($section_ids) > 0) {
                        $is_receivers = DB::table('sections')
                            ->leftjoin('classes', 'classes.id', 'sections.class_id')
                            ->where('sections.status','ACTIVE')->whereIn('sections.id',$section_ids)
                            ->select('section_name as name', 'classes.class_name as name1')->get(); 
                    }
                }
            }   else if($post_type == 2) { // user ids
                $user_ids = $post->receiver_end;
                if(!empty($user_ids)) {
                    $user_ids = explode(',', $user_ids);
                    $user_ids = array_unique($user_ids);
                    $user_ids = array_filter($user_ids);
                    if(count($user_ids) > 0) {
                        $is_receivers = DB::table('users')->where('status','ACTIVE')->whereIn('id',$user_ids)
                            ->select('name', 'admission_no as name1')->get(); 
                    }
                }
            }   else if($post_type == 3) { // all user ids 

            }   else if($post_type == 4) { // group ids 
                $group_ids = $post->receiver_end;
                if(!empty($group_ids)) {
                    $user_ids = [];
                    $group_ids = explode(',', $group_ids);
                    $group_ids = array_unique($group_ids);
                    $group_ids = array_filter($group_ids);
                    if(count($group_ids) > 0) { 
                        $is_receivers = DB::table('communication_groups')->where('status','ACTIVE')->whereIn('id',$group_ids)
                            ->select('group_name as name', DB::RAW('"" as name1'))->get();  
                    } 
                }
            }  
        }
        if(!empty($is_receivers) && $is_receivers->isNotEmpty()) {
            $is_receivers = $is_receivers->toArray();
        }
        return  $is_receivers;
    }

    public function getIsAttachmentAttribute()
    {   $is_attachment = '';
        if(!empty($this->media_attachment)) {
            $is_attachment = config("constants.APP_IMAGE_URL").'uploads/media/'.$this->media_attachment;
        }   else {
            $is_attachment = '';
        }
        
        return $is_attachment;
    }

    public function getIsImageAttachmentAttribute()
    {   $is_image_attachment = [];
        if(!empty($this->image_attachment)) {
            $image_attachment = $this->image_attachment;

            if(!empty($image_attachment)) {
                $image_attachment = explode(',', $image_attachment);
                foreach($image_attachment as $img) {
                    $is_image_attachment[] = ['img' => config("constants.APP_IMAGE_URL").'uploads/media/'.$img];
                }
            } 
        }   else {
            $is_image_attachment = [];
        }
        
        return $is_image_attachment;
    }

    public function getIsVideoAttachmentAttribute()
    {   $is_video_attachment = '';
        if(!empty($this->video_attachment)) {
            $is_video_attachment = config("constants.APP_IMAGE_URL").'uploads/media/'.$this->video_attachment;
        }   else {
            $is_video_attachment = '';
        }
        
        return $is_video_attachment;
    }

    public function getIsFilesAttachmentAttribute()
    {   $is_files_attachment = [];
        if(!empty($this->files_attachment)) {
            $files_attachment = $this->files_attachment;

            if(!empty($files_attachment)) {
                $files_attachment = explode(',', $files_attachment);
                foreach($files_attachment as $img) {
                    $is_files_attachment[] = ['img' => config("constants.APP_IMAGE_URL").'uploads/media/'.$img];
                }
            } 
        }   else {
            $is_files_attachment = [];
        }
        
        return $is_files_attachment;
    }

    public function getPostedUserAttribute() { 

        $posted_user = User::where('id', $this->posted_by)
            ->select('users.id', 'users.name', 'users.profile_image', 'name_code')->first();  

        return $posted_user;
    }

    public function getIsCreatedAgoAttribute(){
        if(!empty($this->created_at)) {
            return  CommonController::gettime_ago(strtotime($this->created_at),1).' ago';
        }   else {
            return '';
        }
    } 

    public function getISNotifyDatetimeAttribute(){
        if(!empty($this->notify_datetime)) {
            return  date('d M, Y h:i A', strtotime($this->notify_datetime));
        }   else {
            return '';
        }
    } 

    public function getIsLikedAttribute() {

        $is_liked = 0; 
        return $is_liked;        
    }

    public function getLikeCountAttribute() {

        $like_count = 0; 
        return $like_count;        
    }

    public function getPostCategoryAttribute() { 

        $post_category = DB::table('categories')->where('id', $this->category_id)->value('name');  

        return $post_category;
    }

    public function getPostThemeAttribute() { 

        $post_theme = BackgroundTheme::where('id', $this->background_id)
            ->select('id', 'image')->first();  

        return $post_theme;
    }
    
   
}
