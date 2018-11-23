<template>
    <div class="main content amount-flow">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">
                <el-col :span="5">
                    <el-form-item label="单号">
                        <el-input v-model="searchParams.trade_no"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item label="游戏">
                        <el-select v-model="searchParams.game_id" placeholder="请选择">
                            <el-option v-for="(value, key) of GameArr" :value="key" :key="key" :label="value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="店铺名称">
                        <el-input v-model="searchParams.seller_nick"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="接单平台">
                        <el-input v-model="searchParams.platform_id"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item label="状态">
                        <el-select v-model="searchParams.status" placeholder="请选择">
                            <el-option v-for="(value, key) of StatusArr" :value="key" :key="key" :label="value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
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
                :height="tableHeight"
                border
                style="width: 100%; margin-top: 1px">
            <el-table-column
                    prop="trade_no"
                    label="内部单号"
                    width="180">
            </el-table-column>
            <el-table-column
                    prop="channel_order_trade_no"
                    label="淘宝单号"
                    width="180">
                <template slot-scope="scope">
                    {{ scope.row.channel_order_trade_no ? scope.row.channel_order_trade_no : '--' }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="source_order_no"
                    label="补款单号"
                    width="180">
                <template slot-scope="scope">
                    {{ scope.row.source_order_no[1] ? scope.row.source_order_no[1] : '--' }}
                    {{ scope.row.source_order_no[2] ? scope.row.source_order_no[2] : '--' }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="game_name"
                    label="游戏"
                    width="80">
            </el-table-column>
            <el-table-column
                    prop="status"
                    label="订单状态"
                    width="">
                <template slot-scope="scope">
                    {{ StatusArr[scope.row.status] }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="seller_nick"
                    label="店铺名称"
                    width="">
                <template slot-scope="scope">
                    {{ scope.row.seller_nick ? scope.row.seller_nick : '--'}}
                </template>
            </el-table-column>
            <el-table-column
                    prop="platform_id"
                    label="接单平台"
                    width="">
                <template slot-scope="scope">
                    {{ scope.row.platform_id ? PlatformArr[scope.row.platform_id] : '--' }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="taobao_amount"
                    label="淘宝金额"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="taobao_refund"
                    label="淘宝退款"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="pay_amount"
                    label="支付代练费用"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="get_amount"
                    label="获得赔偿金额"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="get_complain_amount"
                    label="获得投诉金额"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="poundage"
                    label="手续费"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="profit"
                    label="最终支付金额"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="customer_service_name"
                    label="发单客服"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="taobao_created_at"
                    label="淘宝下单时间"
                    width="180">
                <template slot-scope="scope">
                    {{ scope.row.taobao_created_at ? scope.row.taobao_created_at : '--' }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="complete_at"
                    label="代练结算时间"
                    width="180">
                <template slot-scope="scope">
                    {{ scope.row.complete_at ? scope.row.complete_at : '--' }}
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
    </div>
</template>

<script>
    export default {
        methods: {
            // 加载数据
            handleTableData(){
               this.$api.FinanceOrderDataList(this.searchParams).then(res => {
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
            handleGame(){
               this.$api.FinanceGame(this.searchParams).then(res => {
                    this.GameArr = res;
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
            // 表格高度计算
            handleTableHeight() {
                this.tableHeight = window.innerHeight - 318;
            }
        },
        created () {
            this.handleTableData();
            this.handleGame();
            this.handleTableHeight();
            window.addEventListener('resize', this.handleTableHeight);
        },
        destroyed() {
            window.removeEventListener('resize', this.handleTableHeight);
        },
        data() {
            return {
                tableHeight: 0,
                GameArr:{},
                PlatformArr:{
                    1:'show91',
                    3:'蚂蚁',
                    4:'dd373',
                    5:'丸子'
                },
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
                    24:'已撤单'
                },
                searchParams:{
                    status:'',
                    game_id:'',
                    trade_no:'',
                    platform_id:'',
                    seller_nick:'',
                    date:'',
                    page:1
                },
                TotalPage:0,
                tableData: []
            }
        }
    }
</script>