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

            <el-form label-position="top" label-width="80px" :model="form" :rules="rules" ref="form">

                <div class="game">
                    <el-form-item  prop="game_id">
                    <ul>
                        <li :class=" item.id === form.game_id ? 'activate' : '' " v-for="item in gameOptions"
                            @click="onClickGame(item.id, item.text)">
                            <img src="/channel-pc/images/wz.png" alt="单击选择游戏">
                            <div>{{ item.text }}</div>
                        </li>
                    </ul>
                    </el-form-item>
                    <div class="clear-float"></div>
                </div>

                <div class="form">

                    <el-form-item label="游戏区服" prop="game_region_server">
                        <el-cascader
                                @change="onGameRegionServerChange"
                                style="width: 100%;"
                                :options="gameRegionServerOptions"
                                v-model="form.game_region_server">
                        </el-cascader>
                    </el-form-item>

                    <el-form-item label="代练类型" prop="game_leveling_type_id">
                        <el-select
                                @change="onGameLevelingTypeChange"
                                v-model="form.game_leveling_type_id"
                                style="width: 100%;"
                                placeholder="请选择">
                            <el-option
                                    v-for="item in gameLevelingTypeOptions"
                                    :key="item.id"
                                    :label="item.text"
                                    :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="代练目标">

                        <el-col :span="11">
                            <el-form-item  prop="game_leveling_current_level_id">
                                <el-select
                                        @change="onGameLevelingLevelChange"
                                        v-model="form.game_leveling_current_level_id"
                                        style="width: 100%;"
                                        placeholder="请选择">
                                    <el-option
                                            v-for="item in levelOptions"
                                            :key="item.index"
                                            :label="item.text"
                                            :value="item.index">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>

                        <el-col style="text-align: center" :span="2">-</el-col>

                        <el-col :span="11">
                            <el-form-item  prop="game_leveling_target_level_id">
                                <el-select
                                        @change="onGameLevelingTargetLevelChange"
                                        v-model="form.game_leveling_target_level_id"
                                        style="width: 100%;"
                                        placeholder="请选择">
                                    <el-option
                                            v-for="item in targetLevelOptions"
                                            :key="item.index"
                                            :label="item.text"
                                            :value="item.index">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-form-item>

                    <el-form-item label="所需代练价格与预计耗时">
                        <div class="use-time-preview">
                            <div class="level-type preview-row">
                                <div class="fl">
                                    <div class="fl" style="line-height: 20px">
                                        <div>{{ useTimePreview.currentLevel }}</div>
                                        <div style="font-size: 11px;color:#B1B1B1">当前段位</div>
                                    </div>
                                    <div class="fl" style="line-height: 20px;margin-left: 30px">
                                        <div>{{ useTimePreview.targetLevel }}</div>
                                        <div style="font-size: 11px;color:#B1B1B1">目标段位</div>
                                    </div>
                                </div>
                                <div class="fr" style="line-height: 40px">
                                    {{ useTimePreview.type }}
                                </div>
                                <div class="clear-float"></div>
                            </div>
                            <div class="use-time preview-row">
                                <div class="fl">预计耗时</div>
                                <div class="fr">{{ useTimePreview.time }}</div>
                                <div class="clear-float"></div>
                            </div>
                            <div style="border-bottom: 1.4px dashed rgb(222, 221, 222);padding-top: 10px;margin-bottom: 5px;"></div>
                            <div class="game-amount preview-row">
                                <div class="fl" style="line-height: 20px">
                                    <div class="">{{ useTimePreview.game }}{{useTimePreview.region }}{{useTimePreview.server }}
                                    </div>
                                    <div style="font-size: 11px;color:#B1B1B1">代练价格</div>
                                </div>
                                <div class="fr" style="line-height: 40px;font-size: 18px;color:#ff0000">{{
                                    useTimePreview.discountAmount }} 元
                                </div>
                                <div class="clear-float"></div>
                            </div>
                        </div>
                    </el-form-item>
                </div>
            </el-form>
            <div class="" style="margin: 15px">

                <el-button round
                           class="fr"
                           type="primary"
                           @click="onSubmitForm">
                    立即下单
                </el-button>

                <div class="clear-float"></div>
            </div>
        </div>

    </div>
</template>

