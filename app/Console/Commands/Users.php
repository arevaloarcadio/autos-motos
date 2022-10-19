<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\{Dealer,User};

class Users extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dealers = Dealer::select('email_address')
            ->groupBy('email_address')
            ->havingRaw('count(email_address)>1')
            ->get();

      
        foreach ($dealers as $dealer) {
            
            $d = Dealer::where('email_address',$dealer->email_address)->first();

            $val = User::where('email',$dealer->email_address)->first();
            
            if(!is_null($val) || $dealer->email_address == 'pruebasoftware.22@gmail.com'){
                continue;
            }

            $user = new User;
            $user->first_name =  $d->company_name;
            $user->last_name =  '.';
            $user->mobile_number =  $d->phone_number;
            $user->landline_number =  '+000000000';
            $user->whatsapp_number =  '+000000000';
            $user->email  =  $d->email_address;
            $user->email_verified_at =  null;
            $user->password =  Hash::make($d->company_name.'123456789**');
            $user->dealer_id  =  $d->id;
            $user->remember_token =  null;
            $user->type =  'Profesional';
            $user->code_postal =  $d->zip_code;
            $user->status =  'Aprobado';
            $user->image =  'users/user-default-ocassional.png' ;
            $user->save();

            $this->info($d->email_address);  
             
        }
    }
}
