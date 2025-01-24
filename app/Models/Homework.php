<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Homework extends Model
{
    protected $table = 'homework';
    
    protected $appends = ['is_homework_file', 'is_dailytask_file','is_class_name','is_section_name','is_subject_name'];

    public function getIsHomeworkFileAttribute()    
    {
        if(!empty($this->homework_file)) {
            return config("constants.APP_IMAGE_URL").'uploads/homework/'.$this->homework_file;
        }   else {
            return '';
        } 
    }

    public function getIsDailytaskFileAttribute()    
    {
        if(!empty($this->dailytask_file)) {
            return config("constants.APP_IMAGE_URL").'uploads/dailytask/'.$this->dailytask_file;
        }   else {
            return '';
        } 
    }

    public function getIsClassNameAttribute()    {
        
        return DB::table('classes')->where('id', $this->class_id)->value('class_name');
        
    } 

    public function getIsSectionNameAttribute()    {
        
        return DB::table('sections')->where('id', $this->section_id)->value('section_name');
        
    } 

    public function getIsSubjectNameAttribute()    {
        
        return DB::table('subjects')->where('id', $this->subject_id)->value('subject_name');
        
    } 
}
