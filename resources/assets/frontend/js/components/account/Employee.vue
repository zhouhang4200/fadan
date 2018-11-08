<template>
    <div class="main content amount-flow">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">
                <el-col :span="4">
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
                <el-col :span="6">
                    <el-form-item label="岗位">
                        <el-select v-model="searchParams.station" placeholder="请选择">
                            <el-option v-for="item in AccountEmployeeStation" :value=item.id :key=item.id  :label=item.name>{{item.name}}</el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="handleSearch">查询</el-button>
                        <el-button
                                type="primary"
                                size="small"
                                @click="employeeAdd()">新增</el-button>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
        <el-table
                :data="tableData"
                border
                style="width: 100%; margin-top: 1px">
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
                    prop="station"
                    label="岗位"
                    width="150" >
                <template slot-scope="scope" >
                    <div v-for="item in scope.row.new_roles">{{ item.name ? item.name : '' }}</div>
                </template>
            </el-table-column>
            <el-table-column
                    prop="leveling_type"
                    label="代练类型"
                    width="">
                <template slot-scope="scope">
                    {{ scope.row.leveling_type == 1  ? '接单' : '发单' }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="qq"
                    label="QQ"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="wechat"
                    label="微信"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="phone"
                    label="电话"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="remark"
                    label="备注"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="status"
                    label="状态"
                    width="200">
                <template slot-scope="scope">
                    <el-switch v-model=scope.row.status @change="handleSwitch($event, scope.row)" active-text="启用" inactive-text="禁用" :active-value=0 :inactive-value=1></el-switch>
                </template>
            </el-table-column>
            <el-table-column
                    prop="updated_at"
                    label="最后操作时间"
                    width="200">
            </el-table-column>
            <el-table-column
                label="操作"
                width="200">
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

        <el-dialog :title="title" :visible.sync="dialogFormVisible">
            <el-form :model="form" ref="form" :rules="rules" label-width="80px">
                <el-form-item label="账号" prop="name">
                    <el-input v-model="form.name" name="name" autocomplete="off" :disabled="isDisabled"></el-input>
                </el-form-item>
                <el-form-item label="昵称" prop="username">
                    <el-input v-model="form.username" name="username" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="密码" prop="password">
                    <el-input v-model="form.password" autocomplete="off" placeholder="不填写则为原密码"></el-input>
                </el-form-item>
                <el-form-item label="类型" prop="leveling_type">
                    <el-radio v-model="form.leveling_type" :label=1 autocomplete="off">接单</el-radio>
                    <el-radio v-model="form.leveling_type" :label=2 autocomplete="off">发单</el-radio>
                </el-form-item>
                <el-form-item label="岗位" prop="station">
                    <el-checkbox-group v-model="form.hasStation" @change="switchChange(form.hasStation)">
                        <el-checkbox v-for="item in form.allStation" :key="item.id" :value=item.id :label=item.id>{{ item.name }}</el-checkbox>
                    </el-checkbox-group>
                </el-form-item>
                <el-form-item label="电话" prop="phone">
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
                    <el-button @click="employeeCancel('form')">取消</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        methods: {
            // 新增按钮
            employeeAdd() {
                this.dialogFormVisible = true;
                this.isAdd=true;
                this.isUpdate=false;
                this.isDisabled=false;
                this.title="新增";
                this.form.username='';
                this.form.name='';
                this.form.hasStation=[];
                this.form.phone='';
                this.form.leveling_type='';
                this.form.password='';
                this.form.station='';
                this.form.qq='';
                this.form.wechat='';
                this.form.remark='';
            },
            // 编辑按钮
            employeeUpdate(row) {
                this.dialogFormVisible = true;
                this.title="修改";
                this.form=JSON.parse(JSON.stringify(row));
                this.isAdd=false;
                this.isUpdate=true;
                this.isDisabled=true;
            },
            // 多选框改变事件
            switchChange($stationIds) {
                this.form.station=$stationIds;
            },
            // 修改
            submitFormUpdate(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.$api.AccountEmployeeUpdate(this.form).then(res => {
                            this.$message({
                                showClose: true,
                                type: res.status == 1 ? 'success' : 'error',
                                message: res.message
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
            // 新增
            submitFormAdd(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.$api.AccountEmployeeAdd(this.form).then(res => {
                            this.$message({
                                showClose: true,
                                type: res.status == 1 ? 'success' : 'error',
                                message: res.message
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
                    this.$refs[formName].clearValidate();
                });
            },
            // 取消按钮
            employeeCancel(formName) {
                this.dialogFormVisible = false;
                this.$refs[formName].clearValidate();
            },
            // 加载数据
            handleTableData(){
                this.$api.AccountEmployeeDataList(this.searchParams).then(res => {
                    this.tableData = res.data;
                    this.TotalPage = res.total;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            // 所有子账号
            handleUser(){
                this.$api.AccountEmployeeUser(this.searchParams).then(res => {
                 this.AccountEmployeeUser = res;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            // 所有岗位
            handleStation(){
                this.$api.AccountEmployeeStation(this.searchParams).then(res => {
                   this.AccountEmployeeStation = res;
                   this.form.allStation=res;
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
            // 子账号禁用
            handleSwitch(value, row) {
                this.$api.AccountEmployeeSwitch({status:value, user_id:row.id}).then(res => {
                    this.$message({
                        showClose: true,
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            // 删除
            employeeDelete (id) {
                this.$api.AccountEmployeeDelete({user_id:id}).then(res => {
                    this.$message({
                        showClose: true,
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
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
            var validatePass = (rule, value, callback) => {
                if (value && (value.length < 6 || value.length > 22)) {
                    callback(new Error('请填写6-22位长度的密码！'));
                }
                callback();
            };
            return {
                title:'新增',
                allStation:[],
                isDisabled:false,
                isAdd:true,
                isUpdate:false,
                dialogFormVisible:false,
                AccountEmployeeUser:{},
                AccountEmployeeStation:[],
                searchParams:{
                    username:'',
                    name:'',
                    station:'',
                    page:1
                },
                TotalPage:0,
                tableData: [],
                rules:{
                    password: [{ validator: validatePass, trigger: 'blur' }],
                    phone: [{ required: true, message:'必填项不可为空!', trigger: 'blur' }, { validator: checkPhone, trigger: 'blur' }],
                    username:[{ required: true, message:'必填项不可为空!', trigger: 'blur' }],
                    name:[{ required: true, message:'必填项不可为空!', trigger: 'blur' }],
                    leveling_type:[{ required: true, message:'必填项不可为空!', trigger: 'blur' }]
                },
                form: {
                    username: '',
                    name: '',
                    hasStation:[],
                    allStation:[],
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