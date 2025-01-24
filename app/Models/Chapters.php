<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Chapters extends Model
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
    protected $table = 'chapters';

    protected $appends = [ 'is_class_name','is_subject_name', 'is_term_name' ];

    public function getIsClassNameAttribute()    {
        
        return DB::table('classes')->where('id', $this->class_id)->value('class_name');
        
    } 

    public function getIsSubjectNameAttribute()    {
        
        return DB::table('subjects')->where('id', $this->subject_id)->value('subject_name');
        
    } 

    public function getIsTermNameAttribute()    {
        
        return DB::table('terms')->where('id', $this->term_id)->value('term_name');
        
    } 
}
