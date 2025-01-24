<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Homeworks extends Model
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
    protected $table = 'homeworks';
    public $test_name;
    protected $appends = ['is_hw_attachment','is_dt_attachment', 'is_class_name','is_section_name','is_subject_name','is_hw_date','is_hw_submission_date','is_test_name','is_test_id','is_new_test_name','is_test_list'];

    public function getIsHwAttachmentAttribute()    {
        if(!empty($this->hw_attachment)) {
            return config("constants.APP_IMAGE_URL").'image/homework/'.$this->hw_attachment;
        }   else {
            return '';
        }
    }

    public function getIsDtAttachmentAttribute()    {
        if(!empty($this->dt_attachment)) {
            return config("constants.APP_IMAGE_URL").'image/dailytask/'.$this->dt_attachment;
        }   else {
            return '';
        }
    }

    public function getIsSubjectAttribute() {
        $is_subject = '';
        if($this->subject_id > 0) {
            $is_subject  = DB::table('subjects')->where('id', $this->subject_id)->value('subject_name');
        }
        return $is_subject;
    }

    public function getIsHwDateAttribute() {

        return date('d M y g:i a', strtotime($this->hw_date));
    }

    public function getIsHwSubmissionDateAttribute($value) {
        return date('d M y g:i a', strtotime($this->hw_submission_date));
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

  public function getIsTestNameAttribute()    {
    $val = [];
    $mapped_test = $this->test_id;
    $idsArr = explode(',',$mapped_test);
     foreach( $idsArr as $rowval){

        $val[] = DB::table('tests')->where('id', $rowval)->value('test_name');

    }
    $this->test_name  = $val;
    return $val;

    }

    public function getIsTesthandlingAttribute() {
        // return implode(', ', $this->test_name);
        return $this->test_name;
    }

    public function getIsTestIdAttribute()    {
        if(!empty($this->test_id)) {
        $mapped_test = $this->test_id;
        $test_id = explode(',', $mapped_test);
        return $test_id;
        }
    }

//     public function getIsTestListAttribute() {
//         $mapped_test = $this->test_id;
//         if(!empty($mapped_test)){
//             $test_id = explode(',', $mapped_test);
//         }else{
//             $test_id = [];
//         }

//         $testqry =  DB::table('tests')->leftjoin('terms', 'terms.id', 'tests.term_id')
//         ->leftjoin('classes', 'classes.id', 'tests.class_id')
//         ->leftjoin('subjects', 'subjects.id', 'tests.subject_id')
//         ->where('tests.status', 'ACTIVE')
//         ->where('tests.id',$test_id)
//         ->select('tests.*', 'classes.class_name', 'subjects.subject_name',
//             'terms.term_name');
//     $is_test_list = $testqry->orderby('tests.id', 'desc')->get();

//     return $is_test_list;

//   }

public function getIsNewTestNameAttribute(){
    $val = [];
    $mapped_test = $this->test_id;
    $idsArr = explode(',',$mapped_test);
     foreach( $idsArr as $rowval){

        $val[$rowval] = DB::table('tests')->where('id', $rowval)->value('test_name');

    }
    $this->test_name  = $val;
    return $val;

}

  public function getIsTestListAttribute() {

    $mapped_test = $this->test_id;
    if(!empty($mapped_test)){
        $idsArr = explode(',',$mapped_test);
    }   else {
        $idsArr = [];
    }

    $testqry = Tests::leftjoin('terms', 'terms.id', 'tests.term_id')
        ->leftjoin('classes', 'classes.id', 'tests.class_id')
        ->leftjoin('subjects', 'subjects.id', 'tests.subject_id')
        ->where('tests.status', 'ACTIVE')
        ->whereIn('tests.id', $idsArr)
        ->select('tests.*', 'classes.class_name', 'subjects.subject_name',
            'terms.term_name');
    $is_test_list = $testqry->orderby('tests.id', 'desc')->get();

    return $is_test_list;
}


}
