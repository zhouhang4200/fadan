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
    GameLevelingChannelOrderDelete(params) {
        return post('/channel/delete', params)
    },
    GameLevelingChannelOrderCancelRefund(params) {
        return post('/channel/cancel-refund', params)
    }
}