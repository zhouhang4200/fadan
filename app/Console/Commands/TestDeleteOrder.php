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
        DailianFactory::choose('delete')->run('2018030715463800000035', '8559', false);
    }
}
