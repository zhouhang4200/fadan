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
        $e = 'Xn8huQEpMjdaS5AOVEKM8WdwBqJvdlt3qhbsHjTWaPlpfGUlPrRkqjkYFhIvu2NGz8venJkorE29fb8K2rD48XyzoBw4NphX8n5a3i99jvqol5SU+9cqCl5LwzW13fb7Py3GCSNFDFYj1G+8z+0wj/aY26fx8ntHbwHxDHT1Ncg+sP+8NlfE7x9ojm3UYpG5jKs0SEMBizCZz7s9YI7puixZHu8Qr2QVNE561fDJUqOdyCt7womkWpG1bouRfKVIBUHPfr93YZKxGHCPnHGrzIqtbUidOi8ufzShMq38ZkN960UiTD67SFhVrjzEQB+FQzatx9A+wD97mFz9wfSnSPzOyK8LoJ3RGVbaKMC97BW2PhqHxfT0WwhN05uTscG3CbGDKcNBTf20Qfy+Ej3MbXWY9Vg9Ei3Y/NPUpugIQTzZBhKcKzSUjlJ0er8suUiAAQcq2yfnjlIQHjlaaWk+0nYx0Z4EyxqdfGffAKs+96olTwZMxn/5EPq+s3n1RH4004qwC8YRVII7Onb4i3i78ryyVRZ+ZzkmnseCWY0RATyJWtxrCjtAwzwv5XTEX4fzyy1vMCJFnwpghm0yFM0Aga3N4xhVJTx+2IfPbU1oX0emQTw4ejUvftYVRoTzsk4+lo4QOK/vFelTstSSwp6vbdKxBXG0YlBGUNZA06UgDcKYXu6aenJVbsoEWnmRQwqPRI0iBFdr9DXIFJuZE/1efjH/kChRNqJDZkYYZ/YO4rAQdLsziOmlHxcZjTQKr30zMnZPgQ9a43wSOH79PCeiQQ==';

        dd(openssl_decrypt(base64_decode($e), 'aes-128-cbc', config('partner.platform')[4]['aes_key'], true, config('partner.platform')[4]['aes_iv']));
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
