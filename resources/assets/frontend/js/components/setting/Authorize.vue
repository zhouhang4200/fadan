<template>
    <div class="main content amount-flow">
        <template>
            <el-alert
                    style="margin-bottom: 15px"
                    title="操作提示: 该授权用于抓取您淘宝店铺的订单。授权成功后您店铺订单会自动同步到平台中。"
                    type="success"
                    :closable="false">
            </el-alert>
            <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
                <el-row :gutter="16">
                    <el-col :span="5">
                        <el-form-item>
                            <el-button
                                    type="primary"
                                    size="small"
                                    @click="authorize()">授权</el-button>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>
            <el-table
                    :data="tableData"
                    border
                    style="width: 100%; margin-top: 1px">
                <el-table-column
                        prop="wang_wang"
                        label="店铺旺旺"
                        width="300">
                </el-table-column>
                <el-table-column
                        prop="created_at"
                        label="添加时间"
                        width="">
                </el-table-column>
                <el-table-column
                        label="操作"
                        width="300">
                    <template slot-scope="scope">
                        <el-button
                                type="primary"
                                size="small"
                                @click="authorizeDelete(scope.row.id)">删除</el-button>
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
        </template>
    </div>
</template>

<script>
    export default {
        methods: {
            // 删除
            authorizeDelete(id) {
                this.$api.SettingAuthorizeDelete({id:id}).then(res => {
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
            // 授权
            authorize(){
                this.$api.SettingAuthorizeUrl().then(res => {
                    this.url=res;
                    window.location.href=this.url;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            // 加载数据
            handleTableData(){
                this.$api.SettingAuthorizeDataList(this.searchParams).then(res => {
                    this.tableData = res.data.data;
                    this.TotalPage = res.data.total;

                    if (res.bind === 1) {
                        this.loading=true;
                    }
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
        },
        created () {
            this.handleTableData();
        },
        data() {
            var checkHas = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('必填项不能为空!'));
                }
                callback();
            };
            return {
                loading:false,
                url:'',
                tableData: [],
                searchParams:{
                    page:1,
                },
                TotalPage:0,
            }
        }
    }
</script>