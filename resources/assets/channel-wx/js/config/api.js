import { post, get } from './axios'

// 获取订单数据
export default {
    // 渠道订单
    GameLevelingChannelOrderList(params) {
        return post('/channel/order-list', params)
    },
    GameLevelingChannelOrderComplete(params) {
        return post('/channel/complete', params)
    },
    GameLevelingChannelOrderCancelRefund(params) {
        return post('/channel/cancel-refund', params)
    },
    games() {
        return post('/channel/games')
    },
    gameRegions(params) {
        return post('/channel/game-regions', params)
    },
    gameServers(params) {
        return post('/channel/game-servers', params)
    },
    gameLevelingTypes(params) {
        return post('/channel/game-leveling-types', params)
    },
    //　获取代练等级
    gameLevelingLevels(params) {
        return post('/channel/game-leveling-levels', params)
    },
    // 计算代练价格与时间
    gameLevelingAmountTime(params) {
        return post('/channel/game-leveling-amount-time', params)
    },
    // 创建订单
    gameLevelingChannelOrderCreate(params) {
        return post('/channel/store', params)
    },
    GameLevelingChannelOrderApplyRefund(params) {
        return post('/channel/apply-refund', params)
    },
    GameLevelingChannelOrderShow(params) {
        return post('/channel/show', params)
    }
}