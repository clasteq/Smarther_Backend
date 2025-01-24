<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use DB;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    // Match the name here
    protected $appends = ['is_recepit_name'];

    // And match the name here
    public function getIsRecepitNameAttribute()
    {
        return DB::table('receipt_heads')->where('id', $this->recepit_id)->value('name');
    }
}
