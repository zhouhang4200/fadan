<template>
    <div class="order-create" style="padding:46px 0;">

        <div class="pic-box">
            <van-row>
                <van-col span="12">
                    <div class="pic" style="height: 60px">
                        <div class="title">{{ game }}</div>
                        <img src="/mobile/lib/images/pic.png">
                        <div class="new-pic">{{ amount }}</div>
                        <div class="old-pic a" v-html="discount">
                        </div>
                    </div>
                </van-col>
                <van-col span="12">
                    <div class="time">
                        <div class="title">{{ level }}</div>
                        <img src="/mobile/lib/images/time.png" alt="">
                        <div class="time">{{ time }}</div>
                        <div class="old-pic">预计耗时</div>
                    </div>
                </van-col>
            </van-row>
        </div>

        <section class="van-doc-demo-block">
            <h2 class="van-doc-demo-block__title">
                <van-icon
                        name="pending-orders"
                        style="font-size: 18px;vertical-align: middle;"
                />
                订单信息
            </h2>

            <van-cell-group class="goods-cell-group">

                <van-field
                        :value="gameRegion"
                        v-model="gameRegion"
                        label="游戏区"
                        placeholder="请选择游戏区"
                        is-link
                        readonly
                        @click.prevent.self="gameRegionOptionsShow = true"
                        name="gameRegion"
                        :error-message="errors.first('gameRegion')"
                        v-validate="{ required: true}"
                        data-vv-as="游戏区"
                />

                <van-field
                        :value="gameServer"
                        v-model="gameServer"
                        label="游戏服"
                        placeholder="请选择游戏服"
                        is-link
                        readonly
                        @click.prevent.self="gameServerOptionsShow = true"
                        name="gameServer"
                        :error-message="errors.first('gameServer')"
                        v-validate="{ required: true}"
                        data-vv-as="游戏服"
                />

                <van-field
                        label="游戏角色"
                        :value="form.game_role"
                        v-model="form.game_role"
                        placeholder="请输入角色名称"
                        name="game_role"
                        :error-message="errors.first('game_role')"
                        v-validate="{ required: true}"
                        data-vv-as="角色名称"
                />

                <van-field
                        label="游戏账号"
                        :value="form.game_account"
                        v-model="form.game_account"
                        placeholder="请输入游戏账号"
                        name="game_account"
                        :error-message="errors.first('game_account')"
                        v-validate="{ required: true}"
                        data-vv-as="游戏账号"
                />

                <van-field
                        label="游戏密码"
                        :value="form.game_password"
                        v-model="form.game_password"
                        placeholder="请输入游戏密码"
                        name="game_password"
                        :error-message="errors.first('game_password')"
                        v-validate="{ required: true}"
                        data-vv-as="游戏密码"
                />

                <van-field
                        label="联系电话"
                        :value="form.player_phone"
                        v-model="form.player_phone"
                        placeholder="请输入联系电话"
                        name="player_phone"
                        :error-message="errors.first('player_phone')"
                        v-validate="'required|phone'"
                        data-vv-as="联系电话"
                />

                <van-field
                        label="联系QQ"
                        :value="form.player_qq"
                        v-model="form.player_qq"
                        placeholder="请输入联系QQ"
                        name="player_qq"
                        :error-message="errors.first('player_qq')"
                        v-validate="'required'"
                        data-vv-as="联系QQ"
                />

            </van-cell-group>
        </section>

        <section class="van-doc-demo-block">

            <h2 class="van-doc-demo-block__title">

                <van-icon
                        name="cash-back-record"
                        style="font-size: 21px;vertical-align: bottom;"
                />
                支付方式
            </h2>
            <van-radio-group v-model="form.payment_type">

                <van-cell-group>

                    <van-cell
                            title="微信"
                            clickable
                            @click="form.payment_type = '2'"
                    >
                        <van-icon
                                slot="icon"
                                name="wechat"
                                class="van-cell__left-icon"
                                style="font-size:30px;color:#4b0"
                        />
                        <van-radio name="2" />
                    </van-cell>

                    <van-cell
                            title="支付宝"
                            clickable
                            @click="form.payment_type = '1'"
                    >
                        <van-icon
                                slot="icon"
                                name="alipay"
                                class="van-cell__left-icon"
                                style="font-size:30px;color:#1989fa"
                        />
                        <van-radio name="1" />
                    </van-cell>

                </van-cell-group>

            </van-radio-group>
        </section>

        <div style="padding:15px 30px 10px">
            <van-button
                    size="normal"
                    type="primary"
                    style="width: 100%"
                    @click="onSubmitForm"
            >
                立刻支付
            </van-button>
        </div>

        <van-popup
                v-model="gameRegionOptionsShow"
                position="bottom">
            <van-picker
                    show-toolbar
                    title="请选择游戏区"
                    :columns="gameRegionOptions"
                    @cancel="gameRegionOptionsShow = false"
                    @confirm="onConfirmGameRegion"
            />
        </van-popup>

        <van-popup
                v-model="gameServerOptionsShow"
                position="bottom">
            <van-picker
                    show-toolbar
                    title="请选择游戏服类型"
                    :columns="gameServerOptions"
                    @cancel="gameServerOptionsShow = false"
                    @confirm="onConfirmGameServer"
            />
        </van-popup>

    </div>
