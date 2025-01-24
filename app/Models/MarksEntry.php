<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class MarksEntry extends Model
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
    protected $table = 'marks_entry';

    protected $appends = ['mark_percentage', 'starvalue', 'exam_schedule'];

    public static $exam_id;
    public static $class_id;
    public static $section_id;

    public function getMarkPercentageAttribute()
    {
        $mark_percentage = 0;
        $total_marks = $this->total_marks;  
        $marks = $this->marks;  
        $mcnt = DB::table('marks_entry_items')->where('mark_entry_id', $this->id)->count();
        if($total_marks > 0) {
            $mark_percentage = (100 * $marks) / $total_marks;
        }
        $mark_percentage = round($mark_percentage, 2);
        return $mark_percentage;
    }

    public function getStarvalueAttribute() {
        $starvalue = 0;
        $school_id = DB::table('users')->where('id', $this->user_id)->value('school_college_id');
        $grades = $gr = DB::table('grades')->where('school_id', $school_id)->where('mark', '<=', $this->mark_percentage)->orderby('mark', 'desc')->first(); 
        if(!empty($gr)) {
            $starvalue = $gr->star_value;
        }
        return $starvalue;
    }
	
	public static $subject_id; 
    public function marksentryitems()    {
        if(self::$subject_id > 0) {
            return $this->hasMany('App\Models\MarksEntryItems','mark_entry_id','id')->leftjoin('subjects', 'subjects.id', 'marks_entry_items.subject_id')->select('marks_entry_items.*','subjects.subject_name')
                ->where('subject_id', self::$subject_id); 
        }   else {
            if(self::$section_id > 0) { } else {
                self::$section_id = $this->section_id;
            }  
            return $this->hasMany('App\Models\MarksEntryItems','mark_entry_id','id') 
                ->leftjoin('exam_sessions', 'exam_sessions.subject_id', 'marks_entry_items.subject_id')
                ->leftjoin('subjects', 'subjects.id', 'marks_entry_items.subject_id')
                ->where('exam_sessions.status', 'ACTIVE')->where('exam_sessions.exam_id', self::$exam_id)
                ->where('exam_sessions.class_id', self::$class_id)->whereIn('exam_sessions.section_id', [0, self::$section_id]) 
                ->select('marks_entry_items.*','subjects.subject_name','exam_sessions.status')
                ->groupby('marks_entry_items.id'); 
        }
    } 

    public function getExamScheduleAttribute() {
        $exam_sessions_structure = []; 
        if(self::$section_id > 0) { } else {
            self::$section_id = $this->section_id;
        }//echo self::$exam_id."==".self::$section_id."==".self::$class_id;
        $exam_sessions = DB::table('exam_sessions')->leftjoin('subjects', 'subjects.id', 'exam_sessions.subject_id')
            ->where('exam_id', self::$exam_id)->where('exam_sessions.subject_id', '>', 0)
            ->where('exam_sessions.class_id', self::$class_id)->whereIn('exam_sessions.section_id', [0, self::$section_id]) 
            ->where('exam_sessions.status', 'ACTIVE')->select('exam_sessions.*','subjects.subject_name')->get();
        foreach($exam_sessions as $k => $v) {
            $exam_sessions_structure[] = ['date' => $v->exam_date, 'subject_name' => $v->subject_name, 'session' => $v->session, 'syllabus' =>  $v->syllabus]; 
        }
        return $exam_sessions_structure;
    }
    
}
