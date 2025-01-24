<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class StudentTests extends Model
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
    protected $table = 'student_tests';

    protected $appends = ['test_items', 'no_of_questions', 'is_created_at', 'times_attended'];

    public function getTestItemsAttribute() {
          TestItems::$student_id = $this->user_id;
          TestItems::$student_test_id = $this->id;
          return TestItems::leftjoin('question_bank_items', 'question_bank_items.id', 'test_items.question_bank_item_id')
               ->leftjoin('question_banks', 'question_banks.id', 'question_bank_items.question_bank_id')
               ->where('test_id', $this->test_id)
               ->select('question_type_id', 'question_type', 'test_id')
               ->orderby('question_type_id', 'asc')
               ->groupby('question_type_id')->groupby('question_type')->get();
    }

    public function getNoOfQuestionsAttribute() {
          $no_of_questions = DB::table('test_items')->where('test_id', $this->test_id)->count('id');
          return $no_of_questions;
    }

    public function getIsCreatedAtAttribute() {
      return date('d M, Y H:i A', strtotime($this->created_at));
    }

    public function getTimesAttendedAttribute() { 
      $times_attended = 0;
      $excount = DB::table('student_tests')->where('user_id', $this->user_id)
          ->where('test_id', $this->test_id)->select('id')->get();
      if($excount->isNotEmpty()) {
          $times_attended = count($excount);
      }   else {
          $times_attended = 0;
      }

      return $times_attended;
    }
 
}