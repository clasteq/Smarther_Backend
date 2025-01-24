<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;
use Mail;
use Hash;
use App\User;
use App\Http\Controllers\CommonController;

class UserOTPEmailSender implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user; 
    protected $email; 

    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct($user, $email='')
    {
        $this->user = $user; 
        $this->email = $email; 
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $useremail = $this->email; // $this->user->email;
        $adminEmail = config("constants.support_mail"); 
        $site_name = config("constants.site_name");   
        $exuser = User::find($this->user->id);
        $otpGeneration  = CommonController::generateNumericOTP(4);
        $exuser->otp = $otpGeneration;  
        $exuser->save();  

        Mail::send('email.user-otp', ['user'=>$this->user, 'sitename'=>$site_name, 'otp'=>$otpGeneration], function ($m) use ($adminEmail, $site_name, $useremail) {

            $m->from($adminEmail, $site_name.' - '.' OTP Verification');

            $m->to($useremail)->subject($site_name.' - '.' OTP Verification');
        }); 
        
    }
}