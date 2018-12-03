import { post } from './axios';

// 获取订单数据
export default {
    games() {
        return post('/games');
    },
    // 渠道订单
    GameLevelingChannelOrderList(params) {
        return post('/order-list', params);
    },
    GameLevelingChannelOrderComplete(params) {
        return post('/complete', params);
    },
    GameLevelingChannelOrderCancelRefund(params) {
        return post('/cancel-refund', params);
    },
    gameRegionServer(params) {
        return post('/game-region-server', params);
    },
    gameLevelingTypes(params) {
        return post('/game-leveling-types', params);
    },
    //　获取代练等级
    gameLevelingLevels(params) {
        return post('/game-leveling-levels', params);
    },
    // 计算代练价格与时间
    gameLevelingAmountTime(params) {
        return post('/game-leveling-amount-time', params);
    },
    // 创建订单
    gameLevelingChannelOrderCreate(params) {
        return post('/store', params);
    },
    GameLevelingChannelOrderApplyRefund(params) {
        return post('/apply-refund', params);
    },
    GameLevelingChannelOrderShow(params) {
        return post('/show', params);
    }
}