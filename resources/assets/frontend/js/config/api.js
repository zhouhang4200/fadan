import { post } from './axios'

// 获取订单数据
export default {
    login(params) {
        return post('/v2/order/game-leveling/data-list', params)
    },
    // 游戏
    games(params) {
        return post('/v2/games', params)
    },
    // 游戏区服
    gameRegionServer() {
        return post('/v2/game-region-server', params);
    },
    // 游戏代练类型
    gameLevelingTypes(params) {
        return post('/v2/game-leveling-types', params)
    },
    // 游戏代练订单数据
    gameLevelingOrderList(params) {
        return post('/v2/order/game-leveling/data-list', params)
    },
    // 游戏代练订单状态数量
    gameLevelingOrderStatusQuantity(params) {
        return post('/v2/order/game-leveling/status-quantity', params)
    },
}