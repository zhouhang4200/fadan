<template>
    <div class="order-refund">

        <div style="margin-top: 46px">
            <section class="van-doc-demo-block">
                <h2 class="van-doc-demo-block__title">
                    <van-icon
                            name="pending-orders"
                            style="font-size: 18px;vertical-align: middle;"
                    />
                    订单信息
                </h2>
                <van-cell-group>
                    <van-cell >
                        <template slot="title">
                            <span class="van-cell-text">代练目标</span>
                            {{order.title}}
                        </template>
                    </van-cell>
                    <van-cell >
                        <template slot="title">
                            <span class="van-cell-text">代练类型</span>
                            {{order.game_leveling_type_name}}
                        </template>
                    </van-cell>
                    <van-cell >
                        <template slot="title">
                            <span class="van-cell-text">代练价格</span>
                            {{order.payment_amount}}
                        </template>
                    </van-cell>
                    <van-cell >
                        <template slot="title">
                            <span class="van-cell-text">预计耗时</span>
                            {{order.day ? order.day+'天' : ''}}{{order.hour ? order.hour+'小时' : ''}}
                        </template>
                    </van-cell>
                </van-cell-group>
            </section>
            <section class="van-doc-demo-block">
                <h2 class="van-doc-demo-block__title">
                    <van-icon
                            name="cash-back-record"
                            style="font-size: 20px;vertical-align: bottom;"
                    />
                    退款信息
                </h2>
                <van-cell-group>
                    <van-radio-group v-model="form.type" @change="onChange">
                        <van-cell-group>
                            <van-cell title="全额退款" clickable @click="form.type = '1'">
                                <van-radio name="1" />
                            </van-cell>
                            <van-cell title="部分退款" clickable @click="form.type = '2'">
                                <van-radio name="2" />
                            </van-cell>
                            <van-field v-model="form.refund_amount"
                                       placeholder="请输入退款金额"
                                       v-show="form.type == 2"/>
                        </van-cell-group>
                    </van-radio-group>

                    <van-field
                            v-model="form.refund_reason"
                            placeholder="请填写退款原因"
                            v-validate="{ required: true}"
                            name="refund_reason"
                            :error-message="errors.first('refund_reason')"
                            data-vv-as="退款原因"
                            type="textarea"
                    />

                </van-cell-group>
                <div class="image">
                    <ul class="image-preview" v-for="(item, index) in form.images" :key="index">
                         <li :style="{'background-image':'url(' + item + ')'}">
                             <van-icon name="clear"  class="image-delete" @click="onDeleteImage(index)"/>
                         </li>
                    </ul>
                    <van-uploader :after-read="onRead">
                        <div class="image-chose" v-show="displayUpload">
                            <van-icon name="photograph" />
                        </div>
                    </van-uploader>
                </div>
            </section>
        </div>

        <van-goods-action >
            <van-goods-action-big-btn primary @click="onSubmitForm">
                确认
            </van-goods-action-big-btn>
        </van-goods-action>

    </div>
</template>

<script>
    import { Toast } from 'vant'
    export default {
        name: "OrderRefund",
        data() {
            return {
                order:{
                    title:'',
                    game_leveling_type_name:'',
                    payment_amount:'',
                    day:'',
                    hour:'',
                    user_id:'',
                    trade_no:''
                },
                form: {
                    type:'1',
                    user_id:'',
                    trade_no:'',
                    refund_amount:'',
                    refund_reason:'',
                    images: [],
                },
            };
        },
        created() {
            this.handleOrder();
        },
        computed: {
            displayUpload() {
                // if (this.form.images.length < 3) {
                //     return true;
                // }
            }
        },
        methods: {
            // 页面数据
            handleOrder() {
                this.form.trade_no=this.$route.query.trade_no;
                this.form.user_id=this.$route.query.user_id;
                this.$api.GameLevelingChannelOrderApplyRefundShow({
                    user_id:this.$route.query.user_id,
                    trade_no:this.$route.query.trade_no
                }).then(res => {
                  this.order=res;
                }).catch(err => {

                });
            },
            onClickLeft() {
                this.$router.push({path: '/channel/order'})
            },
            onChange(value) {
            },
            onDeleteImage(index) {
                this.form.images.splice(index, 1);
            },
            onRead(file) {
                this.form.images.push(file.content);
            },
            // 表单提交
            onSubmitForm() {
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.$api.GameLevelingChannelOrderApplyRefund(this.form).then(res => {
                            if (res.status === 1) {
                                Toast.success(res.message);
                                this.$router.push({name:'orderList'})
                            } else {
                                Toast.fail(res.message);
                            }
                        }).catch(err => {
                            Toast.fail('表单数据异常!');
                        });
                    }
                });
            }
        }
    }
</script>

<style lang="less">
    .order-refund {
       .image {
           background: #ffffff;
           padding: 15px;
       }
        .image-preview {

        }
        .image-preview li {
            height: 100px;
            width: 100px;
            border: 1px solid #eee;
            float: left;
            margin-right: 10px;
            background-size: 100%;
        }
        .image-delete {
            position: relative;
            color: #f44;
            top: -12px;
            right: -81px;
            z-index: 1;
            padding: 6px;
            font-size: 20px;
        }
        .image-chose {
            width: 100px;
            height: 100px;
            line-height: 100px;
            border: 1px solid rgb(238, 238, 238);
            text-align: center;
            vertical-align: middle;
            font-size: 30px;
        }
    }
</style>