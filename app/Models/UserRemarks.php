<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use DB;

class UserRemarks extends Model
{
    use HasFactory;

    protected $table = 'user_remarks';

    protected $appends = [ 'posted_user', 'remarked_user' ];

    public function getCreatedAtAttribute($value) {
        return date('d M Y H:i A', strtotime($value));
    }

    public function getPostedUserAttribute() { 

        $posted_user = DB::table('users')->where('id', $this->created_by)
            ->select('users.id', 'users.name', 'name_code')->first();  

        return $posted_user;
    }

    public function getRemarkedUserAttribute() { 

        $remarked_user = Student::where('user_id', $this->user_id)->first();  

        return $remarked_user;
    }
 
}
