<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Exams extends Model
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
    protected $table = 'exams';

    protected $appends = ['exam_sessions', 'exam_session_structure', 'examination_session_structure', 'class_names', 'is_finished' ];

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
        $exam_sessions = DB::table('exam_sessions')->where('exam_id', $this->id)->where('status', 'ACTIVE')->get();
        return $exam_sessions;
    }

    public function getExamSessionStructureAttribute() {
        $exam_sessions_structure = [];
        $exam_sessions = DB::table('exam_sessions')->where('exam_id', $this->id)->where('status', 'ACTIVE')->get();
        foreach($exam_sessions as $k => $v) {
            $exam_sessions_structure[$v->class_id][$v->section_id][$v->exam_date]['subject_id'] = $v->subject_id;
            $exam_sessions_structure[$v->class_id][$v->section_id][$v->exam_date]['session'] = $v->session;
            $exam_sessions_structure[$v->class_id][$v->section_id][$v->exam_date]['syllabus'] = $v->syllabus;
        }
        return $exam_sessions_structure;
    }

    public function getExaminationSessionStructureAttribute() {
        $examination_sessions_structure = [];  
        $exam_sessions = DB::table('exam_sessions')->where('exam_id', $this->id)->where('status', 'ACTIVE')->get();
        foreach($exam_sessions as $k => $v) {
            //[$v->class_id][$v->section_id]
            $subject_name = DB::table('subjects')->where('id', $v->subject_id)->value('subject_name');
            $examination_sessions_structure[$v->subject_id]['subject_id'] = $v->subject_id;
            $examination_sessions_structure[$v->subject_id]['subject_name'] = $subject_name;
            $examination_sessions_structure[$v->subject_id]['exam_date'] = $v->exam_date;
            $examination_sessions_structure[$v->subject_id]['session'] = $v->session;
            $examination_sessions_structure[$v->subject_id]['syllabus'] = $v->syllabus;

            $examination_sessions_structure[$v->subject_id]['theory_mark'] = $v->theory_mark;
            $examination_sessions_structure[$v->subject_id]['theory_pass_mark'] = $v->theory_pass_mark;
            $examination_sessions_structure[$v->subject_id]['is_practical'] = $v->is_practical;
            $examination_sessions_structure[$v->subject_id]['practical_type'] = $v->practical_type;
            $examination_sessions_structure[$v->subject_id]['practical_date'] = $v->practical_date;
            $examination_sessions_structure[$v->subject_id]['practical_mark'] = $v->practical_mark;
            $examination_sessions_structure[$v->subject_id]['practical_pass_mark'] = $v->practical_pass_mark;
            $examination_sessions_structure[$v->subject_id]['psession'] = $v->psession;
        }
        return $examination_sessions_structure;
    }

    public function getClassNamesAttribute() {
        $class_names  = '';
        $class_ids = DB::table('exam_sessions')
            ->leftjoin('classes', 'classes.id', 'exam_sessions.class_id')
            ->where('exam_id', $this->id)->where('exam_sessions.status', 'ACTIVE')
            ->select('class_name')->groupby('class_id')->orderby('classes.position', 'asc')->get();
        if($class_ids->isNotEmpty()) {
            foreach( $class_ids as $ids){
                $val[] = $ids->class_name;        
            }
            $class_names  = implode(',', $val);
        } 

        return $class_names;      
    }
     
}
