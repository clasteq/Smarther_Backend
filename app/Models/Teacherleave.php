<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Teacherleave extends Model
{
    protected $table = 'teacher_leave'; 

    protected $appends = ['is_leave_apply_file','is_descriptionfile', 'is_teacher_name','is_from_date','is_to_date'];

    public function getIsLeaveApplyFileAttribute()    {
        if(!empty($this->leave_apply_file)) {
            return config("constants.APP_IMAGE_URL").'image/teacherleaves/'.$this->leave_apply_file;
        }   else {
            return '';
        } 
    } 

    public function getIsDescriptionfileAttribute()    {
        if(!empty($this->descriptionfile)) {
            return config("constants.APP_IMAGE_URL").'image/teacherleaves/audio/'.$this->descriptionfile;
        }   else {
            return '';
        } 
    } 
    public function getIsTeacherNameAttribute()    {
        
        return DB::table('users')->where('id', $this->user_id)->value('name');
        
    } 

    public function getisFromDateAttribute() {
        if(!empty($this->from_date))
            return date('Y-m-d', strtotime($this->from_date));
        else return '';
    }
    public function getisToDateAttribute() {
        if(!empty($this->to_date))
            return date('Y-m-d', strtotime($this->to_date));
        else return '';
    }
}
