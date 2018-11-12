import { post, get } from './axios'

// 获取订单数据
export default {
    login(params) {
        return post('/login', params);
    },
    register(params) {
        return post('/register', params);
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
    // 游戏代练 订单数据
    gameLevelingOrder(params) {
        return post('/v2/order/game-leveling', params);
    },
    // 游戏代练订单 操作日志
    gameLevelingOrderLog(params) {
        return post('/v2/order/game-leveling/log', params);
    },
    // 游戏代练订单 编辑
    gameLevelingOrderEdit(params) {
        return post('/v2/order/game-leveling/edit', params);
    },
    // 游戏代练订单 详情
    gameLevelingOrderShow(params) {
        return post('/v2/order/game-leveling/show', params);
    },
    // 游戏代练订单 状态数量
    gameLevelingOrderStatusQuantity(params) {
        return post('/v2/order/game-leveling/status-quantity', params);
    },
    // 游戏代练订单 发送消息
    gameLevelingOrderSendMessage() {
        return post('/v2/order/game-leveling/status-quantity', params);
    },
    // 游戏代练订单 上架
    gameLevelingOrderOnSale() {
        return post('/v2/order/game-leveling/on-sale', params);
    },
    // 游戏代练订单 下架
    gameLevelingOrderOffSale() {
        return post('/v2/order/game-leveling/off-sale', params);
    },
    // 游戏代练订单 撤单
    gameLevelingOrderDelete() {
        return post('/v2/order/game-leveling/delete', params);
    },
    // 游戏代练订单 申请仲裁
    gameLevelingOrderApplyComplain() {
        return post('/v2/order/game-leveling/apply-complain', params);
    },
    // 游戏代练订单 取消仲裁
    gameLevelingOrderCancelComplain() {
        return post('/v2/order/game-leveling/cancel-complain', params);
    },
    // 游戏代练订单 申请协商
    gameLevelingOrderApplyConsult() {
        return post('/v2/order/game-leveling/apply-consult', params);
    },
    // 游戏代练订单 取消协商
    gameLevelingOrderCancelConsult() {
        return post('/v2/order/game-leveling/cancel-consult', params);
    },
    // 游戏代练订单 同意协商
    gameLevelingOrderAgreeConsult() {
        return post('/v2/order/game-leveling/agree-consult', params);
    },
    // 游戏代练订单 锁定
    gameLevelingOrderLock() {
        return post('/v2/order/game-leveling/lock', params);
    },
    // 游戏代练订单 取消锁定
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
    // 添加代练订单 增加价格
    gameLevelingOrderAddAmount() {
        return post('/v2/order/game-leveling/add-amount', params);
    },
    // 添加代练订单 增加天数小时
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
    // 游戏代练 商户投诉订单
    businessmanComplain(params) {
        return post('/v2/order/game-leveling/businessman-complain', params);
    },
    // 游戏代练 商户投诉订单状态数量
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
    },
    // 账号-实名认证
    AccountAuthenticationForm(params) {
        return post('/v2/account/authentication-form', params)
    },
    AccountAuthenticationUpdate(params) {
        return post('/v2/account/authentication-update', params)
    },
    AccountAuthenticationAdd(params) {
        return post('/v2/account/authentication-add', params)
    },
    AccountAuthenticationUpload(params) {
        return post('/v2/account/authentication-upload', params)
    },
    // 账号-黑名单
    AccountBlackListName(params) {
        return post('/v2/account/black-list-name', params)
    },
    AccountBlackListUpdate(params) {
        return post('/v2/account/black-list-update', params)
    },
    AccountBlackListDelete(params) {
        return post('/v2/account/black-list-delete', params)
    },
    AccountBlackListAdd(params) {
        return post('/v2/account/black-list-add', params)
    },
    AccountBlackListDataList(params) {
        return post('/v2/account/black-list-data-list', params)
    },
    // 账号-员工管理
    AccountEmployeeUser(params) {
        return post('/v2/account/employee-user', params)
    },
    AccountEmployeeStation(params) {
        return post('/v2/account/employee-station', params)
    },
    AccountEmployeeSwitch(params) {
        return post('/v2/account/employee-switch', params)
    },
    AccountEmployeeUpdate(params) {
        return post('/v2/account/employee-update', params)
    },
    AccountEmployeeDelete(params) {
        return post('/v2/account/employee-delete', params)
    },
    AccountEmployeeAdd(params) {
        return post('/v2/account/employee-add', params)
    },
    AccountEmployeeDataList(params) {
        return post('/v2/account/employee-data-list', params)
    },
    // 账号-登录记录
    AccountLoginHistoryDataList(params) {
        return post('/v2/account/login-history-data-list', params)
    },
    // 账号-岗位管理
    AccountStationPermission(params) {
        return post('/v2/account/station-permission', params)
    },
    AccountStationUpdate(params) {
        return post('/v2/account/station-update', params)
    },
    AccountStationDelete(params) {
        return post('/v2/account/station-delete', params)
    },
    AccountStationAdd(params) {
        return post('/v2/account/station-add', params)
    },
    AccountStationDataList(params) {
        return post('/v2/account/station-data-list', params)
    },
    // 账号-我的账号
    AccountMineUpdate(params) {
        return post('/v2/account/mine-update', params)
    },
    AccountMineForm(params) {
        return post('/v2/account/mine-form', params)
    },
    // 财务-资金流水
    FinanceAmountFlowDataList(params) {
        return post('/v2/finance/amount-flow-data-list', params)
    },
    // 财务-我的资金
    FinanceMyAssetDataList(params) {
        return post('/v2/finance/my-asset-data-list', params)
    },
    // 财务-资金日报
    FinanceDailyAssetDataList(params) {
        return post('/v2/finance/daily-asset-data-list', params)
    },
    // 财务-财务订单统计
    FinanceOrderDataList(params) {
        return post('/v2/finance/order-data-list', params)
    },
    FinanceGame(params) {
        return post('/v2/finance/game', params)
    },
    // 财务-我的提现
    FinanceWithdrawDataList(params) {
        return post('/v2/finance/withdraw-data-list', params)
    },
    FinanceWithdrawAdd(params) {
        return post('/v2/finance/withdraw-add', params)
    },
    FinanceWithdrawCan(params) {
        return post('/v2/finance/withdraw-can', params)
    },
    // 财务-员工统计
    StatisticEmployeeDataList(params) {
        return post('/v2/finance/withdraw-can', params)
    },
    StatisticEmployeeUser(params) {
        return post('/v2/finance/withdraw-can', params)
    },
    // 财务-短信统计
    StatisticEmployeeDataList(params) {
        return post('/v2/statistic/employee-data-list', params)
    },
    StatisticEmployeeUser(params) {
        return post('/v2/statistic/employee-user', params)
    },
    // 财务-订单统计
    StatisticOrderDataList(params) {
        return post('/v2/statistic/order-data-list', params)
    },
    // 财务-短信统计
    StatisticMessageDataList(params) {
        return post('/v2/statistic/message-data-list', params)
    },
    StatisticMessageShow(params) {
        return post('/v2/statistic/message-show', params)
    },
    StatisticMessageShowDataList(params) {
        return post('/v2/statistic/message-show-data-list', params)
    },
    // 设置-店铺授权
    SettingAuthorizeDataList(params) {
        return post('/v2/setting/authorize-data-list', params)
    },
    SettingAuthorizeUrl(params) {
        return post('/v2/setting/authorize-url', params)
    },
    SettingAuthorizeDelete(params) {
        return post('/v2/setting/authorize-delete', params)
    },
    // 设置-辅助
    SettingMarkupDataList (params) {
        return post('/v2/setting/markup-data-list', params)
    },
    SettingMarkupAdd (params) {
        return post('/v2/setting/markup-add', params)
    },
    SettingMarkupUpdate (params) {
        return post('/v2/setting/markup-update', params)
    },
    SettingMarkupDelete (params) {
        return post('/v2/setting/markup-delete', params)
    },
    SettingChannelDataList (params) {
        return post('/v2/setting/channel-data-list', params)
    },
    SettingChannelSwitch (params) {
        return post('/v2/setting/channel-switch', params)
    },
    // 设置-商品配置
    SettingGoodsGame (params) {
        return post('/v2/setting/goods-game', params)
    },
    SettingGoodsAdd (params) {
        return post('/v2/setting/goods-add', params)
    },
    SettingGoodsUpdate (params) {
        return post('/v2/setting/goods-update', params)
    },
    SettingGoodsDelete (params) {
        return post('/v2/setting/goods-delete', params)
    },
    SettingGoodsDataList (params) {
        return post('/v2/setting/goods-data-list', params)
    },
    SettingGoodsDelivery (params) {
        return post('/v2/setting/goods-delivery', params)
    },
    SettingGoodsSellerNick (params) {
        return post('/v2/setting/goods-seller-nick', params)
    },
    // 设置-短信管理
    SettingMessageDataList (params) {
        return post('/v2/setting/message-data-list', params)
    },
    SettingMessageUpdate (params) {
        return post('/v2/setting/message-update', params)
    },
    SettingMessageStatus (params) {
        return post('/v2/setting/message-status', params)
    },
    // 获取极验参数
    captcha() {
        return get('/captcha/geetest?t=' + (new Date()).getTime());
    }
}