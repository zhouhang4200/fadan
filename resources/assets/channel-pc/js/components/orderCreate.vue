<template>
    <div class="order-create">

        <div class="list box-shadow">

            <div class="title">
                <span class="fr">有问题? <span style="color: #409EFF">联系客服</span> </span>
                <div class="clear-float"></div>
            </div>

            <table style="margin-top: 20px;border: 1px solid #efefef;border-spacing: 0;width: 100%;font-size: 11px">
                <tr style="height:40px;background-color: #efefef;">
                    <td width="30%" style="padding-left: 15px">代练游戏/区服</td>
                    <td width="40%">代练目标</td>
                    <td width="10%">类型</td>
                    <td width="20%">预计耗时</td>
                </tr>
                <tr style="height: 40px;">
                    <td style="padding-left: 15px">代代练目标代练目s</td>
                    <td>s</td>
                    <td>s</td>
                </tr>
            </table>

            <el-form ref="form" :model="form" label-width="80px" style="margin-top: 30px">
                <el-row :gutter="30">
                    <el-col :span="10">
                        <el-form-item label="活动名称">
                            <el-input v-model="form.payment_type"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="10">

                    </el-col>
                </el-row>

                <el-row :gutter="30">
                    <el-col :span="10">
                        <el-form-item label="活动名称">
                            <el-input v-model="form.payment_type"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="10">
                        <el-form-item label="活动名称">
                            <el-input v-model="form.payment_type"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="30">
                    <el-col :span="10">
                        <el-form-item label="活动名称">
                            <el-input v-model="form.payment_type"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="10">
                        <el-form-item label="活动名称">
                            <el-input v-model="form.payment_type"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>

            </el-form>

        </div>

        <div class="list box-shadow">
            <div class="title" style="border: none">
                <span class="fl">付款方式</span>
                <span class="fr">有问题? <span style="color: #409EFF">联系客服</span> </span>
                <div class="clear-float"></div>
            </div>

            <div class="pay" style="margin-top: 20px">
                <div class="pay-icon fl" >
                    <img src="/channel-pc/images/ali-pay.png" alt="">
                </div>
                <div class="pay-icon pay-chose fl" style="padding: 15px 30px;margin-left: 10px">
                    <img src="/channel-pc/images/wechat-pay.png" alt="">

                    <span class="gou">
                        <span></span>
                      </span>
                </div>
            </div>
        </div>

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
                        var currentThis = this;
                        this.$api.gameLevelingChannelOrderCreate(this.form).then(res => {
                            var storeRes = res;
                            if (res.content.type == 1) {
                                var span = document.createElement("span");
                                span.innerHTML = res.content;
                                document.body.appendChild(span);
                                document.forms[0].submit();
                            } else {
                                WeixinJSBridge.invoke(
                                    'getBrandWCPayRequest', storeRes.content.par,
                                    function(result){
                                        if(result.err_msg == "get_brand_wcpay_request:ok" ) {
                                            currentThis.$router.push({
                                                name:'paySuccess',
                                                query:{
                                                    'trade_no' : storeRes.content.trade_no
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
            .pay {
                position: relative;
            }
            .pay-icon {

                border: 1px solid #efefef;
                border-radius: 3px;
                width: 100px;
                padding: 10px 30px;
                img {
                    width: 100%;

                }

                .gou span{
                    display: block;
                    width: 0;
                    height: 0;
                    border-width: 22px 0 22px 22px;
                    border-style: solid;
                    border-color: transparent transparent transparent rgb(255, 0, 0);
                    position: absolute;
                    bottom: -19px;
                    right: -5px;
                    transform: rotate(7deg);
                    -ms-transform: rotate(7deg);
                    -moz-transform: rotate(7deg);
                    -webkit-transform: rotate(45deg);
                    -o-transform: rotate(7deg);
                }

                position:relative;
                cursor:pointer;
                overflow:hidden;
                .gou{
                    position:absolute;
                    width: 24px;
                    height: 24px;
                    right:0;
                    bottom: 0;
                }

                .gou::after{
                    position: absolute;
                    top: 10px;
                    left: 14px;
                    width: 5px;
                    height: 7px;
                    border-style: solid;
                    border-color: #fff;
                    border-width: 0 1.5px 1.5px 0;
                    -webkit-transform: rotateZ(45deg);
                    transform: rotateZ(43deg);
                    content: "";
                }
            }

        }
    }
</style>