<?php

namespace App\Console\Commands;

use App\Extensions\Dailian\Controllers\Revoked;
use App\Extensions\Dailian\Controllers\Revoking;
use Illuminate\Console\Command;

class RevokeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Revoke {no}{type}{amount?}{deposit?}{service?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '接单方发起撤销，不调用接口';

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
     * @throws \App\Exceptions\DailianException
     */
    public function handle()
    {
        if ($this->argument('type') == 1) {
            $this->ing();
        } else {
            $this->agree();
        }
    }

    /**
     * 接单方发起
     * @throws \App\Exceptions\DailianException
     */
    private function ing()
    {
        $orderData = \App\Models\Order::where('no', $this->argument('no'))->first();


        $apiAmount  = $this->argument('amount') ?? 0;  // 回传代练费
        $apiDeposit = $this->argument('deposit') ?? 0;; // 回传双金
        $apiService = $this->argument('service') ?? 0;; // 回传手续费

        // 数据
        $data = [
            'user_id'        => $orderData->gainer_primary_user_id,
            'order_no'       => $orderData->no,
            'amount'         => $apiAmount,
            'api_amount'     => $apiAmount,
            'api_deposit'    => $apiDeposit,
            'api_service'    => $apiService,
            'deposit'        => $apiDeposit,
            'consult'        => 2, // 接单发起撤销
            'revoke_message' => '打手撤单',
        ];

        (new Revoking())->run($orderData->no, $orderData->gainer_primary_user_id, $data, false);
    }

    /*
     * 发单方同意
     */
    private function agree()
    {
        $orderData = \App\Models\Order::where('no', $this->argument('no'))->first();

        (new Revoked())->run($orderData->no, $orderData->creator_primary_user_id, false);
    }
}
