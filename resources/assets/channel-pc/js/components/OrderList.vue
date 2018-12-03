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
                        <el-select v-model="searchParams.status" placeholder="活动区域">
                            <el-option label="区域一" value="shanghai"></el-option>
                            <el-option label="区域二" value="beijing"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="searchParams.status" placeholder="审批人"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>

            <el-tabs v-model="searchParams.status" type="border-card" @tab-click="handleClick">
                <el-tab-pane label="全部" name="1"></el-tab-pane>
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

                <div class="table-row">
                    <div class="row-title">
                        <div class="table-warp">
                            <div class="fl">订单号: 545454548785454</div>
                            <div class="fr">22222</div>
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
                                    <span style="line-height: 30px;height: 30px;padding-left: 5px">青铜</span>
                                    <div class="clear-float"></div>
                                </div>
                                <div class="" style="color:#AEAEAE;font-size: 11px">代练游戏: 王者荣耀</div>
                                <div class="" style="color:#AEAEAE;font-size: 11px">区服信息: IOS微信/安卓</div>
                            </div>
                            <div class="fl w20" style="height: 78px;line-height: 78px;">198元</div>
                            <div class="fl w20" :style="tableRowLineHeight('1')">
                                <div style="padding-top: 10px">待收货</div>
                                <div style="font-size: 11px;color: #9A9A9A">距离自动确认</div>
                                <div style="margin:0 auto;font-weight: normal;background-color: #409EFF;border-radius: 10px;width: 45%;height: 20px;line-height: 20px;font-size: 9px;color:#fff;">
                                    <i class="el-icon-time"></i>还有两小时
                                </div>
                            </div>
                            <div class="fl w20" style="height: 78px;line-height: 78px;">

                                <el-popover
                                        placement="top"
                                        width="160"
                                        v-model="visible2">
                                    <p>这是一段内容这是一段内容确定删除吗？</p>
                                    <div style="text-align: right; margin: 0">
                                        <el-button size="mini" type="text" @click="visible2 = false">取消</el-button>
                                        <el-button type="primary" size="mini" @click="visible2 = false">确定</el-button>
                                    </div>
                                    <el-button size="small" slot="reference">确认收货</el-button>
                                </el-popover>

                                <el-button size="small" @click="dialogVisible = true">申请退款</el-button>

                            </div>
                            <div class="clear-float"></div>
                        </div>
                    </div>
                </div>

                <div class="table-row">
                    <div class="row-title">
                        <div class="table-warp">
                            <div class="fl">订单号: 545454548785454</div>
                            <div class="fr">22222</div>
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
                                    <span style="line-height: 30px;height: 30px;padding-left: 5px">青铜</span>
                                    <div class="clear-float"></div>
                                </div>
                                <div class="" style="color:#AEAEAE;font-size: 11px">代练游戏: 王者荣耀</div>
                                <div class="" style="color:#AEAEAE;font-size: 11px">区服信息: IOS微信/安卓</div>
                            </div>
                            <div class="fl w20" style="height: 78px;line-height: 78px;">198元</div>
                            <div class="fl w20" :style="tableRowLineHeight('2')">
                                <div>还有两小时</div>
                            </div>
                            <div class="fl w20" style="height: 78px;line-height: 78px;">

                                <el-popover
                                        placement="top"
                                        width="160"
                                        v-model="visible2">
                                    <p>这是一段内容这是一段内容确定删除吗？</p>
                                    <div style="text-align: right; margin: 0">
                                        <el-button size="mini" type="text" @click="visible2 = false">取消</el-button>
                                        <el-button type="primary" size="mini" @click="visible2 = false">确定</el-button>
                                    </div>
                                    <el-button size="small" slot="reference">确认收货</el-button>
                                </el-popover>

                                <el-popover
                                        placement="top"
                                        width="160"
                                        v-model="visible2">
                                    <p>这是一段内容这是一段内容确定删除吗？</p>
                                    <div style="text-align: right; margin: 0">
                                        <el-button size="mini" type="text" @click="visible2 = false">取消</el-button>
                                        <el-button type="primary" size="mini" @click="visible2 = false">确定</el-button>
                                    </div>
                                    <el-button size="small" slot="reference">申请退款</el-button>
                                </el-popover>

                            </div>
                            <div class="clear-float"></div>
                        </div>
                    </div>
                </div>

                <div class="clear-float"></div>
            </div>
        </div>

        <!--<el-dialog-->
        <!--title="申请退款"-->
        <!--:visible.sync="dialogVisible"-->
        <!--width="30%"-->
        <!--top="15vh"-->
        <!--:before-close="handleClose">-->
        <!--<div class="" style="">-->
        <!--<table style="border: 1px solid #efefef;border-spacing: 0;width: 100%;font-size: 11px">-->
        <!--<tr style="height: 25px;background-color: #efefef;">-->
        <!--<td width="30%" style="padding-left: 5px">代练游戏/区服</td>-->
        <!--<td width="40%">代练目标</td>-->
        <!--<td width="30%">订单编号</td>-->
        <!--</tr>-->
        <!--<tr style="height: 25px;">-->
        <!--<td style="padding-left: 5px">代代练目标代练目s</td>-->
        <!--<td>s</td>-->
        <!--<td>s</td>-->
        <!--</tr>-->
        <!--</table>-->
        <!--<el-form label-position="top" ref="form" :model="refundForm" label-width="80px" style="margin-top: 20px">-->
        <!--<el-form-item label="退款金额">-->
        <!--<el-radio-group v-model="refundForm.amount">-->
        <!--<el-radio label="全额退款"></el-radio>-->
        <!--<el-radio label="部分退款">部分退款 <el-input v-model="refundForm.amount" style="width: 60%"></el-input></el-radio>-->
        <!--</el-radio-group>-->
        <!--</el-form-item>-->

        <!--<el-form-item label="问题截图" style="margin-bottom: 0;">-->
        <!--<el-upload-->
        <!--action="https://jsonplaceholder.typicode.com/posts/"-->
        <!--list-type="picture-card"-->
        <!--:on-preview="handlePictureCardPreview"-->
        <!--:on-remove="handleRemove">-->
        <!--<i class="el-icon-plus"></i>-->
        <!--</el-upload>-->
        <!--&lt;!&ndash;<el-dialog :visible.sync="dialogVisible">&ndash;&gt;-->
        <!--&lt;!&ndash;<img width="100%" :src="dialogImageUrl" alt="">&ndash;&gt;-->
        <!--&lt;!&ndash;</el-dialog>&ndash;&gt;-->
        <!--<div style="margin-top: 10px;color:#959595">-->
        <!--<i class="el-icon-warning" style="color: #FF8900"></i>-->
        <!--如果你对服务存在异议,请提交问题截图</div>-->
        <!--</el-form-item>-->


        <!--<el-form-item label="退款原因">-->
        <!--<el-input-->
        <!--type="textarea"-->
        <!--rows="3"-->
        <!--v-model="refundForm.amount">-->
        <!--</el-input>-->
        <!--</el-form-item>-->
        <!--</el-form>-->

        <!--</div>-->
        <!--<span slot="footer" class="dialog-footer">-->
        <!--<el-button type="primary" @click="dialogVisible = false" >确定提交</el-button>-->
        <!--</span>-->
        <!--</el-dialog>-->

        <el-dialog
                class="step"
                title="查看退款"
                :visible.sync="dialogVisible"
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
                        <td style="padding-left: 5px">代代练目标代练目s</td>
                        <td>s</td>
                        <td>s</td>
                    </tr>
                </table>


                <div style="margin-top: 20px;">
                    <div class="" style="height: 30px;line-height: 30px;padding-bottom: 10px;">协商历史</div>
                    <div style="padding:20px 20px 10px 10px;height: 150px;overflow-x:auto;border: 1px solid #c0c4cc;background-color: #F2F2F2;">
                        <el-steps direction="vertical" :active="2">
                            <el-step title="步骤" description="这是一段很长很长很长的描述性文字">
                                <div class="" slot="description">
                                    这是一段很长很长很长的描述性文字 <span class="fr">sdd</span>
                                </div>
                            </el-step>
                            <el-step title="步骤" description="这是一段很长很长很长的描述性文字">
                                <div class="" slot="description">
                                    这是一段很长很长很长的描述性文字 <span class="fr">sdd</span>
                                </div>
                            </el-step>
                            <el-step title="步骤" description="这是一段很长很长很长的描述性文字">
                                <div class="" slot="description">
                                    这是一段很长很长很长的描述性文字 <span class="fr">sdd</span>
                                </div>
                            </el-step>
                            <el-step title="步骤" description="这是一段很长很长很长的描述性文字">
                                <div class="" slot="description">
                                    这是一段很长很长很长的描述性文字 <span class="fr">sdd</span>
                                </div>
                            </el-step>
                            <el-step title="步骤" description="这是一段很长很长很长的描述性文字">
                                <div class="" slot="description">
                                    这是一段很长很长很长的描述性文字 <span class="fr">sdd</span>
                                </div>
                            </el-step>
                            <el-step title="步骤" description="这是一段很长很长很长的描述性文字">
                                <div class="" slot="description">
                                    这是一段很长很长很长的描述性文字 <span class="fr">sdd</span>
                                </div>
                            </el-step>
                            <el-step title="步骤" description="这是一段很长很长很长的描述性文字">
                                <div class="" slot="description">
                                    这是一段很长很长很长的描述性文字 <span class="fr">sdd</span>
                                </div>
                            </el-step>
                            <el-step title="步骤" description="这是一段很长很长很长的描述性文字">
                                <div class="" slot="description">
                                    这是一段很长很长很长的描述性文字 <span class="fr">sdd</span>
                                </div>
                            </el-step>
                            <el-step title="步骤" description="这是一段很长很长很长的描述性文字">
                                <div class="" slot="description">
                                    这是一段很长很长很长的描述性文字 <span class="fr">sdd</span>
                                </div>
                            </el-step>

                        </el-steps>
                    </div>
                </div>

            </div>
        </el-dialog>

    </div>
