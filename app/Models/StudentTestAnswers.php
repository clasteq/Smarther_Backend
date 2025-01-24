<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class StudentTestAnswers extends Model
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
    protected $table = 'student_test_answers';

    protected $appends = ['tt_items'];

    public function getTtItemsAttribute()  {
        $qb_items = DB::table('test_items')
            ->leftjoin('question_bank_items', 'question_bank_items.id', 'test_items.question_bank_item_id')
            ->leftjoin('question_banks', 'question_banks.id', 'question_bank_items.question_bank_id')
            ->where('question_type_id', $this->question_type_id)
            ->where('question_type', $this->question_type)->where('test_id', $this->test_id)->get();
        return $qb_items;
    }

}