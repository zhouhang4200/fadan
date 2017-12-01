<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\PunishOrReward;

class AutoPunishUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'punish:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'punish over time user weight';

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
        $today = Carbon::now()->startOfDay()->addHours(18)->toDateTimeString();

        // 权重
        $punishOrRewards = PunishOrReward::whereIn('type', ['4'])
                ->whereIn('status', ['7', '9', '10'])
                ->where('deadline', $today)
                ->get();

        // 到点自动确认
        foreach($punishOrRewards as $punishOrReward) {
            $punishOrReward->confirm = 1;
            $punishOrReward->save();
        }

    }
}
