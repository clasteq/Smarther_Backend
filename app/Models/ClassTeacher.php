<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ClassTeacher extends Model
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
    protected $table = 'class_teachers';

    protected $appends = ['is_class_name','is_section_name'];


    public function getIsClassNameAttribute(){
        
        return DB::table('classes')->where('id', $this->class_id)->value('class_name');

    }
    public function getIsSectionNameAttribute(){
        return DB::table('sections')->where('id', $this->section_id)->value('section_name');
    }
}
