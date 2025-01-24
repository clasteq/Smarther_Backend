<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Terms extends Model
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
    protected $table = 'terms';

    protected $appends = ['is_classname', 'is_term_name'];

    public function getIsClassNameAttribute()    {
        if(!empty($this->class_ids)) {
            $arr = explode(',', $this->class_ids);
            $arr = array_unique($arr);
            $arr = array_filter($arr);
            $classnames = DB::table('classes')->whereIn('id', $arr)->select( DB::raw('group_concat(class_name) as is_classname'))->first();
            
            $is_classname='';
            if(!empty($classnames)>0) {  
                foreach($classnames as $k => $v) {
                    $is_classname = $v;
                }
            } else { $is_classname= ''; }
            return $is_classname;
        }   else {
            return '';
        }
    }

    public function getIsTermNameAttribute()    {
        
        return DB::table('terms')->where('id', $this->term_id)->value('term_name');
        
    } 

     
}
