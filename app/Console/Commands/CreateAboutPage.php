<?php

namespace App\Console\Commands;

use App\Models\CustomContent;
use App\Models\CustomImage;
use Illuminate\Console\Command;

class CreateAboutPage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-about-page';

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
        CustomContent::create([
           'slug' => 'ABOUT'
        ]);
        CustomImage::create([
            'slug' => 'ABOUT'
        ]);
    }
}
