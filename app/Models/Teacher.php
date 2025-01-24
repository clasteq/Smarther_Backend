<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Teacher extends Model
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
    protected $table = 'teachers';
    public $classes_handling;
    public $subjects_handling;

    protected $appends = ['is_class_name','is_subject_name','is_classes_handling', 'is_subjects_handling', 'is_class_id','is_subject_id','is_class_tutor','is_section_name'];

    

    public function getIsClassNameAttribute()    {
        $val = [];
        $mapped_subjects = $this->class_id;
        $idsArr = explode(',', $mapped_subjects);
        foreach( $idsArr as $rowval){

            $val[] = DB::table('classes')->where('id', $rowval)->value('class_name');

        }
        $this->classes_handling  = $val;
        return $val;
    }

    public function getIsSubjectNameAttribute()    {
        $val = [];
        $mapped_subjects = $this->subject_id;
        $idsArr = explode(',', $mapped_subjects);
        foreach( $idsArr as $rowval){

            $val[] = DB::table('subjects')->where('id', $rowval)->value('subject_name');

        }
        $this->subjects_handling  = $val;
        return $val;
    }

    public function getIsClassesHandlingAttribute() {
        return implode(', ', $this->classes_handling);
    }

    public function getIsSubjectsHandlingAttribute() {
        return implode(', ', $this->subjects_handling);
    }

    public function getIsClassIdAttribute()    {

        $mapped_subjects = $this->class_id;
        $classes = explode(',', $mapped_subjects);
        return $classes;
    }

    public function getIsSubjectIdAttribute()    {

        $mapped_subjects = $this->subject_id;
        $subjects = explode(',', $mapped_subjects);
        return $subjects;
    }

    public function getIsClassTutorAttribute()    {

        return DB::table('classes')->where('id', $this->class_tutor)->value('class_name');

    }
    public function getIsSectionNameAttribute()    {

        return DB::table('sections')->where('id', $this->section_id)->value('section_name');
    }


    public function users(){

        return $this->belongsTo('App\Models\User','user_id','id');
    }
}
