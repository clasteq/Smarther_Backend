<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SchoolBankList extends Model
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
    //  protected $table = '';
    
    protected $table = 'school_bank_lists';
    
    protected $appends = ['is_qr_code_image'];

    public function getIsQrCodeImageAttribute(){

        return config("constants.APP_IMAGE_URL").'uploads/schoolbanks/'.$this->qr_code_image;
    }
}
