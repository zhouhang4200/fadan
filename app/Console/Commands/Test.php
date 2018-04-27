<?php

namespace App\Console\Commands;

use App\Services\Leveling\DD373Controller;
use App\Services\Leveling\MayiDailianController;
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
            'oid' => 'ORD180427213653468705',
//            'appeal.title' => '申请仲裁',
//            'appeal.content' => '申请仲裁',
//            'pic1' => fopen(public_path('frontend/images/3.png'), 'r'),
        ];
//        dd(MayiDailianController::delete([
//            'mayi_order_no' => 163849,
//        ]));
    dd(    DD373Controller::delete([
        'dd373_order_no' => 'XQ20180427213655-75739',
    ]));
        dd(Show91::chedan($options));
    }
}
