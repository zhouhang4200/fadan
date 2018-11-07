import { post } from './axios'

// 获取订单数据
export default {
    login(params) {
        return post('/v2/order/game-leveling/data-list', params);
    },
    // 游戏
    games(params) {
        return post('/v2/games', params);
    },
    // 游戏区服
    gameRegionServer(params) {
        return post('/v2/game-region-server', params);
    },
    // 游戏代练类型
    gameLevelingTypes(params) {
        return post('/v2/game-leveling-types', params);
    },
    // 游戏代练订单下单
    gameLevelingOrderCreate(params) {
        return post('/v2/game-leveling/store', params);
    },
    // 游戏代练订单数据
    gameLevelingOrder(params) {
        return post('/v2/order/game-leveling', params);
    },
    // 游戏代练订单操作日志
    gameLevelingOrderLog(params) {
        return post('/v2/order/game-leveling/log', params);
    },
    // 游戏代练订单编辑
    gameLevelingOrderEdit(params) {
        return post('/v2/order/game-leveling/edit', params);
    },
    // 游戏代练订单详情
    gameLevelingOrderShow(params) {
        return post('/v2/order/game-leveling/show', params);
    },
    // 游戏代练订单状态数量
    gameLevelingOrderStatusQuantity(params) {
        return post('/v2/order/game-leveling/status-quantity', params);
    },
    // 游戏代练订单 发送消息
    gameLevelingOrderSendMessage() {
        return post('/v2/order/game-leveling/status-quantity', params);
    },
    // 游戏代练订单上架
    gameLevelingOrderOnSale() {
        return post('/v2/order/game-leveling/on-sale', params);
    },
    // 游戏代练订单下架
    gameLevelingOrderOffSale() {
        return post('/v2/order/game-leveling/off-sale', params);
    },
    // 游戏代练订单 撤单
    gameLevelingOrderDelete() {
        return post('/v2/order/game-leveling/delete', params);
    },
    // 游戏代练订单取消仲裁
    gameLevelingOrderCancelComplain() {
        return post('/v2/order/game-leveling/cancel-complain', params);
    },
    // 游戏代练订单取消协商
    gameLevelingOrderCancelConsult() {
        return post('/v2/order/game-leveling/cancel-consult', params);
    },
    // 游戏代练订单同意协商
    gameLevelingOrderAgreeConsult() {
        return post('/v2/order/game-leveling/agree-consult', params);
    },
    // 游戏代练订单锁定
    gameLevelingOrderLock() {
        return post('/v2/order/game-leveling/lock', params);
    },
    // 游戏代练订单取消锁定
    gameLevelingOrderCancelLock() {
        return post('/v2/order/game-leveling/cancel-lock', params);
    },
    // 游戏代练订单 仲裁信息
    gameLevelingOrderComplainInfo() {
        return post('/v2/order/game-leveling/complain-info', params);
    },
    // 游戏代练订单 添加仲裁信息
    gameLevelingOrderAddComplainInfo() {
        return post('/v2/order/game-leveling/add-complain-info', params);
    },
    // 游戏代练订单 拒绝协商
    gameLevelingOrderRejectConsult() {
        return post('/v2/order/game-leveling/reject-consult', params);
    },
    // 游戏代练订单 申请验收图片
    gameLevelingOrderApplyCompleteImage() {
        return post('/v2/order/game-leveling/apply-complete-image', params);
    },
    gameLevelingOrderBusinessmanComplainStore() {
        return post('/v2/order/game-leveling/status-quantity', params);
    },
    // 添加代练价格
    gameLevelingOrderAddAmount() {
        return post('/v2/order/game-leveling/add-amount', params);
    },
    // 添加代练天数小时
    gameLevelingOrderAddDayHour() {
        return post('/v2/order/game-leveling/add-day-hour', params);
    },
    // 游戏代练订单消息
    gameLevelingOrderMessage() {
        return post('/v2/order/game-leveling/message', params);
    },
    // 游戏代练 订单完成验收
    gameLevelingOrderComplete() {
        return post('/v2/order/game-leveling/complete', params);
    },
    // 游戏代练 淘宝订单
    gameLevelingOrdertTaobaoOrder(params) {
        return post('/v2/order/game-leveling/taobao-order', params)
    },
    // 游戏代练商户投诉订单
    businessmanComplain(params) {
        return post('/v2/order/game-leveling/businessman-complain', params);
    },
    // 游戏代练商户投诉订单状态数量
    businessmanComplainStatusQuantity(params) {
        return post('/v2/order/game-leveling/businessman-complain/status-quantity', params);
    },
    // 游戏代练商户投诉订单状态数量
    businessmanComplainImage(params) {
        return post('/v2/order/game-leveling/businessman-complain/images', params);
    },
    // 游戏代练商户投诉订单状态数量
    businessmanComplainCancel(params) {
        return post('/v2/order/game-leveling/businessman-complain/cancel', params);
    },
    // 淘宝订单（待发订单）列表
    taobaoOrder(params) {
        return post('/v2/order/game-leveling/taobao', params);
    },
    // 淘宝订单（待发订单）详情
    taobaoOrderShow(params) {
        return post('/v2/order/game-leveling/taobao/show', params);
    },
    // 淘宝订单 待发订单）状态数量
    taobaoOrderStatusQuantity(params) {
        return post('/v2/order/game-leveling/taobao/status-quantity', params);
    }
}