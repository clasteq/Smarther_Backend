<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class SubjectMapping extends Model
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
    protected $table = 'subject_mapping';
    protected $appends = ['is_subject_name','is_class_name','is_section_name', 'handling_classes'];

    public function getIsSubjectNameAttribute()    {
        
        return $val = DB::table('subjects')->where('id', $this->subject_id)->value('subject_name');        
             
    } 

    public function getIsClassNameAttribute()    {
        
        return $val = DB::table('classes')->where('id', $this->class_id)->value('class_name');        
             
    } 

    public function getIsSectionNameAttribute()    {
        
        return $val = DB::table('sections')->where('id', $this->section_id)->value('section_name');        
             
    } 

    public function getHandlingClassesAttribute()    {
        $handling_classes = '';
        $hc = DB::table('subject_mapping')
            ->leftjoin('classes', 'classes.id', 'subject_mapping.class_id')
            ->leftjoin('sections', 'sections.id', 'subject_mapping.section_id')
            ->leftjoin('subjects', 'subjects.id', 'subject_mapping.subject_id')
            ->where('teacher_id', $this->teacher_id) 
            ->where('subject_mapping.status', 'ACTIVE') 
            ->select('subject_mapping.*','subjects.subject_name','classes.class_name', 'sections.section_name')
            ->get();

        if($hc->isNotEmpty()) {
            foreach($hc as $handles) {
                $handling_classes .= $handles->class_name." ".$handles->section_name." ".$handles->subject_name." ; ";
            }
        }

        return $handling_classes;        
             
    } 

    public static function getHandlingClasses($teacher_id)    {
        $handling_classes = '';
        $hc = DB::table('subject_mapping')
            ->leftjoin('classes', 'classes.id', 'subject_mapping.class_id')
            ->leftjoin('sections', 'sections.id', 'subject_mapping.section_id')
            ->leftjoin('subjects', 'subjects.id', 'subject_mapping.subject_id')
            ->where('teacher_id', $teacher_id) 
            ->where('subject_mapping.status', 'ACTIVE') 
            ->select('subject_mapping.*','subjects.subject_name','classes.class_name', 'sections.section_name')
            ->get();

        if($hc->isNotEmpty()) {
            foreach($hc as $handles) {
                $handling_classes .= $handles->class_name." ".$handles->section_name." ".$handles->subject_name." ; ";
            }
        }

        return $handling_classes;        
             
    } 



}
