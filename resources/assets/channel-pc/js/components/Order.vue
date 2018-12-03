<template>
    <div class="order">

        <div class="banner box-shadow">

            <el-carousel height="250px">

                <el-carousel-item v-for="item in 4" :key="item">
                    <img src="/channel-pc/images/banner.jpeg" alt="">
                </el-carousel-item>

            </el-carousel>

        </div>

        <div class="order-info box-shadow">

            <div class="notice">

                <el-alert
                        :closable="false"
                        type="success"
                        description="用户可根据服务需求付款下单,平台将订单需求全网推送至符合要求的大神,大神自行抢单, 抢单成功后订单生效,订单超时将自动取消;
                        大神抢单前用户可取消订单,大神抢单成功后取消订单则需大神同意。"
                        center
                        show-icon>
                </el-alert>

            </div>

            <div class="game">
                <ul>
                    <li>
                        <img src="/channel-pc/images/wz.png" alt="">
                        <div>王者荣耀</div>
                    </li>
                    <li>
                        <img src="/channel-pc/images/wz.png" alt="">
                        <div>王者荣耀</div>
                    </li>
                    <li>
                        <img src="/channel-pc/images/wz.png" alt="">
                        <div>王者荣耀</div>
                    </li>
                    <li>
                        <img src="/channel-pc/images/wz.png" alt="">
                        <div>王者荣耀</div>
                    </li>
                    <li>
                        <img src="/channel-pc/images/wz.png" alt="">
                        <div>王者荣耀</div>
                    </li>
                    <li>
                        <img src="/channel-pc/images/wz.png" alt="">
                        <div>王者荣耀</div>
                    </li>
                    <li>
                        <img src="/channel-pc/images/wz.png" alt="">
                        <div>王者荣耀</div>
                    </li>
                    <li>
                        <img src="/channel-pc/images/wz.png" alt="">
                        <div>王者荣耀</div>
                    </li>
                    <li>
                        <img src="/channel-pc/images/wz.png" alt="">
                        <div>王者荣耀</div>
                    </li>
                    <li>
                        <img src="/channel-pc/images/wz.png" alt="">
                        <div>王者荣耀</div>
                    </li>
                </ul>
                <div class="clear-float"></div>
            </div>

            <div class="form">

                <el-form label-position="top" label-width="80px" :model="form">

                    <el-form-item label="游戏区服">
                        <el-cascader
                                style="width: 100%;"
                                :options="options"
                                v-model="selectedOptions"
                        >
                        </el-cascader>
                    </el-form-item>

                    <el-form-item label="代练类型">
                        <el-select
                                v-model="form.game_id"
                                style="width: 100%;"
                                placeholder="请选择">
                            <el-option
                                    v-for="item in options2"
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value"
                                    :disabled="item.disabled">
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="代练目标">

                        <el-col :span="11">
                            <el-date-picker type="date"
                                            placeholder="选择日期"
                                            v-model="form.date1"
                                            style="width: 100%;">
                            </el-date-picker>
                        </el-col>

                        <el-col style="text-align: center" :span="2">-</el-col>

                        <el-col :span="11">
                            <el-time-picker type="fixed-time"
                                            placeholder="选择时间"
                                            v-model="form.date2"
                                            style="width: 100%;">
                            </el-time-picker>
                        </el-col>
                    </el-form-item>

                    <el-form-item label="所需代练价格与预计耗时">
                        <div class="use-time-preview">
                            <div class="level-type preview-row">
                                <div class="fl">
                                    <div class="fl" style="line-height: 20px">
                                        <div>青铜三星</div>
                                        <div style="font-size: 11px;color:#B1B1B1">当前段位</div>
                                    </div>
                                    <div class="fl" style="line-height: 20px;margin-left: 30px">
                                        <div>青铜一星</div>
                                        <div style="font-size: 11px;color:#B1B1B1">目标段位</div>
                                    </div>
                                </div>
                                <div class="fr" style="line-height: 40px">
                                    排位
                                </div>
                                <div class="clear-float"></div>
                            </div>
                            <div class="use-time preview-row">
                                <div class="fl">预计耗时</div>
                                <div class="fr">5天小时</div>
                                <div class="clear-float"></div>
                            </div>
                            <div style="border-bottom: 1.4px dashed rgb(222, 221, 222);padding-top: 10px;margin-bottom: 5px;"></div>
                            <div class="game-amount preview-row">
                                <div class="fl" style="line-height: 20px">
                                    <div class="">王者荣耀/IOS/微信</div>
                                    <div style="font-size: 11px;color:#B1B1B1">代练价格</div>
                                </div>
                                <div class="fr" style="line-height: 40px;font-size: 18px;color:#ff0000">109元
                                </div>
                                <div class="clear-float"></div>
                            </div>
                        </div>
                    </el-form-item>

                </el-form>

            </div>

            <div class="" style="margin: 15px">
                <el-button round class="fr" type="primary">立即下单</el-button>
                <div class="clear-float"></div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "Order",

        data() {
            return {
                options2: [{
                    value: '选项1',
                    label: '黄金糕'
                }, {
                    value: '选项2',
                    label: '双皮奶',
                    disabled: true
                }, {
                    value: '选项3',
                    label: '蚵仔煎'
                }, {
                    value: '选项4',
                    label: '龙须面'
                }, {
                    value: '选项5',
                    label: '北京烤鸭'
                }],
                options: [
                    {
                        value: 'zhinan',
                        label: '指南',
                        children: [
                            {
                                value: 'shejiyuanze',
                                label: '设计原则',
                            },
                            {
                                value: 'daohang',
                                label: '导航',
                            }
                        ]
                    },
                    {
                        value: 'ziyuan',
                        label: '资源',
                        children: [{
                            value: 'axure',
                            label: 'Axure Components'
                        }, {
                            value: 'sketch',
                            label: 'Sketch Templates'
                        }, {
                            value: 'jiaohu',
                            label: '组件交互文档'
                        }]
                    }],
                selectedOptions: [],
                selectedOptions2: [],
                active: 0,
                form: {
                    game_id: '',
                }
            };
        },

        mounted() {
            this.getGameOptions();
        },

        methods: {
            onClickLeft() {
                this.$toast('返回');
            },
            getGameOptions() {
                this.$api.games().then(res => {
                    this.gamesOptions = res.content;
                });
            },
            getGameLevelingOptions() {
                this.$api.gameLevelingTypes({game_id: this.gameId}).then(res => {
                    this.gameLevelingTypeOptions = res.content;
                });
            },
            getGameLevelingLevelOptions() {
                this.$api.gameLevelingLevels({
                    game_id: this.gameId,
                    game_leveling_type_id: this.gameLevelingTypeId
                }).then(res => {
                    this.levels = res.content;
                    this.gameLevelingLevelOptions[0].values = res.content;
                    this.gameLevelingLevelOptions[1].values = res.content[0].level;
                });
            },
            getGameLevelingAmountTime() {
                this.$api.gameLevelingAmountTime({
                    game_id: this.gameId,
                    game_leveling_type_id: this.gameLevelingTypeId,
                    game_leveling_current_level_id: this.gameLevelingCurrentLevelId,
                    game_leveling_target_level_id: this.gameLevelingTargetLevelId,
                }).then(res => {
                    this.time = res.content.time;
                    this.amount = res.content.discount_amount + '元';
                    this.discount = '原价' + '<s>' + res.content.amount + '元</s>';
                });
            },
            onConfirmGame(value) {
                this.gameId = value.id;
                this.gameName = value.text;

                this.gameLevelingTypeId = 0;
                this.gameLevelingTypeName = '';
                this.gameLevelingCurrentLevelId = 0;
                this.gameLevelingTargetLevelId = 0;
                this.gameLevelingTarget = '';

                this.getGameLevelingOptions();
                this.gamesShow = false;
                this.initAmountTime();
            },
            onConfirmGameLevelingType(value) {
                this.gameLevelingTypeId = value.id;
                this.gameLevelingTypeName = value.text;

                this.gameLevelingCurrentLevelId = 0;
                this.gameLevelingTargetLevelId = 0;
                this.gameLevelingTarget = '';
                this.getGameLevelingLevelOptions();
                this.gameLevelingTypesShow = false;
                this.initAmountTime();
            },
            onChangeGameLevelingLevel(picker, values) {
                let index = picker.getIndexes();
                picker.setColumnValues(1, this.levels[index[0]].level);
            },
            onConfirmGameLevelingLevel(values) {
                this.gameLevelingTarget = values[0].text + ' - ' + values[1].text;
                this.gameLevelingCurrentLevelId = values[0].index;
                this.gameLevelingTargetLevelId = values[1].index;
                this.gameLevelingLevelShow = false;
                this.getGameLevelingAmountTime();
            },
            onSubmitForm() {
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.$router.push({
                            name: 'orderCreate',
                            query: {
                                'game': this.gameId,
                                'type': this.gameLevelingTypeId,
                                'current': this.gameLevelingCurrentLevelId,
                                'target': this.gameLevelingTargetLevelId,
                            }
                        })
                    }
                });
            },
            initAmountTime() {
                this.amount = '待评估';
                this.time = '待评估';
                this.discount = '代练价格';
            },
            // $router.back(-1)
            test() {
                this.$router.push({
                    name:'paySuccess',
                    query:{
                        'trade_no' : 'sd'
                    }
                })
            }
        }
    }
</script>

<style lang="less">
    .order {
        .banner {
            margin: 15px 0;
            border: 1px solid #ffffff;
            box-shadow: 0 8px 16px 0 rgba(7, 17, 27, .05);
            border-radius: 12px;
            overflow: hidden;
        }
        .order-info {
            background: #ffffff;
            margin: 15px 0;
            .notice {
                padding: 15px;
                border-bottom: 1px solid #dddddd;
            }
            .game {
                text-align: center;
                color: #CDCDCD;
                ul {
                    li {
                        padding: 30px 0 0 40px;
                        float: left;
                        img {
                            width: 50px;
                            height: 50px;
                            border-radius: 50%;
                        }
                    }
                }
            }
            .form {
                width: 500px;
                margin: 20px;
            }
            .use-time-preview {
                line-height: 30px;
                color: #606266;
                padding: 10px 20px 20px;
                background-color: #F0F0F0;
                border-radius: 5px;
                .preview-row {
                    padding: 10px 0 0 0;
                }
                .level-type {
                    .level {
                        float: left;
                    }
                    .type {
                        float: right;
                    }
                }
            }
        }
    }
</style>