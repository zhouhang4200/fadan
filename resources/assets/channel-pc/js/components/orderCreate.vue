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
                    <td width="30%">代练目标</td>
                    <td width="10%">类型</td>
                    <td width="10%">预计耗时</td>
                    <td width="10%">总价</td>
                </tr>
                <tr style="height: 40px;">
                    <td style="padding-left: 15px">{{ useTimePreview.game }}{{ useTimePreview.region }}{{ useTimePreview.server }}</td>
                    <td>{{ useTimePreview.currentLevel }} - {{ useTimePreview.targetLevel }}</td>
                    <td>{{ useTimePreview.type }}</td>
                    <td>{{ useTimePreview.time }}</td>
                    <td>{{ useTimePreview.discountAmount }}</td>
                </tr>
            </table>

            <el-form ref="form" :model="form" :rules="rules" label-width="80px" style="margin-top: 30px">
                <el-row :gutter="30">
                    <el-col :span="10">
                        <el-form-item label="游戏角色" prop="game_role">
                            <el-input v-model="form.game_role"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="10">

                    </el-col>
                </el-row>

                <el-row :gutter="30">
                    <el-col :span="10">
                        <el-form-item label="游戏账号" prop="game_account">
                            <el-input v-model="form.game_account"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="10">
                        <el-form-item label="游戏密码" prop="game_password">
                            <el-input v-model="form.game_password"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="30">
                    <el-col :span="10">
                        <el-form-item label="联系电话" prop="player_phone">
                            <el-input v-model="form.player_phone"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="10">
                        <el-form-item label="联系QQ" prop="player_qq">
                            <el-input v-model="form.player_qq"></el-input>
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

                <div class="pay-method fl" :class=" form.payment_type === 1 ? 'pay-method-activate' : '' " @click="form.payment_type = 1">
                    <img src="/channel-pc/images/ali-pay.png" alt="">

                    <span class="pay-method-activate-icon">
                        <span></span>
                    </span>
                </div>

                <div class="pay-method fl"
                     style="padding: 15px 30px;margin-left: 10px"
                     :class=" form.payment_type === 2 ? 'pay-method-activate' : '' "
                     @click="form.payment_type = 2">
                    <img src="/channel-pc/images/wechat-pay.png" alt="">

                    <span class="pay-method-activate-icon">
                        <span></span>
                    </span>
                </div>

                <div class="clear-float"></div>
            </div>

        </div>

        <div class="amount box-shadow">
            <div class="fl">
                <div class="fl">实付金额:</div>
                <div class="fl" style="font-size: 20px;color:#ff0000;">￥ {{ useTimePreview.discountAmount  }}</div>
                <div class="fl" style="padding-left: 5px;text-decoration: line-through;"> ￥ {{ useTimePreview.amount }}</div>
            </div>
            <div class="fr">
                <el-button type="primary" @click="onSubmitForm">立即支付</el-button>
            </div>
            <div class="clear-float"></div>
        </div>

        <el-dialog
                title="在线支付订单"
                :visible.sync="payDialogVisible"
                width="20%">
            <div class="title" slot="title" style="font-size: 16px;font-weight: 700">
                <span style="color: #F53839">在线</span>支付订单
            </div>
            <div class="" style="text-align: center">
                <div class="qr-image">
                    <img :src="qr">
                </div>
                <div style="height: 30px;line-height: 30px;font-weight: 700;">应付金额</div>
                <div style="height: 30px;line-height: 30px;font-size: 23px;color:#ff0000">￥{{ useTimePreview.discountAmount }}</div>
                <div class="">请打开 {{ form.payment_type === 1 ? '支付宝' : '微信' }} "扫一扫"完成支付</div>
            </div>
        </el-dialog>

    </div>
</template>

<script>
    let Base64 = require('js-base64').Base64;
    export default {
        name: "orderCreate",

        data() {
            return {
                qr: '',
                payDialogVisible: false,
                gameServerOptionsShow: false,
                gameRegionOptionsShow: false,
                gameServer: '',
                gameRegion: '',
                amount: '待评估',
                time: '待评估',
                discount: '代练价格',
                game: '',
                level: '',
                form: {
                    payment_type: 1,
                    game_account: '',
                    game_password: '',
                    game_role: '',
                    player_phone: '',
                    player_qq: '',
                    game_id: '',
                    game_region_id: '',
                    game_server_id: '',
                    game_leveling_type_id: '',
                    current_level_id: '',
                    target_level_id: '',
                },
                rules: {
                    game_role: [
                        { required: true, message: '请输入游戏角色', trigger: 'blur' },
                    ],
                    game_account: [
                        { required: true, message: '请输入游戏账号', trigger: 'blur' },
                    ],
                    game_password: [
                        { required: true, message: '请输入游戏密码', trigger: 'blur' },
                    ],
                    player_phone: [
                        { required: true, message: '请输入联系电话', trigger: 'blur' },
                    ],
                    player_qq: [
                        { required: true, message: '请输入联系QQ', trigger: 'blur' },
                    ]
                },
                useTimePreview: {
                    game: '',
                    region: '',
                    server: '',
                    time: '',
                    currentLevel: '',
                    targetLevel: '',
                    amount: '0',
                    discountAmount: '0',
                },
                gameRegionOptions: [],
                gameServerOptions: [],
            }
        },

        created() {
            let order = JSON.parse(Base64.decode(this.$route.query.t));
            this.form.game_id = order.game;
            this.form.game_region_id = order.region;
            this.form.game_server_id = order.server;
            this.form.game_leveling_type_id = order.type;
            this.form.current_level_id = order.current;
            this.form.target_level_id = order.target;
            this.useTimePreview = order.useTimePreview;
        },

        methods: {
            onSubmitForm() {
                this.$refs.form.validate((valid) => {
                    if (valid) {
                        var currentThis = this;
                        this.$api.gameLevelingChannelOrderCreate(this.form).then(res => {
                            if (res.content.type == 1) {
                                this.qr = res.content.qr;
                                currentThis.payDialogVisible = true;
                            } else {
                                this.qr = res.content.qr;
                                currentThis.payDialogVisible = true;
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
            .pay-method {
                border: 1px solid #E1E1E1;
                border-radius: 5px;
                width: 100px;
                padding: 10px 30px;
                position: relative;
                cursor: pointer;
                overflow: hidden;

                img {
                    width: 100%;

                }

                .pay-method-activate-icon {
                    position: absolute;
                    width: 24px;
                    height: 24px;
                    right: 0;
                    bottom: 0;
                    display: none;
                }
                .pay-method-activate-icon span {
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
                .pay-method-activate-icon::after {
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
            .pay-method-activate {
                border: 1px solid #ff0000;

                .pay-method-activate-icon {
                    display: block;
                }
            }
        }
        .amount {
            padding: 15px;
            margin-bottom: 15px;
            height: 70px;
            line-height: 70px;
            background-color: #fff;
        }
    }

</style>