</template>

<script>
    import {Toast} from 'vant';

    export default {
        name: "Order",
        data() {
            return {
                dialogVisible: false,

                status: {
                    1: '待付款',
                    2: '进行中',
                    3: '待收货',
                    4: '完成',
                    6: '退款中',
                    7: '已退款',
                },
                searchParams: {
                    status: "1"
                },
                list: [],
                loading: false,
                noData: false,
                refundForm: {
                    amount: 0,
                }
            };
        },

        mounted() {
            this.onLoadData();
        },

        methods: {
            tableRowLineHeight(status) {
                if (status === '1') {
                    return {
                        textAlign: 'center',
                        height: '78px',
                        lineHeight: '22px',
                    };
                } else if (status === '2') {
                    return {
                        textAlign: 'center',
                        height: '78px',
                        lineHeight: '78px',
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
                this.$confirm('确认关闭？')
                    .then(_ => {
                        done();
                    })
                    .catch(_ => {
                    });
            },

            // 详情页
            orderShow(item) {
                this.$router.push({name: 'orderDetail', query: {trade_no: item.trade_no, user_id: item.user_id}})
            },
            // 申请退款按钮
            applyRefund(item) {
                this.$router.push({name: 'orderRefund', query: {trade_no: item.trade_no, user_id: item.user_id}})
            },
            onClickLeft() {
                this.$router.push({path: '/channel/order'})
            },
            // 完成
            complete(item) {
                this.$api.GameLevelingChannelOrderComplete({
                    user_id: item.user_id,
                    trade_no: item.trade_no,
                    game_leveling_channel_user_id: item.game_leveling_channel_user_id
                }).then(res => {
                    if (res.status === 1) {
                        Toast.success(res.message);
                    } else {
                        Toast.fail(res.message);
                    }
                    this.searchParams.status = 3;
                    this.handleOrderList();
                }).catch(err => {

                });
            },
            // 取消退款
            cancelRefund(item) {
                this.$api.GameLevelingChannelOrderCancelRefund({
                    user_id: item.user_id,
                    trade_no: item.trade_no,
                    game_leveling_channel_user_id: item.game_leveling_channel_user_id
                }).then(res => {
                    if (res.status === 1) {
                        Toast.success(res.message);
                    } else {
                        Toast.fail(res.message);
                    }
                    this.searchParams.status = 6;
                    this.handleOrderList();
                }).catch(err => {

                });
            },
            // 点击获取各状态数据
            onClick(index, title) {
                if (index === 0) {
                    this.searchParams.status = 0;
                    this.onLoadData();
                } else if (index === 1) {
                    this.searchParams.status = 2;
                    this.onLoadData();
                } else if (index === 2) {
                    this.searchParams.status = 3;
                    this.onLoadData();
                } else if (index === 3) {
                    this.searchParams.status = 4;
                    this.onLoadData();
                } else if (index === 4) {
                    this.searchParams.status = 6;
                    this.onLoadData();
                } else if (index === 5) {
                    this.searchParams.status = 7;
                    this.onLoadData();
                }
            },
            // 列表数据
            onLoadData() {
                this.$api.GameLevelingChannelOrderList(this.searchParams).then(res => {
                    this.noData = res.length == 0 ? true : false;
                    this.list = res;
                    this.loading = false;
                    this.finished = true;
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
                min-height: 700px;
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