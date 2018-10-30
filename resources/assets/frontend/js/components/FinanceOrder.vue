<template>
    <div class="main content amount-flow">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">
                <el-col :span="5">
                    <el-form-item label="订单号">
                        <el-input v-model="searchParams.no"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item label="游戏">
                        <el-select v-model="searchParams.game_id" placeholder="请选择">
                            <el-option v-for="(value, key) of GameArr" :value="key" :key="key" :label="value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item label="店铺名称">
                        <el-input v-model="searchParams.seller_nick"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item label="接单平台">
                        <el-input v-model="searchParams.platform"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item label="订单状态">
                        <el-select v-model="searchParams.status" placeholder="请选择">
                            <el-option v-for="(value, key) of StatusArr" :value="key" :key="key" :label="value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
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
                style="width: 100%">
            <el-table-column
                    prop="id"
                    label="流水号"
                    width="80">
            </el-table-column>
            <el-table-column
                    prop="trade_type"
                    label="类型"
                    width="150">
                <template slot-scope="scope">
                    {{ TradeTypeArr["" + scope.row.trade_type] }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="trade_subtype"
                    label="子类型"
                    width="150">
                <template slot-scope="scope">
                    {{ TradeSubTypeArr["" + scope.row.trade_subtype] }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="trade_no"
                    label="订单号">
            </el-table-column>
            <el-table-column
                    prop="order"
                    label="天猫单号">
                <template slot-scope="scope">
                    {{ scope.row.order ? scope.row.order.foreign_order_no : '' }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="fee"
                    label="变动金额"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="balance"
                    label="账户余额"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="created_at"
                    label="时间"
                    width="150">
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
            'AmountFlowApi',
            'GameArrApi',
        ],
        methods: {
            // 加载数据
            handleTableData(){
                axios.post(this.AmountFlowApi, this.searchParams).then(res => {
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
            this.handleTableData();
        },
        watch: {
            t: function (val) {
                this.weekse = val;
                console.log(this.weekse);
                this.getL()
            }
        },
        data() {
            return {
                GameArr:this.GameArrApi,
                StatusArr:{
                    1:'未接单',
                    13:'代练中',
                    14:'待验收',
                    15:'撤销中',
                    16:'仲裁中',
                    17:'异常',
                    18:'已锁定',
                    19:'已撤销',
                    20:'已结算',
                    21:'已仲裁',
                    22:'已下架',
                    23:'强制撤销',
                    24:'已撤单',
                },
                searchParams:{
                    trade_type:'',
                    trade_no:'',
                    channel_order_trade_no:'',
                    date:'',
                    page:1,
                },
                TotalPage:0,
                tableData: []
            }
        }
    }
</script>