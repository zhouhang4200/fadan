<template>
    <div class="main content amount-flow">
        <el-form :model="form" ref="form" :rules="rules" label-width="80px">
            <el-form-item label="*账号" prop="name">
                <el-input v-model="form.name" name="name" autocomplete="off"></el-input>
            </el-form-item>
            <el-form-item label="*昵称" prop="username">
                <el-input v-model="form.username" name="username"  autocomplete="off"></el-input>
            </el-form-item>
            <el-form-item label="*密码" prop="password">
                <el-input v-model="form.password" name="password" autocomplete="off"></el-input>
            </el-form-item>
            <el-form-item label="*类型" prop="leveling_type">
                <el-radio-group v-model="form.leveling_type">
                    <el-radio label="1" name="leveling_type" autocomplete="off">接单</el-radio>
                    <el-radio label="2" name="leveling_type" autocomplete="off">发单</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="*岗位" prop="station">
                <el-checkbox-group v-model="form.station">
                    <el-checkbox v-for="(value, key) of AccountEmployeeStation" :value="key" :key="key"  :label="key">{{ value }}</el-checkbox>
                </el-checkbox-group>
            </el-form-item>
            <el-form-item label="*电话" prop="phone">
                <el-input v-model.number="form.phone" autocomplete="off"></el-input>
            </el-form-item>
            <el-form-item label="QQ" prop="qq">
                <el-input v-model="form.qq" name="qq" autocomplete="off"></el-input>
            </el-form-item>
            <el-form-item label="微信" prop="wechat">
                <el-input v-model="form.wechat" name="wechat" autocomplete="off"></el-input>
            </el-form-item>
            <el-form-item label="备注" prop="remark">
                <el-input type="textarea" v-model="form.remark" name="remark" autocomplete="off"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="submitForm('form')">立即创建</el-button>
                <el-button @click="resetForm('form')">重置</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script>
    export default {
        props: [
            'AccountEmployeeAddApi',
            'AccountEmployeeStationApi',
        ],
        data() {
            var checkPhone = (rule, value, callback) => {
                if (!value) {
                    return callback(new Error('必填项不能为空!'));
                }

                if (!Number.isInteger(value)) {
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
            var validatePass = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('必填项不能为空!'));
                } else if (value.length < 6 || value.length > 22) {
                    callback(new Error('请填写6-22位长度的密码！'));
                }
                callback();
            };
            var checkHas = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('必填项不能为空!'));
                }
                callback();
            };
            return {
                AccountEmployeeStation:[],
                rules:{
                    password: [{ validator: validatePass, trigger: 'blur' }],
                    phone: [{ validator: checkPhone, trigger: 'blur' }],
                    username:[{ validator: checkHas, trigger: 'blur' }],
                    name:[{ validator: checkHas, trigger: 'blur' }],
                    leveling_type:[{ validator: checkHas, trigger: 'blur' }],
                },
                form: {
                    username: '',
                    name: '',
                    phone: '',
                    password: '',
                    leveling_type: '',
                    station: [],
                    qq: '',
                    wechat: '',
                    remark: ''
                }
            }
        },
        created () {
            this.handleStation();
        },
        methods: {
            handleStation(){
                axios.post(this.AccountEmployeeStationApi, this.searchParams).then(res => {
                    this.AccountEmployeeStation = res.data;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        axios.post(this.AccountEmployeeAddApi, this.form).then(res => {
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
            resetForm(formName) {
                this.$refs[formName].resetFields();
            }
        }
    }
</script>