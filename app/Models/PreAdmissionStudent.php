<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class PreAdmissionStudent extends Model
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
    protected $table = 'pre_admission_students';
    
    protected $appends = ['is_std_image','is_class_name','is_section_name', 'is_academicyear','is_student_name', 
        'is_profile_image'];

    public function getIsProfileImageAttribute()
    {
       $pictures =$this->profile_image;  
   
        $profile_picture = '';
        if (!empty($pictures)) {           
                $profile_picture = config("constants.APP_IMAGE_URL"). 'uploads/userdocs/' . $this->profile_image;
            
        }else{
            $profile_picture = config("constants.APP_IMAGE_URL"). 'image/default.png';
        } 
        return $profile_picture;
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
        return date('Y').'-'.(date('Y')+1);
    }


}
