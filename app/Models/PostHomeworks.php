<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\CommonController;

class PostHomeworks extends Model
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
    protected $table = 'homeworks';

    protected $appends = ['homeworks_list', 'posted_user', 'is_created_ago', 'is_post_receivers', 'is_read_count', 'acknowledged_count', 'hw_submitted_count'];

    public function getIsReadCountAttribute()  {  
        $is_read_count = 0;
        $read = DB::table('notifications')->where('main_ref_no', $this->main_ref_no)->where('type_no', 7)
            ->where('read_status', 1)->select('id')->get();
        if($read->isNotEmpty()) {
            $is_read_count = count($read);
        }
        return $is_read_count;
    }

    public function getAcknowledgedCountAttribute()  { 

        $acknowledged_count = 0;
        $acknowledged = DB::table('notifications')->where('main_ref_no', $this->main_ref_no)->where('type_no', 7)
            ->select('id')->get();
        if($acknowledged->isNotEmpty()) {
            $acknowledged_count = count($acknowledged);
        }
        return $acknowledged_count;
    }

    public function getHwSubmittedCountAttribute()  { 

        $hw_submitted_count = 0;
        $hw_submitted = DB::table('homework_submissions')->where('main_ref_no', $this->main_ref_no)->where('status', 'ACTIVE')
            ->select('id')->get();
        if($hw_submitted->isNotEmpty()) {
            $hw_submitted_count = count($hw_submitted);
        }
        return $hw_submitted_count;
    }
    

    public function getHomeworksListAttribute()  {
        //$homeworks_list = Homeworks::where('ref_no', $this->ref_no)->where('status', 'ACTIVE')->orderby('id','asc')->get();

        $homeworks_list = Homeworks::where('main_ref_no', $this->main_ref_no)->where('ref_no', $this->main_ref_no)->where('status', 'ACTIVE')->orderby('id','asc')->get();
        return $homeworks_list;
    }
    
    public function getPostedUserAttribute() { 

        $posted_user = DB::table('users')->where('id', $this->created_by)
            ->select('users.id', 'users.name', 'users.profile_image', 'name_code')->first(); 
        if(!empty($posted_user)) {
            $posted_user->is_profile_image = User::getUserProfileImageAttribute($this->created_by);
            $posted_user->is_shortname = CommonController::getShortcode($posted_user->name);
        } 

        return $posted_user;
    }

    public function getIsCreatedAgoAttribute(){
        if(!empty($this->created_at)) {
            return  CommonController::gettime_ago(strtotime($this->created_at),1).' ago';
        }   else {
            return '';
        }
    } 

    public function getIsPostReceiversAttribute() {

        $is_all = $this->is_all; 

        $is_post_receivers = [];  $is_receivers = '';
        if($is_all == 1) {
            $is_post_receivers = [['name' => "All", "name1" => ""]];

            $class_id = $this->class_id;
            if($class_id > 0) { 
                $is_receivers = DB::table('sections')
                    ->leftjoin('classes', 'classes.id', 'sections.class_id')
                    ->where('sections.status','ACTIVE')->where('sections.class_id',$class_id)
                    ->select('section_name as name1', 'classes.class_name as name')->orderby('name', 'asc')->get(); 
                 
            }
              
        } else {
            $section_id = $this->section_id;
            if($section_id > 0) { 
                $is_receivers = DB::table('sections')
                    ->leftjoin('classes', 'classes.id', 'sections.class_id')
                    ->where('sections.status','ACTIVE')->where('sections.id',$section_id)
                    ->select('section_name as name1', 'classes.class_name as name')->orderby('name', 'asc')->get(); 
                 
            }
        }
        if(!empty($is_receivers) && $is_receivers->isNotEmpty()) {
            foreach($is_receivers as $rec)  {
                $is_post_receivers[] = ['name' => $rec->name, "name1" => $rec->name1];
            }
            //$is_post_receivers = array_merge($is_post_receivers, $is_receivers);
        } else {
            $is_post_receivers = [['name' => "All", "name1" => ""]];
        }
        return  $is_post_receivers;
    }
}
