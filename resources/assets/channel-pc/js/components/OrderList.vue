<template>
    <div class="order-list">

        <div class="menu box-shadow">
            <ul>
                <li>我的订单</li>
            </ul>
        </div>

        <div class="list box-shadow">
            <div class="title">
                <span class="fr">有问题? <span style="color: #409EFF">联系客服</span> </span>
                <div class="clear-float"></div>
            </div>

            <div class="search">
                <el-form :inline="true" :model="searchParams" class="demo-form-inline">

                    <el-form-item label="筛选">
                        <el-select
                                v-model="searchParams.game_id"
                                placeholder="游戏">
                            <el-option
                                    label="所有游戏"
                                    value="0">
                            </el-option>
                            <el-option
                                    v-for="item in gameOptions"
                                    :key="item.id"
                                    :label="item.text"
                                    :value="item.id">
                            </el-option>
                        </el-select>

                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="searchParams.trade_no" placeholder="订单号"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="onClickSearch">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>

            <el-tabs v-model="searchParams.status" type="border-card" @tab-click="onClickSearch">
                <el-tab-pane label="全部" name="0"></el-tab-pane>
                <el-tab-pane label="进行中" name="2"></el-tab-pane>
                <el-tab-pane label="待收货" name="3"></el-tab-pane>
                <el-tab-pane label="已完成" name="4"></el-tab-pane>
                <el-tab-pane label="退款中" name="5"></el-tab-pane>
                <el-tab-pane label="已退款" name="6"></el-tab-pane>
            </el-tabs>

            <div class="table">
                <div class="title">
                    <div class="table-th">
                        <div class="table-warp">
                            <div class="fl w40">订单信息</div>
                            <div class="fl w20">价格</div>
                            <div class="fl w20">状态</div>
                            <div class="fl w20">操作</div>
                            <div class="clear-float"></div>
                        </div>
                    </div>
                </div>

                <div class="table-row"
                     v-for="item in list" :key="item.id">
                    <div class="row-title">
                        <div class="table-warp">
                            <div class="fl">订单号: {{ item.trade_no }}</div>
                            <div class="fr">下单时间: {{ item.created_at }}</div>
                            <div class="clear-float"></div>
                        </div>
                    </div>
                    <div class="row-content">
                        <div class="table-warp">
                            <div class="fl w40">
                                <div class="">
                                    <div class="fl">
                                        <img src="/channel-pc/images/wz.png" style="height: 30px; border-radius: 50%">
                                    </div>
                                    <span style="line-height: 30px;height: 30px;padding-left: 5px">{{ item.title }}</span>
                                    <div class="clear-float"></div>
                                </div>
                                <div class="" style="color:#AEAEAE;font-size: 11px">代练游戏: {{ item.game_name }}</div>
                                <div class="" style="color:#AEAEAE;font-size: 11px">区服信息: {{ item.game_region_name }}/{{
                                    item.game_server_name }}
                                </div>
                            </div>
                            <div class="fl w20" style="height: 78px;line-height: 78px;">{{ item.amount }}元</div>
                            <div class="fl w20" :style="tableRowLineHeight(item.status)">

                                <div v-if="item.status === 3">
                                    <div style="padding-top: 10px">{{ status[item.status] }}</div>
                                    <div style="font-size: 11px;color: #9A9A9A">距离自动确认</div>
                                    <div style="margin:0 auto;font-weight: normal;background-color: #409EFF;border-radius: 10px;width: 45%;height: 20px;line-height: 20px;font-size: 9px;color:#fff;">
                                        <i class="el-icon-time"></i>还有两小时
                                    </div>
                                </div>

                                <div v-else>{{ status[item.status] }}</div>

                            </div>
                            <div class="fl w20" style="height: 78px;line-height: 78px;">
                                <el-button v-if="item.status === 2" size="small" @click="onClickApplyRefund(item)">申请退款
                                </el-button>

                                <el-popover
                                        v-if="item.status == 3"
                                        placement="top"
                                        width="160"
                                        v-model="confirmCompleteVisible">
                                    <p>您确认收货吗？</p>
                                    <div style="text-align: right; margin: 0">
                                        <el-button size="mini" type="text" @click="confirmCompleteVisible = false">取消</el-button>
                                        <el-button type="primary" size="mini" @click="onClickConfirmComplete(item.trade_no)">确定</el-button>
                                    </div>
                                    <el-button size="small" slot="reference">确认收货</el-button>
                                </el-popover>
                                <el-button v-if="item.status === 3" size="small" @click="onClickApplyRefund(item)">申请退款</el-button>

                                <div v-if="item.status === 5">
                                    <el-popover
                                            placement="top"
                                            width="160"
                                            v-model="confirmCancelRefundPopoverVisible">
                                        <p>您确定取消退款吗？</p>
                                        <div style="text-align: right; margin: 0">
                                            <el-button size="mini" type="text" @click="confirmCancelRefundPopoverVisible = false">取消</el-button>
                                            <el-button type="primary" size="mini" @click="onClickCancelRefund(item.trade_no)">确定</el-button>
                                        </div>
                                        <el-button size="small" slot="reference">取消退款</el-button>
                                    </el-popover>
                                    <el-button size="small" @click="onClickShowRefund(item.trade_no)">查看退款</el-button>
                                </div>

                                <el-button v-if="item.status === 6" size="small" @click="onClickShowRefund(item.trade_no)">查看退款</el-button>
                            </div>
                            <div class="clear-float"></div>
                        </div>
                    </div>
                </div>

                <div class="clear-float"></div>
            </div>
        </div>

        <el-dialog
                title="申请退款"
                :visible.sync="applyRefundDialogVisible"
                width="30%"
                top="15vh"
                :before-close="handleClose">
            <div class="" style="">
                <table style="border: 1px solid #efefef;border-spacing: 0;width: 100%;font-size: 11px">
                    <tr style="height: 25px;background-color: #efefef;">
                        <td width="30%" style="padding-left: 5px">代练游戏/区服</td>
                        <td width="40%">代练目标</td>
                        <td width="30%">订单编号</td>
                    </tr>
                    <tr style="height: 25px;">
                        <td style="padding-left: 5px">{{ refundForm.game_name }}/{{ refundForm.game_region_name }}/{{ refundForm.game_server_name }}</td>
                        <td>{{ refundForm.title }}</td>
                        <td style="padding-right: 5px">{{ refundForm.trade_no }}</td>
                    </tr>
                </table>
                <el-form label-position="top" ref="refundForm" :rules="refundFormRules" :model="refundForm" label-width="80px"
                         style="margin-top: 20px">
                    <el-form-item label="退款金额" prop="refund_amount" class="refund-amount">
                        <el-radio v-model="refundForm.type" label="1">全额退款</el-radio>
                        <el-radio v-model="refundForm.type" label="2">部分退款
                            <el-input  v-model="refundForm.refund_amount" style="width: 60%"></el-input>
                        </el-radio>
                    </el-form-item>

                    <el-form-item label="问题截图" style="margin-bottom: 0;">
                        <el-upload
                                action="https://jsonplaceholder.typicode.com/posts/"
                                list-type="picture-card">
                            <i class="el-icon-plus"></i>
                        </el-upload>
                        <!--<el-dialog :visible.sync="dialogVisible">-->
                        <!--<img width="100%" :src="dialogImageUrl" alt="">-->
                        <!--</el-dialog>-->
                        <div style="margin-top: 10px;color:#959595">
                            <i class="el-icon-warning" style="color: #FF8900"></i>
                            如果你对服务存在异议,请提交问题截图
                        </div>
                    </el-form-item>

                    <el-form-item label="退款原因" prop="refund_reason">
                        <el-input
                                type="textarea"
                                rows="3"
                                v-model="refundForm.refund_reason">
                        </el-input>
                    </el-form-item>
                </el-form>

            </div>
            <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="onClickConfirmApplyRefund">确定提交</el-button>
        </span>
        </el-dialog>

        <el-dialog
                class="step"
                title="查看退款"
                :visible.sync="showRefundDialogVisible"
                width="30%"
                top="15vh"
                :before-close="handleClose">
            <div style="">
                <table style="border: 1px solid #efefef;border-spacing: 0;width: 100%;font-size: 11px">
                    <tr style="height: 25px;background-color: #efefef;">
                        <td width="30%" style="padding-left: 5px">代练游戏/区服</td>
                        <td width="40%">代练目标</td>
                        <td width="30%">订单编号</td>
                    </tr>
                    <tr style="height: 25px;">
                        <td style="padding-left: 5px">{{ refundInfo.game_name }}/{{ refundInfo.game_region_name }}{{ refundInfo.game_server_name }}</td>
                        <td>{{ refundInfo.title }}</td>
                        <td style="padding-right: 5px">{{ refundInfo.trade_no }}</td>
                    </tr>
                </table>

                <div style="margin-top: 20px;">
                    <div class="" style="height: 30px;line-height: 30px;padding-bottom: 10px;">协商历史</div>
                    <div style="padding:20px 20px 10px 10px;height: 150px;overflow-x:auto;border: 1px solid #c0c4cc;background-color: #F2F2F2;">
                        <el-steps direction="vertical" :active="2" >

                            <template v-for="item in refundInfo.game_leveling_channel_refund">
                                <el-step v-if="item.status === 3" title="对方 拒绝本次退款申请">
                                    <div class="" slot="description">
                                        原因: {{ item.refuse_refund_reason }} <span class="fr">{{ item.updated_at }}</span>
                                    </div>
                                </el-step>

                                <el-step title="我 发起退款申请">
                                    <div class="" slot="description">
                                        <span>查看截图</span> 申请退款金额: {{ item.refund_amount }} <span class="fr">{{ item.created_at }}</span>
                                    </div>
                                </el-step>

                            </template>

                        </el-steps>
                    </div>
                </div>

            </div>
        </el-dialog>

    </div>
