<?php
namespace App\Console\Commands;

use App\Events\NotificationEvent;
use App\Models\LevelingMessage;
use App\Services\DailianMama;
use App\Services\Show91;
use Illuminate\Console\Command;

/**
 * 获取代练留言
 * Class GetMessage
 * @package App\Console\Commands
 */
class GetMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getMessage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取代练留言';

    /**
     * 代练妈妈留言
     * @var array
     */
    protected $dailianMamaMessageList = [];

    /**
     * Create a new command instance.
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
        while(1) {
            // 获取所有需要获取留言的订单
            foreach (levelingMessageGet() as $orderNo => $data) {
                $data = json_decode($data);

                if ($data->platform == 1) { // 91 平台
                    // 取留言
                    $messageList = Show91::messageList(['oid' => $data->third_order_no]);
                    // 转为数组
                    $messageListArr = json_decode(json_encode($messageList), true);

                    $ids = [];
                    $messageArr = [];
                    foreach ($messageListArr as $item) {
                        if (isset($item['id']) && $item['uid'] != config('show91.uid')) {
                            $ids[] = $item['id'];
                            $messageArr[] = $item;
                        }
                    }
                    // 用ID倒序
                    array_multisort($ids, SORT_DESC, $messageArr);
                    // 本次获取的留言数量
                    $currentCount = count($messageArr);

                    $addCount = $currentCount - $data->count;

                    $message = [];

                    if ($currentCount != $data->count) {
                        for ($i = $addCount - 1; $i >= 0; $i--) {
                            $message[] = [
                                'third' => $data->platform,
                                'user_id' => $data->user_id,
                                'order_no' => $data->order_no,
                                'third_order_no' => $data->third_order_no,
                                'foreign_order_no' => $data->foreign_order_no,
                                'contents' => $messageArr[$i]['mess'],
                                'date' => $messageArr[$i]['created_on'],
                                'send_time' => $messageArr[$i]['created_on'],
                            ];
                        }
                        LevelingMessage::insert($message);
                        // 更新数量
                        levelingMessageAdd($data->user_id,
                            $orderNo,
                            $data->third_order_no,
                            $data->platform,
                            $data->foreign_order_no,
                            $currentCount);
                        // 更新角标
                        levelingMessageCount($data->user_id, 1, $addCount);
                    }
                } else if ($data->platform == 2) { // 代练妈妈

                }
            }
            sleep(1);
        }
    }

    /**
     * 代练妈妈留言获取
     * @param $orderNO
     * @param int $beginId
     */
    protected function dailianMamaMessage($orderNO, $beginId = 0)
    {
        $message = DailianMama::chatOldList($orderNO, $beginId);

        if (count($message['list'])) {

            $this->dailianMamaMessageList = array_merge($this->dailianMamaMessageList, $message['list']);
            $this->dailianMamaMessage($orderNO, $message['beginid']);
        }
    }
}
