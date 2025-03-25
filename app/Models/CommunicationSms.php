<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\CommonController;
use DB;


class CommunicationSms extends Model
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
    protected $table = 'communication_sms';
    
    protected $appends = [ 'posted_user', 'is_created_ago', 'post_category', 'is_template', 'is_notify_datetime', 'is_receivers', 'is_post_receivers' ];

    public function getISNotifyDatetimeAttribute(){
        if(!empty($this->notify_datetime)) {
            return  date('d M, Y h:i A', strtotime($this->notify_datetime));
        }   else {
            return '';
        }
    } 

    public function getPostedUserAttribute() { 
        if($this->created_by > 0) {
            $posted = $this->created_by;
        } else {
            $posted = $this->posted_by;
        }
        if($posted > 0) { } else { $posted = $this->posted_by; }
        $posted_user = User::where('id', $posted)
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

    public function getPostCategoryAttribute() { 

        $post_category = DB::table('categories')->where('id', $this->category_id)->value('name');  

        return $post_category;
    }

    public function getIsTemplateAttribute() { 

        $is_template = DB::table('dlt_templates')->where('id', $this->template_id)->value('name');  

        return $is_template;
    }

    public static function getIsReceiversAttribute($post_id) {

        $post  = DB::table('communication_sms')->where('id', $post_id)->first();

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

    public function getIsPostReceiversAttribute() {

        $post_id = $this->id;
        $post  = DB::table('communication_sms')->where('id', $post_id)->first();

        $is_receivers = [];
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
            }   else if($post_type == 2) { // user ids
                $user_ids = $post->receiver_end;
                if(!empty($user_ids)) {
                    $user_ids = explode(',', $user_ids);
                    $user_ids = array_unique($user_ids);
                    $user_ids = array_filter($user_ids);
                    if(count($user_ids) > 0) {
                        $is_receivers = DB::table('users')->where('status','ACTIVE')->whereIn('id',$user_ids)
                            ->select('name', 'admission_no as name1')->orderby('name', 'asc')->get(); 
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
                            ->select('group_name as name', DB::RAW('"" as name1'))->orderby('name', 'asc')->get();  
                    } 
                }
            }  
        }
        if(!empty($is_receivers) && $is_receivers->isNotEmpty()) {
            $is_receivers = $is_receivers->toArray();
        } else {
            $is_receivers = [];
        }
        return  $is_receivers;
    }
   
}
