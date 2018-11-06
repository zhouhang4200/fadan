<template>
    <div class="main content amount-flow">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">
                <el-col :span="5">
                    <el-form-item label="账号">
                        <el-input v-model="searchParams.name"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item label="员工昵称">
                        <el-select v-model="searchParams.username" placeholder="请选择">
                            <el-option v-for="(value, key) of AccountEmployeeUser" :value="key" :key="key"  :label="value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item label="岗位">
                        <el-select v-model="searchParams.station" placeholder="请选择">
                            <el-option v-for="(value, key) of AccountEmployeeStation" :value="key" :key="key"  :label="value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">查询</el-button>
                    <el-button
                               type="primary"
                               size="small"
                               @click="employeeAdd()">新增</el-button>
                </el-form-item>
            </el-row>
        </el-form>
        <el-table
                :data="tableData"
                border
                style="width: 100%">
            <el-table-column
                    prop="id"
                    label="编号"
                    width="100">
            </el-table-column>
            <el-table-column
                    prop="username"
                    label="员工昵称"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="name"
                    label="账号"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="leveling_type"
                    label="代练类型"
                    width="100">
                <template slot-scope="scope">
                    {{ scope.row.leveling_type == 1  ? '接单' : '发单' }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="station"
                    label="岗位"
                    width="100" >
                <template slot-scope="scope" >
                    <div v-for="(value, key) of scope.row.new_roles">{{ value.name ? value.name : '' }}</div>
                </template>
            </el-table-column>
            <el-table-column
                    prop="qq"
                    label="QQ"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="wechat"
                    label="微信"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="phone"
                    label="电话"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="updated_at"
                    label="最后操作时间"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="remark"
                    label="备注"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="status"
                    label="状态"
                    width="150">
                <template slot-scope="scope">
                    <el-switch v-model=scope.row.status @change="handleSwitch($event, scope.row)" active-text="启用" inactive-text="禁用" :active-value=0 :inactive-value=1></el-switch>
                </template>
            </el-table-column>
            <el-table-column
                label="操作"
                width="250">
                <template slot-scope="scope">
                    <el-button
                               type="primary"
                               size="small"
                               @click="employeeUpdate(scope.row)">编辑</el-button>
                    <el-button
                               type="primary"
                               size="small"
                               @click="employeeDelete(scope.row.id)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination
                style="margin-top: 25px"
                @current-change="handleCurrentChange"
                :current-page.sync="searchParams.page"
                :page-size="15"
                layout="prev, pager, next, jumper"
                :total="TotalPage">
        </el-pagination>

        <el-dialog title="岗位编辑" :visible.sync="dialogFormVisible">
            <el-form :model="form" ref="form" :rules="rules" label-width="80px">
                <el-form-item label="*账号" prop="name">
                    <el-input v-model="form.name" name="name" autocomplete="off" :disabled="isDisabled"></el-input>
                </el-form-item>
                <el-form-item label="*昵称" prop="username">
                    <el-input v-model="form.username" name="username" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="*密码" prop="password">
                    <el-input v-model="form.password" autocomplete="off" placeholder="不填写则为原密码"></el-input>
                </el-form-item>
                <el-form-item label="*类型" prop="leveling_type">
                    <el-radio v-model="form.leveling_type" :label=1 autocomplete="off">接单</el-radio>
                    <el-radio v-model="form.leveling_type" :label=2 autocomplete="off">发单</el-radio>
                </el-form-item>
                <el-form-item label="*岗位" prop="station">
                    <el-checkbox-group v-model="form.station">
                        <el-checkbox v-for="item in AccountEmployeeStation" :key=item.id  :label=item.id>{{ item.name }}</el-checkbox>
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
                    <el-button v-if="isAdd" type="primary" @click="submitFormAdd('form')">确认添加</el-button>
                    <el-button v-if="isUpdate" type="primary" @click="submitFormUpdate('form')">确认修改</el-button>
                    <el-button @click="dialogFormVisible = false">取消</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        props: [
            'AccountEmployeeUserApi',
            'AccountEmployeeStationApi',
            'AccountEmployeeDataListApi',
            'AccountEmployeeSwitchApi',
            'AccountEmployeeUpdateApi',
            'AccountEmployeeAddApi',
            'AccountEmployeeDeleteApi',
            'AccountEmployeeCreateApi',
        ],
        methods: {
            employeeAdd() {
                this.dialogFormVisible = true;
                this.isAdd=true;
                this.isUpdate=false;
                this.isDisabled=false;
                this.$refs.form.resetFields();
            },
            employeeUpdate(row) {
                this.dialogFormVisible = true;
                this.form = row;
                let arr = [];
                if (row.new_roles) {
                    row.new_roles.forEach(function (item) {
                        arr.push(item.id);
                    })
                    this.form.station = [];
                }
                this.form.station=[];
                this.isAdd=false;
                this.isUpdate=true;
                this.isDisabled=true;
            },
            submitFormUpdate(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        axios.post(this.AccountEmployeeUpdateApi, this.form).then(res => {
                            this.$message({
                                showClose: true,
                                type: res.data.status == 1 ? 'success' : 'error',
                                message: res.data.message
                            });
                            this.handleTableData();
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
            submitFormAdd(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        axios.post(this.AccountEmployeeAddApi, this.form).then(res => {
                            this.$message({
                                showClose: true,
                                type: res.data.status == 1 ? 'success' : 'error',
                                message: res.data.message
                            });
                            this.handleTableData();
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
            // 加载数据
            handleTableData(){
                axios.post(this.AccountEmployeeDataListApi, this.searchParams).then(res => {
                    this.tableData = res.data.data;
                    this.TotalPage = res.data.total;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            handleUser(){
                axios.post(this.AccountEmployeeUserApi, this.searchParams).then(res => {
                 this.AccountEmployeeUser = res.data;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
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
            handleSearch() {
                this.handleTableData();
            },

            handleCurrentChange(page) {
                this.searchParams.page = page;
                this.handleTableData();
            },
            handleSwitch(value, row) {
                axios.post(this.AccountEmployeeSwitchApi, {status:value, user_id:row.id}).then(res => {
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
            },
            employeeDelete (id) {
                axios.post(this.AccountEmployeeDeleteApi, {user_id:id}).then(res => {
                    this.$message({
                        showClose: true,
                        type: res.data.status == 1 ? 'success' : 'error',
                        message: res.data.message
                    });
                    this.handleTableData();
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
        },
        created () {
            this.$store.commit('handleOpenMenu', '3');
            this.$store.commit('handleOpenSubmenu', '3-5');
            this.handleTableData();
            this.handleUser();
            this.handleStation();
        },
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
                if (value && (value.length < 6 || value.length > 22)) {
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
                allStation:[],
                isDisabled:false,
                isAdd:true,
                isUpdate:false,
                dialogFormVisible:false,
                AccountEmployeeUser:{},
                AccountEmployeeStation:{},
                searchParams:{
                    username:'',
                    name:'',
                    station:'',
                    page:1,
                },
                TotalPage:0,
                tableData: [],
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
        }
    }
</script>