<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Periodtiming extends Model
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
    protected $table = 'period_timings';
     protected $appends = ['is_class_name'];

     public function getIsClassNameAttribute()    {
        
         return DB::table('classes')->where('id', $this->class_id)->value('class_name');
        
    } 
}
