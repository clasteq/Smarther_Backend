<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\CommonController;

class QuestionBankItems extends Model
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
    protected $table = 'question_bank_items';

    protected $appends = ['qb_items'];

    public static $noofquestions; 
    public static $student_id;
    public static $student_test_id;
    public static $random;
    public static $admin;
    public static $qb_ids;

    public function getQbItemsAttribute()  {
        $url = config("constants.APP_IMAGE_URL").'image/qb/';
        if(self::$noofquestions > 0) { 
            $qb_items = DB::table('question_bank_items')->where('question_type_id', $this->question_type_id)->where('deleted_status',0)
                ->where('question_type', $this->question_type)->whereIn('question_bank_id', self::$qb_ids)
                ->orderby(DB::RAW('RAND()'))->take(self::$noofquestions)
                ->select('question_bank_items.*',DB::RAW('concat("'.$url.'",hint_file) as is_hint_file'))
                ->get();
        }   else { 
            $qb_items = DB::table('question_bank_items')->where('question_type_id', $this->question_type_id)->where('deleted_status',0)
                ->where('question_type', $this->question_type)->where('question_bank_id', $this->question_bank_id)
                ->select('question_bank_items.*',DB::RAW('concat("'.$url.'",hint_file) as is_hint_file'))
                ->get();
        }

        $match = [];
        if(!empty($qb_items)) {
            if(isset(self::$admin) && (self::$admin == 1)) {

            }   else {
                foreach($qb_items as $qk=>$qitem) {
                    $arr = [8,9,10,6,16,18]; 
                    // Missing letters, jumbled words, jumbled letters, match
                    if(in_array($qitem->question_type_id, $arr)) {
                        if($qitem->question_type_id == 8) {
                            // Missing letters
                            //$qitem->question = mb_convert_encoding($qitem->question, 'UTF-8', 'UTF-8');
                            //$encoded = json_encode( CommonController::utf8ize( $qitem->question ) );
                            $qitem->question = CommonController::missingletters($qitem->question); 
                        }   else if($qitem->question_type_id == 9 || $qitem->question_type_id == 18) {
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
                        }  else if($qitem->question_type_id == 11) {
                            $url = config("constants.APP_IMAGE_URL").'image/qb/'; 
                            if(!empty($qitem->question_file)){
                                $qitem->question_file = $url.$qitem->question_file; 
                            } 
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
            }
        }   

        return $qb_items;
    }

    public function questiontype_settings() {
        return $this->hasOne('App\Models\QuestionTypeSettings','question_type_id','question_type_id');
    }

}