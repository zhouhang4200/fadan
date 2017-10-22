<?php
namespace App\Repositories\Api;

use stdClass;

class GoodsRepository
{
    static public function find($goodsId)
    {
        $service = new stdClass;
        $service->id   = 1;
        $service->name = '充值';

        $game = new stdClass;
        $game->id   = 88;
        $game->name = '王者荣耀';

        $template = new Template;

        $goods = new stdClass;
        $goods->name     = '商品1';
        $goods->service  = $service;
        $goods->game     = $game;
        $goods->price    = 168.88;
        $goods->template = $template;

        return $goods;
    }
}

class Template
{
    public function getWidgetPluck()
    {
        return [
            'version' => '版本',
            'account' => '账号',
            'region'  => '区服',
        ];
    }
}