<script>
    let Base64 = require('js-base64').Base64;
    export default {
        name: "Order",

        data() {
            return {
                gameOptions: [], // 游戏选项
                gameRegionServerOptions: [],  // 代练区服选项
                gameLevelingTypeOptions: [], // 代练类型选项
                levelOptions: [], // 代练等级
                targetLevelOptions: [], // 代练等级目标
                form: {
                    game_id: '',
                    game_region_server:[],
                    game_leveling_type_id: '',
                    game_leveling_current_level_id: '', // 当前段位
                    game_leveling_target_level_id: '', // 目标段位
                },
                rules: {
                    game_id: [
                        { required: true, message: '请选择游戏', trigger: 'blur' },
                    ],
                    game_region_server: [
                        { required: 'array', message: '请选择游戏区服', trigger: 'change' },
                    ],
                    game_leveling_type_id: [
                        { required: true, message: '请选择游戏代练类型', trigger: 'change' },
                    ],
                    game_leveling_current_level_id: [
                        { required: true, message: '请选择您当段位', trigger: 'change' },
                    ],
                    game_leveling_target_level_id: [
                        { required: true, message: '请选择目标段位', trigger: 'change' },
                    ],
                },
                useTimePreview: {
                    game: '请选择',
                    region: '/请选择',
                    server: '/请选择',
                    time: '请选择',
                    currentLevel: '请选择',
                    targetLevel: '请选择',
                    amount: '0',
                    discountAmount: '0',
                }
            };
        },

        created() {
            this.getGameOptions();
        },

        methods: {
            initUseTimePreview() {
                this.useTimePreview = {
                    game: '请选择',
                    region: '/请选择',
                    server: '/请选择',
                    type: '请选择',
                    time: '请选择',
                    currentLevel: '请选择',
                    targetLevel: '请选择',
                    amount: '0',
                }
            },
            // 获取游戏选项
            getGameOptions() {
                this.$api.games().then(res => {
                    this.gameOptions = res.content;
                });
            },
            // 获取游戏区服选项
            getGameRegionServerOptions() {
                this.$api.gameRegionServer({game_id: this.form.game_id}).then(res => {
                    this.gameRegionServerOptions = res.content;
                });
            },
            // 获取游戏代练类型选项
            getGameLevelingTypeOptions() {
                this.$api.gameLevelingTypes({game_id: this.form.game_id}).then(res => {
                    this.gameLevelingTypeOptions = res.content;
                });
            },
            // 获取游戏代练等级选项
            getGameLevelingLevelOptions() {
                this.$api.gameLevelingLevels({
                    game_id: this.form.game_id,
                    game_leveling_type_id: this.form.game_leveling_type_id
                }).then(res => {
                    this.levelOptions = res.content;
                });
            },
            // 获取 代练价格与时间
            getGameLevelingAmountTime() {
                this.$api.gameLevelingAmountTime(this.form).then(res => {
                    this.useTimePreview.time = res.content.time;
                    this.useTimePreview.amount = res.content.amount;
                    this.useTimePreview.discountAmount = res.content.discount_amount;
                });
            },
            // 点击游戏时获取代练类型与区服
            onClickGame(value, label) {
                this.initUseTimePreview();
                this.form.game_id = value;
                this.useTimePreview.game = label;
                this.gameLevelingTypeOptions = [];
                this.form.game_leveling_type_id = '';
                this.gameRegionServerOptions = [];
                this.gameRegionServerActivate = [];
                this.levelOptions = [];
                this.form.game_leveling_current_level_id = '';
                this.form.game_leveling_target_level_id = '';
                this.targetLevelOptions = [];
                this.getGameRegionServerOptions();
                this.getGameLevelingTypeOptions();
                this.$refs.form.validate();
            },
            // 点击区服获取 区服名称
            onGameRegionServerChange(value) {
                let currentThis = this;
                let obj = {};
                obj = this.gameRegionServerOptions.find((item) => {
                    return item.id === value[0];
                });
                this.useTimePreview.region = '/' + obj.label;

                obj.children.find((item) => {
                    if (item.id === value[1]) {
                        currentThis.useTimePreview.server = '/' + item.label;
                    }
                });
            },
            // 点击游戏代练类型时获取代练等级
            onGameLevelingTypeChange(value) {
                this.form.game_leveling_type_id = value;

                let obj = {};
                obj = this.gameLevelingTypeOptions.find((item) => {
                    return item.id === value;
                });
                this.useTimePreview.type = obj.text;

                this.getGameLevelingLevelOptions();
            },
            // 改变代练等级时获取 目标等级
            onGameLevelingLevelChange(value) {
                // 获取选中的文字
                let obj = {};
                obj = this.levelOptions.find((item) => {
                    return item.index === value;
                });
                this.useTimePreview.currentLevel = obj.text;
                // 处理目标段位
                let currentThis = this;
                currentThis.targetLevelOptions = [];
                this.levelOptions.forEach(function (item) {
                    if (item.index > value) {
                        currentThis.targetLevelOptions.push(item);
                    }
                });
                // 将目标段位重置
                this.form.game_leveling_target_level_id = '';
                this.useTimePreview.targetLevel = '请选择';
            },
            // 选择目标等级时 获取代练时间
            onGameLevelingTargetLevelChange(value) {
                let obj = {};
                obj = this.levelOptions.find((item) => {
                    return item.index === value;
                });
                this.useTimePreview.targetLevel = obj.text;
                this.getGameLevelingAmountTime();
            },
            // 提交表单
            onSubmitForm() {
                this.$refs.form.validate((valid) => {
                    if (valid) {
                        let t = Base64.encode(JSON.stringify({
                            game: this.form.game_id,
                            region: this.form.game_region_server[0],
                            server: this.form.game_region_server[1],
                            type: this.form.game_leveling_type_id,
                            current: this.form.game_leveling_current_level_id,
                            target: this.form.game_leveling_target_level_id,
                            useTimePreview:this.useTimePreview,
                        }));
                        this.$router.push({
                            name: 'orderCreate',
                            query: {
                                t:t,
                            }
                        })
                    }
                })
            },
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
                /*color: #CDCDCD;*/
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
                    .activate {
                        color: #409EFF;
                    }
                }
                .el-form-item__error {
                    margin-left: 20px;
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