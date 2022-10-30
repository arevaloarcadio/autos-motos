<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\{Dealer,RentalAd,Ad,DealerShowRoom,User};

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
        $tables = ['mechanic_ads','rental_ads'];

        foreach ($tables as $table) {

            $dealers = Dealer::whereRaw("email_address IN(select email_address from ".$table.")")->get();

            foreach ($dealers as $dealer) {
                
                $user = User::where('email',$dealer->email_address)->first();
                 
                $this->info($dealer->company_name);  
                
                if (is_null($user)) {
                    $user = new User;
                    $user->first_name =  $dealer->company_name;
                    $user->last_name =  '.';
                    $user->mobile_number =  $dealer->phone_number;
                    $user->landline_number =  '+000000000';
                    $user->whatsapp_number =  '+000000000';
                    $user->email  =  $dealer->email_address;
                    $user->email_verified_at =  null;
                    $user->password =  Hash::make($dealer->company_name.'123456789**');
                    $user->dealer_id  =  $dealer->id;
                    $user->remember_token =  null;
                    $user->type =  'Profesional';
                    $user->code_postal =  $dealer->zip_code;
                    $user->status =  'Aprobado';
                    $user->image =  'users/user-default-ocassional.png' ;
                    $user->save();
                }
                
                $dealer_show_room = DealerShowRoom::where('dealer_id',$dealer->id)->first();

                RentalAd::where('email_address',$dealer->email_address)
                    ->update(['dealer_show_room_id' => $dealer_show_room->id ?? null,'dealer_id' => $dealer->id]);

                Ad::whereRaw("id in(select ad_id from rental_ads where email_address = '".$dealer->email_address."')")
                    ->update(['user_id' => $user->id]);
            }
        }
    }
}
