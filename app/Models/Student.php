<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Student extends Model
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
    protected $table = 'students';
    
    protected $appends = ['is_std_image','is_class_name','is_section_name', 'is_academicyear', 'is_current_academic_year', 'is_student_name', 'is_blood_group'];

    public function getIsBloodGroupAttribute() {
        $is_blood_group = DB::table('blood_groups')->where('id',$this->bloodgroup)->value('name');

        return $is_blood_group;
    }

    public function getIsStdImageAttribute()
    {   
        return config("constants.APP_IMAGE_URL").'image/students/'.$this->std_image;
    }

    public function getIsClassNameAttribute()    {
        
        return DB::table('classes')->where('id', $this->class_id)->value('class_name');
        
    } 

    public function getIsSectionNameAttribute()    {
        
        return DB::table('sections')->where('id', $this->section_id)->value('section_name');
        
    } 

    public function getIsStudentNameAttribute()    {
        
        return DB::table('users')->where('id', $this->user_id)->value('name');
        
    }
    
    public function users(){

        return $this->belongsTo('App\User','user_id','id');
    }

    public function getIsAcademicYearAttribute() {
        //return date('Y').'-'.(date('Y')+1);
        $is_academicyear = DB::table('admin_settings')->where('school_id', $this->school_id)->value('display_academic_year');

        return $is_academicyear;
    }

    public function getIsCurrentAcademicYearAttribute() {
        $is_current_academic_year = DB::table('admin_settings')->where('school_id', $this->school_id)->value('acadamic_year');
        return $is_current_academic_year;
        //return date('Y').'-'.(date('Y')+1);
    }


}
