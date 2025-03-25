<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\CommonController;
use DB;

class Survey extends Model
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
    protected $table = 'survey';
    
    protected $appends = [ 'posted_user', 'is_created_ago', 'is_post_receivers', 'is_staff_receivers', 'is_notify_datetime', 
        'is_read_count', 'is_responded_option1', 'is_responded_option2', 'is_responded_option3', 'is_responded_option4' ];

    public function getIsRespondedOption1Attribute() {
        $is_responded_option1 = DB::table('notifications')->where('post_id', $this->id)->where('type_no', 7)
            ->where('notify_response', 1)->select('id')->count();
        $is_staff_responded_option1 = DB::table('staff_notifications')->where('post_id', $this->id)->where('type_no', 7)
            ->where('notify_response', 1)->select('id')->count();
        return $is_responded_option1 + $is_staff_responded_option1;
    }
    public function getIsRespondedOption2Attribute() {
        $is_responded_option2 = DB::table('notifications')->where('post_id', $this->id)->where('type_no', 7)
            ->where('notify_response', 2)->select('id')->count();
        $is_staff_responded_option2 = DB::table('staff_notifications')->where('post_id', $this->id)->where('type_no', 7)
            ->where('notify_response', 2)->select('id')->count();
        return $is_responded_option2 + $is_staff_responded_option2;
    }
    public function getIsRespondedOption3Attribute() {
        $is_responded_option3 = DB::table('notifications')->where('post_id', $this->id)->where('type_no', 7)
            ->where('notify_response', 3)->select('id')->count();
        $is_staff_responded_option3 = DB::table('staff_notifications')->where('post_id', $this->id)->where('type_no', 7)
            ->where('notify_response', 3)->select('id')->count();
        return $is_responded_option3 + $is_staff_responded_option3;
    }
    public function getIsRespondedOption4Attribute() {
        $is_responded_option4 = DB::table('notifications')->where('post_id', $this->id)->where('type_no', 7)
            ->where('notify_response', 4)->select('id')->count();
        $is_staff_responded_option4 = DB::table('staff_notifications')->where('post_id', $this->id)->where('type_no', 7)
            ->where('notify_response', 4)->select('id')->count();
        return $is_responded_option4 + $is_staff_responded_option4;
    }


    public function getIsReadCountAttribute() {
        $is_read_count = DB::table('notifications')->where('post_id', $this->id)->where('type_no', 7)
            ->where('read_status', 1)->select('id')->count();
        $is_staff_read_count = DB::table('staff_notifications')->where('post_id', $this->id)->where('type_no', 7)
            ->where('read_status', 1)->select('id')->count();
        return $is_read_count + $is_staff_read_count;
    }

    public function getPostedUserAttribute() { 

        $posted_user = User::where('id', $this->created_by)
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

    public function getIsPostReceiversAttribute() {

        $post_id = $this->id;
        $post  = DB::table('survey')->where('id', $post_id)->first();

        $is_receivers = [];
        if(!empty($post)) {
            $post_type = $post->scholar_post_type;
            $receiver_end = $post->scholar_receiver_end;
            if($post_type == 1) { // section ids
                $section_ids = $post->scholar_receiver_end;
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
                $user_ids = $post->scholar_receiver_end;
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
                $group_ids = $post->scholar_receiver_end;
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

    public function getIsStaffReceiversAttribute() {

        $post_id = $this->id;
        $post  = DB::table('survey')->where('id', $post_id)->first();

        $is_staff_receivers = [];
        if(!empty($post)) {
            $post_type = $post->staff_post_type;
            $receiver_end = $post->staff_receiver_end;
            if($post_type == 1) { // section ids
                $section_ids = $post->staff_receiver_end;
                if(!empty($section_ids)) {
                    $section_ids = explode(',', $section_ids);
                    $section_ids = array_unique($section_ids);
                    $section_ids = array_filter($section_ids);
                    if(count($section_ids) > 0) {
                        $is_staff_receivers = DB::table('sections')
                            ->leftjoin('classes', 'classes.id', 'sections.class_id')
                            ->where('sections.status','ACTIVE')->whereIn('sections.id',$section_ids)
                            ->select('section_name as name1', 'classes.class_name as name')->orderby('name', 'asc')->get(); 
                    }
                }
            }   else if($post_type == 2) { // role ids 
                $role_ids = $post->staff_receiver_end;
                if(!empty($role_ids)) {
                     
                    $role_ids = explode(',', $role_ids);
                    $role_ids = array_unique($role_ids);
                    $role_ids = array_filter($role_ids);
                    if(count($role_ids) > 0) { 
                        $is_staff_receivers = DB::table('userroles')//->where('status','ACTIVE')
                            ->whereIn('id',$role_ids)
                            ->select('user_role as name', DB::RAW('"" as name1'))->orderby('name', 'asc')->get();  
                    } 
                }
            }   else if($post_type == 6) { // user ids
                $user_ids = $post->staff_receiver_end;
                if(!empty($user_ids)) {
                    $user_ids = explode(',', $user_ids);
                    $user_ids = array_unique($user_ids);
                    $user_ids = array_filter($user_ids);
                    if(count($user_ids) > 0) {
                        $is_staff_receivers = DB::table('users')->leftjoin('userroles', 'userroles.ref_code', 'users.user_type')
                            ->where('users.school_college_id',$post->school_id)->where('userroles.school_id',$post->school_id)
                            ->whereIn('users.id',$user_ids)
                            ->select('name', 'userroles.user_role as name1')->orderby('name', 'asc')->get(); 
                    }
                }
            }   else if($post_type == 3) { // all user ids 

            }   else if($post_type == 4) { // group ids 
                $group_ids = $post->staff_receiver_end;
                if(!empty($group_ids)) {
                     
                    $group_ids = explode(',', $group_ids);
                    $group_ids = array_unique($group_ids);
                    $group_ids = array_filter($group_ids);
                    if(count($group_ids) > 0) { 
                        $is_staff_receivers = DB::table('communication_groups')->where('status','ACTIVE')->whereIn('id',$group_ids)
                            ->select('group_name as name', DB::RAW('"" as name1'))->orderby('name', 'asc')->get();  
                    } 
                }
            }   else if($post_type == 5) { // department ids 
                $department_ids = $post->staff_receiver_end;
                if(!empty($department_ids)) {
                     
                    $department_ids = explode(',', $department_ids);
                    $department_ids = array_unique($department_ids);
                    $department_ids = array_filter($department_ids);
                    if(count($department_ids) > 0) { 
                        $is_staff_receivers = DB::table('departments')->where('status','ACTIVE')->whereIn('id',$department_ids)
                            ->select('department_name as name', DB::RAW('"" as name1'))->orderby('name', 'asc')->get();  
                    } 
                }
            }  
        }
        if(!empty($is_staff_receivers) && $is_staff_receivers->isNotEmpty()) {
            $is_staff_receivers = $is_staff_receivers->toArray();
        }else {
            $is_staff_receivers = [];
        }
        return  $is_staff_receivers;
    }
}
