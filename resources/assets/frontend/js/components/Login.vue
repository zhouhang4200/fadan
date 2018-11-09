<template>
    <div class="login" style="width: 1000px;margin:120px auto;background: #ffffff">
        <el-container>

            <div style="padding: 50px">
                <el-tabs v-model="active" @tab-click="handleClick">
                    <el-tab-pane label="登录" name="login">
                        <el-form :model="form" status-icon :rules="rules" ref="form" class="demo-ruleForm">
                            <div style="width: 400px">

                                <el-input
                                        class="input-box"
                                        placeholder="请输入内容"
                                        prefix-icon="el-icon-search"
                                        v-model="input21">
                                </el-input>

                                <el-input
                                        class="input-box"
                                        placeholder="请输入内容"
                                        prefix-icon="el-icon-search"
                                        v-model="input21">
                                </el-input>
                                <el-input
                                        class="input-box"
                                        placeholder="请输入内容"
                                        prefix-icon="el-icon-search"
                                        v-model="input21">
                                </el-input>
                                <el-input
                                        class="input-box"
                                        placeholder="请输入内容"
                                        prefix-icon="el-icon-search"
                                        v-model="input21">
                                </el-input>


                                <el-form-item>
                                    <el-button type="primary" @click="handleSubmitForm('form')">提交</el-button>
                                    <el-button @click="resetForm('form')">重置</el-button>
                                </el-form-item>
                            </div>
                        </el-form>
                    </el-tab-pane>
                    <el-tab-pane label="注册" name="register">
                        <el-form :model="form" status-icon :rules="rules" ref="form" class="demo-ruleForm">
                            <div style="width: 400px">

                                <el-input
                                        class="input-box"
                                        placeholder="请输入内容"
                                        prefix-icon="el-icon-search"
                                        v-model="input21">
                                </el-input>

                                <el-input
                                        class="input-box"
                                        placeholder="请输入内容"
                                        prefix-icon="el-icon-search"
                                        v-model="input21">
                                </el-input>
                                <el-input
                                        class="input-box"
                                        placeholder="请输入内容"
                                        prefix-icon="el-icon-search"
                                        v-model="input21">
                                </el-input>
                                <el-input
                                        class="input-box"
                                        placeholder="请输入内容"
                                        prefix-icon="el-icon-search"
                                        v-model="input21">
                                </el-input>
                                <el-input
                                        class="input-box"
                                        placeholder="请输入内容"
                                        prefix-icon="el-icon-search"
                                        v-model="input21">
                                </el-input>
                                <el-input
                                        class="input-box"
                                        placeholder="请输入内容"
                                        prefix-icon="el-icon-search"
                                        v-model="input21">
                                </el-input>
                                <el-input
                                        class="input-box"
                                        placeholder="请输入内容"
                                        prefix-icon="el-icon-search"
                                        v-model="input21">
                                </el-input>

                                <el-form-item>
                                    <el-button type="primary" @click="handleSubmitForm('form')">提交</el-button>
                                    <el-button @click="resetForm('form')">重置</el-button>
                                </el-form-item>
                            </div>
                        </el-form>
                    </el-tab-pane>
                </el-tabs>
            </div>

            <div class="" style="">
                <img height="500px" src="/frontend/v1/images/right-login.jpg" alt="">
            </div>
        </el-container>
    </div>
</template>

<script>
    export default {
        name: "Login",
        data() {
            return {
                active:'login',
                form:{
                    username:'',
                    password:'',
                }
            }
        },
        methods:{
            handleSubmitForm(){

            }
        }
    }
</script>

<style  lang="less">
   .login {
        .input-box {
            margin-bottom: 15px;
        }
        .el-input__inner {
            border: none;
            border-radius: 0;
            border-bottom: 1px solid #dcdfe6
       }
   }
</style>