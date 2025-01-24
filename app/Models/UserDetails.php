<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserDetails extends Model
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
    protected $table = 'user_details'; 

    protected $appends = ['is_classname','is_sectionname', 'is_academicyear', 'is_current_academic_year', 'is_blood_group'];

    public function getIsBloodGroupAttribute() {
        $is_blood_group = DB::table('blood_groups')->where('id',$this->bloodgroup)->value('name');

        return $is_blood_group;
    }

    public function getIsClassnameAttribute() {
        $class_name = DB::table('classes')->where('id',$this->class_id)->value('class_name');

        return $class_name;
    }

    public function getIsSectionnameAttribute() {
        $section_name = DB::table('sections')->where('id',$this->section_id)->value('section_name');

        return $section_name;
    }

    public function getIsAcademicYearAttribute() {
        $is_academicyear = DB::table('admin_settings')->where('school_id', $this->school_id)->value('display_academic_year');
        return $is_academicyear;
        //return date('Y').'-'.(date('Y')+1);
    }

    public function getIsCurrentAcademicYearAttribute() {
        $is_current_academic_year = DB::table('admin_settings')->where('school_id', $this->school_id)->value('acadamic_year');
        return $is_current_academic_year;
        //return date('Y').'-'.(date('Y')+1);
    }
}
