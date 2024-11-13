<?php

namespace App\Console\Commands;

use App\Models\VSConfig;
use Illuminate\Console\Command;

class CreateBuyTicketConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-buy-ticket-config';

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
        $config = VSConfig::whereSlug('CAN_BUY_TICKET')->first();
        if (!$config) {
            VSConfig::create([
                'slug' => 'CAN_BUY_TICKET',
                'value' => true
            ]);
            echo 'create';
        } else {
            echo 'exist';
        }
    }
}
