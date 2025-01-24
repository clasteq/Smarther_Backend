<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Districts extends Model
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
    protected $table = 'districts';
    
    protected $appends = ['is_state_name'];

    public function getIsStateNameAttribute()    {
        
        return DB::table('states')->where('id', $this->state_id)->value('state_name');
        
    } 

   
}
