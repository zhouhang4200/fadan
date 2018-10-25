<template>
    <el-dialog
            width="40%"
            title="申请撤销"
            :visible=true
            :before-close="handleBeforeClose">
        <el-form :model="form" ref="form" label-width="204px" class="demo-ruleForm">
            <el-alert
                    title="双方友好协商撤单，若有分歧可以在订单中留言或申请客服介入；若申请成功，此单将被锁定，若双方取消撤单会退回至原有状态。"
                    type="success"
                    style="margin-bottom: 15px"
                    :closable="false">
            </el-alert>

            <el-form-item label="我已支付代练费（元）"
                          :rules="[{ required: true, message: '仲裁原因不能为空'}]">
                <el-input type="input"
                          :disabled="inputDisabled"
                          v-model="form.amount"></el-input>
            </el-form-item>

            <el-form-item label="需要对方赔付保证金"
                          :rules="[{ required: true, message: '仲裁原因不能为空'}]">
                <el-input type="input"
                          v-model="form.paymentDeposit"></el-input>
            </el-form-item>

            <el-form-item label="对方已预付安全保证金（元）">
                <el-input type="input"
                          :disabled="inputDisabled"
                          v-model="form.securityDeposit"></el-input>
            </el-form-item>

            <el-form-item label="对方已预付效率保证金（元）">
                <el-input type="input"
                          :disabled="inputDisabled"
                          v-model="form.efficiencyDeposit"></el-input>
            </el-form-item>

            <el-form-item label="我愿意支付代练费（元）"
                          :rules="[{ required: true, message: '仲裁原因不能为空'}]">
                <el-input type="input"
                          v-model="form.paymentAmount" prop="no"></el-input>
            </el-form-item>

            <el-form-item label="撤销理由"
                          :rules="[{ required: true, message: '仲裁原因不能为空'}]">
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
            return {
                fileReader:'',
                visible: false,
                inputDisabled:true,
                dialogImageUrl: '',
                dialogVisible: false,
                form: {
                    reason: '',
                    paymentAmount: '',
                    paymentDeposit: '',
                    tradeNo: this.tradeNo,
                    amount: this.amount,
                    securityDeposit: this.securityDeposit,
                    efficiencyDeposit: this.efficiencyDeposit,
                },
            };
        },
        methods: {
            handleBeforeClose() {
                this.$emit("handleApplyConsultVisible", {"visible":false});
            },
            handleSubmitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.$emit("handleApplyConsultVisible", {"visible":false});

                        alert('submit!');

                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            HandleResetForm(formName) {
                this.$refs[formName].resetFields();
            },
        },
        watch: {
            // getVisible(val) {
            //     this.visible = val;
            // }
        },
        mounted () {
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