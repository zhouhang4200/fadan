<template>

    <div class="game-leveling-order">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">

                <el-col :span="6">
                    <el-form-item label="活动名称" prop="name">
                        <el-input v-model="searchParams.trade_no"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="活动名称" prop="name">
                        <el-input v-model="searchParams.trade_no"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="活动名称" prop="name">
                        <el-input v-model="searchParams.trade_no"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="活动名称" prop="name">
                        <el-input v-model="searchParams.trade_no"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>

            <el-row :gutter="16">
                <el-col :span="6">
                    <el-form-item label="玩家旺旺">
                        <el-input v-model="searchParams.trade_no" placeholder="玩家旺旺"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="玩家旺旺">
                        <el-input v-model="searchParams.trade_no" placeholder="玩家旺旺"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item label="玩家旺旺">
                        <el-input v-model="searchParams.trade_no" placeholder="玩家旺旺"></el-input>
                    </el-form-item>
                </el-col>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">查询</el-button>
                </el-form-item>
            </el-row>

        </el-form>

        <el-tabs  @tab-click="handleParamsStatus" >
            <el-tab-pane>
                <span slot="label" data-status="1">
                    全部
                    <el-badge  :value="0"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane>
                <span slot="label" data-status="1">
                    未接单
                    <el-badge  :value="1"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane>
                <span slot="label" data-status="1">
                    代练中
                    <el-badge  :value="13"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane>
                <span slot="label" data-status="1">
                    待验收
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane>
                <span slot="label" data-status="1">
                    撤销中
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane>
                <span slot="label" data-status="1">
                    淘宝退款中
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane>
                <span slot="label" data-status="1">
                    异常
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane>
                <span slot="label" data-status="1">
                    锁定
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane>
                <span slot="label" data-status="1">
                    已撤销
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane>
                <span slot="label" data-status="1">
                    已结算
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane>
                <span slot="label" data-status="1">
                    已仲裁
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane>
                <span slot="label" data-status="1">
                    已下架
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

            <el-tab-pane>
                <span slot="label" data-status="1">
                    已撤单
                    <el-badge  :value="12"></el-badge>
                </span>
            </el-tab-pane>

        </el-tabs>

        <el-table
                :height="tableHeight"
                :data="tableData"
                border
                style="width: 100%;height:800px">
            <el-table-column
                    fixed
                    prop="trade_no"
                    label="订单号"
                    width="150">
                <template slot-scope="scope">
                    <div style="margin-left: 10px"> 淘宝：{{ scope.row.trade_no }}</div>
                    <div style="margin-left: 10px">渠道：{{ scope.row.trade_no }}</div>
                </template>
            </el-table-column>
            <el-table-column
                    prop="status"
                    label="订单状态"
                    width="120">
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
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="代练价格"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="效率/安全保证金"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="发单/接单时间"
                    width="120">
            </el-table-column>
            <el-table-column
                    prop="zip"
                    label="代练时间"
                    width="120">
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
                        <el-button size="small" @click="handleDelete(scope.row)">撤单</el-button>
                        <el-button size="small" type="primary" @click="handleDelete(scope.row)">下架</el-button>
                    </div>
                    <!--撤销 申请仲裁-->
                    <div v-if="scope.row.status == 13">
                        <el-button size="small" @click="handleDelete(scope.row)">撤单</el-button>
                        <el-button size="small" type="primary" @click="handleDelete(scope.row)">申请仲裁</el-button>
                    </div>
                    <!--查看图片 完成验收-->
                    <div v-if="scope.row.status == 14">
                        <el-button size="small" @click="handleDelete(scope.row)">查看图片</el-button>
                        <el-button size="small" type="primary" @click="handleDelete(scope.row)">完成验收</el-button>
                    </div>
                    <!--取消撤销/同意撤销 申请仲裁-->
                    <div v-if="scope.row.status == 15">
                        <el-button size="small" @click="handleDelete(scope.row)">取消撤销</el-button>
                        <el-button size="small" type="primary" @click="handleDelete(scope.row)">申请仲裁</el-button>
                    </div>
                    <!--取消仲裁  同意撤销-->
                    <div v-if="scope.row.status == 16">
                        <el-button size="small" @click="handleDelete(scope.row)">取消仲裁</el-button>
                        <el-button size="small" type="primary" @click="handleDelete(scope.row)">同意撤销</el-button>
                    </div>
                    <!--锁定  撤销-->
                    <div v-if="scope.row.status == 17">
                        <el-button size="small" @click="handleDelete(scope.row)">锁定</el-button>
                        <el-button size="small" type="primary" @click="handleDelete(scope.row)">撤销</el-button>
                    </div>
                    <!--取消锁定  撤销-->
                    <div v-if="scope.row.status == 18">
                        <el-button size="small" @click="handleDelete(scope.row)">取消锁定</el-button>
                        <el-button size="small" type="primary" @click="handleDelete(scope.row)">撤销</el-button>
                    </div>
                    <!--上架 撤单-->
                    <div v-if="scope.row.status == 22">
                        <el-button size="small" @click="handleDelete(scope.row)">上架</el-button>
                        <el-button size="small" type="primary" @click="handleDelete(scope.row)">撤单</el-button>
                    </div>
                    <!--重发-->
                    <div v-if="(scope.row.status == 19
                    || scope.row.status == 20
                    || scope.row.status == 21
                    || scope.row.status ==23
                    || scope.row.status == 24)">
                        <el-button size="small" @click="handleDelete(scope.row)">重发</el-button>
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

        <ApplyComplain :visible="visible"></ApplyComplain>
    </div>

