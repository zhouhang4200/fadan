<template>
    <div class="main content game-leveling-order">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">
                <el-col :span="5">
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
                <el-col :span="2">
                    <el-form-item>
                        <el-button type="primary" @click="handleSearch">查询</el-button>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
    <el-table
            :data="tableData"
            border
            style="width: 100%; margin-top: 1px">
        <el-table-column
                prop="date"
                label="日期"
                width="">
        </el-table-column>
        <el-table-column
            prop="balance"
            label="余额"
            width="200">
        </el-table-column>
        <el-table-column
                prop="recharge"
                label="充值"
                width="200">
        </el-table-column>
        <el-table-column
                prop="withdraw"
                label="提现"
                width="200">
        </el-table-column>
        <el-table-column
                prop="consume"
                label="消费"
                width="200">
        </el-table-column>
        <el-table-column
                prop="refund"
                label="退款"
                width="200">
        </el-table-column>
        <el-table-column
                prop="expend"
                label="支出"
                width="200">
        </el-table-column>
        <el-table-column
                prop="income"
                label="收入"
                width="200">
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
    </div>
</template>

<script>
    export default {
        created () {
            this.handleTableData();
        },
        methods:{
            // 加载数据
            handleTableData(){
                this.$api.FinanceDailyAssetDataList(this.searchParams).then(res => {
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
            handleCurrentChange(page) {

                this.searchParams.page = page;
                this.handleTableData();
            },
            handleSearch() {
                this.handleTableData();
            }
        },
        data() {
            return {
                tableData: [],
                searchParams:{
                    date:'',
                    page:1
                },
                TotalPage:0
            }
        }
    }
</script>