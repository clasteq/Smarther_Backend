<?php

namespace App\Jobs;

use App\User;
use App\Order;

use App\Http\Controllers\CommonController;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;
use Mail;
use Hash;

class UserLoginCredentialEmailSender implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $password;

    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {

        $user = $this->user;
        
        if(!empty($user)) {
            $user_password = $this->password;
            $user_reg_no = $user->reg_no;  
            $username  = $user->name;
            $useremail = $user->email;
            $sitename = config("constants.site_name");
            $filepath = "email.user-email-logincredentials";  
            $subject = config("constants.site_name"). " - Login Credentials";
            $support_mail = config("constants.support_mail");

            Mail::send($filepath, ['username' => $username, 'subject' => $subject, 'sitename' => $sitename, 'user_password' => $user_password, 'user_reg_no' => $user_reg_no], function ($m) use ($useremail, $subject, $support_mail) {

                $m->from($support_mail, $subject);

                $m->to($useremail)->subject($subject);
            });
            
        }

    }
}