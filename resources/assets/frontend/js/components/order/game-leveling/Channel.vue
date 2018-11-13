<template>
    <div class="main content amount-flow">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">
                <el-col :span="5">
                    <el-form-item label="订单编号">
                        <el-input v-model="searchParams.trade_no"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item label="绑定游戏">
                        <el-select v-model="searchParams.game_name" placeholder="请选择">
                            <el-option v-for="value, key in games" :value="value" :key="key" :label="value">{{value}}</el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="发布时间">
                        <el-date-picker
                                v-model="searchParams.date"
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
                    <el-form-item>
                        <el-button type="primary" @click="handleSearch">查询</el-button>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>

        <el-tabs v-model="searchParams.status" @tab-click="handleClick">
            <el-tab-pane name="0">
                <span slot="label">全部</span>
            </el-tab-pane>
            <el-tab-pane name="1">
                <span slot="label">待付款<el-badge v-if="(this.statusCount[1] != undefined)"  :value="this.statusCount[1]"></el-badge></span>
            </el-tab-pane>
            <el-tab-pane name="2">
                <span slot="label">进行中<el-badge v-if="(this.statusCount[2] != undefined)"  :value="this.statusCount[2]"></el-badge></span>
            </el-tab-pane>
            <el-tab-pane name="3">
                <span slot="label">待收货<el-badge v-if="(this.statusCount[3] != undefined)"  :value="this.statusCount[3]"></el-badge></span>
            </el-tab-pane>
            <el-tab-pane name="6">
                <span slot="label">退款中<el-badge v-if="(this.statusCount[6] != undefined)"  :value="this.statusCount[6]"></el-badge></span>
            </el-tab-pane>
            <el-tab-pane name="4">
                <span slot="label">已完成<el-badge v-if="(this.statusCount[4] != undefined)"  :value="this.statusCount[4]"></el-badge></span>
            </el-tab-pane>
            <el-tab-pane name="7">
                <span slot="label">已退款<el-badge v-if="(this.statusCount[7] != undefined)"  :value="this.statusCount[7]"></el-badge></span>
            </el-tab-pane>
        </el-tabs>

        <el-table
                :data="tableData"
                :height="tableHeight"
                border
                style="width: 100%; margin-top: 1px">
            <el-table-column
                    prop="trade_no"
                    label="订单号"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="status"
                    label="订单状态"
                    width="100">
                <template slot-scope="scope">{{status[scope.row.status]}}</template>
            </el-table-column>
            <el-table-column
                    prop="game_leveling_order_status"
                    label="平台订单状态"
                    width="100">
                <template slot-scope="scope">
                    <span v-if="scope.row.game_leveling_orders" v-for="item in scope.row.game_leveling_orders">{{status_leveling[item.status]}}</span>
                </template>
            </el-table-column>
            <el-table-column
                    prop="game_name"
                    label="绑定游戏"
                    width="100">
            </el-table-column>
            <el-table-column
                    prop="player_info"
                    label="卖家QQ/电话"
                    width="200">
                <template slot-scope="scope">QQ：{{scope.row.player_qq ? scope.row.player_qq : ''}}<br/>电话：{{scope.row.player_phone}}</template>
            </el-table-column>
            <el-table-column
                    prop="amount"
                    label="购买单价"
                    width="100">
            </el-table-column>
            <el-table-column
                    prop="payment_amount"
                    label="实付金额"
                    width="100">
            </el-table-column>
            <el-table-column
                    prop="created_at"
                    label="下单时间"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="remark"
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
                            v-if="scope.row.status === 6"
                            @click="showRefund(scope.row.trade_no)">同意退款</el-button>
                    <el-button
                            type="primary"
                            size="small"
                            v-if="scope.row.status === 6"
                            @click="showRefuseRefund(scope.row.trade_no)">拒绝退款</el-button>
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
        <el-dialog title="同意退款" :modal=true :modal-append-to-body=true :visible.sync="dialogAgreeRefundFormVisible">
            <div style="font-size: 18px">
                你确定同意用户全额退款（部分退款）申请吗？<br/>
                退款金额：{{refund_amount}}<br/>
                退款原因：{{refund_reason}}
            </div>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogAgreeRefundFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="agreeRefund(trade_no)">确 定</el-button>
            </div>
        </el-dialog>
        <el-dialog title="拒绝退款" :modal=true :modal-append-to-body=true :visible.sync="dialogRefuseRefundFormVisible">
            <el-form :model="form">
                <div style="font-size: 18px;margin-bottom: 10px">你确定拒绝用户全额退款（部分退款）申请吗？</div>
                <el-form-item>
                    <el-input type="textarea" placeholder="请输入拒绝原因" v-model="form.refuse_refund_reason"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogRefuseRefundFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="refuseRefund('agreeRefundForm')">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        methods: {
            //显示同意退款弹窗
            showRefund(tradeNo) {
                this.dialogAgreeRefundFormVisible=true;
                this.$api.GameLevelingChannelOrderRefund({trade_no:tradeNo}).then(res => {
                    this.refund_amount = res.refund_amount;
                    this.refund_reason = res.refund_reason;
                    this.trade_no=res.game_leveling_channel_order_trade_no;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            //显示拒绝退款弹窗
            showRefuseRefund(tradeNo) {
                this.dialogRefuseRefundFormVisible=true;
                this.form.trade_no=tradeNo;
                this.$api.GameLevelingChannelOrderRefund({trade_no:tradeNo}).then(res => {
                    this.trade_no=res.game_leveling_channel_order_trade_no;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            // 同意退款
            agreeRefund(tradeNo) {
                this.$api.GameLevelingChannelOrderAgreeRefund({trade_no:tradeNo}).then(res => {
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
            // 拒绝退款
            refuseRefund(formName) {
                this.$api.GameLevelingChannelOrderRefuseRefund(this.form).then(res => {
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
                this.$api.GameLevelingChannelOrder(this.searchParams).then(res => {
                    this.tableData = res.data;
                    this.TotalPage = res.total;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
                this.handStatusCount();
            },
            // 获取渠道游戏
            handleGameData(){
                this.$api.GameLevelingChannelGame().then(res => {
                    this.games = res;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            // 获取状态数量
            handStatusCount()
            {
                this.$api.GameLevelingChannelStatus(this.searchParams).then(res => {
                    this.statusCount = res;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            // 标签
            handleClick() {
                this.handleTableData();
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
            },
        },
        created(){
            this.handleTableData();
            this.handleGameData();
            this.handleTableHeight();
            window.addEventListener('resize', this.handleTableHeight);
        },
        destroyed() {
            window.removeEventListener('resize', this.handleTableHeight);
        },
        data(){
            return {
                status_leveling:{
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
                status:{
                    1:'代付款',
                    2:'进行中',
                    3:'待收货',
                    4:'完成',
                    6:'退款中',
                    7:'已退款'
                },
                trade_no:'',
                refund_amount:'',
                refund_reason:'',
                statusCount:[],
                activeChannel:'',
                games:[],
                agreeRefundForm:{},
                refuseRefundForm:{},
                sendOrder:false,
                agreeRefundButton:false,
                refuseRefundButton:false,
                dialogAgreeRefundFormVisible:false,
                dialogRefuseRefundFormVisible:false,
                tableHeight: 0,
                dialogFormVisible:false,
                searchParams:{
                    trade_no:'',
                    game_name:'',
                    status:'',
                    date:'',
                    page:1
                },
                form:{
                    trade_no:'',
                    refuse_refund_reason:''
                },
                TotalPage:0,
                tableData: [],
            }
        }
    }
</script>