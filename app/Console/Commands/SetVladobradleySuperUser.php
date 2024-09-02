<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SetVladobradleySuperUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-vladobradley-super-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::where('email', "vladobradley@yahoo.com")->first();
        $user->is_admin = true;
        $user->save();

    }
}