</template>

<script>
    export default {
        name: "OrderList",
        data() {
            return {
                refundStatus:false,
                confirmCancelRefundPopoverVisible: false,
                confirmCompleteVisible: false,
                showRefundDialogVisible: false,
                applyRefundDialogVisible: false,
                gameOptions: [],
                status: {
                    2: '进行中',
                    3: '待收货',
                    4: '完成',
                    5: '退款中',
                    6: '已退款',
                },
                searchParams: {
                    status: "0",
                    trade_no: "",
                },
                list: [],
                loading: false,
                noData: false,
                refundForm: {
                    game_name: '',
                    game_region_name: '',
                    game_server_name: '',
                    payment_amount: '',
                    trade_no: '',
                    type: '1',
                    refund_amount: '',
                    images:[],
                    refund_reason:'',
                },
                refundFormRules: {
                    refund_amount: [
                        {
                            validator:(rule, value, callback)=>{
                                if(this.refundForm.type === '2' && value === '') {
                                    callback(new Error("请输入需要退款的金额"));
                                } else if(this.refundForm.type === '2' && value > this.refundForm.payment_amount){
                                    callback(new Error("退款金额不可大于订单金额"));
                                } else if(this.refundForm.type === '2' && value <= 0){
                                    callback(new Error("退款金额需大于0"));
                                } else {
                                    callback();
                                }
                            },
                            trigger:'blur'
                        },
                    ],
                    refund_reason: [
                        { required: true, message: '请输入退款原因', trigger: 'blur' },
                    ],
                },
                refundInfo: {
                    trade_no: '',
                    title: '',
                    game_name: '',
                    game_region_name: '',
                    game_server_name: '',
                    game_leveling_channel_refund:[]
                },
            };
        },

        mounted() {
            this.getList();
            this.getGameOptions();
        },

        methods: {
            // 列表数据
            getList() {
                this.$api.GameLevelingChannelOrderList(this.searchParams).then(res => {
                    this.noData = res.length == 0 ? true : false;
                    this.list = res;
                    this.loading = false;
                    this.finished = true;
                }).catch(err => {

                });
            },
            // 获取游戏选项
            getGameOptions() {
                this.$api.games().then(res => {
                    this.gameOptions = res.content;
                });
            },
            tableRowLineHeight(status) {
                if (status == 3) {
                    return {
                        textAlign: 'center',
                        height: '78px',
                        lineHeight: '22px',
                    };
                } else {
                    return {
                        textAlign: 'center',
                        height: '78px',
                        lineHeight: '78px',
                    };
                }
            },

            handleClose(done) {
                done();
            },
            // 搜索
            onClickSearch() {
                this.getList();
            },
            // 完成
            onClickComplete(item) {
                this.$api.GameLevelingChannelOrderComplete({
                    trade_no: item.trade_no,
                }).then(res => {
                    this.confirmCancelRefundPopoverVisible = false;
                    this.getList();
                }).catch(err => {

                });
            },
            // 申请退款
            onClickApplyRefund(item) {
                console.log(item);
                this.refundForm.title = item.title;
                this.refundForm.trade_no = item.trade_no;
                this.refundForm.game_name = item.game_name;
                this.refundForm.game_region_name = item.game_region_name;
                this.refundForm.game_server_name = item.game_server_name;
                this.refundForm.trade_no = item.trade_no;
                this.refundForm.payment_amount = item.payment_amount;
                this.applyRefundDialogVisible = true
            },
            // 确认申请退款
            onClickConfirmApplyRefund() {
                this.$refs.refundForm.validate((valid) => {
                    if (valid) {
                        this.$api.GameLevelingChannelOrderApplyRefund(this.refundForm).then(res => {
                            if (res.status === 1) {
                                this.applyRefundDialogVisible = false;
                                this.getList();
                            } else {
                                this.$toast.fail(res.message);
                            }
                        }).catch(err => {

                        });
                    }
                });
            },
            // 取消退款
            onClickCancelRefund(tradeNo) {
                this.$api.GameLevelingChannelOrderCancelRefund({
                    trade_no: tradeNo,
                }).then(res => {
                    this.confirmCancelRefundPopoverVisible = false;
                    this.getList();
                }).catch(err => {

                });
            },
            // 查看退款
            onClickShowRefund(tradeNo) {
                this.showRefundDialogVisible = true;
                // 获取订单信息
                this.$api.GameLevelingChannelOrderShow({
                    trade_no:tradeNo
                }).then(res => {
                    this.refundInfo = res;
                }).catch(err => {

                });
            },
            // 确认完成
            onClickConfirmComplete(tradeNo) {
                this.$api.GameLevelingChannelOrderComplete({
                    trade_no:tradeNo,
                }).then(res => {
                    this.getList();
                }).catch(err => {
                });
            }
        }
    }
