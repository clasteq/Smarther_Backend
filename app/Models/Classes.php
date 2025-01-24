<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Classes extends Model
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
    protected $table = 'classes'; 
    protected $appends = ['is_mapped_subjects'];

    public static $section_id;

    public function getIsMappedSubjectsAttribute()  {
        $sections = Sections::where('class_id',$this->id)->where('status','ACTIVE');
        if(isset(self::$section_id) && (self::$section_id > 0)) {
            $sections->where('id',self::$section_id);
        }
        $sections = $sections->first();
        $subjects = [];
        if(!empty($sections)){
            $mapsubs = $sections->mapped_subjects;
            $mapsubs = explode(',', $mapsubs);
            $subjects = DB::table('subjects')->whereIn("id", $mapsubs)->where('status','ACTIVE')
            ->select("subject_name", "id")->orderby('position','asc')->get();
        }
        return $subjects;
      
     

        // }
  
        
    }
}
