<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\CommonController;

class HomeworkSubmissions extends Model
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
    protected $table = 'homework_submissions';

    protected $appends = ['is_submitted_documents'];

    public function getIsSubmittedDocumentsAttribute()
    {   
        $is_submitted_documents = [];
        if(!empty($this->submitted_documents)) {
            $file_attachments = $this->submitted_documents;

            if(!empty($file_attachments)) {
                $file_attachments = explode(';', $file_attachments);
                foreach($file_attachments as $img) {
                    $is_submitted_documents[] = ['img' => config("constants.APP_IMAGE_URL").'uploads/homeworks/'.$img];
                }
            } 
        }   else {
            $is_submitted_documents = [];
        }
        
        return $is_submitted_documents;
    }

}