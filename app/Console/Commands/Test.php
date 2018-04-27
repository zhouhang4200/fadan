<?php

namespace App\Console\Commands;

use App\Services\Show91;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Test';

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
        $options = [
            'aid' => 'ORD180427132554429739',
//            'appeal.title' => '申请仲裁',
//            'appeal.content' => '申请仲裁',
//            'pic1' => fopen(public_path('frontend/images/3.png'), 'r'),
        ];
        dd(Show91::cancelAppeal($options));
    }
}
