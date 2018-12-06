<template>
    <div class="main content game-leveling-order" :class="tableDataEmpty">
        <el-form :inline=true
                 :model="search"
                 class="search-form-inline"
                 size="small">
            <el-row :gutter="16">
                <el-col :span="6">
                    <el-form-item label="订单单号" prop="name">
                        <el-input v-model="search.order_no"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="玩家旺旺" prop="name">
                        <el-input v-model="search.buyer_nick"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="代练游戏" prop="name">
                        <el-select v-model="search.game_id"
                                   @change="handleSearchGameId"
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
                        <el-select v-model="search.game_leveling_type_id" placeholder="请选择">
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
                        <el-input v-model="search.user_id" placeholder="发单客服"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="代练平台">
                        <el-select v-model="search.platform_id" placeholder="请选择">
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
                                v-model="search.created_at"
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
                <el-button type="primary" @click="handleSearch">查询</el-button>
                <el-button type="primary" @click="handleResetForm">重置</el-button>
            </el-row>
        </el-form>

        <el-tabs
                v-model="search.status"
                @tab-click="handleParamsStatus"
                size="small"
                class="game-leveling-order-tab">

            <el-tab-pane name="0">
                <span slot="label">
                    全部
                </span>
            </el-tab-pane>

            <el-tab-pane name="1">
                <span slot="label">
                    未接单
                    <el-badge v-if="(this.statusQuantity[1] != undefined)" :value="this.statusQuantity[1]"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane name="13">
                <span slot="label">
                    代练中
                    <el-badge v-if="(this.statusQuantity[13] != undefined)" :value="this.statusQuantity[13]"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane name="14">
                <span slot="label">
                    待验收
                    <el-badge v-if="(this.statusQuantity[14] != undefined)" :value="this.statusQuantity[14]"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane name="15">
                <span slot="label">
                    撤销中
                    <el-badge v-if="(this.statusQuantity[15] != undefined)" :value="this.statusQuantity[15]"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane name="16">
                <span slot="label">
                    仲裁中
                    <el-badge v-if="(this.statusQuantity[16] != undefined)" :value="this.statusQuantity[16]"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane name="99">
                <span slot="label">
                    淘宝退款中
                    <el-badge v-if="(this.statusQuantity[99] != undefined)" :value="this.statusQuantity[99]"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane name="17">
                <span slot="label">
                    异常
                    <el-badge v-if="(this.statusQuantity[17] != undefined)" :value="this.statusQuantity[17]"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane name="18">
                <span slot="label">
                    已锁定
                    <el-badge v-if="(this.statusQuantity[18] != undefined)" :value="this.statusQuantity[18]"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane name="19">
                <span slot="label">
                    已撤销
                </span>
            </el-tab-pane>

            <el-tab-pane name="20">
                <span slot="label">
                    已结算
                </span>
            </el-tab-pane>

            <el-tab-pane name="21">
                <span slot="label">
                    已仲裁
                </span>
            </el-tab-pane>

            <el-tab-pane name="22">
                <span slot="label">
                    已下架
                </span>
            </el-tab-pane>

            <el-tab-pane name="24">
                <span slot="label">
                    已撤单
                </span>
            </el-tab-pane>

        </el-tabs>

        <el-table
                @cell-mouse-enter="handleCellMouseEnter"
                @cell-mouse-leave="handleCellMouseLeave"
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
                    width="250">
                <template slot-scope="scope">
                    <router-link :to="{name:'gameLevelingOrderShow', query:{trade_no:scope.row.trade_no}}">
                        <div style="margin-left: 10px">淘宝：{{ scope.row.trade_no }}</div>
                        <div style="margin-left: 10px">{{ platformMap[scope.row.platform_id] }}：{{ scope.row.platform_trade_no }}</div>
                    </router-link>
                </template>
            </el-table-column>
            <el-table-column
                    prop="status"
                    label="订单状态"
                    width="70">
                <template slot-scope="scope">
                    {{ statusMap[scope.row.status] }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="province"
                    label="玩家旺旺"
                    width="120">
                <template slot-scope="scope">
                    {{ scope.row.buyer_nick }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="user_remark"
                    label="客服备注"
                    width="120">
                <template slot-scope="scope">
                    <el-input
                            type="textarea"
                            v-if="scope.row.remark_edit"
                            v-model="scope.row.game_leveling_order_detail.user_remark"></el-input>
                    <span v-else>
                        {{ scope.row.game_leveling_order_detail.user_remark }}
                    </span>
                </template>
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
                    <div>{{ scope.row.game_leveling_order_detail.game_name }}</div>
                    <div>{{ scope.row.game_leveling_order_detail.game_region_name }}</div>
                    <div>{{ scope.row.game_leveling_order_detail.game_server_name }}</div>
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
                    <div>{{ scope.row.game_account }}</div>
                    <div>{{ scope.row.game_password }}</div>
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
                    <div>{{ scope.row.security_deposit }}</div>
                    <div>{{ scope.row.efficiency_deposit }}</div>
                </template>
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="发单/接单时间"
                    width="140">
                <template slot-scope="scope">
                    <div>{{ scope.row.created_at }}</div>
                    <div>{{ scope.row.take_at }}</div>
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
                    prop="left_time"
                    label="剩余时间"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="打手QQ/电话"
                    width="120">
                <template slot-scope="scope">
                    <div>{{ scope.row.game_leveling_order_detail.hatchet_man_phone }}</div>
                    <div>{{ scope.row.game_leveling_order_detail.hatchet_man_qq }}</div>
                </template>
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="号主电话"
                    width="120">
                <template slot-scope="scope">
                    {{ scope.row.game_leveling_order_detail.player_phone }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="来源价格"
                    width="120">
                <template slot-scope="scope">
                    {{ scope.row.source_amount }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="pay_amount"
                    label="支付代练费用"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="get_amount"
                    label="获得赔偿金额"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="get_poundage"
                    label="手续费"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="profit"
                    label="最终支付金额"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="发单客服"
                    width="120">
                <template slot-scope="scope">
                    {{ scope.row.game_leveling_order_detail.username }}
                </template>
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
                                @click="handleDelete(scope.row)">撤单
                        </el-button>
                        <el-button
                                size="small"
                                type="primary"
                                @click="handleOffSale(scope.row)">下架
                        </el-button>
                    </div>
                    <!--撤销 申请仲裁-->
                    <div v-if="scope.row.status == 13">
                        <el-button size="small"
                                   @click="handleApplyConsult(scope.row)">协商撤销
                        </el-button>
                        <el-button size="small"
                                   type="primary"
                                   @click="handleApplyComplain(scope.row)">申请仲裁
                        </el-button>
                    </div>
                    <!--查看图片 完成验收-->
                    <div v-if="scope.row.status == 14">
                        <el-button size="small" @click="handleApplyCompleteImage(scope.row)">查看图片</el-button>
                        <el-button size="small" type="primary" @click="handleComplete(scope.row)">完成验收</el-button>
                    </div>
                    <!--取消撤销/同意撤销 申请仲裁-->
                    <div v-if="scope.row.status == 15">
                        <el-button
                                v-if="scope.row.game_leveling_order_consult.initiator == 1 && scope.row.game_leveling_order_consult.status == 1"
                                size="small" @click="handleCancelConsult(scope.row)">取消撤销
                        </el-button>
                        <el-button
                                v-if="scope.row.game_leveling_order_consult.initiator == 2 && scope.row.game_leveling_order_consult.status == 1"
                                size="small" @click="handleAgreeConsult(scope.row)">同意撤销
                        </el-button>
                        <el-button size="small" type="primary" @click="handleApplyComplain(scope.row)">申请仲裁</el-button>
                    </div>
                    <!--取消仲裁  同意撤销-->
                    <div v-if="scope.row.status == 16">
                        <el-button size="small"
                                   v-if="scope.row.game_leveling_order_complain.initiator == 1 && scope.row.game_leveling_order_complain.status == 1"
                                   @click="handleCancelComplain(scope.row)">取消仲裁
                        </el-button>
                        <el-button
                                v-if="scope.row.game_leveling_order_consult && scope.row.game_leveling_order_consult.initiator == 2 && scope.row.game_leveling_order_consult.status == 1"
                                size="small" type="primary" @click="handleAgreeConsult(scope.row)">同意撤销
                        </el-button>
                    </div>
                    <!--锁定  撤销-->
                    <div v-if="scope.row.status == 17">
                        <el-button
                                size="small"
                                @click="handleLock(scope.row)">锁定
                        </el-button>
                        <el-button
                                size="small"
                                type="primary" @click="handleApplyConsult(scope.row)">协商撤销
                        </el-button>
                    </div>
                    <!--取消锁定  撤销-->
                    <div v-if="scope.row.status == 18">
                        <el-button
                                size="small" @click="handleCancelLock(scope.row)">取消锁定
                        </el-button>
                        <el-button
                                size="small" type="primary" @click="handleApplyConsult(scope.row)">协商撤销
                        </el-button>
                    </div>
                    <!--上架 撤单-->
                    <div v-if="scope.row.status == 22">
                        <el-button size="small" @click="handleOnSale(scope.row)">上架</el-button>
                        <el-button size="small" type="primary" @click="handleDelete(scope.row)">撤单</el-button>
                    </div>

                    <!--重发-->
                    <div v-if="([19, 20, 21, 22, 23, 24].indexOf(scope.row.status)) != -1">
                        <router-link :to="{name:'gameLevelingOrderRepeat', query: {trade_no:scope.row.trade_no}}">
                            <el-button size="small">
                                重发
                            </el-button>
                        </router-link>
                    </div>
                </template>
            </el-table-column>
        </el-table>

        <div class="block" style="margin-top:15px;">
            <el-pagination
                    background
                    @current-change="handleParamsPage"
                    :current-page.sync="search.page"
                    :page-size="20"
                    layout="total, prev, pager, next, jumper"
                    :total="tableDataTotal">
            </el-pagination>
        </div>

        <ApplyComplain v-if="applyComplainVisible"
                       :tradeNo="tradeNo"
                       @handleApplyComplainVisible="handleApplyComplainVisible">
        </ApplyComplain>

        <ApplyConsult v-if="applyConsultVisible"
                      :tradeNo="tradeNo"
                      :amount="amount"
                      :securityDeposit="securityDeposit"
                      :efficiencyDeposit="efficiencyDeposit"
                      @handleApplyConsultVisible="handleApplyConsultVisible">
        </ApplyConsult>
    </div>
</template>

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
        ],
        computed: {
            tableDataEmpty() {
                // return [
                //     this.tableData.length === 0 ? ' el-table_empty' : '',
                // ]
            },
        },
        data() {
            return {
                editRemark: [],
                tradeNo: '',
                amount: 0,
                securityDeposit: 0,
                efficiencyDeposit: 0,
                applyConsultVisible: false,
                applyComplainVisible: false,
                statusQuantity: [],
                platformOptions: [
                    {key: 0, value: '所有平台'},
                    {key: 5, value: '丸子代练'},
                    {key: 1, value: '91代练'},
                    {key: 3, value: '蚂蚁代练'},
                ],
                platformMap: {
                    5: '丸子代练',
                    1: '91代练',
                    4: 'DD373',
                    3: '蚂蚁代练',
                },
                gameLevelingTypeOptions: [],
                gameOptions: [],
                search: {
                    status: '',
                    order_no: '',
                    buyer_nick: '',
                    game_id: '',
                    game_leveling_type_id: '',
                    user_id: '',
                    platform_id: 0,
                    start_created_at: '',
                    created_at: '',
                    page: 1,
                },
                statusMap: {
                    1: '未接单',
                    13: '代练中',
                    14: '待验收',
                    15: '撤销中',
                    16: '仲裁中',
                    17: '异常',
                    18: '已锁定',
                    19: '已撤销',
                    20: '已结算',
                    21: '已仲裁',
                    22: '已下架',
                    23: '强制撤销',
                    24: '已撤单',
                },
                tableLoading: false,
                tableHeight: 0,
                tableDataTotal: 0,
                tableData: []
            }
        },
        methods: {
            // 设置仲裁窗口是否显示
            handleApplyComplainVisible(data) {
                this.applyComplainVisible = data.visible;
                if (data.visible == false) {
                    this.handleTableData();
                }
            },
            // 设置协商窗口是否显示
            handleApplyConsultVisible(data) {
                this.applyConsultVisible = data.visible;
                if (data.visible == false) {
                    this.handleTableData();
                }
            },
            // 表格高度计算
            handleTableHeight() {
                this.tableHeight = window.innerHeight - 366;
            },
            // 获取订单状态数量
            handleStatusQuantity() {
                this.$api.gameLevelingOrderStatusQuantity({}).then(res => {
                    this.statusQuantity = res;
                });
            },
            // 加载订单数据
            handleTableData() {
                this.tableLoading = true;
                this.$api.gameLevelingOrder(this.search).then(res => {
                    this.tableData = res.data.items;
                    this.tableDataTotal = res.data.total;
                    this.tableLoading = false;
                });
                this.handleStatusQuantity();
            },
            // 加载游戏选项
            handleGameOptions() {
                this.$api.games().then(res => {
                    this.gameOptions = res;
                });
            },
            // 搜索
            handleSearch() {
                this.handleTableData();
            },
            // 切换页码
            handleParamsPage(page) {
                this.search.page = page;
                this.handleTableData();
            },
            // 切换状态tab
            handleParamsStatus() {
                this.handleTableData();
            },
            // 选择游戏后加载代练类型
            handleSearchGameId() {
                if (this.search.game_id) {
                    this.$api.gameLevelingTypes({
                        'game_id': this.search.game_id
                    }).then(res => {
                        this.gameLevelingTypeOptions = res;
                    });
                } else {
                    this.gameLevelingTypeOptions = [];
                }
            },
            // 撤单
            handleDelete(row) {
                this.$confirm('您确定要"撤单"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$api.gameLevelingOrderDelete({
                        'trade_no': row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                    this.$api.gameLevelingOrderOnSale({
                        'trade_no': row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                    this.$api.gameLevelingOrderOffSale({
                        'trade_no': row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
            handleCancelComplain(row) {
                this.$confirm('您确定要"取消仲裁"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$api.gameLevelingOrderCancelComplain({
                        'trade_no': row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
            handleApplyCompleteImage(row) {
                // 请求图片
                this.$api.gameLevelingOrderApplyCompleteImage({
                    'trade_no': row.trade_no
                }).then(res => {
                    if (res.status == 1) {
                        const h = this.$createElement;
                        let item = [];
                        res.content.forEach(function (val) {
                            item.push(h('el-carousel-item', null, [
                                h('img', {
                                    attrs: {
                                        src: val['url']
                                    }
                                }, '')
                            ]))
                        });

                        this.$msgbox({
                            title: '查看验收图片',
                            message: h('el-carousel', null, item),
                            showCancelButton: true,
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                        });
                    } else {
                        this.$message({
                            type: 'error',
                            message: res.message
                        });
                    }
                }).catch(err => {
                    this.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            },
            // 完成验收
            handleComplete(row) {
                this.$confirm('您确定要"完成验收"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$api.gameLevelingOrderComplete({
                        'trade_no': row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                    this.$api.gameLevelingOrderCancelConsult({
                        'trade_no': row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                let str=row.consult_describe + " ，确认 同意撤销 吗?";

                this.$confirm(str, '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$api.gameLevelingOrderAgreeConsult({
                        'trade_no': row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                    this.$api.gameLevelingOrderRejectConsult({
                        'trade_no': row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                    this.gameLevelingOrderLock({
                        'trade_no': row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                    this.$api.gameLevelingOrderCancelLock({
                        'trade_no': row.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
            // 重置表单
            handleResetForm() {
                this.search = {
                    status: '',
                    order_no: '',
                    buyer_nick: '',
                    game_id: '',
                    game_leveling_type_id: '',
                    user_id: '',
                    platform_id: 0,
                    start_created_at: '',
                    created_at: '',
                    page: 1,
                };
                this.handleTableData();
            },
            handleCellMouseEnter(row, column, cell, event) {
                if (column.property === 'user_remark') {
                    row.remark_edit = true;
                }
            },
            handleCellMouseLeave(row, column, cell, event) {
                if (column.property === 'user_remark') {
                    row.remark_edit = false;
                    // if (row.game_leveling_order_detail.user_remark !== '') {
                        this.$api.gameLevelingOrderUserRemark({
                            trade_no:row.trade_no,
                            user_remark:row.game_leveling_order_detail.user_remark}
                        ).then(res => {
                        });
                    // }
                }
            }
        },
        created() {
            this.handleTableHeight();
            this.handleTableData();
            this.handleGameOptions();
            window.addEventListener('resize', this.handleTableHeight);
        },
    }
</script>

<style lang="less">
    .game-leveling-order-tab .el-tabs__item {
        font-weight: normal;
    }

    .game-leveling-order-table .el-button {
        width: 80px;
    }

    .el-table_empty .el-table__empty-block {
        width: auto !important;
    }

    .game-leveling-order {
        .el-table--small th, .el-table--small td {
            padding: 2px 0;
        }
    }
</style>
