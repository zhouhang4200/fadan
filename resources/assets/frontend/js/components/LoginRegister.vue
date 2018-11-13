<template>
    <div class="login-register"
         style="width: 950px;margin:100px auto 0 auto;background: #ffffff;overflow: hidden;border: 1px solid #fff; border-radius: 10px">
        <el-container>
            <div style="width: 500px;padding: 50px 50px 0 50px;">
                <el-tabs
                        v-model="active"
                        id="form"
                        @tab-click="handleTabClick">
                    <el-tab-pane label="登录" name="/login">
                        <el-form
                                :model="formLogin"
                                status-icon
                                :rules="formLoginRules"
                                ref="formLogin"
                                class="demo-ruleForm">
                            <div style="margin-top: 30px">

                                <el-form-item
                                        prop="name">
                                    <el-input
                                            placeholder="请输入账号"
                                            prefix-icon="icon-user-o"
                                            v-model="formLogin.name">
                                    </el-input>
                                </el-form-item>

                                <el-form-item
                                        :error="loginPasswordErrorMessage"
                                        prop="password">
                                    <el-input
                                            type="password"
                                            placeholder="请输入密码"
                                            prefix-icon="icon-lock-o"
                                            v-model="formLogin.password">
                                    </el-input>
                                </el-form-item>

                                <el-form-item
                                        ref="captchaLogin"
                                        prop="geetest_seccode"
                                        style="margin: 30px 0">
                                    <div id="captcha-login"
                                         v-model="formLogin.geetest_seccode"></div>
                                </el-form-item>
                                <el-form-item>
                                    <el-row :gutter="20">
                                        <el-col :span="12">
                                            <el-button
                                                    type="primary"
                                                    @click="handleSubmitLoginForm('form')"
                                                    style="width: 100%">
                                                登录
                                            </el-button>
                                        </el-col>
                                        <el-col
                                                :span="6"
                                                :offset="6"
                                                style="text-align: right">
                                            <span>忘记密码?</span>
                                        </el-col>
                                    </el-row>
                                </el-form-item>
                            </div>
                        </el-form>

                    </el-tab-pane>
                    <el-tab-pane label="注册" name="/register">
                        <el-form
                                :model="formRegister"
                                status-icon
                                :rules="formRegisterRules"
                                ref="formRegister"
                                class="demo-ruleForm">

                            <el-form-item prop="name">
                                <el-input
                                        class="input-box"
                                        placeholder="请输入您要注册的登录账号"
                                        prefix-icon="icon-user-o"
                                        v-model="formRegister.name">
                                </el-input>
                            </el-form-item>

                            <el-form-item prop="username">
                                <el-input
                                        class="input-box"
                                        placeholder="请输入账号呢称"
                                        prefix-icon="icon-user-o"
                                        v-model="formRegister.username">
                                </el-input>
                            </el-form-item>

                            <el-form-item prop="email">
                                <el-input
                                        class="input-box"
                                        placeholder="请输入您的邮箱"
                                        prefix-icon="icon-youxiang"
                                        v-model="formRegister.email">
                                </el-input>
                            </el-form-item>

                            <el-form-item prop="qq">
                                <el-input
                                        class="input-box"
                                        placeholder="请输入您的QQ"
                                        prefix-icon="icon-QQ"
                                        v-model="formRegister.qq">
                                </el-input>
                            </el-form-item>

                            <el-form-item prop="phone">
                                <el-input
                                        class="input-box"
                                        placeholder="请输入您的手机号"
                                        prefix-icon="icon-shouji"
                                        v-model="formRegister.phone">
                                </el-input>
                            </el-form-item>

                            <el-form-item prop="password">
                                <el-input
                                        type="password"
                                        class="input-box"
                                        placeholder="请输入您的账号密码"
                                        prefix-icon="icon-lock-o"
                                        v-model="formRegister.password">
                                </el-input>
                            </el-form-item>

                            <el-form-item prop="password_confirmation">
                                <el-input
                                        type="password"
                                        class="input-box"
                                        placeholder="请再次输入您的账号密码"
                                        prefix-icon="icon-lock-o"
                                        v-model="formRegister.password_confirmation">
                                </el-input>
                            </el-form-item>

                            <el-form-item
                                    ref="captchaRegister"
                                    prop="geetest_seccode"
                                    style="margin: 30px 0">
                                <div id="captcha-register"
                                     v-model="formRegister.geetest_seccode"></div>
                            </el-form-item>

                            <el-form-item>
                                <el-button
                                        style="width: 100%"
                                        type="primary"
                                        @click="handleSubmitRegisterForm()">
                                    确定
                                </el-button>
                            </el-form-item>
                        </el-form>
                    </el-tab-pane>
                </el-tabs>
            </div>

            <div style="background: #fd570d;">
                <img height="500px" src="/frontend/v1/images/right-login.jpg" alt="">
            </div>
        </el-container>
    </div>
