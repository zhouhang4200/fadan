<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Extensions\Dailian\Controllers\DailianFactory;

class TestDeleteOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:order';

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
     * @return mixed
     */
    public function handle()
    {
        DailianFactory::choose('delete')->run('2018030716061300000332', '8317', false);
        DailianFactory::choose('delete')->run('2018030714543200000295', '8317', false);
        DailianFactory::choose('delete')->run('2018030619343100000639', '8317', false);
    }
}
