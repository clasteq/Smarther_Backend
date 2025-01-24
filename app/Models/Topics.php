<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Topics extends Model
{
    protected $table = 'topics'; 

    protected $appends = ['is_topic_file', 'chapter_name', 'is_video_token','is_class_name', 'is_topic_name', 
        'is_term_name','is_subject_name'];

    public function getIsTopicFileAttribute()
    {
        if(!empty($this->topic_file)) {
            return config("constants.APP_IMAGE_URL").'image/topics/'.$this->topic_file;
        }   else {
            return '';
        }        
    }

    public function getChapterNameAttribute() {
        if($this->chapter_id > 0) {
            return DB::table('chapters')->where('id', $this->chapter_id)->value('chaptername');
        }   else {
            return '';
        }
    }
 
    public function getIsVideoTokenAttribute() {
        if(!empty($this->video_link)) {
            $arr = explode('/', $this->video_link);
            $last = end($arr);
            $arr1 = explode('?', $last);
            return current($arr1);
        }   else {
            return '';
        }
    }

    public function getIsClassNameAttribute()    {
        
        return DB::table('classes')->where('id', $this->class_id)->value('class_name');
        
    } 
    public function getIsTopicNameAttribute()    {
        
        return DB::table('chapter_topics')->where('id', $this->topic_id)->value('chapter_topic_name');
        
    } 

    public function getIsTermNameAttribute()    {
        
        return DB::table('terms')->where('id', $this->term_id)->value('term_name');
        
    } 

    public function getIsSubjectNameAttribute()    {
        
        return DB::table('subjects')->where('id', $this->subject_id)->value('subject_name');
        
    } 
}
