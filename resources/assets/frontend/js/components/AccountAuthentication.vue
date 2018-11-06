<template>
    <div class="main content amount-flow">
        <el-tabs v-model="activeName" @tab-click="handleClick">
            <el-tab-pane label="个人认证" name="personal" :disabled="isPersonalDisabled">
                <el-form :model="form" ref="form" :rules="rules" label-width="80px">
                    <el-form-item label="*真实姓名" prop="name">
                        <el-input v-model="form.name" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="*手机号" prop="phone_number">
                        <el-input v-model.number="form.phone_number" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="*开户银行卡号" prop="bank_number">
                        <el-input v-model="form.bank_number" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="*开户银行名称" prop="bank_name">
                        <el-input v-model="form.bank_name" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="*身份证号" prop="identity_card">
                        <el-input v-model="form.identity_card" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="*身份证正面照" prop="front_card_picture">
                        <el-upload
                                class="avatar-uploader"
                                :action="this.AccountAuthenticationUploadApi+'?name=front_card_picture'"
                                :show-file-list="false"
                                accept="image/jpeg,image/jpg,image/png"
                                :on-success="handleAvatarSuccess"
                                :before-upload="beforeAvatarUpload">
                            <img v-if="imageUrl1" :src="imageUrl1" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input v-model="form.front_card_picture" autocomplete="off" type="hidden"></el-input>
                    </el-form-item>
                    <el-form-item label="*身份证背面照" prop="back_card_picture">
                        <el-upload
                                class="avatar-uploader"
                                :action="this.AccountAuthenticationUploadApi+'?name=back_card_picture'"
                                :show-file-list="false"
                                :on-success="handleAvatarSuccess"
                                :before-upload="beforeAvatarUpload">
                            <img v-if="imageUrl2" :src="imageUrl2" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input v-model="form.back_card_picture" autocomplete="off" type="hidden"></el-input>
                    </el-form-item>
                    <el-form-item label="*手持身份证正面照" prop="hold_card_picture">
                        <el-upload
                                class="avatar-uploader"
                                :action="this.AccountAuthenticationUploadApi+'?name=hold_card_picture'"
                                :show-file-list="false"
                                :on-success="handleAvatarSuccess"
                                :before-upload="beforeAvatarUpload">
                            <img v-if="imageUrl3" :src="imageUrl3" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input v-model="form.hold_card_picture" autocomplete="off" type="hidden"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button v-if="this.isPersonalShowAdd" type="primary" @click="submitForm('form')">确认添加</el-button>
                        <el-button v-if="this.isPersonalShowEdit" type="primary" @click="submitFormUpdate('form')">确认修改</el-button>
                    </el-form-item>
                </el-form>
            </el-tab-pane>
            <el-tab-pane label="企业认证" name="company" :disabled="isCompanyDisabled">
                <el-form :model="companyForm" ref="companyForm" :rules="companyFormRules" label-width="80px">
                    <el-form-item label="*真实姓名" prop="name">
                        <el-input v-model="companyForm.name" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="*手机号" prop="phone_number">
                        <el-input v-model.number="companyForm.phone_number" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="*开户银行卡号" prop="bank_number">
                        <el-input v-model="companyForm.bank_number" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="*开户银行名称" prop="bank_name">
                        <el-input v-model="companyForm.bank_name" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="*营业执照名称" prop="license_name">
                        <el-input v-model="companyForm.license_name" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="*营业执照号码" prop="license_number">
                        <el-input v-model="companyForm.license_number" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="*法人姓名" prop="corporation">
                        <el-input v-model="companyForm.corporation" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="*营业执照正面照" prop="license_picture">
                        <el-upload
                                class="avatar-uploader"
                                :action="this.AccountAuthenticationUploadApi+'?name=license_picture'"
                                :show-file-list="false"
                                accept="image/jpeg,image/jpg,image/png"
                                :on-success="handleAvatarSuccess"
                                :before-upload="beforeAvatarUpload">
                            <img v-if="imageUrl4" :src="imageUrl4" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input v-model="companyForm.license_picture" autocomplete="off" type="hidden"></el-input>
                    </el-form-item>
                    <el-form-item label="*银行开户许可证照片" prop="bank_open_account_picture">
                        <el-upload
                                class="avatar-uploader"
                                :action="this.AccountAuthenticationUploadApi+'?name=bank_open_account_picture'"
                                :show-file-list="false"
                                :on-success="handleAvatarSuccess"
                                :before-upload="beforeAvatarUpload">
                            <img v-if="imageUrl5" :src="imageUrl5" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input v-model="companyForm.bank_open_account_picture" autocomplete="off" type="hidden"></el-input>
                    </el-form-item>
                    <el-form-item label="*代办协议照片" prop="agency_agreement_picture">
                        <el-upload
                                class="avatar-uploader"
                                :action="this.AccountAuthenticationUploadApi+'?name=agency_agreement_picture'"
                                :show-file-list="false"
                                :on-success="handleAvatarSuccess"
                                :before-upload="beforeAvatarUpload">
                            <img v-if="imageUrl6" :src="imageUrl6" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input v-model="form.agency_agreement_picture" autocomplete="off" type="hidden"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" v-if="this.isCompanyShowAdd" @click="submitForm('companyForm')">确认添加</el-button>
                        <el-button type="primary" v-if="this.isCompanyShowEdit" @click="submitFormUpdate('companyForm')">确认修改</el-button>
                    </el-form-item>
                </el-form>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>
