<template>
    <div class="main content amount-flow">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">
                <el-col :span="5">
                    <el-form-item>
                        <el-button
                                type="primary"
                                size="small"
                                @click="ShowAddForm">新增</el-button>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
        <el-table
                :data="tableData"
                :height="tableHeight"
                border
                style="width: 100%; margin-top: 1px">
            <el-table-column
                    prop="id"
                    label="序号"
                    width="100">
            </el-table-column>
            <el-table-column
                    prop="name"
                    label="岗位名称"
                    width="100">
            </el-table-column>
            <el-table-column
                    prop="station"
                    label="岗位员工"
                    width="">
                <template slot-scope="scope">
                    <span style="margin-right: 10px" v-for="(value, key) of scope.row.new_users">{{ value.username ? value.username : '' }}</span>
                </template>
            </el-table-column>
            <el-table-column
                    prop="permission"
                    label="拥有权限"
                    width="">
                <template slot-scope="scope">
                    <span style="margin-right: 10px" v-for="(value, key) of scope.row.new_permissions">{{ value.alias ? value.alias : '' }}</span>
                </template>
            </el-table-column>
            <el-table-column
                    label="操作"
                    width="200">
                <template slot-scope="scope">
                    <el-button
                            type="primary"
                            size="small"
                            @click="ShowEditForm(scope.row)">编辑</el-button>
                    <el-button
                            type="primary"
                            size="small"
                            @click="stationDelete(scope.row.id)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination
                style="margin-top: 25px"
                background
                @current-change="handleCurrentChange"
                :current-page.sync="searchParams.page"
                :page-size="15"
                layout="total, prev, pager, next, jumper"
                :total="TotalPage">
        </el-pagination>

        <el-dialog title="新增岗位" :visible.sync="dialogFormVisibleAdd">
            <el-form :model="form" ref="form" :rules="rules" label-width="80px">
                <el-form-item label="岗位名称" prop="name">
                    <el-input v-model="form.name" name="name" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="拥有权限" prop="permission">
                    <el-tree
                            :props="defaultProps"
                            ref="tree"
                            :default-expand-all="expendAll"
                            :data="permissionTree"
                            node-key="id"
                            show-checkbox>
                    </el-tree>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="submitFormAdd('form')">确认添加</el-button>
                    <el-button @click="cancel('form')">取消</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>

        <el-dialog title="编辑岗位" :visible.sync="dialogFormVisible">
            <el-form :model="editForm" ref="editForm" :rules="editFormRules" label-width="80px">
                <el-form-item label="岗位名称" prop="name">
                    <el-input v-model="editForm.name" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="拥有权限" prop="checkedPermission">
                    <template v-for="item in stations">
                        <el-checkbox :indeterminate="isIndeterminate" v-model="item.id">{{item.alias}}</el-checkbox>
                        <!--<div style="margin: 15px 0;"></div>-->
                        <el-checkbox-group style="padding-left:25px" v-model="editForm.checkedPermission" @change="handleCheckedStationChange">
                            <el-checkbox v-for="option in item.new_permissions" :label="option.id" :key="option.id">{{option.alias}}</el-checkbox>
                        </el-checkbox-group>
                    </template>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="submitFormUpdate('editForm')">确认修改</el-button>
                    <el-button @click="cancel('editForm')">取消</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        methods: {
            // 新增按钮
            ShowAddForm() {
                this.dialogFormVisibleAdd = true;
                this.form={
                    name: '',
                    permission: ''
                };
            },
            // 编辑按钮
            ShowEditForm(row) {
                this.handleTableData();
                this.dialogFormVisible = true;
                this.editForm=JSON.parse(JSON.stringify(row));
            },
            // 取消按钮
            cancel(formName){
                this.dialogFormVisible = false;
                this.dialogFormVisibleAdd = false;
                this.$refs[formName].clearValidate();
            },
            // 添加
            submitFormAdd(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        let permission = '';
                        this.$refs.tree.getCheckedNodes().forEach(function(v) {
                            permission += v.id+',';
                        });
                        this.form.permission=permission;
                       this.$api.AccountStationAdd(this.form).then(res => {
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
            // 修改
            submitFormUpdate(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                       this.$api.AccountStationUpdate(this.editForm).then(res => {
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
            // 删除
            stationDelete (id) {
               this.$api.AccountStationDelete({id:id}).then(res => {
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
                this.handleTableData();
            },
            // 加载数据
            handleTableData(){
               this.$api.AccountStationDataList(this.searchParams).then(res => {
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
            // 获取当前用户所有的权限
            allPermissions() {
               this.$api.AccountStationPermission().then(res => {
                    this.permissionTree = res;
                    this.stations=res;
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
            handleCheckedStationChange(value) {
                let checkedCount = value.length;
                this.isIndeterminate = true;
            },
            // 表格高度计算
            handleTableHeight() {
                this.tableHeight = window.innerHeight - 318;
            },
        },
        created () {
            this.handleTableData();
            this.allPermissions();
            this.handleTableHeight();
            window.addEventListener('resize', this.handleTableHeight);
        },
        destroyed() {
            window.removeEventListener('resize', this.handleTableHeight);
        },
        data() {
            return {
                tableHeight: 0,
                stations:[],
                isIndeterminate: true,
                expendAll:true,
                permissionTree:[],
                defaultProps: {
                    children: 'new_permissions',
                    label: 'alias'
                },
                dialogFormVisible:false,
                dialogFormVisibleAdd:false,
                searchParams:{
                    page:1
                },
                TotalPage:0,
                tableData: [],
                rules:{
                    name:[{ required: true, message:'必填项不可为空!', trigger: 'blur' }],
                },
                editFormRules:{
                    name:[{ required: true, message:'必填项不可为空!', trigger: 'blur' }],
                    checkedPermission:[{ required: true, message:'必填项不可为空!', trigger: 'blur' }]
                },
                form: {
                    name: '',
                    permission: '',
                    checkedPermission:[]
                },
                editForm: {
                    id: '',
                    name: '',
                    permission: '',
                    checkedPermission:[]
                }
            }
        }
    }
</script>