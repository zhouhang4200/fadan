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
class GetMessageDailianmama extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getMessageDailianmama';

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

            // 获取所有需要获取留言的订单
            foreach (levelingMessageGet() as $orderNo => $data) {
                $data = json_decode($data);

                if ($data->platform == 1) { // 91 平台

                } else if ($data->platform == 2) { // 代练妈妈
                    // 取留言
                    $this->dailianMamaMessage($data->foreign_order_no);

                    // 本次获取的留言数量
                    $currentCount = count($this->dailianMamaMessageList);

                    $addCount = $currentCount - $data->count;
                    $message = [];
                    if ($currentCount != $data->count) {
                        for ($i = $addCount; $i >= 0; $i--) {
                            $message[] = [
                                'third' => $data->platform,
                                'user_id' => $data->user_id,
                                'order_no' => $data->order_no,
                                'contents' => $this->dailianMamaMessageList[$i]['content'],
                                'date' => $this->dailianMamaMessageList[$i]['createtime'],
                                'send_time' => $this->dailianMamaMessageList[$i]['createtime'],
                            ];
                        }
                        LevelingMessage::insert($message);
                        // 更新数量
                        levelingMessageAdd($data->user_id, $orderNo, $data->foreign_order_no, $data->platform, $currentCount);
                        // 更新角标
                        levelingMessageCount($data->user_id, 1, $addCount);
                    }
                    $this->dailianMamaMessageList = [];
                }
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