</template>

<script>
    export default {
        name: "orderCreate",

        data() {
          return {
              gameServerOptionsShow:false,
              gameRegionOptionsShow:false,
              gameServer:'',
              gameRegion:'',
              amount:'待评估',
              time:'待评估',
              discount:'代练价格',
              game:'',
              level:'',
              form: {
                  payment_type:'2',
                  game_region_id:0,
                  game_server_id:0,
                  game_account:'',
                  game_password:'',
                  game_role:'',
                  player_phone:'',
                  player_qq:'',
                  game_id:this.$route.query.game,
                  game_leveling_type_id:this.$route.query.type,
                  current_level_id:this.$route.query.current,
                  target_level_id:this.$route.query.target,
              },
              gameRegionOptions:[],
              gameServerOptions:[],
          }
        },

        mounted() {
            this.getGameRegionOptions();
            this.getGameLevelingAmountTime();
        },

        methods: {
            onClickLeft() {
                this.$router.back(-1)
            },
            getGameRegionOptions() {
                this.$api.gameRegions({game_id:this.$route.query.game}).then(res => {
                    this.gameRegionOptions = res.content;
                });
            },
            getGameServerOptions() {
                this.$api.gameServers({region_id:this.form.game_region_id}).then(res => {
                    this.gameServerOptions = res.content;
                });
            },
            getGameLevelingAmountTime() {
                this.$api.gameLevelingAmountTime({
                    game_id:this.$route.query.game,
                    game_leveling_type_id:this.$route.query.type,
                    game_leveling_current_level_id:this.$route.query.current,
                    game_leveling_target_level_id:this.$route.query.target,
                }).then(res => {
                    this.game = res.content.game;
                    this.level = res.content.level;
                    this.time = res.content.time;
                    this.amount = res.content.discount_amount　+　'元';
                    this.discount = '原价' + '<s>' + res.content.amount + '元</s>';
                });
            },
            onConfirmGameRegion(value) {
                this.form.game_region_id = value.id;
                this.gameRegion = value.text;
                this.getGameServerOptions();
                this.gameRegionOptionsShow = false;
            },
            onConfirmGameServer(value) {
                this.form.game_server_id = value.id;
                this.gameServer = value.text;
                this.gameServerOptionsShow = false;
            },
            onSubmitForm(){
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.$api.gameLevelingChannelOrderCreate(this.form).then(res => {
                            if (res.content.type == 1) {
                                var span = document.createElement("span");
                                span.innerHTML = res.content;
                                document.body.appendChild(span);
                                document.forms[0].submit();
                            } else {
                                WeixinJSBridge.invoke(
                                    'getBrandWCPayRequest', res.content.par,
                                    function(result){
                                        if(result.err_msg == "get_brand_wcpay_request:ok" ) {
                                            // 使用以上方式判断前端返回,微信团队郑重提示：
                                            // res.err_msg将在用户支付成功后返回
                                            // ok，但并不保证它绝对可靠。
                                            this.$router.push({
                                                name:'paySuccess',
                                                query:{
                                                    'trade_no' : '2'
                                                }
                                            });
                                        }
                                    }
                                );
                            }
                        });
                    }
                });
            }
        }

    }
</script>

<style lang="less">
    .order-create {
        &-poster {
            width: 100%;
            display: block;
        }

        .van-picker__title {
            max-width: 100%;
        }
        .dd .van-cell__title {
            text-align: left !important;
            flex: 0.37;
        }
        .dd .van-cell__value {
            text-align: left !important;
            flex: 1;
        }
        .pic-box {
            margin: 20px 20px 10px 20px;
            padding: 30px 5% 15px 5%;
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
                    font-size: 16px;
                    font-weight: 600;
                    color: #484848;
                    position: relative;
                    top: -17px;
                    padding-left: 10px;
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
                    font-size: 16px;
                    font-weight: 600;
                    color: #484848;
                    position: relative;
                    top: -17px;
                    padding-left: 10px;
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