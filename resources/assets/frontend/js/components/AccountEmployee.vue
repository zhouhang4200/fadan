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
                    label="员工姓名"
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
                        <el-button v-if="scope.row.id > 0"
                                   type="primary"
                                   size="small"
                                   @click="employeeEdit(scope.row.id)">编辑</el-button>
                        <el-button v-if="scope.row.id > 0"
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
    </div>
</template>

<script>
    export default {
        props: [
            'AccountEmployeeUserApi',
            'AccountEmployeeStationApi',
            'AccountEmployeeDataListApi',
            'AccountEmployeeSwitchApi',
            'AccountEmployeeEditApi',
            'AccountEmployeeDeleteApi',
        ],
        methods: {
            // 加载数据
            handleTableData(){
                axios.post(this.AccountEmployeeDataListApi, this.searchParams).then(res => {
                    console.log(res);
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
                console.log(this.searchParams);
                this.handleTableData();
            },

            handleCurrentChange(page) {
                console.log(`当前页: ${page}`);
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
            employeeEdit(id) {
                window.location.gref=this.AccountEmployeeEditApi+"?user_id="+id;
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
            }
        },
        created () {
            this.handleTableData();
            this.handleUser();
            this.handleStation();
        },
        data() {
            return {
                AccountEmployeeUser:{},
                AccountEmployeeStation:{},
                searchParams:{
                    username:'',
                    name:'',
                    station:'',
                    page:1,
                },
                TotalPage:0,
                tableData: []
            }
        }
    }
</script>