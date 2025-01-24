<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class FeesPaymentDetail extends Model
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

    protected $table = 'fees_payment_details';

    protected $appends = [ 'is_item_name', 'is_paid_date' ];

    public function getConcessionDateAttribute($value) {
        if(!empty($value))
            return date('Y-m-d', strtotime($value));
        else 
            return '';
    }

    public function getCreatedAtAttribute($value) {
        if(!empty($value))
            return date('Y-m-d H:i:s', strtotime($value));
        else 
            return '';
    }

    public function getIsItemNameAttribute() {

        $fee_item_id = DB::table('fee_structure_items')->where('id', $this->fee_structure_item_id)->value('fee_item_id');

        $is_item_name = DB::table('fee_items')->where('id', $fee_item_id)->value('item_name');
        return $is_item_name;
    }

    public function getIsPaidDateAttribute() {

        $is_paid_date = date('d M Y', strtotime($this->paid_date)); 
        return $is_paid_date;
    }
}
