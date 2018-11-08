<template>
    <div class="main content amount-flow">

        <el-form :inline=true
                 ref="form"
                 :model="searchParams"
                 class="search-form-inline"
                 size="small">
            <el-row :gutter="16">
                <el-col :span="4">
                    <el-form-item label="单号" prop="name">
                        <el-input v-model="searchParams.trade_no"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="4">
                    <el-form-item label="类型" prop="trade_type">
                        <el-select v-model="searchParams.trade_type"
                                   placeholder="请选择">
                            <el-option key="0"
                                       label="所有类型"
                                       value="0">
                            </el-option>
                            <el-option
                                    v-for="(value, key) of TradeTypeArr"
                                    :value="key"
                                    :key="key"
                                    :label="value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>

                <el-col :span="5">
                    <el-form-item label="天猫单号">
                        <el-input v-model="searchParams.channel_order_trade_no"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="发布时间" prop="name">
                        <el-date-picker
                                v-model="searchParams.created_at"
                                type="daterange"
                                align="right"
                                unlink-panels
                                range-separator="至"
                                start-placeholder="开始日期"
                                end-placeholder="结束日期"
                                format="yyyy-MM-dd"
                                value-format="yyyy-MM-dd">
                        </el-date-picker>
                    </el-form-item>
                </el-col>
                <el-col :span="4">
                <el-form-item style="padding:0 8px">
                    <el-button type="primary" @click="handleSearch">查询</el-button>
                    <el-button type="primary" @click="handleResetForm">重置</el-button>
                </el-form-item>
                </el-col>
            </el-row>

            <el-row :gutter="16">

            </el-row>
        </el-form>

        <el-table
                :data="tableData"
                :height="tableHeight"
                border
                style="width: 100%; margin-top: 1px">
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
                    {{ TradeTypeArr[scope.row.trade_type] }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="trade_subtype"
                    label="子类型"
                    width="150">
                <template slot-scope="scope">
                    {{ TradeSubTypeArr[scope.row.trade_subtype] }}
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
                this.$api.FinanceAmountFlowDataList(this.searchParams).then(res => {
                    this.tableData =res.data;
                    this.TotalPage =res.total;
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
            // 重置表单
            handleResetForm() {
                this.$refs.form.resetFields;
                this.handleTableData();
            },
            // 表格高度计算
            handleTableHeight() {
                this.tableHeight = window.innerHeight - 318;
            },
        },
        created () {
            this.handleTableData();
            this.handleTableHeight();
            window.addEventListener('resize', this.handleTableHeight);
        },
        destroyed() {
            window.removeEventListener('resize', this.handleTableHeight)
        },
        watch: {
            t: function (val) {
                this.weekse = val;
                this.getL()
            }
        },
        data() {
            return {
                tableHeight: 0,
                TradeSubTypeArr:{
                    11:'自动加款',
                    12:'手动加款',
                    13:'奖励加款',
                    21:'手动提现',
                    22:'手动减款',
                    23:'自动提现',
                    31:'提现冻结',
                    32:'抢单冻结',
                    33:'减款冻结',
                    41:'提现解冻',
                    42:'抢单解冻',
                    51:'手续费支出',
                    52:'违规扣款',
                    53:'奖励撤销扣款',
                    54:'短信费',
                    55:'steam手续费扣款',
                    61:'手续费退款',
                    62:'违规退款',
                    63:'steam手续费退款',
                    71:'订单集市支出',
                    72:'订单售后扣款',
                    73:'代练手续费支出',
                    74:'安全保证金支出',
                    75:'效率保证金支出',
                    76:'代练下单支出',
                    77:'游戏代练加款',
                    78:'支付订单集市押金',
                    79:'订单投诉支出',
                    81:'订单集市收入',
                    82:'发货失败退款',
                    83:'售后退款',
                    84:'取消订单退款',
                    85:'订单售后退款',
                    86:'代练手续费收入',
                    87:'退还代练费',
                    88:'退还安全保证金',
                    89:'退还效率保证金',
                    810:'安全保证金收入',
                    811:'效率保证金收入',
                    812:'代练收入',
                    813:'代练撤消退款',
                    814:'代练改价退款',
                    815:'保证金退款',
                    816:'退还订单集市押金',
                    817:'订单投诉收入'
                },
                TradeTypeArr:{
                    1:'加款',
                    2:'减款',
                    3:'冻结',
                    4:'解冻',
                    7:'支出',
                    8:'收入'
                },
                searchParams:{
                    trade_type:'',
                    trade_no:'',
                    channel_order_trade_no:'',
                    date:'',
                    page:1
                },
                TotalPage:0,
                tableData: []
            }
        }
    }
</script>