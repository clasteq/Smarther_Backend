<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Examinations extends Model
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
    protected $table = 'examinations';

    protected $appends = ['exam_sessions', 'exam_session_structure', 'class_names', 'is_finished', 'section_names' ];

    public function getIsFinishedAttribute() { 
        $is_finished = 0;
        $exam_enddate = $this->exam_enddate;
        $current = date('Y-m-d');
        if((strtotime($exam_enddate) > 0) && (strtotime($exam_enddate) < strtotime($current))) {
            $is_finished = 1;
        }
        return $is_finished;
    }

    public function getExamSessionsAttribute() { 
        $exam_sessions = DB::table('exam_sessions')->where('exam_id', $this->examination_id)->where('status', 'ACTIVE')->get();
        return $exam_sessions;
    }

    public function getExamSessionStructureAttribute() {
        $exam_sessions_structure = [];
        $exam_sessions = DB::table('exam_sessions')->where('exam_id', $this->examination_id)->where('status', 'ACTIVE')->get();
        foreach($exam_sessions as $k => $v) {
            $exam_sessions_structure[$v->class_id][$v->section_id][$v->exam_date]['subject_id'] = $v->subject_id;
            $exam_sessions_structure[$v->class_id][$v->section_id][$v->exam_date]['session'] = $v->session;
            $exam_sessions_structure[$v->class_id][$v->section_id][$v->exam_date]['syllabus'] = $v->syllabus;
        }
        return $exam_sessions_structure;
    }

    public function getClassNamesAttribute() {
        $class_names  = '';
        $class_ids = DB::table('classes') 
            ->where('id', $this->class_ids)->select('class_name')->groupby('id')->orderby('classes.position', 'asc')->get();
        if($class_ids->isNotEmpty()) {
            foreach( $class_ids as $ids){
                $val[] = $ids->class_name;        
            }
            $class_names  = implode(',', $val);
        }   else {
            $class_names  = '';
        }

        return $class_names;      
    }

    public function getSectionNamesAttribute() {
        $section_names  = '';
        $class_ids = DB::table('sections')->where('id', $this->section_ids)->select('section_name')
            ->groupby('id')->orderby('sections.position', 'asc')->get();
        if($class_ids->isNotEmpty()) {
            foreach( $class_ids as $ids){
                $val[] = $ids->section_name;        
            }
            $section_names  = implode(',', $val);
        }  else {
            if($this->class_ids >0) {
                $section_names  = 'All';
            }  else {
                $section_names  = '';
            }
        }

        return $section_names;      
    }

}