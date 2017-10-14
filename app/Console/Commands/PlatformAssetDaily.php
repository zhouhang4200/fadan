<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Exception;
use App\Repositories\Commands\PlatformAssetDailyRepository;

class PlatformAssetDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform:asset-daily {date=yesterday}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Platform asset daily';

    protected $platformAssetDailyRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PlatformAssetDailyRepository $platformAssetDailyRepository)
    {
        parent::__construct();

        $this->platformAssetDailyRepository = $platformAssetDailyRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = $this->argument('date');
        if ($date == 'yesterday') {
            $dailyDate = Carbon::yesterday()->toDateString();
        } else {
            $dailyDate = $date;
        }

        try {
            $this->platformAssetDailyRepository->generateDaily($dailyDate);
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
