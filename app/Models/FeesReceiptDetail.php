<?php

namespace App\Models;
use DB;
use App\Models\User;
use App\Http\Controllers\CommonController;

use Illuminate\Database\Eloquent\Model;


class FeesReceiptDetail extends Model
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
    protected $table = 'fees_receipt_details';

    protected $appends = [ 'is_created_name','is_account_name','is_receipthead_name', 'is_school', 'is_student', 
      'is_payment_mode', 'is_amount_words', 'is_pdf','is_canceled_name'];

      public function getIsCanceledNameAttribute()    {

        return DB::table('users')->where('id', $this->canceled_by)->value('name');

      }


      public function getIsPdfAttribute() { 
   
              $is_pdf = '';
              if (!empty($this->receipt_pdf)) {            
                  $is_pdf = config("constants.APP_IMAGE_URL"). 'uploads/receipt_pdf/'. $this->school_id .'/'. $this->student_id .'/'.$this->id.'.pdf'; 
              } 
              return $is_pdf;
      }

      public function getIsCreatedNameAttribute()    {

        return DB::table('users')->where('id', $this->posted_by)->value('name');

      }

      public function getIsSchoolAttribute()    {

        $is_school = DB::table('users')->leftjoin('schools', 'schools.user_id', 'users.id')
          ->where('users.id', $this->school_id)->select('users.name', 'users.name_code', 'users.profile_image', 'schools.address')
          ->first();

        if(!empty($is_school)) {
          $is_school->profile_image = User::getUserProfileImageAttribute($this->school_id);
        }

        return $is_school;
      }

      public function getIsStudentAttribute()    {

        $is_student = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
          ->where('users.id', $this->student_id)->select('users.name', 'users.admission_no', 'users.profile_image', 
              'students.roll_no', 'class_id', 'section_id')
          ->first();

        if(!empty($is_student)) {
          //$is_school->profile_image = User::getUserProfileImageAttribute($this->school_id);
          $is_student->class_name = DB::table('classes')->where('id', $is_student->class_id)->value('class_name');
          $is_student->section_name = DB::table('sections')->where('id', $is_student->section_id)->value('section_name');
        }

        return $is_student;
      }

      public function getIsAccountNameAttribute()    {

        return DB::table('accounts')->where('id', $this->account_id)->value('account_name');

      }

      public function getIsReceiptHeadNameAttribute()    {

        $get_receipt_id=DB::table('accounts')->where('id',$this->account_id)->value('recepit_id');

        return DB::table('receipt_heads')->where('id', $get_receipt_id)->value('name');

      }

      public function feepayments() {
          return $this->hasMany('App\Models\FeesPaymentDetail','receipt_id','id');
      }


      public function getIsPaymentModeAttribute()    {

        $is_payment_mode = DB::table('payment_modes')->where('id',$this->payment_mode)->value('name');

        return $is_payment_mode;

      }

      public function getIsAmountWordsAttribute()    {

        $is_amount = $this->amount;
        $words = CommonController::getIndianCurrency($is_amount);
        return $words;

      }
}
