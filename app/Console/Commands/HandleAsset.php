<?php

namespace App\Console\Commands;

use DB;
use Asset;
use Exception;
use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;
use Illuminate\Console\Command;

class HandleAsset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handle:asset {money} {no} {creator_primary_user_id} {gainer_primary_user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发单效率保证金收入，接单效率保证金扣除';

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
        DB::beginTransaction();
        try {
            Asset::handle(new Expend($this->argument('money'), 5, $this->argument('no'), '效率保证金支出', $this->argument('gainer_primary_user_id')));
            
            Asset::handle(new Income($this->argument('money'), 11, $this->argument('no'), '效率保证金收入', $this->argument('creator_primary_user_id')));
        } catch (Exception $e) {
            DB::rollback();
        }
        DB::commit();
    }
}
