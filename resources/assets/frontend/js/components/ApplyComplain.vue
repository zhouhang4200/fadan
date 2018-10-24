<template>
    <el-dialog
            title="申请仲裁"
            :visible="visible"
            :before-close="handleVisible"
            :show-close="showClose">
        <el-form :model="form" ref="form" label-width="100px" class="demo-ruleForm">
            <el-form-item label="仲裁图片">
                <el-upload
                        action="https://jsonplaceholder.typicode.com/posts/"
                        list-type="picture-card"
                        :on-preview="handleUploadPreview"
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
                          v-model="form.desc"></el-input>
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
        props: {

        },
        computed: {
            getVisible() {
                console.log(2);
                return this.$store.state.applyConsultVisible;
            }
        },
        data() {
            return {
                visible: false,
                showClose: true,
                dialogImageUrl: '',
                dialogVisible: false,
                form: {
                    age: '',
                    desc: ''
                },
            };
        },
        methods: {
            handleVisible() {
                this.$store.commit('handleApplyConsultVisible',{visible:false});
            },
            handleSubmitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {

                        alert('submit!');
                        this.$store.commit('handleApplyConsultVisible',{visible:false});
                    } else {
                        console.log('error submit!!');
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
            handleUpload(res, file) {
                this.imageUrl = URL.createObjectURL(file.raw);
            },
            HandleBeforeUpload(file) {
                // const isJPG = file.type === 'image/jpeg';
                const isLt2M = file.size / 1024 / 1024 < 2;

                // if (!isJPG) {
                //     this.$message.error('上传头像图片只能是 JPG 格式!');
                // }

                if (!isLt2M) {
                    this.$message.error('上传头像图片大小不能超过 2MB!');
                }
                return isLt2M;
            }
        },
        watch: {
            getVisible(val) {
                this.visible = val;
            }
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