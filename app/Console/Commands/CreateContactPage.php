<?php

namespace App\Console\Commands;

use App\Models\CustomBlock;
use App\Models\CustomContent;
use App\Models\CustomGroup;
use Illuminate\Console\Command;

class CreateContactPage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-contact-page';

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
            'slug' => 'CONTACT'
        ]);
        CustomBlock::create([
            'slug' => 'CONTACT-MAP',
            'title' => 'Карта'
        ]);
        CustomGroup::create([
            'slug' => 'CONTACT-JOB',
            'title' => 'Время работы',
            'sort_order' => 1,
        ]);
        CustomGroup::create([
            'slug' => 'CONTACT-ADDRESS',
            'title' => 'Адрес',
            'sort_order' => 2,
        ]);
        CustomGroup::create([
            'slug' => 'CONTACT-PHONES',
            'title' => 'Касса',
            'sort_order' => 3,
        ]);
    }
}