</template>

<script>
    export default {
        name: "Login",
        computed:{
            active: {
                get: function () {
                    return this.$route.path;
                },
                set: function () {
                }
            },
        },
        data() {
            var validateCaptcha = (rule, value, callback) => {
                if (value == '') {
                    callback(new Error('请点击按钮完成验证'));
                } else {
                    callback();
                }
            };
            var validatePass = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请再次输入密码'));
                } else if (value !== this.formRegister.password) {
                    callback(new Error('两次输入密码不一致!'));
                } else {
                    callback();
                }
            };
            return {
                // active: '/login',
                loginPasswordErrorMessage:'',
                formLogin: {
                    name: '',
                    password: '',
                    geetest_challenge: '',
                    geetest_seccode: '',
                    geetest_validate: '',
                },
                formLoginRules: {
                    name: [
                        {required: true, message: '请输入账号', trigger: 'blur'}
                    ],
                    password: [
                        {required: true, message: '请输入密码', trigger: 'blur'}
                    ],
                    geetest_seccode: [
                        {validator: validateCaptcha, trigger: 'change'}
                    ],
                },
                registerPassword: '',
                formRegister: {
                    name: '',
                    email: '',
                    qq: '',
                    phone: '',
                    username: '',
                    password: '',
                    password_confirmation: '',
                    geetest_challenge: '',
                    geetest_seccode: '',
                    geetest_validate: '',
                },
                formRegisterErrorMessage: {
                    name: '',
                    email: '',
                    qq: '',
                    phone: '',
                    username: '',
                    password: '',
                    password_confirmation: '',
                    geetest_challenge: '',
                    geetest_seccode: '',
                    geetest_validate: '',
                },
                formRegisterRules: {
                    name: [
                        {required: true, message: '请输入账号', trigger: 'blur'}
                    ],
                    username: [
                        {required: true, message: '请输入账号呢称', trigger: 'blur'}
                    ],
                    email: [
                        {required: true, message: '请输入您的邮箱', trigger: 'blur'},
                        {
                            validator:(rule,value,callback)=>{
                                if(value != ""){
                                    if((/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/).test(value) == false){
                                        callback(new Error("请输入正确的邮箱地址"));
                                    }else{
                                        callback();
                                    }
                                }else{
                                    callback();
                                }
                            },
                            trigger:'blur'
                        },
                    ],
                    qq: [
                        {required: true, message: '请输入QQ', trigger: 'blur'},
                        {
                            validator:(rule,value,callback)=>{
                                if((/^[0-9]{5,12}$/).test(value) == false){
                                    callback(new Error("请输入正确的QQ"));
                                }else{
                                    callback();
                                }
                            },
                            trigger:'blur'
                        },
                    ],
                    phone: [
                        {required: true, message: '请输入手机号', trigger: 'blur'},
                        {
                            validator:(rule,value,callback)=>{
                                if(value != ""){
                                    if((/^1[34578]\d{9}$/).test(value) == false){
                                        callback(new Error("请输入正确的手机号"));
                                    }else{
                                        callback();
                                    }
                                }else{
                                    callback();
                                }
                            },
                            trigger:'blur'
                        },
                    ],
                    password: [
                        {required: true, message: '请输入密码', trigger: 'blur'}
                    ],
                    password_confirmation: [
                        {validator: validatePass, trigger: 'blur'}
                    ],
                    geetest_seccode: [
                        {validator: validateCaptcha, trigger: 'change'}
                    ],
                }
            }
        },
        methods: {
            // 初始化登录验证码
            initLoginCaptcha() {
                let currentThis = this;
                this.$api.captcha({}).then(res => {
                    window.initGeetest({
                        gt: res.gt,
                        challenge: res.challenge,
                        offline: !res.success,
                        new_captcha: true,
                        product: 'float',
                        area: '#form',
                        next_width: '50%',
                        bg_color: '#ffffff',
                        lang: 'zh-cn',
                        http: 'http://',
                        width: '100%',
                    }, function (captchaObj) {
                        captchaObj.appendTo("#captcha-login");
                        captchaObj.onReady(function () {
                        }).onSuccess(function () {
                            currentThis.formLogin.geetest_challenge = document.getElementById('captcha-login').getElementsByTagName('input')['geetest_challenge'].value;
                            currentThis.formLogin.geetest_seccode = document.getElementById('captcha-login').getElementsByTagName('input')['geetest_seccode'].value;
                            currentThis.formLogin.geetest_validate = document.getElementById('captcha-login').getElementsByTagName('input')['geetest_validate'].value;
                            currentThis.$refs['captchaLogin'].clearValidate();
                        }).onError(function () {

                        })
                    });
                });
            },
            // 初始化注册验证码
            initRegisterCaptcha() {
                let currentThis = this;
                this.$api.captcha({}).then(res => {
                    window.initGeetest({
                        gt: res.gt,
                        challenge: res.challenge,
                        offline: !res.success,
                        new_captcha: true,
                        product: 'float',
                        area: '#form',
                        next_width: '50%',
                        bg_color: 'black',
                        lang: 'zh-cn',
                        http: 'http://',
                        width: '100%',
                    }, function (captchaObj) {
                        captchaObj.appendTo("#captcha-register");
                        captchaObj.onReady(function () {
                        }).onSuccess(function () {
                            currentThis.formRegister.geetest_challenge = document.getElementById('captcha-register').getElementsByTagName('input')['geetest_challenge'].value;
                            currentThis.formRegister.geetest_seccode = document.getElementById('captcha-register').getElementsByTagName('input')['geetest_seccode'].value;
                            currentThis.formRegister.geetest_validate = document.getElementById('captcha-register').getElementsByTagName('input')['geetest_validate'].value;
                            currentThis.$refs['captchaRegister'].clearValidate();
                        }).onError(function () {
                        })
                    });
                });
            },
            handleTabClick(tab, event) {
                sessionStorage.setItem('loginRegisterActive', tab.name);
                this.$router.push(tab.name);
            },
            // 提交登录表单
            handleSubmitLoginForm() {
                this.$refs.formLogin.validate((valid) => {
                    if (valid) {
                        this.formLogin.password = encrypt(this.formLogin.password);
                        this.$api.login(this.formLogin).then(res => {
                            if (res.status == 1) {
                                sessionStorage.setItem('openMenu', '/v2/order/');
                                window.location.href = res.content.url;
                            } else {
                                this.loginPasswordErrorMessage = res.message;
                                this.formLogin.password = '';
                            }
                        });
                    }
                });
            },
            // 提交注册表单
            handleSubmitRegisterForm() {
                this.$refs.formRegister.validate((valid) => {
                    if (valid) {
                        this.registerPassword = this.formRegister.password_confirmation;
                        this.formRegister.password = encrypt(this.formRegister.password);
                        this.formRegister.password_confirmation = encrypt(this.formRegister.password_confirmation);
                        this.$api.register(this.formRegister).then(res => {
                            if (res.status == 1) {
                                sessionStorage.setItem('openMenu', '/v2/order/');
                                this.$confirm('注册成功, 现在登录?', '提示', {
                                    confirmButtonText: '确定',
                                    cancelButtonText: '取消',
                                    type: 'warning'
                                }).then(() => {
                                    this.$route.push('/login');
                                }).catch(() => {

                                });
                            } else {
                                // console.log(res.content);
                                // this.formRegister.password = this.registerPassword;
                                // this.formRegister.password_confirmation = this.registerPassword;

                                // res.content.forEach(function (key, item) {
                                //     console.log(key.email[0],item);
                                //     // this.formRegisterErrorMessage.key = item[0];
                                // });

                                // this.loginPasswordErrorMessage = res.message;
                                // this.formLogin.password = '';
                            }
                        });
                    }
                });
            },
        },
        created() {
            this.initLoginCaptcha();
            this.initRegisterCaptcha();
        }
    }
