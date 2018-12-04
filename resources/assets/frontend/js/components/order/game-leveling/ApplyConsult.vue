<template>
    <el-dialog
                width="40%"
                title="申请撤销"
                :visible=true
                :before-close="handleBeforeClose">
        <el-form :model="form" ref="form" :rules="consultRules" label-width="204px" class="demo-ruleForm">
                <el-alert
                        title="双方友好协商撤单，若有分歧可以在订单中留言或申请客服介入；若申请成功，此单将被锁定，若双方取消撤单会退回至原有状态。"
                        type="success"
                        style="margin-bottom: 15px"
                        :closable="false">
                </el-alert>

                <el-form-item label="我已支付代练费（元）">
                    <el-input type="input"
                              :disabled=true
                              v-model="form.amount"></el-input>
                </el-form-item>

                <el-form-item label="我愿意支付代练费（元）"
                              prop="payment_amount"
                              >
                    <el-input type="input"
                              v-model.number="form.payment_amount"></el-input>
                </el-form-item>

                <el-form-item label="对方已预付安全保证金（元）">
                    <el-input type="input"
                              :disabled=true
                              v-model="form.security_deposit"></el-input>
                </el-form-item>

                <el-form-item label="对方已预付效率保证金（元）">
                    <el-input type="input"
                              :disabled=true
                              v-model="form.efficiency_deposit"></el-input>
                </el-form-item>

                <el-form-item label="需要对方赔付保证金"
                              prop="payment_deposit"
                >
                    <el-input type="input"
                              v-model.number="form.payment_deposit"></el-input>
                </el-form-item>

                <el-form-item label="撤销理由"
                              prop="reason"
                             >
                    <el-input type="textarea"
                              :rows="5"
                              v-model="form.reason"></el-input>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="handleSubmitForm('form')">提交</el-button>
                    <el-button @click="handleResetForm('form')">重置</el-button>
                </el-form-item>
            </el-form>
    </el-dialog>

</template>

<script>
    export default {
        name:"ApplyConsult",
        props: [
            'tradeNo',
            'amount',
            'securityDeposit',
            'efficiencyDeposit',
        ],
        computed: {
            // getVisible() {
            //     return this.$store.state.applyConsultVisible;
            // }
        },
        data() {
            var mustOverZero = (rule, value, callback) => {
                let grep=/^([1-9]\d*|0)(\.\d{1,2})?$/;
                if (!grep.test(value)) {
                    callback(new Error('金额必须大于0，支持2位小数!'));
                } else {
                    callback();
                }
            };
            var amountRule = (rule, value, callback) => {
                if (value > this.form.amount) {
                    callback(new Error('填写赔偿代练费不得超过订单代练费！'));
                } else {
                    callback();
                }
            };
            var depositRule = (rule, value, callback) => {
                let total=Number(this.form.security_deposit)+Number(this.form.efficiency_deposit);
                if (value > total) {
                    callback(new Error('填写赔偿双金不得大于订单双金！'));
                } else {
                    callback();
                }
            };
            var reasonRule = (rule, value, callback) => {
                if (value.length > 50) {
                    callback(new Error('申请协商原因不得大于50字！'));
                } else {
                    callback();
                }
            };
            return {
                fileReader:'',
                visible: false,
                dialogImageUrl: '',
                dialogVisible: false,
                form: {
                    reason: '',
                    payment_amount: '',
                    payment_deposit: '',
                    trade_no: this.tradeNo,
                    amount: this.amount,
                    security_deposit: this.securityDeposit,
                    efficiency_deposit: this.efficiencyDeposit
                },
                consultRules: {
                    payment_amount: [
                        { required: true, message: '必填项不能为空'},
                        { validator: mustOverZero, trigger: 'blur' },
                        { validator: amountRule, trigger: 'blur' }
                    ],
                    payment_deposit: [
                        { required: true, message: '必填项不能为空'},
                        { validator: mustOverZero, trigger: 'blur' },
                        { validator: depositRule, trigger: 'blur' }
                    ],
                    reason: [
                        { required: true, message: '必填项不能为空'},
                        { validator: reasonRule, trigger: 'blur' }
                    ]
                }
            };
        },
        methods: {
            handleBeforeClose() {
                this.$emit("handleApplyConsultVisible", {"visible":false});
            },
            handleSubmitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.$api.gameLevelingOrderApplyConsult(this.form).then(res => {
                            this.$message({
                                type: res.status == 1 ? 'success' : 'error',
                                message: res.message
                            });

                            if(res.status == 1) {
                                // 关闭窗口
                                this.$emit("handleApplyConsultVisible", {"visible":false});
                            }
                        }).catch(err => {
                            this.$message({
                                type: 'error',
                                message: '操作失败'
                            });
                        });
                    }
                });
            },
            handleResetForm(formName) {
                this.$refs[formName].resetFields();
            }
        },
        watch: {
            // getVisible(val) {
            //     this.visible = val;
            // }
        }
    }
</script>

<style scoped>
    .avatar-uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .avatar-uploader .el-upload:hover {
        border-color: #409EFF;
    }
    .avatar-uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 178px;
        height: 178px;
        line-height: 178px;
        text-align: center;
    }
    .avatar {
        width: 178px;
        height: 178px;
        display: block;
    }

</style>