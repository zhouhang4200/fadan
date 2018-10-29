<template>
    <el-dialog
            title="申请仲裁"
            :visible=true
            :on-success="handleUploadSuccess"
            :before-close="handleBeforeClose">
        <el-form :model="form" ref="form" label-width="100px" class="demo-ruleForm">
            <el-form-item label="仲裁证据">
                <el-upload
                        action="action"
                        list-type="picture-card"
                        :on-preview="handleUploadPreview"
                        :http-request="handleUploadFile"
                        :on-remove="handleRemove">
                    <i class="el-icon-plus"></i>
                </el-upload>
                <el-dialog :visible.sync="dialogVisible">
                    <img width="100%" :src="dialogImageUrl" alt="">
                </el-dialog>
            </el-form-item>
            <el-form-item label="仲裁原因"
                          :rules="[{ required: true, message: '仲裁原因不能为空'}]">
                <el-input type="textarea"
                          :rows="8"
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
        name:"ApplyComplain",
        props: [
            'tradeNo',
            'applyComplainApi',
        ],
        computed: {
            // getVisible() {
            //     return this.$store.state.applyComplainVisible;
            // }
        },
        data() {
            return {
                fileReader:'',
                dialogImageUrl: '',
                dialogVisible: false,
                form: {
                    image1: '',
                    reason: '',
                    trade_no: this.tradeNo
                },
            };
        },
        methods: {
            handleBeforeClose() {
                this.$emit("handleApplyComplainVisible", {"visible":false});
            },
            handleSubmitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        axios.post(this.applyComplainApi, this.form).then(res => {
                            this.$message({
                                type: res.data.status == 1 ? 'success' : 'error',
                                message: res.data.message
                            });

                            if(res.data.status == 1) {
                                // 关闭窗口
                                this.$emit("handleApplyComplainVisible", {"visible":false});
                            }
                        }).catch(err => {
                            this.$message({
                                type: 'error',
                                message: '操作失败'
                            });
                        });
                    } else {
                        return false;
                    }
                });
            },
            HandleResetForm(formName) {
                this.$refs[formName].resetFields();
            },
            handleRemove(file, fileList) {
                console.log(file, fileList);
            },
            handleUploadPreview(file) {
                this.dialogImageUrl = file.url;
                this.dialogVisible = true;
            },
            handleUploadSuccess(res, file) {
                this.imageUrl = URL.createObjectURL(file.raw);
                console.log(this.imageUrl);
            },
            handleUploadFile(options){
                let file = options.file;
                if (file) {
                    this.fileReader.readAsDataURL(file)
                }
                this.fileReader.onload = () => {
                    let base64Str = this.fileReader.result;
                    // 图片base64
                    console.log(base64Str);
                }
            },
            HandleBeforeUpload(file) {
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isLt2M) {
                    this.$message.error('上传头像图片大小不能超过 2MB!');
                }
                return true;
            }
        },
        watch: {
            // getVisible(val) {
            //     this.visible = val;
            // }
        },
        mounted () {
            this.fileReader = new FileReader()
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