<template>
    <div class="main content amount-flow">
        <template>
            <div style="margin-bottom: 20px;color: #909399">
                操作提示: 该授权用于抓取您淘宝店铺的订单。授权成功后您店铺订单会自动同步到平台中。
            </div>
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
                    style="width: 100%">
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
                    @current-change="handleCurrentChange"
                    :current-page.sync="searchParams.page"
                    :page-size="15"
                    layout="prev, pager, next, jumper"
                    :total="TotalPage">
            </el-pagination>
        </template>
    </div>
</template>

<script>
    export default {
        props: [
            'SettingAuthorizeDataListApi',
            'SettingAuthorizeUrlApi',
            'SettingAuthorizeDeleteApi',
        ],
        methods: {
            // 删除
            authorizeDelete(id) {
                axios.post(this.SettingAuthorizeDeleteApi, {id:id}).then(res => {
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
            // 授权
            authorize(){
                axios.post(this.SettingAuthorizeUrlApi).then(res => {
                    this.url=res.data;
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
                axios.post(this.SettingAuthorizeDataListApi, this.searchParams).then(res => {
                    console.log(res);
                    this.tableData = res.data.data.data;
                    this.TotalPage = res.data.data.total;

                    if (res.data.bind === 1) {
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
                console.log(this.searchParams);
                this.handleTableData();
            },
            handleCurrentChange(page) {
                console.log(`当前页: ${page}`);
                this.searchParams.page = page;
                this.handleTableData();
            },
        },
        created () {
            this.$store.commit('handleOpenMenu', '4');
            this.$store.commit('handleOpenSubmenu', '4-3');
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