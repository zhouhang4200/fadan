<template>
    <el-dialog
            title="申请仲裁"
            :visible=true
            :before-close="handleBeforeClose">
        <el-form :model="form"
                 ref="form"
                 label-width="100px"
                 class="demo-ruleForm">
            <el-form-item label="仲裁证据"
                          prop="images"
                          :rules="[
                            { required: true, message: '最少上传一张图片', trigger: 'change'}
                          ]"
                          ref="image">
                <el-upload :class="uploadExceedLimit"
                           action="action"
                           list-type="picture-card"
                           :limit="3"
                           :on-preview="handleUploadPreview"
                           :on-remove="handleUploadRemove"
                           :http-request="handleUploadFile">
                    <i class="el-icon-plus"></i>
                </el-upload>
                <el-dialog :visible.sync="dialogVisible">
                    <img width="100%"
                         :modal=false
                         :modal-append-to-body=false
                         style="z-index: 2000"
                         :src="dialogImageUrl">
                </el-dialog>
            </el-form-item>
            <el-form-item label="仲裁原因"
                          prop="reason"
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
        name: "ApplyComplain",
        props: [
            'tradeNo',
        ],
        computed: {
            // getVisible() {
            //     return this.$store.state.applyComplainVisible;
            // }
            // 如果上传图片等于3张时就隐藏增加图片按钮
            uploadExceedLimit() {
                return [
                    this.form.images.length == 3 ? 'exceed' : ''
                ];
            }
        },
        data() {
            return {
                fileReader: '',
                dialogImageUrl: '',
                dialogVisible: false,
                form: {
                    pic1: '',
                    pic2: '',
                    pic3: '',
                    reason: '',
                    images:[],
                    trade_no: this.tradeNo
                },
            };
        },
        methods: {
            handleBeforeClose() {
                this.$emit("handleApplyComplainVisible", {"visible": false});
            },
            handleSubmitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.form.pic1 = this.form.images[0];
                        this.form.pic2 = this.form.images[1];
                        this.form.pic3 = this.form.images[2];
                        this.form.images = [];
                        this.$api.gameLevelingOrderApplyComplain(this.form).then(res => {
                            this.$message({
                                type: res.status == 1 ? 'success' : 'error',
                                message: res.message
                            });

                            if (res.status == 1) {
                                // 关闭窗口
                                this.$emit("handleApplyComplainVisible", {"visible": false});
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
            handleResetForm(formName) {
                this.$refs[formName].resetFields();
            },
            // 预览图片
            handleUploadPreview(file) {
                const h = this.$createElement;
                this.$msgbox({
                    message: h('img', {attrs:{src:file.url}}),
                    showCancelButton: false,
                    cancelButtonText: false,
                    showConfirmButton: false,
                    customClass:'preview-image'
                });
            },
            // 删除图片
            handleUploadRemove(file, fileList) {
                let index = this.form.images.indexOf(file.response);
                this.form.images.splice(index, 1);
            },
            handleUploadFile(options) {
                let file = options.file;
                if (file) {
                    this.fileReader.readAsDataURL(file)
                }
                this.fileReader.onload = () => {
                    this.form.images.push(this.fileReader.result);
                    this.$refs.image.clearValidate();
                }
            }
        },
        watch: {
            // getVisible(val) {
            //     this.visible = val;
            // }
        },
        mounted() {
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