<style>
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
        width: 500px;
        height: 400px;
        line-height: 400px;
        text-align: center;
    }
    .avatar {
        width: 500px;
        height: 400px;
        display: block;
    }
</style>
<script>
    export default {
        props: [
            'AccountAuthenticationFormApi',
            'AccountAuthenticationUpdateApi',
            'AccountAuthenticationAddApi',
            'AccountAuthenticationUploadApi',
        ],
        methods: {
            // 获取编辑页面的数据
            authenticationForm() {
                axios.post(this.AccountAuthenticationFormApi).then(res => {
                    if (res.data && res.data.type === 1) {
                        this.form = res.data;
                        this.isCompanyDisabled = true;
                        this.imageUrl1 = this.form.front_card_picture;
                        this.imageUrl2 = this.form.back_card_picture;
                        this.imageUrl3 = this.form.hold_card_picture;

                        if (res.data.status === 1) {
                            this.isPersonalShowAdd = false;
                            this.isPersonalShowEdit = false;
                        } else {
                            this.isPersonalShowAdd = false;
                            this.isPersonalShowEdit = true;
                        }
                    } else if (res.data && res.data.type === 2) {
                        this.companyForm = res.data;
                        this.isPersonalDisabled = true;
                        this.imageUrl4 = this.form.license_picture;
                        this.imageUrl5 = this.form.bank_open_account_picture;
                        this.imageUrl6 = this.form.agency_agreement_picture;

                        if (res.data.status === 1) {
                            this.isCompanyShowAdd = false;
                            this.isCompanyShowEdit = false;
                        } else {
                            this.isCompanyShowAdd = false;
                            this.isCompanyShowEdit = true;
                        }
                    }
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            handleClick(tab, event) {
            },
            // 图片上传成功将地址回传给表单
            handleAvatarSuccess(res, file) {
                if (res.status > 0) {
                    if (res.name === 'front_card_picture') {
                        this.imageUrl1 = URL.createObjectURL(file.raw);
                        this.form.front_card_picture = res.path;
                    } else if (res.name === 'back_card_picture') {
                        this.imageUrl2 = URL.createObjectURL(file.raw);
                        this.form.back_card_picture = res.path;
                    } else if (res.name === 'hold_card_picture') {
                        this.imageUrl3 = URL.createObjectURL(file.raw);
                        this.form.hold_card_picture = res.path;
                    } else if (res.name === 'license_picture') {
                        this.imageUrl4 = URL.createObjectURL(file.raw);
                        this.companyForm.license_picture = res.path;
                    } else if (res.name === 'bank_open_account_picture') {
                        this.imageUrl5 = URL.createObjectURL(file.raw);
                        this.companyForm.bank_open_account_picture = res.path;
                    } else if (res.name === 'agency_agreement_picture') {
                        this.imageUrl6 = URL.createObjectURL(file.raw);
                        this.companyForm.agency_agreement_picture = res.path;
                    }
                }
            },
            // 图片上传
            beforeAvatarUpload(file) {
                const isJPEG = file.type === 'image/jpeg';
                // const isPng = file.type === 'image/png';
                // const isJPG = file.type === 'image/jpg';
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isJPEG) {
                    this.$message.error('上传头像图片只能是 JPG JPEG PNG格式!');
                }
                if (!isLt2M) {
                    this.$message.error('上传头像图片大小不能超过 2MB!');
                }
                return isJPEG && isLt2M;
            },
            // 修改
            submitFormUpdate(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        let data = '';
                        if (this.form.name) {
                            data = this.form;
                        } else if (this.companyForm.name) {
                            data = this.companyForm;
                        }
                        axios.post(this.AccountAuthenticationUpdateApi, data).then(res => {
                            this.$message({
                                showClose: true,
                                type: res.data.status == 1 ? 'success' : 'error',
                                message: res.data.message
                            });
                        }).catch(err => {
                            this.$alert('获取数据失败, 请重试!', '提示', {
                                confirmButtonText: '确定',
                                callback: action => {
                                }
                            });
                        });
                    } else {
                        return false;
                    }
                });
            },
            // 新增
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        let data = '';
                        if (this.form.name) {
                            data = this.form;
                        } else if (this.companyForm.name) {
                            data = this.companyForm;
                        }
                        axios.post(this.AccountAuthenticationAddApi, data).then(res => {
                            this.$message({
                                showClose: true,
                                type: res.data.status == 1 ? 'success' : 'error',
                                message: res.data.message
                            });
                            this.authenticationForm();
                        }).catch(err => {
                            this.$alert('获取数据失败, 请重试!', '提示', {
                                confirmButtonText: '确定',
                                callback: action => {
                                }
                            });
                        });
                    } else {
                        return false;
                    }
                });
            },
            updateForm(row) {
                this.form = row;
            },
            submitUpdateForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        axios.post(this.AccountAuthenticationUpdateApi, this.form).then(res => {
                            this.$message({
                                showClose: true,
                                type: res.data.status == 1 ? 'success' : 'error',
                                message: res.data.message
                            });
                        }).catch(err => {
                            this.$alert('获取数据失败, 请重试!', '提示', {
                                confirmButtonText: '确定',
                                callback: action => {
                                }
                            });
                        });
                    } else {
                        return false;
                    }
                });
            },
        },
        created () {
            this.$store.commit('handleOpenMenu', '3');
            this.$store.commit('handleOpenSubmenu', '3-3');
            this.authenticationForm();
        },
        data() {
            var checkHas = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('必填项不能为空!'));
                }
                callback();
            };
            var checkPhone = (rule, value, callback) => {
                if (!value) {
                    return callback(new Error('必填项不能为空!'));
                }

                if (!Number.isInteger(parseInt(value))) {
                    callback(new Error('请输入数字值！'));
                } else {
                    const reg = /^1[3|4|5|7|8][0-9]\d{8}$/
                    if (reg.test(value)) {
                        callback();
                    } else {
                        callback(new Error('请输入正确的手机号！'));
                    }
                    callback();
                }
                callback();
            };
            var checkQq = (rule, value, callback) => {
                if (!value) {
                    return callback(new Error('必填项不能为空!'));
                }

                if (!Number.isInteger(parseInt(value))) {
                    callback(new Error('请输入数字值！'));
                }
                callback();
            };
            return {
                isPersonalShowAdd:true,
                isPersonalShowEdit:false,
                isCompanyShowAdd:true,
                isCompanyShowEdit:false,
                isPersonalDisabled:false,
                isCompanyDisabled:false,
                imageUrl1:'',
                imageUrl2:'',
                imageUrl3:'',
                imageUrl4:'',
                imageUrl5:'',
                imageUrl6:'',
                UploadUrl:'',
                activeName:'personal',

                rules:{
                    identity_card:[{ validator: checkQq, trigger: 'blur' }],
                    bank_number:[{ validator: checkQq, trigger: 'blur' }],
                    phone_number:[{ validator: checkPhone, trigger: 'blur' }],
                    bank_name:[{ validator: checkHas, trigger: 'blur' }],
                    name:[{ validator: checkHas, trigger: 'blur' }],
                    front_card_picture:[{ validator: checkHas, trigger: 'blur' }],
                    back_card_picture:[{ validator: checkHas, trigger: 'blur' }],
                    hold_card_picture:[{ validator: checkHas, trigger: 'blur' }],
                },
                form: {
                    type:'',
                    name: '',
                    phone_number: '',
                    bank_name: '',
                    bank_number: '',
                    identity_card:'',
                    front_card_picture:'',
                    back_card_picture:'',
                    hold_card_picture:'',
                },
                companyForm:{
                    type:'',
                    name: '',
                    phone_number: '',
                    bank_name: '',
                    bank_number: '',
                    license_name:'',
                    license_number:'',
                    corporation:'',
                    license_picture:'',
                    bank_open_account_picture:'',
                    agency_agreement_picture:'',
                },
                companyFormRules:{
                    bank_number:[{ validator: checkQq, trigger: 'blur' }],
                    phone_number:[{ validator: checkPhone, trigger: 'blur' }],
                    bank_name:[{ validator: checkHas, trigger: 'blur' }],
                    name:[{ validator: checkHas, trigger: 'blur' }],
                    license_name:[{ validator: checkHas, trigger: 'blur' }],
                    license_number:[{ validator: checkHas, trigger: 'blur' }],
                    corporation:[{ validator: checkHas, trigger: 'blur' }],
                    bank_open_account_picture:[{ validator: checkHas, trigger: 'blur' }],
                    agency_agreement_picture:[{ validator: checkHas, trigger: 'blur' }],
                    license_picture:[{ validator: checkHas, trigger: 'blur' }],
                }
            }
        }
    }
</script>