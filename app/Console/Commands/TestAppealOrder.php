<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Extensions\Dailian\Controllers\DailianFactory;

class TestAppealOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appeal:order {no} {user_id}';

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
        DailianFactory::choose('arbitration')->run($this->argument('no'), $this->argument('user_id'), false);
    }
}
