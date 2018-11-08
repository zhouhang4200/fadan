<template>
    <div class="main content amount-flow">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">
                <el-col :span="7">
                    <el-form-item label="日期">
                        <el-date-picker
                                v-model="searchParams.date"
                                type="daterange"
                                align="right"
                                unlink-panels
                                format="yyyy-MM-dd"
                                value-format="yyyy-MM-dd"
                                range-separator="至"
                                start-placeholder="开始日期"
                                end-placeholder="结束日期">
                        </el-date-picker>
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
                style="width: 100%; margin-top: 1px">
            <el-table-column
                    prop="id"
                    label="序号"
                    width="100">
            </el-table-column>
            <el-table-column
                    prop="name"
                    label="账号"
                    width="200">
                <template slot-scope="scope">
                    {{ scope.row.user ? scope.row.user.name : '' }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="ip"
                    label="登录IP"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="city_id"
                    label="登录城市"
                    width="">
                <template slot-scope="scope">
                    {{ scope.row.city ? scope.row.city.name : '' }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="created_at"
                    label="登录时间">
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
        methods: {
            // 加载数据
            handleTableData(){
                this.$api.AccountLoginHistoryDataList(this.searchParams).then(res => {
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
            // this.userArr();
        },
        data() {
            return {
                searchParams:{
                    date:'',
                    page:1
                },
                TotalPage:0,
                tableData: []
            }
        }
    }
</script>