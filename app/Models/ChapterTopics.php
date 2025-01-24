<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ChapterTopics extends Model
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
    protected $table = 'chapter_topics';


    protected $appends = ['topics','is_class_name','is_chapter_name','is_subject_name', 'is_term_name'  ];

    // public function topics() {
    //     return $this->hasMany('App\Topics','subject_id','id')->where('subject_id',$this->subject_id)->where('class_id',$this->class_id)->where('topic_id', '>', 0);
    // }

    public function getTopicsAttribute(){
        return DB::table('topics')->where('subject_id',$this->subject_id)->where('class_id',$this->class_id)->where('status','ACTIVE')->get();

    }

    public function getIsClassNameAttribute()    {
        
        return DB::table('classes')->where('id', $this->class_id)->value('class_name');
        
    } 

    public function getIsChapterNameAttribute()    {
        
        return DB::table('chapters')->where('id', $this->chapter_id)->value('chaptername');
        
    } 

    
    public function getIsSubjectNameAttribute()    {
        
        return DB::table('subjects')->where('id', $this->subject_id)->value('subject_name');
        
    } 

    public function getIsTermNameAttribute()    {
        
        return DB::table('terms')->where('id', $this->term_id)->value('term_name');
        
    } 
}