</script>

<style lang="less">
    .login-register {
        .el-input__inner {
            border: none;
            border-radius: 0;
            border-bottom: 1px solid #f0f2f5
        }
        .el-input__prefix .el-input__icon {
            font-size: 26px;
        }
        .icon-youxiang {
            font-size: 20px;
        }
        /*@keyframes move_wave {*/
        /*0% {*/
        /*transform: translateX(0) translateZ(0) scaleY(1)*/
        /*}*/
        /*50% {*/
        /*transform: translateX(-25%) translateZ(0) scaleY(0.55)*/
        /*}*/
        /*100% {*/
        /*transform: translateX(-50%) translateZ(0) scaleY(1)*/
        /*}*/
        /*}*/
        /*.waveWrapper {*/
        /*overflow: hidden;*/
        /*position: absolute;*/
        /*left: 0;*/
        /*right: 0;*/
        /*!*bottom: 0;*!*/
        /*top: 0;*/
        /*margin: auto;*/
        /*width: 1000px;*/
        /*height: 100px;*/
        /*}*/
        /*.waveWrapperInner {*/
        /*position: absolute;*/
        /*width: 100%;*/
        /*overflow: hidden;*/
        /*height: 100%;*/
        /*bottom: -1px;*/
        /*background-image: linear-gradient(to top, #fd6019 20%, #fd6019 80%);*/
        /*}*/
        /*.bgTop {*/
        /*z-index: 15;*/
        /*opacity: 0.5;*/
        /*}*/
        /*.bgMiddle {*/
        /*z-index: 10;*/
        /*opacity: 0.75;*/
        /*}*/
        /*.bgBottom {*/
        /*z-index: 5;*/
        /*}*/
        /*.wave {*/
        /*position: absolute;*/
        /*left: 0;*/
        /*width: 200%;*/
        /*height: 100%;*/
        /*background-repeat: repeat no-repeat;*/
        /*background-position: 0 bottom;*/
        /*transform-origin: center bottom;*/
        /*}*/
        /*.waveTop {*/
        /*background-size: 50% 100px;*/
        /*}*/
        /*.waveAnimation .waveTop {*/
        /*animation: move-wave 3s;*/
        /*-webkit-animation: move-wave 3s;*/
        /*-webkit-animation-delay: 1s;*/
        /*animation-delay: 1s;*/
        /*}*/
        /*.waveMiddle {*/
        /*background-size: 50% 120px;*/
        /*}*/
        /*.waveAnimation .waveMiddle {*/
        /*animation: move_wave 10s linear infinite;*/
        /*}*/
        /*.waveBottom {*/
        /*background-size: 50% 100px;*/
        /*}*/
        /*.waveAnimation .waveBottom {*/
        /*animation: move_wave 15s linear infinite;*/
        /*}*/
    }
</style>