</script>

<style lang="less">
    .order-list {
        .el-tabs--border-card {
            border: none;
            box-shadow: none;
            -webkit-box-shadow: none;
        }
        .el-tabs__content {
            padding: 10px;
        }
        .el-tabs__item {
            padding: 0 25px;
        }
        .el-tabs--border-card > .el-tabs__header .el-tabs__item {
            border: none;
            margin: 0;
            border-top: 2px solid #f5f7fa;
            height: 45px;
            line-height: 45px;
        }
        .el-tabs--border-card > .el-tabs__header .el-tabs__item.is-active {
            border-top: 2px solid #409EFF;

        }
        .el-tabs--border-card > .el-tabs__header {
            border-bottom: none;
        }
        .el-dialog__header {
            border-bottom: 1px solid #efefef;
        }
        .el-dialog__body {
            padding: 15px 20px 0;
        }

        .el-radio__input.is-checked .el-radio__inner:after {
            transform: rotate(45deg) scaleY(1);
        }
        .el-radio__inner:after {
            border-radius: 0;
            background-color: transparent;
            box-sizing: content-box;
            content: "";
            border: 1px solid #fff;
            border-left: 0;
            border-top: 0;
            height: 7px;
            left: 4px;
            position: absolute;
            top: 1px;
            transform: rotate(45deg) scaleY(0);
            width: 3px;
            transition: transform .15s ease-in .05s;
            transform-origin: center;
        }
        .refund-amount .el-form-item__error {
            margin-left: 200px;
        }
        /*重写上传框*/
        .el-upload--picture-card {
            width: 100px;
            height: 100px;
            line-height: 100px;
        }
        /*重写单选项*/
        .el-radio {
            font-weight: normal
        }
        .el-radio__input.is-checked + .el-radio__label {
            color: #606266;
        }
        /*重选步骤*/
        .el-step__icon {
            left: 5px;
            width: 12px;
            height: 12px;
            font-size: 12px;
            top: -3px;
            background-color: #C0C4CC;
            color: #C0C4CC;
        }
        .el-step__icon.is-text {
            border: 1px solid;
        }
        .el-step__head {
            top: 6px;
        }
        .el-step.is-vertical .el-step__line {
            width: 1px;
        }

        .step {
            .el-dialog__body {
                padding: 20px;
            }
            .el-step__title {
                font-size: 12px;
            }
            .el-step.is-vertical .el-step__title {
                padding-bottom: 0;
            }
            .el-step__main {
                margin-bottom: 10px
            }
            .el-step__title.is-wait,
            .el-step__description.is-wait,
            .el-step__title.is-process,
            .el-step__description.is-finish,
            .el-step__description.is-process,
            .el-step__title.is-finish {
                color: #606266;
            }
            .el-step__line-inner {
                border: none;
            }
            .el-step__head.is-finish {
                color: #409EFF;
                border-bottom-width: 1px;
                border-color: #606266;
            }
            .el-step__title.is-process {
                font-weight: normal;
            }
            .el-step__description {
                padding-right: 0;
            }
            .el-step.is-vertical {
                display: -ms-flexbox;
                display: -webkit-box;
            }
        }

        .menu {
            height: 50px;
            line-height: 50px;
            background-color: #fff;
            padding: 0 15px;
        }
        .list {
            padding: 0 15px 15px;
            margin: 15px 0;
            background-color: #fff;
            .title {
                border-bottom: 1px solid #efefef;
                span {
                    height: 40px;
                    line-height: 40px;
                }
            }
            .search {
                height: 66px;
                padding-top: 30px;
            }
            .table {
                /*border: 1px solid #efefef;*/
                min-height: 600px;
                .table-warp {
                    padding: 0 15px;
                }
                .table-th {
                    height: 40px;
                    line-height: 40px;
                    background-color: #3d9fff;
                    color: #fff;
                }
                .w20 {
                    width: 20%;
                    text-align: center;
                }
                .w15 {
                    width: 15%;
                    text-align: center;
                }
                .w40 {
                    width: 40%;
                }

                .table-row {
                    border: 1px solid #efefef;
                    .row-title {
                        height: 35px;
                        line-height: 35px;
                        color: #0b0b0b;
                        background-color: #F2F2F2;
                    }
                    .row-content {
                        padding: 20px 0;
                        color: #0b0b0b;
                    }
                }

            }
        }
    }

</style>