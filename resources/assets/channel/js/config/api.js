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
    getGames() {
        return post('/channel/game')
    },
    GameLevelingChannelOrderApplyRefund(params) {
        return post('/channel/apply-refund', params)
    },
    GameLevelingChannelOrderApplyRefundShow(params) {
        return post('/channel/apply-refund-show', params)
    }
}