<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class TeacherAttendance extends Model
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
    protected $table = 'teachers_attendance'; 

    public function users(){

        return $this->belongsTo('App\Models\User','user_id','id');
        
    }
}
