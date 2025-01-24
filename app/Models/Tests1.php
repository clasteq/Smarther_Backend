<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Tests extends Model
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
    protected $table = 'tests';

    protected $appends = ['test_items', 'no_of_questions','test_list','selected_test'];

     public function getTestItemsAttribute() { 

          return TestItems::leftjoin('question_bank_items', 'question_bank_items.id', 'test_items.question_bank_item_id')
               ->leftjoin('question_banks', 'question_banks.id', 'question_bank_items.question_bank_id')
               ->where('test_id', $this->id)
               ->where('test_items.status','ACTIVE')
               ->select('question_type_id', 'question_type', 'test_id')
               ->orderby('question_type_id', 'asc') 
               ->groupby('question_type_id')->groupby('question_type')
               
               ->get();


     }

     public function getNoOfQuestionsAttribute() {
          $no_of_questions = DB::table('test_items')->where('test_id', $this->test_id)->where('status','ACTIVE')->count('id');
          return $no_of_questions;
     }
    

     public function getTestListAttribute() {
          $qb_items = TestItems::leftjoin('question_bank_items', 'question_bank_items.id', 'test_items.question_bank_item_id')
          ->leftjoin('question_banks', 'question_banks.id', 'question_bank_items.question_bank_id')
          ->where('test_id', $this->id)
          ->where('test_items.status','ACTIVE')
          ->select('question_type_id', 'question_type', 'test_id','question_bank_items.question_bank_id')
          ->orderby('question_type_id', 'asc') 
          ->groupby('question_type_id')->groupby('question_type')
          
          ->get();

          if(!empty($qb_items)) {
               foreach($qb_items as $qk=>$qitem) {
                  $question_type_id = $qitem->question_type_id ;

                
                 $answer = DB::table('question_bank_items')
               ->where('question_type_id',$question_type_id)->get();

               $qb_items[$qk]->question_bank = $answer;

               }

               return $qb_items;
          }

     }

     public function getSelectedTestAttribute(){
        return  $qb_items = DB::table('test_items')
          ->where('test_id', $this->id)
          ->where('status','ACTIVE')
          ->select('question_bank_item_id','mark','id')
           ->get();

        
     }
    

}