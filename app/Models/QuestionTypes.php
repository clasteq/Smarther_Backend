<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class QuestionTypes extends Model
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
    protected $table = 'question_types';

    public function questiontype_settings() {
          return $this->hasOne('App\Models\QuestionTypeSettings','question_type_id','id');
    }

}