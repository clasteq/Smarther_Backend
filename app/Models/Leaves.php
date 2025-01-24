<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Leaves extends Model
{
    protected $table = 'leaves';
    
    protected $appends = ['is_leave_attachment','is_student_name','is_class_name','is_section_name'];

    public function getIsLeaveAttachmentAttribute()    {
        if(!empty($this->leave_attachment)) {
            return config("constants.APP_IMAGE_URL").'image/leaves/'.$this->leave_attachment;
        }   else {
            return '';
        } 
    } 

    public function getIsStudentNameAttribute()    {
        
        return DB::table('users')->where('id', $this->student_id)->value('name');
        
    } 

    public function getIsClassNameAttribute()    {
        
        return DB::table('classes')->where('id', $this->class_id)->value('class_name');
        
    } 

    public function getIsSectionNameAttribute()    {
        
        return DB::table('sections')->where('id', $this->section_id)->value('section_name');
        
    } 


}
