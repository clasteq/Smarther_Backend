<?php

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Model;


class FeeItems extends Model
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
    protected $table = 'fee_items';


    protected $appends = [ 'is_category_name'];

    public function getIsCategoryNameAttribute()    {
        
      return DB::table('fee_categories')->where('id', $this->category_id)->value('name');
      
  } 
    
   
}