</template>

<style scoped>
    .el-tabs .el-tabs__item {
        font-weight: normal;
    }
</style>

<script>
    import ApplyComplain from './ApplyComplain';
    export default {
        components: {
            ApplyComplain
        },
        props: [
            'pageTitle',
            'gameLevelingOrderApi',
            'gameLevelingImageBase64Api',
            'gameLevelingOrderDeleteApi',
        ],
        data() {
            return {
                visible:false,
                searchParams:{
                    status:'',
                    order_no:'',
                    buyer_nick:'',
                    game_id:'',
                    game_leveling_type_id:'',
                    user_id:'',
                    platform_id:'',
                    start_created_at:'',
                    end_created_at:'',
                    page:1,
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
                this.tableHeight = window.innerHeight - 380;
            },
            // 加载数据
            handleTableData(){
                axios.post(this.gameLevelingOrderApi, this.searchParams).then(res => {
                    this.tableData = res.data.data;
                    this.tableDataTotal = res.data.total;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
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
            handleParamsStatus(tab, event) {
                this.searchParams.status = event.target.getAttribute('data-status');
                this.handleTableData();
            },
            // 查看订单
            handleShow(index) {
                this.$Modal.info({
                    title: 'User Info',
                    content: `Name：${this.tableData[index].status}<br>Age：${this.tableData[index].title}<br>Address：${this.tableData[index].amount}`
                })
            },
            // 撤单
            handleDelete(index) {
                this.$confirm('您确定要"撤单"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$store.commit('handleApplyConsultVisible',{visible:true});
                });
            },
            // 上架
            handleOnSale(index) {

            },
            // 下架
            handleOffSale(index) {
                this.$confirm('您确定要"下架"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.gameLevelingOrderDeleteApi, {
                        'trade_no' : this.tableData[index].trade_no
                    }).then(res => {

                    }).catch(err => {

                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });

            },
            // 申请仲裁
            handleApplyComplain(index) {

            },
            // 取消仲裁
            handleCancelComplain(index) {
                this.$confirm('您确定要"下架"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.gameLevelingOrderDeleteApi, {
                        'trade_no' : this.tableData[index].trade_no
                    }).then(res => {

                    }).catch(err => {

                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            },
            // 查看图片
            handleApplyCompleteImage(index) {

            },
            // 完成验收
            handleComplete(index) {
                this.$confirm('您确定要"下架"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.gameLevelingOrderDeleteApi, {
                        'trade_no' : this.tableData[index].trade_no
                    }).then(res => {

                    }).catch(err => {

                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            },
            // 申请撤销
            handleApplyConsult(index) {

            },
            // 取消撤销
            handleCancelConsult(index) {
                this.$confirm('您确定要"下架"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.gameLevelingOrderDeleteApi, {
                        'trade_no' : this.tableData[index].trade_no
                    }).then(res => {

                    }).catch(err => {

                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            },
            // 同意撤销
            handleAgreeConsult(index) {
                this.$confirm('您确定要"下架"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.gameLevelingOrderDeleteApi, {
                        'trade_no' : this.tableData[index].trade_no
                    }).then(res => {

                    }).catch(err => {

                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            },
            // 不同意撤销
            handleRefuseConsult(index) {
                this.$confirm('您确定要"下架"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.gameLevelingOrderDeleteApi, {
                        'trade_no' : this.tableData[index].trade_no
                    }).then(res => {

                    }).catch(err => {

                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            },
            // 锁定
            handleLock(index) {
                this.$confirm('您确定要"下架"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.gameLevelingOrderDeleteApi, {
                        'trade_no' : this.tableData[index].trade_no
                    }).then(res => {

                    }).catch(err => {

                    });

                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            },
            // 取消锁定
            handleCancelLock(index) {
                this.$confirm('您确定要"下架"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.gameLevelingOrderDeleteApi, {
                        'trade_no' : this.tableData[index].trade_no
                    }).then(res => {

                    }).catch(err => {

                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            },
            // 重新下单
            handleRepeatOrder(index) {
            },
        },
        created() {
            this.handlePageTitle();
            this.handleTableHeight();
            this.handleTableData();
            window.addEventListener('resize', this.handleTableHeight);
        },
        destroyed() {
            window.removeEventListener('resize', this.handleTableHeight)
        },
    }
</script>