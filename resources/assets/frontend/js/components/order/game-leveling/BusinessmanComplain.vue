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

            <el-row :gutter="16">

            </el-row>
        </el-form>

        <el-tabs  v-model="searchParams.status"
                  @tab-click="handleParamsStatus"
                  size="small"
                  class="game-leveling-order-tab">

            <el-tab-pane name="99">
                <span slot="label">
                    全部
                </span>
            </el-tab-pane>

            <el-tab-pane name="1">
                <span slot="label">
                    投诉中
                    <el-badge v-if="(this.statusQuantity[1] != undefined)"  :value="this.statusQuantity[1]"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane  name="2">
                <span slot="label">
                    已取消
                    <el-badge v-if="(this.statusQuantity[2] != undefined)"  :value="this.statusQuantity[2]"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane  name="3">
                <span slot="label">
                    投诉成功
                    <el-badge v-if="(this.statusQuantity[3] != undefined)"  :value="this.statusQuantity[3]"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane  name="4">
                <span slot="label">
                    投诉失败
                    <el-badge v-if="(this.statusQuantity[4] != undefined)"  :value="this.statusQuantity[4]"></el-badge>
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
                    prop="tid"
                    label="订单号"
                    width="250">
                <template slot-scope="scope">
                    <a :href="showApi + '/' + scope.row.game_leveling_order_trade_no">
                        <div style="margin-left: 10px"> 淘宝：{{ scope.row.game_leveling_order.channel_order_trade_no }}</div>
                        <div style="margin-left: 10px"> {{ platform[scope.row.game_leveling_order.platform_id] }}：{{ scope.row.game_leveling_order.platform_trade_no }}</div>
                    </a>
                </template>
            </el-table-column>
            <el-table-column
                    prop="province"
                    label="淘宝订单状态">
                <template slot-scope="scope">
                    {{ taobaoStatusMap[scope.row.game_leveling_order.channel_order_status] }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="city"
                    label="平台订单状态">
            </el-table-column>
            <el-table-column
                    prop="game_name"
                    label="游戏">
                <template slot-scope="scope">
                    {{ gameMap[scope.row.game_leveling_order.game_id] }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="要求赔偿金额">
                <template slot-scope="scope">
                    <a :href="'http://www.taobao.com/webww/ww.php?ver=3&touid=' + scope.row.buyer_nick +'&siteid=cntaobao&status=1&charset=utf-8'" target="_blank">
                        <div style="margin-left: 10px">{{ scope.row.buyer_nick }}</div>
                    </a>
                </template>
            </el-table-column>
            <el-table-column
                    prop="price"
                    label="投诉状态">
                <template slot-scope="scope">
                    {{ complainStatusMap[scope.row.status] }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="created_at"
                    label="投诉时间">
            </el-table-column>

            <el-table-column
                    fixed="right"
                    label="操作"
                    width="200">
                <template slot-scope="scope">
                    <el-button
                            size="small"
                            @click="handleShowImage(scope.row)">查看截图</el-button>

                    <el-button
                            v-if="scope.row.status == 1"
                            size="small"
                            type="primary"
                            @click="handleCancelComplain(scope.row)">取消投诉</el-button>
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
    export default {
        props: [],
        computed: {
            tableDataEmpty() {
                return [
                    this.tableData.length === 0 ? ' el-table_empty' : '',
                ]
            },
        },
        data() {
            return {
                statusQuantity:[],
                gameLevelingTypeOptions:[],
                gameOptions:[],
                searchParams:{
                    status:'99',
                    order_no:'',
                    buyer_nick:'',
                    game_id:'',
                    game_leveling_type_id:'',
                    start_created_at:'',
                    created_at:'',
                    page:1,
                },
                platform: {
                    5:'丸子代练',
                    2:'91代练',
                    3:'蚂蚁代练',
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
                    }]
                },
                gameMap:[],
                taobaoStatusMap: {
                    1: '投诉中',
                    2: '已取消',
                    3: '投诉成功',
                    4: '投诉失败',
                },
                complainStatusMap: {
                    1: '投诉中',
                    2: '已取消',
                    3: '投诉成功',
                    4: '投诉失败',
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
                tableLoading:false,
                tableHeight: 0,
                tableDataTotal:0,
                tableData: []
            }
        },
        methods: {
            // 设置当前页面包屑
            handlePageTitle() {
                // this.$store.commit('handlePageTitle',{pageTitle:this.pageTitle})
            },
            // 表格高度计算
            handleTableHeight() {
                this.tableHeight = window.innerHeight - 345;
            },
            // 获取订单状态数量
            handleStatusQuantity() {
                this.$api.businessmanComplainStatusQuantity().then(res => {
                    this.statusQuantity = res;
                });
            },
            // 加载订单数据
            handleTableData(){
                this.tableLoading = true;
                this.$api.businessmanComplain(this.searchParams).then(res => {
                    this.tableData = res.data;
                    this.tableDataTotal = res.total;
                    this.tableLoading = false;
                });
                this.handleStatusQuantity();
            },
            // 加载游戏选项
            handleGameOptions() {
                this.$api.games().then(res => {
                    this.gameOptions = res;
                    let currentThis = this;
                    res.forEach(function (item) {
                        currentThis.gameMap[item.id]  = item.name;
                    });
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
                    this.$api.gameLevelingTypes({
                        'game_id' : this.searchParams.game_id
                    }).then(res => {
                        this.gameLevelingTypeOptions = res;
                    });
                } else {
                    this.gameLevelingTypeOptions = [];
                }
            },
            // 查看投诉图片
            handleShowImage(row) {
                // 请求图片
                this.$api.businessmanComplainImage({
                    'id' : row.id
                }).then(res => {
                    const h = this.$createElement;
                    let item = [];
                    res.content.forEach(function (val) {
                        item.push(h('el-carousel-item', null, [
                            h('img', {
                                attrs:{
                                    src:val
                                }
                            }, '')
                        ]))
                    });

                    this.$msgbox({
                        title: '查看仲裁图片',
                        message: h('el-carousel', null, item),
                        showCancelButton: true,
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                    });

                }).catch(err => {
                    this.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            },
            // 取消投诉操作
            handleCancelComplain(row) {
                this.$confirm('您确定要"取消投诉"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    // 取消操作
                    this.$api.businessmanComplainCancel({
                        'id' : row.id
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
                this.searchParams = {
                    status:'',
                    order_no:'',
                    buyer_nick:'',
                    game_id:'',
                    game_leveling_type_id:'',
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
        }
    }
</script>