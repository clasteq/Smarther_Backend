<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ContactsList extends Model
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
    protected $table = 'school_contacts_list';

    protected $appends = ['contacts_list'];

    public function getContactsListAttribute()  {
        $faqs = DB::table('school_contacts_list')->where('contact_for', $this->contact_for)->where('status', 'YES')->get();
        return $faqs;
    }
    
   
}
