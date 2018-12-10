<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\GameLevelingType;
use App\Models\GameRegion;
use App\Models\GameServer;
use Illuminate\Console\Command;

/**
 * Class importGameInfoCommand
 * @package App\Console\Commands
 */
class importGameInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-game-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导入游戏信息';


    /**
     * @throws \Exception
     */
    public function handle()
    {
        // 查找代练游戏
        \DB::beginTransaction();
        $games = \DB::connection('market')
            ->table('goods_templates')
            ->where('service_id', 4)
            ->orderBy('id')->get();
        foreach ($games as $item) {
            // 查游戏名
            $games = \DB::connection('market')
                ->table('games')
                ->where('id', $item->game_id)
                ->first();
            // 写入游戏
            $newGame = Game::create([
                'name' => $games->name,
                'icon' => 1,
            ]);

            // 查找组件ID
            $widget = \DB::connection('market')
                ->table('goods_template_widgets')
                ->where('goods_template_id', $item->id)
                ->where('field_name', 'region')
                ->first();

            $typeId = \DB::connection('market')
                ->table('goods_template_widgets')
                ->where('goods_template_id', $item->id)
                ->where('field_name', 'game_leveling_type')
                ->first();


            // 写入代练类型 game_leveling_type
            $types = \DB::connection('market')
                ->table('goods_template_widget_values')
                ->where('goods_template_widget_id', $typeId->id)
                ->orderBy('id')
                ->get();

            foreach ($types as $type) {
                GameLevelingType::create([
                    'name' => $type->field_value,
                    'game_id' => $newGame->id,
                    'poundage' => 1,
                ]);
            }

            // 查找区
            $regions = \DB::connection('market')
                ->table('goods_template_widget_values')
                ->where('goods_template_widget_id', $widget->id)
                ->orderBy('id')
                ->get();

            // 写入区
            foreach ($regions as $region) {
                $newRegion = GameRegion::create([
                    'name' => $region->field_value,
                    'game_id' => $newGame->id,
                    'initials' => 1,
                ]);
                // 查找服
                $servers = \DB::connection('market')
                    ->table('goods_template_widget_values')
                    ->where('parent_id', $region->id)
                    ->get();

                foreach ($servers as $server) {
                    GameServer::create([
                        'name' => $server->field_value,
                        'game_region_id' => $newRegion->id,
                        'initials' => 1,
                    ]);
                }
            }
        }
        \DB::commit();
    }
}
