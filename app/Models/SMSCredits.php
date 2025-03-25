<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class SMSCredits extends Model
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
    protected $table = 'communication_sms_credits';

    protected $appends = ['is_total_credits'];
    
    public function getCreatedAtAttribute($value) {
        return date('Y-m-d H:i:s', strtotime($value));
    }

    public function getIsTotalCreditsAttribute($value) {
        $is_total_credits = DB::table('communication_sms_credits')->where('school_id', $this->school_id)
            ->where('status', 'YES')->sum('total_credits');
        return $is_total_credits;
    }

}
