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
        $dealers = Dealer::whereRaw('code is null')->get();

        foreach ($dealers as $dealer) {

            $code = Dealer::whereRaw('code is not null')->count();
            
            $this->info($dealer->company_name."->".$code);
            
            $dealer->code = str_pad($code, 5, "0",STR_PAD_LEFT);
            
            $dealer->save();
        }
    }
}
