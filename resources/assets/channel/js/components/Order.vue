<template>
    <div class="order">

        <div style="padding-top: 46px">

            <img class="order-poster" src="/channel/images/banner.jpg">

            <van-cell-group class="goods-cell-group">

                <van-field
                        :value="gameName"
                        v-model="gameName"
                        label="代练游戏"
                        placeholder="请选择代练游戏"
                        is-link
                        readonly
                        @click.prevent.self="gamesShow = true"
                        name="gameName"
                        :error-message="errors.first('gameName')"
                        v-validate="{ required: true}"
                        data-vv-as="代练游戏"
                />

                <van-field
                        :value="gameLevelingTypeName"
                        v-model="gameLevelingTypeName"
                        label="代练类型"
                        placeholder="请选择代练类型"
                        is-link
                        readonly
                        @click.prevent.self="gameLevelingTypesShow = true"
                        name="gameLevelingTypeName"
                        :error-message="errors.has('gameLevelingTypeName') ? errors.first('gameLevelingTypeName') : ''"
                        v-validate="{ required: true}"
                        data-vv-as="代练类型"
                />

                <van-field
                        :value="gameLevelingTarget"
                        v-model="gameLevelingTarget"
                        label="代练目标"
                        placeholder="请选择代练目标"
                        is-link
                        readonly
                        @click.prevent.self="gameLevelingLevelShow = true"
                        name="gameLevelingTarget"
                        :error-message="errors.first('gameLevelingTarget')"
                        v-validate="{ required: true}"
                        data-vv-as="代练目标"
                />

            </van-cell-group>

            <div
                    class="pic-box"
                    style="margin: 30px 30px"
            >
                <van-row>
                    <van-col span="12">
                        <div
                                class="pic"
                                style="height: 60px"
                        >
                            <img src="/mobile/lib/images/pic.png">
                            <div class="new-pic">{{ amount }}</div>
                            <div class="old-pic a" 　v-html="discount">
                            </div>
                        </div>
                    </van-col>
                    <van-col span="12">
                        <div class="time">
                            <img src="/mobile/lib/images/time.png">
                            <div class="time">{{ time }}</div>
                            <div class="old-pic">预计耗时</div>
                        </div>
                    </van-col>
                </van-row>
            </div>

            <div style="margin: 30px">
                <van-button
                        size="normal"
                        type="primary"
                        style="width: 100%"
                        @click="onSubmitForm"
                >
                    我要代练
                </van-button>
            </div>
        </div>

        <van-popup
                v-model="gamesShow"
                position="bottom">
            <van-picker
                    show-toolbar
                    title="请选择游戏"
                    :columns="gamesOptions"
                    @cancel="gamesShow = false"
                    @confirm="onConfirmGame"
            />
        </van-popup>

        <van-popup
                v-model="gameLevelingTypesShow"
                position="bottom">
            <van-picker
                    show-toolbar
                    title="请选择代练类型"
                    @cancel="gameLevelingTypesShow = false"
                    :columns="gameLevelingTypeOptions"
                    @confirm="onConfirmGameLevelingType"
            />
        </van-popup>

        <van-popup
                v-model="gameLevelingLevelShow"
                position="bottom"
        >
            <van-picker
                    show-toolbar
                    title="当前段位　　　　　　　目标段位"
                    :columns="gameLevelingLevelOptions"
                    @cancel="gameLevelingLevelShow = false"
                    @change="onChangeGameLevelingLevel"
                    @confirm="onConfirmGameLevelingLevel"
            />
        </van-popup>

    </div>
</template>

<script>
    export default {
        name: "Order",

        data() {
            return {
                amount: '待评估',
                time: '待评估',
                discount: '代练价格',
                levels: [],
                gamesShow: false,
                gameLevelingTypesShow: false,
                gameLevelingLevelShow: false,
                gamesOptions: [],
                gameId: 0,
                gameName: '',
                gameLevelingTypeOptions: [],
                gameLevelingTypeId: 0,
                gameLevelingTypeName: '',
                gameLevelingLevelOptions: [
                    {
                        values: [],
                    },
                    {
                        values: [],
                        defaultIndex: 0
                    }
                ],
                gameLevelingCurrentLevelId: 0,
                gameLevelingTargetLevelId: 0,
                gameLevelingTarget: '',
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
                let indexs = picker.getIndexes();
                picker.setColumnValues(1, this.levels[indexs[0]].level);
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
            }
        }
    }
</script>

<style lang="less">
    .order {
        &-poster {
            width: 100%;
            display: block;
        }
        .van-picker__title {
            max-width: 100%;
        }
        .pic-box {
            padding: 20px 5%;
            box-sizing: border-box;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 0 24px rgba(0, 0, 0, 0.18);
            .pic {
                flex: 1;
                margin-right: 10px;
                position: relative;
                .title {
                    height: 30px;
                    text-align: center;
                    font-size: 16px;
                    font-weight: 600;
                    color: #484848;
                }
                img {
                    width: 45px;
                    height: 45px;
                    vertical-align: middle;
                    position: absolute;
                    left: 0;
                    top: 10px;
                }
                .new-pic {
                    width: 80px;
                    font-size: 20px;
                    color: #3ec369;
                    position: absolute;
                    left: 55px;
                    top: 10px;
                }
                .old-pic {
                    position: absolute;
                    left: 55px;
                    top: 35px;
                    font-size: 14px;
                    color: #606060;
                    white-space: nowrap;
                    s {
                        text-decoration: line-through;
                    }
                }
            }
            .time {
                flex: 1;
                position: relative;
                .title {
                    height: 30px;
                    text-align: center;
                    font-size: 16px;
                    font-weight: 600;
                    color: #484848;
                }
                img {
                    width: 45px;
                    height: 45px;
                    vertical-align: middle;
                    position: absolute;
                    left: 0;
                    top: 10px;
                }
                .time {
                    width: 100px;
                    font-size: 20px;
                    color: #3ec369;
                    position: absolute;
                    left: 55px;
                    top: 10px;
                    white-space: nowrap;
                }
                .old-pic {
                    position: absolute;
                    left: 55px;
                    top: 35px;
                    font-size: 14px;
                    color: #606060;
                    white-space: nowrap;
                    s {
                        text-decoration: line-through;
                    }
                }
            }
        }
    }
</style>