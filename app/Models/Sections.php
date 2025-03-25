<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Sections extends Model
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
    protected $table = 'sections'; 

    protected $appends = ['is_subject_name','is_mapped_subjects','is_subject_id','boys','girls','total', 'academic_year','is_class_name', 'is_class_teacher'];

    protected $boys;
    protected $girls;

    public static $acadamic_year;

    public function getIsClassTeacherAttribute()    {
        
        $is_class_teacher = DB::table('class_teachers')->leftjoin('users', 'class_teachers.teacher_id', 'users.id')
                        ->leftjoin('teachers', 'class_teachers.teacher_id', 'teachers.user_id')
                        ->where('class_teachers.class_id', $this->class_id)->where('class_teachers.section_id', $this->id)
                        ->where('users.status', 'ACTIVE')->where('users.delete_status', 0)
                        ->select('users.id', 'users.name', 'users.mobile', 'teachers.emp_no')->first();
        return $is_class_teacher;      
    } 

    public function getIsSubjectNameAttribute()    {
        
        $mapped_subjects = $this->mapped_subjects;
        $idsArr = explode(',', $mapped_subjects);
        foreach( $idsArr as $rowval){

            $val[] = DB::table('subjects')->where('id', $rowval)->value('subject_name');        

        }

        return $val;      
    } 

    public function getIsMappedSubjectsAttribute()    {
        
        $mapped_subjects = $this->mapped_subjects;
        $idsArr = explode(',', $mapped_subjects);
        foreach( $idsArr as $rowval){
         $val[] = DB::table('subjects')->where('id', $rowval)->select('id','subject_name')->get();        
        }

        return $val;      
    } 

    public function getIsSubjectIdAttribute()    {
        
        $mapped_subjects = $this->mapped_subjects;
        $idsArr = explode(',', $mapped_subjects);
        foreach( $idsArr as $rowval){

            $val[] = DB::table('subjects')->where('id', $rowval)->value('id');        

        }

        return $val;      
    }

    public function getBoysAttribute() {
        if(self::$acadamic_year) {
            $acadamic_year = self::$acadamic_year;
        } else {
            $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
            $acadamic_year = $settings->acadamic_year;
        }
        $boys = 0;
        $boys_cnt = DB::table('student_class_mappings')
            ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
            ->where('student_class_mappings.academic_year', $acadamic_year)
            ->where('student_class_mappings.class_id', $this->class_id)
            ->where('student_class_mappings.section_id', $this->id)
            ->where('users.status', 'ACTIVE')->where('users.gender', 'MALE')
            ->select('users.id')->get();
        if($boys_cnt->isNotEmpty()) {
            $boys = count($boys_cnt);
        }
        $this->boys = $boys;
        return $boys;
    }

    public function getGirlsAttribute() {
        if(self::$acadamic_year) {
            $acadamic_year = self::$acadamic_year;
        } else {
            $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
            $acadamic_year = $settings->acadamic_year;
        }
        $girls = 0;
        $girls_cnt = DB::table('student_class_mappings')
            ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
            ->where('student_class_mappings.academic_year', $acadamic_year)
            ->where('student_class_mappings.class_id', $this->class_id)
            ->where('student_class_mappings.section_id', $this->id)
            ->where('users.status', 'ACTIVE')->where('users.gender', 'FEMALE')
            ->select('users.id')->get();
        if($girls_cnt->isNotEmpty()) {
            $girls = count($girls_cnt);
        }
        $this->girls = $girls;
        return $girls;
    }

    public function getTotalAttribute() {
        $total = 0; 
        $total = $this->boys + $this->girls;
        return $total;
    }

    public function getAcademicYearAttribute() {
        $academic_year = '';
        if(self::$acadamic_year) {
            $academic_year = self::$acadamic_year;
        } else {
            $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
            $academic_year = $settings->acadamic_year;
        }
        return $academic_year;
    }

    public function getIsClassNameAttribute()    {
        
        return DB::table('classes')->where('id', $this->class_id)->value('class_name');
        
    }

}
