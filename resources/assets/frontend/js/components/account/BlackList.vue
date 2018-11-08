<template>
    <div class="main content amount-flow">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">
                <el-col :span="5">
                    <el-form-item label="打手昵称">
                        <el-select v-model="searchParams.hatchet_man_name" placeholder="请选择">
                            <el-option v-for="(value, key) of AccountBlackListName" :value="value" :key="key" :label="value">{{ value }}</el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item label="电话">
                        <el-input v-model="searchParams.hatchet_man_phone"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item label="QQ">
                        <el-input v-model="searchParams.hatchet_man_qq"></el-input>
                    </el-form-item>
                </el-col>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">查询</el-button>
                    <el-button
                            type="primary"
                            size="small"
                            @click="blackListAdd()">新增</el-button>
                </el-form-item>
            </el-row>
        </el-form>
        <el-table
                :data="tableData"
                border
                style="width: 100%; margin-top: 1px">
            <el-table-column
                    prop="hatchet_man_name"
                    label="打手昵称"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="hatchet_man_phone"
                    label="电话"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="hatchet_man_qq"
                    label="QQ"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="content"
                    label="备注"
                    width="">
            </el-table-column>
            <el-table-column
                    label="操作"
                    width="250">
                <template slot-scope="scope">
                    <el-button
                            type="primary"
                            size="small"
                            @click="blackListUpdate(scope.row)">编辑</el-button>
                    <el-button
                            type="primary"
                            size="small"
                            @click="blackListDelete(scope.row.id)">删除</el-button>
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
                <el-form-item label="打手昵称" prop="hatchet_man_name">
                    <el-input v-model="form.hatchet_man_name" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="打手电话" prop="hatchet_man_phone">
                    <el-input v-model.number="form.hatchet_man_phone" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="打手QQ" prop="hatchet_man_qq">
                    <el-input v-model.number="form.hatchet_man_qq" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="备注" prop="content">
                    <el-input type="textarea" v-model="form.content" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button v-if="isAdd" type="primary" @click="submitFormAdd('form')">确认</el-button>
                    <el-button v-if="isUpdate" type="primary" @click="submitFormUpdate('form')">确认修改</el-button>
                    <el-button @click="blackListCancel('form')">取消</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        methods: {
            //新增按钮
            blackListAdd(){
                this.isAdd=true;
                this.isUpdate=false;
                this.title='打手黑名单新增',
                this.dialogFormVisible = true;
                this.form={
                    hatchet_man_name: '',
                    hatchet_man_phone: '',
                    hatchet_man_qq: '',
                    content: ''
                };
            },
            // 编辑按钮
            blackListUpdate(row) {
                this.isAdd=false;
                this.isUpdate=true;
                this.title='打手黑名单修改',
                this.dialogFormVisible = true;
                this.form=JSON.parse(JSON.stringify(row));
            },
            // 取消按钮
            blackListCancel(formName) {
                this.dialogFormVisible = false;
                this.$refs[formName].clearValidate();
            },
            // 添加
            submitFormAdd(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.$api.AccountBlackListAdd(this.form).then(res => {
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
                    } else {
                        return false;
                    }
                    this.$refs[formName].clearValidate();
                });
                this.handleTableData();
            },
            // 修改
            submitFormUpdate(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.$api.AccountBlackListUpdate(this.form).then(res => {
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
                    } else {
                        return false;
                    }
                });
                this.handleTableData();
            },
            // 加载数据
            handleTableData(){
                this.$api.AccountBlackListDataList(this.searchParams).then(res => {
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
            handleName(){
                this.$api.AccountBlackListName(this.searchParams).then(res => {
                    this.AccountBlackListName = res;
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
            // 删除
            blackListDelete (id) {
                this.$api.AccountBlackListDelete({id:id}).then(res => {
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
        created(){
            this.handleTableData();
            this.handleName();
        },
        data(){
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
                isAdd:true,
                isUpdate:false,
                title:'新增',
                url:'',
                dialogFormVisible:false,
                AccountBlackListName:{},
                searchParams:{
                    hatchet_man_name:'',
                    hatchet_man_phone:'',
                    hatchet_man_qq:'',
                    page:1
                },
                TotalPage:0,
                tableData: [],
                rules:{
                    hatchet_man_qq:[{ required: true, message:'必填项不可为空!', trigger: 'blur' }, { validator: checkQq, trigger: 'blur' }],
                    hatchet_man_name:[{ required: true, message:'必填项不可为空!', trigger: 'blur' }],
                    hatchet_man_phone:[{ required: true, message:'必填项不可为空!', trigger: 'blur' }, { validator: checkPhone, trigger: 'blur' }]
                },
                form: {
                    hatchet_man_name: '',
                    hatchet_man_phone: '',
                    hatchet_man_qq: '',
                    content: ''
                }
            }
        }
    }
</script>