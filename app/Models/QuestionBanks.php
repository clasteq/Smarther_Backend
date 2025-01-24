<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class QuestionBanks extends Model
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
    protected $table = 'question_banks';

    protected $appends = ['is_notes_file','questionbank_items'];

    
    public function getIsNotesFileAttribute()
    {
        if(!empty($this->notes)) {
            return config("constants.APP_IMAGE_URL").'image/notes/'.$this->notes;
        }   else {
            return '';
        }        
    }

    public function getQuestionbankItemsAttribute() {
          return QuestionBankItems::with('questiontype_settings')->where('deleted_status',0)->where('question_bank_id', $this->id)
               ->select('question_type_id', 'question_type', 'question_bank_id')
               ->orderby('question_type_id', 'asc')
               ->groupby('question_type_id')->groupby('question_type')->get();
    }

}