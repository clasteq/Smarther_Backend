<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\CommonController;

class TestItemsPapers extends Model
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
    protected $table = 'test_items_papers';

    protected $appends = ['tt_items'];

    public static $student_id;
    public static $student_test_id;
    public static $random;
    public static $admin;

    public function getTtItemsAttribute()  {
        $url = config("constants.APP_IMAGE_URL").'image/qb/';
        $qb_items = DB::table('test_items_papers')
            ->leftjoin('question_bank_items', 'question_bank_items.id', 'test_items_papers.question_bank_item_id')
            ->leftjoin('question_banks', 'question_banks.id', 'question_bank_items.question_bank_id')
            ->where('question_type_id', $this->question_type_id)
            ->where('test_items_papers.status','ACTIVE')
            ->where('question_bank_items.deleted_status',0)
            ->where('question_type', $this->question_type)->where('test_id', $this->test_id)->where('test_no', $this->test_no)
            ->select('*', DB::RAW('concat("'.$url.'",question_file) as is_question_file'),
                          DB::RAW('concat("'.$url.'",hint_file) as is_hint_file')
                    );

        if(self::$random == 1) {
            $qb_items->orderby(DB::RAW('RAND()'));
        }
        $qb_items = $qb_items->get();

        $match = [];
        if(!empty($qb_items)) {
            foreach($qb_items as $qk=>$qitem) {
                $arr = [8,9,10,6,16]; 
                // Missing letters, jumbled words, jumbled letters, match
                if(in_array($qitem->question_type_id, $arr)) {
                    if($qitem->question_type_id == 8) {
                        // Missing letters
                        //$qitem->question = mb_convert_encoding($qitem->question, 'UTF-8', 'UTF-8');
                        //$encoded = json_encode( CommonController::utf8ize( $qitem->question ) );
                        $qitem->question = CommonController::missingletters($qitem->question); 
                    }   else if($qitem->question_type_id == 9) {
                        $qitem->question = CommonController::jumbledwords($qitem->question); 
                    }   else if($qitem->question_type_id == 10) {
                        $qitem->question = CommonController::jumbledletters($qitem->question); 
                    }   else if($qitem->question_type_id == 6) {
                        $match[$qk] = $qitem->answer;  
                        $qitem->display_answer = $qitem->answer;  
                    } else if($qitem->question_type_id == 16) {
                        $url = config("constants.APP_IMAGE_URL").'image/questionbank/';
                        $qitem->question = $url.$qitem->question;
                       if(!empty($qitem->option_1)){
                        $qitem->option_1 = $url.$qitem->option_1;

                       }
                       if(!empty($qitem->option_2)){
                        $qitem->option_2 = $url.$qitem->option_2;

                       }
                       if(!empty($qitem->option_3)){
                        $qitem->option_3 = $url.$qitem->option_3;

                       }
                       if(!empty($qitem->option_4)){
                        $qitem->option_4 = $url.$qitem->option_4;

                       }
                    } 
                }
            }

            if(count($match) > 0) {
                if(isset(self::$admin) && (self::$admin == 1)) {

                }   else {
                    shuffle($match);
                    foreach($qb_items as $qk=>$qitem) {
                        $qitem->answer = $match[$qk];
                    }
                }
            }
        }
         
        if(self::$student_id > 0) {
            foreach($qb_items as $qk=>$qbi) {
                $answer = DB::table('student_test_answers')
                    ->where('student_id', self::$student_id) 
                    ->where('student_test_id',self::$student_test_id)
                    ->where('question_bank_item_id', $qbi->question_bank_item_id)->first();
                if(!empty($answer)) {
                    $qb_items[$qk]->student_answer = $answer->answer;
                    $qb_items[$qk]->student_mark = $answer->mark;
                }   else {
                    $qb_items[$qk]->student_answer = '';
                    $qb_items[$qk]->student_mark = '';
                } 


                $atts = DB::table('student_test_answers')
                    ->leftjoin('student_tests', 'student_tests.id', 'student_test_answers.student_test_id')
                    ->where('student_test_answers.student_id', self::$student_id) 
                    ->where('student_tests.test_id',$this->test_id)
                    ->where('question_bank_item_id', $qbi->question_bank_item_id)
                    ->select('student_test_answers.id', 'student_test_answers.mark')->get();
                $right = $wrong = 0;
                if($atts->isNotEmpty()) {
                    $qb_items[$qk]->times_attended = count($atts); 
                    foreach($atts as $ans) {
                        if($ans->mark > 0) {
                            $right = $right + 1;
                        }   else {
                            $wrong = $wrong + 1;
                        }
                    }
                    $qb_items[$qk]->right = $right; 
                    $qb_items[$qk]->wrong = $wrong; 
                }   else {
                    $qb_items[$qk]->times_attended = 0; 
                    $qb_items[$qk]->right = 0; 
                    $qb_items[$qk]->wrong = 0; 
                } 

            }
        }   
        return $qb_items;
    }

}