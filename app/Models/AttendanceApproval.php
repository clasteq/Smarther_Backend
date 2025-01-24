<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class AttendanceApproval extends Model
{
    protected $table = 'attendance_approval';

    protected $appends = ['is_fn_status','is_an_status','is_student_name','is_class_name','is_section_name'];
    
   
    
    public function getIsFnStatusAttribute(){
        if($this->fn_status == 1){
            return "Present";
        }elseif($this->fn_status == 2){
            return "Absent";
        }else{
            return "";
        }
    }

    public function getIsAnStatusAttribute(){
        if($this->an_status == 1){
            return "Present";
        }elseif($this->an_status == 2){
            return "Absent";
        }else{
            return "";
        }
    }


    public function getIsStudentNameAttribute()    {
        
        return DB::table('users')->where('id', $this->user_id)->value('name');
        
    } 

    public function getIsClassNameAttribute()    {
        
        return DB::table('classes')->where('id', $this->class_id)->value('class_name');
        
    } 

    public function getIsSectionNameAttribute()    {
        
        return DB::table('sections')->where('id', $this->section_id)->value('section_name');
        
    } 


}
