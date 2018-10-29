<template>
    <div class="main content game-leveling-order" :class="tableDataEmpty">
        <el-form :inline=true
                 :model="searchParams"
                 class="search-form-inline"
                 size="small">
            <el-row :gutter="16">
                <el-col :span="6">
                    <el-form-item label="订单单号" prop="name">
                        <el-input v-model="searchParams.order_no"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="玩家旺旺" prop="name">
                        <el-input v-model="searchParams.buyer_nick"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="代练游戏" prop="name">
                        <el-select v-model="searchParams.game_id"
                                   @change="handleSearchParamsGameId"
                                   placeholder="请选择">
                            <el-option key="0"
                                       label="所有游戏"
                                       value="0">
                            </el-option>
                            <el-option v-for="item in gameOptions"
                                       :key="item.id"
                                       :label="item.name"
                                       :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="代练类型" prop="name">
                        <el-select v-model="searchParams.game_leveling_type_id" placeholder="请选择">
                            <el-option
                                    v-for="item in gameLevelingTypeOptions"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
            </el-row>

            <el-row :gutter="16">
                <el-col :span="6">
                    <el-form-item label="发单客服">
                        <el-input v-model="searchParams.user_id" placeholder="发单客服"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="代练平台">
                        <el-select v-model="searchParams.platform_id" placeholder="请选择">
                            <el-option
                                    v-for="item in platformOptions"
                                    :key="item.key"
                                    :label="item.value"
                                    :value="item.key">
                            </el-option>
                        </el-select>
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
                                format="yyyy 年 MM 月 dd 日"
                                value-format="yyyy-MM-dd"
                                :picker-options="pickerOptions">
                        </el-date-picker>
                    </el-form-item>
                </el-col>
                    <el-button type="primary" @click="handleSearch">查询</el-button>
                    <el-button type="primary" @click="handleResetForm">重置</el-button>
            </el-row>
        </el-form>

        <el-tabs  v-model="searchParams.status"
                  @tab-click="handleParamsStatus"
                  size="small"
                  class="game-leveling-order-tab">

            <el-tab-pane name="0">
                <span slot="label">
                    全部
                    <el-badge  :value="0"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane name="1">
                <span slot="label">
                    未接单
                    <el-badge  :value="1"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane  name="13">
                <span slot="label">
                    代练中
                    <el-badge  :value="13"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane  name="14">
                <span slot="label">
                    待验收
                    <el-badge  :value="14"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane  name="15">
                <span slot="label">
                    撤销中
                    <el-badge  :value="15"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane  name="16">
                <span slot="label">
                    仲裁中
                    <el-badge  :value="0"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane  name="99">
                <span slot="label">
                    淘宝退款中
                    <el-badge  :value="99"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane  name="17">
                <span slot="label">
                    异常
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane  name="18">
                <span slot="label">
                    已锁定
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane  name="19">
                <span slot="label">
                    已撤销
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane name="20">
                <span slot="label" >
                    已结算
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane  name="21">
                <span slot="label">
                    已仲裁
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane name="22">
                <span slot="label" >
                    已下架
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane name="24">
                <span slot="label">
                    已撤单
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

        </el-tabs>

        <el-table
                class="game-leveling-order-table"
                v-loading="tableLoading"
                :height="tableHeight"
                :data="tableData"
                border
                style="width: 100%;height:800px">
            <el-table-column
                    fixed
                    prop="trade_no"
                    label="订单号"
                    width="230">
                <template slot-scope="scope">
                    <a :href="showApi + '/' + scope.row.trade_no">
                        <div style="margin-left: 10px"> 淘宝：{{ scope.row.trade_no }}</div>
                        <div style="margin-left: 10px">渠道：{{ scope.row.trade_no }}</div>
                    </a>
                </template>
            </el-table-column>
            <el-table-column
                    prop="status"
                    label="订单状态"
                    width="70">
                <template slot-scope="scope">
                    {{ statusMap['s' + scope.row.status] }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="province"
                    label="玩家旺旺"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="city"
                    label="客服备注"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="title"
                    label="代练标题"
                    width="300">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="游戏/区/服"
                    width="120">
                <template slot-scope="scope">
                    {{ scope.row.game_name }} / {{ scope.row.take_at }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="game_role"
                    label="角色名称"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="game_name"
                    label="账号/密码"
                    width="120">
                <template slot-scope="scope">
                    {{ scope.row.account }} / {{ scope.row.password }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="amount"
                    label="代练价格"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="效率/安全保证金"
                    width="120">
                <template slot-scope="scope">
                    {{ scope.row.security_deposit }} / {{ scope.row.efficiency_deposit }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="发单/接单时间"
                    width="120">
                <template slot-scope="scope">
                    {{ scope.row.created_at }} / {{ scope.row.take_at }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="代练时间"
                    width="120">
                <template slot-scope="scope">
                    {{ scope.row.day }} 天 {{ scope.row.hour }} 小时
                </template>
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="剩余时间"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="打手QQ电话"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="号主电话"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="来源价格"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="支付代练费用"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="获得赔偿金额"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="手续费"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="最终支付金额"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="发单客服"
                    width="120">
            </el-table-column>
            <el-table-column
                    fixed="right"
                    label="操作"
                    width="200">
                <template slot-scope="scope">
                    <!--撤单 下架-->
                    <div v-if="scope.row.status == 1">
                        <el-button
                                size="small"
                                @click="handleDelete(scope.row)">撤单</el-button>
                        <el-button
                                size="small"
                                type="primary"
                                @click="handleOffSale(scope.row)">下架</el-button>
                    </div>
                    <!--撤销 申请仲裁-->
                    <div v-if="scope.row.status == 13">
                        <el-button size="small"
                                   @click="handleApplyConsult(scope.row)">协商撤销</el-button>
                        <el-button size="small"
                                   type="primary"
                                   @click="handleApplyComplain(scope.row)">申请仲裁</el-button>
                    </div>
                    <!--查看图片 完成验收-->
                    <div v-if="scope.row.status == 14">
                        <el-button size="small" @click="handleApplyCompleteImage(scope.row)">查看图片</el-button>
                        <el-button size="small" type="primary" @click="handleComplete(scope.row)">完成验收</el-button>
                    </div>
                    <!--取消撤销/同意撤销 申请仲裁-->
                    <div v-if="scope.row.stefficiencyDepositatus == 15">
                        <el-button v-if="scope.row.game_leveling_order_consults.initiator == 1" size="small" @click="handleCancelConsult(scope.row)">取消撤销</el-button>
                        <el-button v-if="scope.row.game_leveling_order_consults.initiator == 2" size="small" @click="handleAgreeConsult(scope.row)">同意撤销</el-button>
                        <el-button size="small" type="primary" @click="handleApplyComplain(scope.row)">申请仲裁</el-button>
                    </div>
                    <!--取消仲裁  同意撤销-->
                    <div v-if="scope.row.status == 16">
                        <el-button size="small" @click="handleCancelComplain(scope.row)">取消仲裁</el-button>
                        <el-button size="small" type="primary" @click="handleAgreeConsult(scope.row)">同意撤销</el-button>
                    </div>
                    <!--锁定  撤销-->
                    <div v-if="scope.row.status == 17">
                        <el-button
                                size="small"
                                @click="handleLock(scope.row)">锁定</el-button>
                        <el-button
                                size="small"
                                type="primary" @click="handleApplyConsult(scope.row)">协商撤销</el-button>
                    </div>
                    <!--取消锁定  撤销-->
                    <div v-if="scope.row.status == 18">
                        <el-button
                                size="small" @click="handleCancelLock(scope.row)">取消锁定</el-button>
                        <el-button
                                size="small" type="primary" @click="handleApplyConsult(scope.row)">协商撤销</el-button>
                    </div>
                    <!--上架 撤单-->
                    <div v-if="scope.row.status == 22">
                        <el-button size="small" @click="handleOnSale(scope.row)">上架</el-button>
                        <el-button size="small" type="primary" @click="handleDelete(scope.row)">撤单</el-button>
                    </div>
                    <!--重发-->
                    <div v-if="(scope.row.status == 19
                    || scope.row.status == 20
                    || scope.row.status == 21
                    || scope.row.status ==23
                    || scope.row.status == 24)">
                        <el-button size="small" @click="handleRepeatOrder(scope.row)">重发</el-button>
                    </div>

                </template>
            </el-table-column>
        </el-table>

        <div class="block" style="margin-top:15px;">
            <el-pagination
                    background
                    @current-change="handleParamsPage"
                    :current-page.sync="searchParams.page"
                    :page-size="20"
                    layout="total, prev, pager, next, jumper"
                    :total="tableDataTotal">
            </el-pagination>
        </div>

        <ApplyComplain v-if="applyComplainVisible"
                       :tradeNo="tradeNo"
                       :applyComplainApi="applyComplainApi"
                       @handleApplyComplainVisible="handleApplyComplainVisible">
        </ApplyComplain>

        <ApplyConsult v-if="applyConsultVisible"
                      :tradeNo="tradeNo"
                      :amount="amount"
                      :securityDeposit="securityDeposit"
                      :efficiencyDeposit="efficiencyDeposit"
                      :applyConsultApi="applyConsultApi"
                      @handleApplyConsultVisible="handleApplyConsultVisible">
        </ApplyConsult>
    </div>
</template>

<style >
    .game-leveling-order-tab .el-tabs__item {
        font-weight: normal;
    }
    .game-leveling-order-table .el-button {
        width: 80px;
    }
    .el-table_empty .el-table__empty-block {
        width: auto !important;
    }
    .search-form-inline .el-select,
    .search-form-inline .el-date-editor--daterange.el-input__inner,
    .search-form-inline .el-form-item {
        width:100%;
    }
    .search-form-inline .el-range-separator {
        width:10%;
    }
    .search-form-inline .el-form-item__content {
        width:80%;
    }
</style>

<script>
    import ApplyComplain from './ApplyComplain';
    import ApplyConsult from './ApplyConsult';
    export default {
        components: {
            ApplyComplain,
            ApplyConsult,
        },
        props: [
            'pageTitle',
            'orderApi',
            'gameLevelingTypesApi',
            'gamesApi',
            'showApi',
            'deleteApi',
            'onSaleApi',
            'offSaleApi',
            'applyConsultApi',
            'applyComplainApi',
            'cancelConsultApi',
            'rejectConsultApi',
            'agreeConsultApi',
            'completeApi',
            'lockApi',
            'cancelLockApi',
            'anomalyApi',
            'cancelAnomalyApi',
        ],
        computed: {
            tableDataEmpty() {
                return [
                    this.tableData.length === 0 ? ' el-table_empty' : '',
                ]
            },
            showHref(tradeNo) {
                return this.showApi + '/' + tradeNo
            }
        },
        data() {
            return {
                tradeNo:'',
                amount:0,
                securityDeposit:0,
                efficiencyDeposit:0,
                applyConsultVisible:false,
                applyComplainVisible:false,
                // applyConsultApi: this.applyConsultApi,
                platformOptions: [
                    { key:0, value:'所有平台'},
                    { key:5, value:'丸子代练'},
                    { key:1, value:'91代练'},
                    { key:3, value:'蚂蚁代练'},
                ],
                gameLevelingTypeOptions:[],
                gameOptions:[],
                searchParams:{
                    status:'',
                    order_no:'',
                    buyer_nick:'',
                    game_id:'',
                    game_leveling_type_id:'',
                    user_id:'',
                    platform_id:0,
                    start_created_at:'',
                    created_at:'',
                    page:1,
                },
                pickerOptions: {
                    shortcuts: [{
                        text: '最近一周',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近一个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近三个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                            picker.$emit('pick', [start, end]);
                        }
                    }]
                },
                statusMap: {
                    s1: '未接单',
                    s13: '代练中',
                    s14: '待验收',
                    s15: '撤销中',
                    s16: '仲裁中',
                    s17: '异常',
                    s18: '已锁定',
                    s19: '已撤销',
                    s20: '已结算',
                    s21: '已仲裁',
                    s22: '已下架',
                    s23: '强制撤销',
                    s24: '已撤单',
                },
                tableLoading:false,
                tableHeight: 0,
                tableDataTotal:0,
                tableData: []
            }
        },
        methods: {
            // 设置仲裁窗口是否显示
            handleApplyComplainVisible(data) {
                this.applyComplainVisible = data.visible;
            },
            // 设置协商窗口是否显示
            handleApplyConsultVisible(data) {
                this.applyConsultVisible = data.visible;
            },
            // 设置当前页面包屑
            handlePageTitle() {
                // this.$store.commit('handlePageTitle',{pageTitle:this.pageTitle})
            },
            // 表格高度计算
            handleTableHeight() {
                this.tableHeight = window.innerHeight - 345;
            },
            // 加载订单数据
            handleTableData(){
                this.tableLoading = true;
                axios.post(this.orderApi, this.searchParams).then(res => {
                    this.tableData = res.data.data;
                    this.tableDataTotal = res.data.total;
                    this.tableLoading = false;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                    this.tableLoading = false;
                });
            },
            // 加载游戏选项
            handleGameOptions() {
                axios.post(this.gamesApi).then(res => {
                    this.gameOptions = res.data;
                }).catch(err => {
                });
            },
            // 搜索
            handleSearch(){
                this.handleTableData();
            },
            // 切换页码
            handleParamsPage(page){
                this.searchParams.page = page;
                this.handleTableData();
            },
            // 切换状态tab
            handleParamsStatus() {
                this.handleTableData();
            },
            // 选择游戏后加载代练类型
            handleSearchParamsGameId() {
                if(this.searchParams.game_id) {
                    axios.post(this.gameLevelingTypesApi, {
                        'game_id' : this.searchParams.game_id
                    }).then(res => {
                        this.gameLevelingTypeOptions = res.data;
                    }).catch(err => {
                    });
                } else {
                    this.gameLevelingTypeOptions = [];
                }
            },
            // 查看订单
            handleShow(index) {
                this.$Modal.info({
                    title: 'User Info',
                    content: `Name：${this.tableData[index].status}<br>Age：${this.tableData[index].title}<br>Address：${this.tableData[index].amount}`
                })
            },
            // 撤单
            handleDelete(row) {
                this.$confirm('您确定要"撤单"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.deleteApi, {
                        'trade_no' : row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleTableData();
                        }
                    }).catch(err => {
                        this.$message({
                            message: '操作失败',
                            type: 'error',
                        });
                    });
                });
            },
            // 上架
            handleOnSale(row) {
                this.$confirm('您确定要"上架"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.onSaleApi, {
                        'trade_no' : row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleTableData();
                        }
                    }).catch(err => {
                        this.$message({
                            type: 'error',
                            message: '操作失败'
                        });
                    });
                });
            },
            // 下架
            handleOffSale(row) {
                this.$confirm('您确定要"下架"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.offSaleApi, {
                        'trade_no' : row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleTableData();
                        }
                    }).catch(err => {
                        this.$message({
                            type: 'error',
                            message: '操作失败'
                        });
                    });
                });
            },
            // 申请仲裁
            handleApplyComplain(row) {
                this.tradeNo = row.trade_no;
                this.applyComplainVisible = true;
            },
            // 取消仲裁
            handleCancelComplain(index) {
                this.$confirm('您确定要"取消仲裁"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.cancelConsultApi, {
                        'trade_no' : this.tableData[index].trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleTableData();
                        }
                    }).catch(err => {
                        this.$message({
                            type: 'error',
                            message: '操作失败'
                        });
                    });
                });
            },
            // 查看图片
            handleApplyCompleteImage(index) {

            },
            // 完成验收
            handleComplete(row) {
                this.$confirm('您确定要"完成验收"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.completeApi, {
                        'trade_no' : row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleTableData();
                        }
                    }).catch(err => {
                        this.$message({
                            type: 'error',
                            message: '操作失败'
                        });
                    });
                });
            },
            // 申请撤销
            handleApplyConsult(row) {
                this.tradeNo = row.trade_no;
                this.amount = row.amount;
                this.securityDeposit = row.security_deposit;
                this.efficiencyDeposit = row.efficiency_deposit;
                this.applyConsultVisible = true;
            },
            // 取消撤销
            handleCancelConsult(row) {
                this.$confirm('您确定要"取消撤销"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.cancelConsultApi, {
                        'trade_no' : row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleTableData();
                        }
                    }).catch(err => {
                        this.$message({
                            type: 'error',
                            message: '操作失败'
                        });
                    });
                });
            },
            // 同意撤销
            handleAgreeConsult(row) {
                this.$confirm('您确定要"同意撤销"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.agreeConsultApi, {
                        'trade_no' : row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleTableData();
                        }
                    }).catch(err => {
                        this.$message({
                            type: 'error',
                            message: '操作失败'
                        });
                    });
                });
            },
            // 不同意撤销
            handleRejectConsult(row) {
                this.$confirm('您确定"不同意撤销"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.rejectConsultApi, {
                        'trade_no' : row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleTableData();
                        }
                    }).catch(err => {
                        this.$message({
                            type: 'error',
                            message: '操作失败'
                        });
                    });
                });
            },
            // 锁定
            handleLock(row) {
                this.$confirm('您确定要"锁定"订单吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.lockApi, {
                        'trade_no' : row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleTableData();
                        }
                    }).catch(err => {
                        this.$message({
                            type: 'error',
                            message: '操作失败'
                        });
                    });
                });
            },
            // 取消锁定
            handleCancelLock(row) {
                this.$confirm('您确定要"取消锁定"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.cancelLock, {
                        'trade_no' : row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleTableData();
                        }
                    }).catch(err => {
                        this.$message({
                            type: 'error',
                            message: '操作失败'
                        });
                    });
                });
            },
            // 重新下单
            handleRepeatOrder(index) {
            },
            // 重置表单
            handleResetForm() {
                this.searchParams = {
                    status:'',
                    order_no:'',
                    buyer_nick:'',
                    game_id:'',
                    game_leveling_type_id:'',
                    user_id:'',
                    platform_id:0,
                    start_created_at:'',
                    created_at:'',
                    page:1,
                };
                this.handleTableData();
            }
        },
        created() {
            this.handlePageTitle();
            this.handleTableHeight();
            this.handleTableData();
            this.handleGameOptions();
            window.addEventListener('resize', this.handleTableHeight);
        },
        destroyed() {
            window.removeEventListener('resize', this.handleTableHeight)
        },
    }
</script>