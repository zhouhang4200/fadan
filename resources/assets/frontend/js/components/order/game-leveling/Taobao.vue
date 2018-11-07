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

            <el-tab-pane name="99">
                <span slot="label">
                    全部
                </span>
            </el-tab-pane>

            <el-tab-pane name="0">
                <span slot="label">
                    待处理
                    <el-badge v-if="(this.statusQuantity[0] != undefined)"  :value="this.statusQuantity[0]"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane  name="1">
                <span slot="label">
                    已发布
                    <el-badge v-if="(this.statusQuantity[1] != undefined)"  :value="this.statusQuantity[1]"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane  name="2">
                <span slot="label">
                    已隐藏
                    <el-badge v-if="(this.statusQuantity[2] != undefined)"  :value="this.statusQuantity[2]"></el-badge>
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
                    prop="seller_nick"
                    label="店铺"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="tid"
                    label="订单号"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="province"
                    label="淘宝订单状态"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="city"
                    label="平台订单状态"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="game_name"
                    label="绑定游戏"
                    width="80">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="买家旺旺"
                    width="120">
                <template slot-scope="scope">
                    <a :href="'http://www.taobao.com/webww/ww.php?ver=3&touid=' + scope.row.buyer_nick +'&siteid=cntaobao&status=1&charset=utf-8'" target="_blank">
                        <div style="margin-left: 10px">{{ scope.row.buyer_nick }}</div>
                    </a>
                </template>
            </el-table-column>
            <el-table-column
                    prop="price"
                    label="购买单价">
            </el-table-column>
            <el-table-column
                    prop="num"
                    label="购买数量">
            </el-table-column>
            <el-table-column
                    prop="payment"
                    label="实付金额">
            </el-table-column>
            <el-table-column
                    prop="created"
                    label="下单时间">
            </el-table-column>
            <el-table-column
                    prop="remark"
                    label="备注">
            </el-table-column>

            <el-table-column
                    fixed="right"
                    label="操作"
                    width="200">
                <template slot-scope="scope">
                    <el-button
                            size="small"
                            @click="handleCreate(scope.row)">发布</el-button>
                    <el-button
                            size="small"
                            type="primary"
                            @click="handleHide(scope.row)">隐藏</el-button>
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
        props: [
            'pageTitle',
            'orderApi',
            'gamesApi',
            'statusQuantityApi',
            'gameLevelingTypesApi',
        ],
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
                    status:'',
                    order_no:'',
                    buyer_nick:'',
                    game_id:'',
                    game_leveling_type_id:'',
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
                    }]
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
                axios.post(this.statusQuantityApi, this.searchParams).then(res => {
                    this.statusQuantity = res.data;
                }).catch(err => {
                });
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
                this.handleStatusQuantity();
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
            // 发布操作
            handleCreate(row) {
                // location.href= this.orderRepeatApi + '/' + row.trade_no;
            },
            // 隐藏操作
            handleHide(row) {
                // location.href= this.orderRepeatApi + '/' + row.trade_no;
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
            this.$store.commit('handleOpenMenu', '1');
            this.$store.commit('handleOpenSubmenu', '1-1');
            this.handlePageTitle();
            this.handleTableHeight();
            this.handleTableData();
            this.handleGameOptions();
            window.addEventListener('resize', this.handleTableHeight);
        },
        destroyed() {
            window.removeEventListener('resize', this.handleTableHeight)
        },
        mounted() {
            this.$cookieStore.setCookie('menu', '1');
            this.$cookieStore.setCookie('submenu', '1-1');
        },
    }
</script>