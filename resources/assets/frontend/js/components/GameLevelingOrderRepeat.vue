<template>
    <div class="game-leveling-order-repeat">
        <div class="main">
            <el-row :gutter="10">
                <el-form ref="form"  :rules="rules" :model="form" label-width="120px">
                    <el-col :span="16" style="margin-bottom:60px">
                        <div class="grid-content bg-purple" style="padding: 15px;background-color: #fff" >
                            <el-tabs>
                                <el-tab-pane label="订单信息">
                                    <el-card class="box-card">
                                        <div  class="text item">
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item
                                                            label="游戏/区/服"
                                                            prop="game_region_server">
                                                        <el-cascader
                                                                @change="handleFromGameLevelingTypeIdOptions"
                                                                :options="gameRegionServerOptions"
                                                                v-model="form.game_region_server">
                                                        </el-cascader>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="代练类型" prop="game_leveling_type_id">
                                                        <el-select
                                                                v-model="form.game_leveling_type_id"
                                                                placeholder="请选择">
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
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="角色名称" prop="game_role">
                                                        <el-input
                                                                
                                                                type="input"
                                                                v-model.number="form.game_role"
                                                                autocomplete="off"></el-input>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="游戏账号" prop="game_account">
                                                        <el-input
                                                                
                                                                type="input"
                                                                v-model="form.game_account"
                                                                autocomplete="off"></el-input>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="游戏密码" prop="game_password">
                                                        <el-input
                                                                
                                                                type="input"
                                                                v-model="form.game_password"
                                                                autocomplete="off"></el-input>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                        </div>
                                    </el-card>
                                    <el-card class="box-card">
                                        <div  class="text item">
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="代练标题" prop="title">
                                                        <el-input
                                                                
                                                                type="input"
                                                                v-model="form.title"
                                                                autocomplete="off">
                                                            <el-tooltip slot="append" placement="top">
                                                                <div slot="content">多行信息<br/>第二行信息</div>
                                                                <el-button><i class="el-icon-question"></i></el-button>
                                                            </el-tooltip>
                                                        </el-input>
                                                    </el-form-item>
                                                </el-col>

                                                <el-col :span="12">
                                                    <el-form-item
                                                            label="接单密码"
                                                            prop="take_order_password">
                                                        <el-input
                                                                
                                                                type="input"
                                                                v-model="form.take_order_password"
                                                                autocomplete="off"></el-input>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="代练天/小时" prop="day_hour">
                                                        <el-cascader
                                                                
                                                                :options="dayHourOptions"
                                                                v-model="form.day_hour"
                                                        ></el-cascader>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="代练要求模版">
                                                        <el-select
                                                                v-model="form"
                                                                placeholder="请选择">
                                                        </el-select>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item  label="代练说明"  prop="explain">
                                                        <el-input
                                                                type="textarea"
                                                                :rows="3"
                                                                placeholder="请输入内容"
                                                                v-model="form.explain">
                                                        </el-input>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="代练要求"  prop="requirement">
                                                        <el-input
                                                                type="textarea"
                                                                :rows="3"
                                                                placeholder="请输入内容"
                                                                v-model="form.requirement">
                                                        </el-input>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="代练价格"  prop="amount">
                                                        <el-input
                                                                type="input"
                                                                placeholder="请输入内容"
                                                                v-model="form.amount">
                                                        </el-input>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="来源价格"  prop="source_amount">
                                                        <el-input
                                                                type="input"
                                                                placeholder="请输入内容"
                                                                v-model="form.source_amount">
                                                        </el-input>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="安全保证金" prop="security_deposit">
                                                        <el-input
                                                                type="input"
                                                                placeholder="请输入内容"
                                                                v-model="form.security_deposit">
                                                        </el-input>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="效率保证金" prop="efficiency_deposit">
                                                        <el-input
                                                                type="input"
                                                                placeholder="请输入内容"
                                                                v-model="form.efficiency_deposit">
                                                        </el-input>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="玩家电话" prop="player_phone">
                                                        <el-input
                                                                type="input"
                                                                placeholder="请输入内容"
                                                                v-model="form.player_phone">
                                                        </el-input>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="商户QQ" prop="user_qq">
                                                        <el-input
                                                                type="input"
                                                                placeholder="请输入内容"
                                                                v-model="form.user_qq">
                                                        </el-input>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                        </div>
                                    </el-card>
                                    <el-card class="box-card">
                                        <div  class="text item">
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="加价幅度"  prop="price_increase_step">
                                                        <el-input
                                                                
                                                                type="input"
                                                                v-model="form.price_increase_step"
                                                                autocomplete="off">
                                                        </el-input>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="加价上限"  prop="price_ceiling">
                                                        <el-input
                                                                
                                                                type="input"
                                                                v-model="form.price_ceiling"
                                                                autocomplete="off">
                                                        </el-input>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">

                                                    <el-form-item label="补款单号"  prop="take_order_password">
                                                        <el-input
                                                                type="input"
                                                                v-model="form.take_order_password"
                                                                autocomplete="off">
                                                            <el-button
                                                                    slot="append"
                                                                    @click.prevent="addDomain(domain)">
                                                                <i class="el-icon-circle-plus-outline"></i> 增加补款单号
                                                            </el-button>
                                                        </el-input>
                                                    </el-form-item>

                                                    <el-form-item
                                                            v-for="(domain, index) in form.domains"
                                                            :label="'补款单号' + (index +1)"
                                                            :key="domain.key"
                                                            :prop="'domains.' + index + '.value'"
                                                            :rules="{required: true, message: '补款单号不能为空', trigger: 'blur'}">
                                                        <el-input v-model="domain.value">
                                                            <el-button  slot="append" @click.prevent="removeDomain(domain)">删除</el-button>
                                                        </el-input>

                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">

                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="客服备注">
                                                        <el-input
                                                                type="textarea"
                                                                :rows="2"
                                                                placeholder="请输入内容"
                                                                v-model="form.remark">
                                                        </el-input>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">

                                                </el-col>
                                            </el-row>
                                        </div>
                                    </el-card>
                                </el-tab-pane>
                            </el-tabs>
                        </div>
                    </el-col>
                </el-form>
                <el-col :span="8">
                    <div class="grid-content bg-purple" style="padding: 15px;background-color: #fff" >
                        <el-tabs>
                            <el-tab-pane label="淘宝数据">
                                <el-table
                                        :data="taobaoData"
                                        :show-header=false
                                        border
                                        style="width: 100%">
                                    <el-table-column
                                            prop="name"
                                            label=""
                                            width="120">
                                    </el-table-column>
                                    <el-table-column
                                            prop="value"
                                            label="">
                                        <template slot-scope="scope">
                                            <span v-html="scope.row.value"></span>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </el-tab-pane>
                        </el-tabs>
                    </div>
                </el-col>
            </el-row>
        </div>
        <div class="footer">
            <el-row>
                <el-col :span="16">
                    <div style="text-align: center;line-height: 60px;">
                        <el-button
                                   type="primary"
                                   @click="handleSubmitForm('form')"
                                   style="margin-right: 8px">确认下单</el-button>
                    </div>
                </el-col>
            </el-row>
        </div>

    </div>
</template>

<script>
    export default {
        name: "GameLevelingRepeat",
        props:[
            'tradeNo',
            'orderEditApi',
            'orderCreateApi',
            'gameRegionServerApi',
            'gameLevelingTypesApi',
        ],
        computed: {
            displayFooter() {
                let status = [19, 20, 21, 22, 23, 24];
                if (this.tabCurrent == "1" && status.indexOf(this.form.status) == -1) {
                    return true;
                } else if (status.indexOf(this.form.status) != -1) {
                    return false;
                } else {
                    return false;
                }
            }
        },
        data() {
            return {
                gameRegionServerOptions: [], // 游戏/区/服 选项
                dayHourOptions:[],
                gameLevelingTypeOptions:[], // 游戏代练类型 选项
                form: {
                    trade_no:this.tradeNo,
                    status:0,
                    channel_order_trade_no:'',
                    game_leveling_order_consult:[],
                    game_leveling_order_complain:[],
                    game_region_server: [], // 选择的 游戏/区/服
                    day_hour:[], // 选择的代练天/小时
                    game_id: 0, // 游戏ID
                    game_region_id: 0, // 游戏区ID
                    game_server_id: 0, // 游戏服务器ID
                    game_leveling_type_id: '', // 代练类型ID
                    amount:'', // 代练金额
                    source_amount:'', // 来源价格
                    security_deposit:'', // 安全保证金
                    efficiency_deposit:'', // 效率保证金
                    title:'', //代练标题
                    game_role:'', // 游戏角色
                    game_account:'', // 游戏账号
                    game_password:'', // 游戏密码
                    price_increase_step:'', // 自动加价步长
                    price_ceiling:'', // 自动加价上限
                    explain:'', // 代练说明
                    requirement:'', // 代练要求
                    take_order_password:'', // 接单密码
                    player_phone:'', // 玩家电话
                    user_qq:'', // 商户qq
                    domains: [],
                    remark: '',
                },
                rules: {
                    game_leveling_type_id:[
                        { required: true, message: '请选择代练类型', trigger: 'change' },
                    ],
                    game_role:[
                        { required: true, message: '请输入游戏角色', trigger: 'blur' },
                    ],
                    game_account:[
                        { required: true, message: '请输入游戏账号', trigger: 'change' },
                    ],
                    game_password:[
                        { required: true, message: '请输入游戏密码', trigger: 'change' },
                    ],
                    title:[
                        { required: true, message: '请输入代练标题', trigger: 'change' },
                        { min: 3, max: 35, message: '长度在 3 到 35 个字符', trigger: 'change' }
                    ],
                    day_hour:[
                        {type: 'array', required: true, message: '请选择代练天/小时',  trigger: 'change'},
                    ],
                    game_region_server:[
                        {type: 'array', required: true, message: '请选择游戏/区/服', trigger: 'change' },
                    ],
                    explain:[
                        { required: true, message: '请输入代练说明', trigger: 'change' },
                    ],
                    requirement:[
                        { required: true, message: '请输入代练要求', trigger: 'change' },
                    ],
                    amount: [
                        { required: true, message: '请输入代练价格', trigger: 'change' }
                    ],
                    source_amount: [
                        {
                            validator:(rule, value, callback)=>{
                                if(value != "" && value != undefined){
                                    if((/^[+]{0,1}(\d+)$|^[+]{0,1}(\d+\.\d+)$/).test(value) == false){
                                        callback(new Error("加价幅度必须为数字值"));
                                    }else{
                                        callback();
                                    }
                                }else{
                                    callback();
                                }
                            },
                            trigger:'blur'
                        },
                    ],
                    efficiency_deposit: [
                        { required: true, message: '请输入效率保证金', trigger: 'change' }
                    ],
                    security_deposit: [
                        { required: true, message: '请输入安全保证金', trigger: 'change' }
                    ],
                    user_qq: [
                        { required: true, message: '请输入商户QQ号', trigger: 'change' }
                    ],
                    player_phone: [
                        { required: true, message: '请输入无家电话', trigger: 'blur' },
                        { min: 3, max: 5, message: '长度在 3 到 5 个字符', trigger: 'blur' }
                    ],
                    price_increase_step: [
                        {
                            validator:(rule, value, callback)=>{
                                if(value != "" && value != undefined){
                                    if((/^[+]{0,1}(\d+)$|^[+]{0,1}(\d+\.\d+)$/).test(value) == false){
                                        callback(new Error("加价幅度必须为数字值"));
                                    }else{
                                        callback();
                                    }
                                }else{
                                    callback();
                                }
                            },
                            trigger:'blur'
                        },
                    ],
                    price_ceiling: [
                        {
                            validator:(rule,value,callback)=>{
                                if(value != "" && value != undefined){
                                    if((/^[+]{0,1}(\d+)$|^[+]{0,1}(\d+\.\d+)$/).test(value) == false){
                                        callback(new Error("加价上限必须为数字值"));
                                    }else{
                                        callback();
                                    }
                                }else{
                                    callback();
                                }
                            },
                            trigger:'blur'
                        },
                    ],

                },
                taobaoData:[],
            };
        },
        methods: {
            handleFromData() {
                console.log(this.tradeNo);
                axios.post(this.orderEditApi, {trade_no: this.tradeNo}).then(res => {
                        this.form.status = res.data.status;
                        this.form.channel_order_trade_no = res.data.channel_order_trade_no;
                        this.form.game_leveling_order_consult = res.data.game_leveling_order_consult;
                        this.form.game_leveling_order_complain = res.data.game_leveling_order_complain;
                        this.form.game_region_server =  [  // 选择的 游戏/区/服
                            res.data.game_id,
                            res.data.game_region_id,
                            res.data.game_server_id,
                        ];
                        this.handleFromGameLevelingTypeIdOptions();
                        this.form.day_hour = [   // 选择的代练天/小时
                            res.data.day,
                            res.data.hour,
                        ];
                        this.form.game_id = res.data.game_id; // 游戏ID
                        this.form.game_region_id =  res.data.game_region_id; // 游戏区ID
                        this.form.game_server_id =  res.data.game_server_id;// 游戏服务器ID
                        this.form.game_leveling_type_id =  res.data.game_leveling_type_id; // 代练类型ID
                        this.form.amount = res.data.amount; // 代练金额
                        this.form.source_amount = res.data.source_amount; // 来源价格
                        this.form.security_deposit = res.data.security_deposit; // 安全保证金
                        this.form.efficiency_deposit = res.data.efficiency_deposit; // 效率保证金
                        this.form.title = res.data.title; //代练标题
                        this.form.game_role = res.data.game_role; // 游戏角色
                        this.form.game_account = res.data.game_account; // 游戏账号
                        this.form.game_password = res.data.game_password; // 游戏密码
                        this.form.price_increase_step = res.data.price_increase_step != '0.0000' ? res.data.price_increase_step : ''; // 自动加价步长
                        this.form.price_ceiling = res.data.price_ceiling != '0.0000' ? res.data.price_ceiling : ''; // 自动加价上限
                        this.form.explain = res.data.game_leveling_order_detail.explain; // 代练说明
                        this.form.requirement = res.data.game_leveling_order_detail.requirement; // 代练要求
                        this.form.take_order_password = res.data.take_order_password; // 接单密码
                        this.form.player_phone = res.data.game_leveling_order_detail.player_phone; // 玩家电话
                        this.form.user_qq = res.data.game_leveling_order_detail.user_qq; // 商家qq
                        this.form.remark =  res.data.remark;
                        this.form.domains = [];

                        this.taobaoData = [
                            {
                                name: '店铺名',
                                value: res.data.taobao_data.seller_nick,
                            },
                            {
                                name: '天猫单号',
                                value: res.data.taobao_data.tid,
                            },
                            {
                                name: '订单状态',
                                value: res.data.taobao_data.trade_status,
                            },
                            {
                                name: '买家旺旺',
                                value: res.data.taobao_data.buyer_nick,
                            }, {
                                name: '购买单价',
                                value: res.data.taobao_data.price,
                            },
                            {
                                name: '购买数量',
                                value: res.data.taobao_data.num,
                            },
                            {
                                name: '实付金额',
                                value: res.data.taobao_data.payment,
                            },
                            {
                                name: '所在区/服',
                                value: res.data.taobao_data.region_server,
                            },
                            {
                                name: '角色名称',
                                value: res.data.taobao_data.role,
                            },
                            {
                                name: '买家留言',
                                value: res.data.taobao_data.buyer_message,
                            },
                            {
                                name: '下单时间',
                                value: res.data.taobao_data.created,
                            }
                        ];
                }).catch(err => {
                });
            },
            handleFromGameRegionServerOptions() {
                axios.post(this.gameRegionServerApi).then(res => {
                    this.gameRegionServerOptions = res.data;
                }).catch(err => {
                });
            },
            handleFromGameLevelingTypeIdOptions(val) {
                axios.post(this.gameLevelingTypesApi, {
                    'game_id' : this.form.game_region_server[2]
                }).then(res => {
                    this.gameLevelingTypeOptions = res.data;
                }).catch(err => {
                });
            },
            handleSubmitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.form.game_id = this.form.game_region_server[0];
                        this.form.game_region_id = this.form.game_region_server[1];
                        this.form.game_server_id = this.form.game_region_server[2];
                        this.form.day = this.form.day_hour[0];
                        this.form.hour = this.form.day_hour[1];
                        axios.post(this.orderCreateApi, this.form).then(res => {
                            this.$message({
                                'type': res.data.status == 1 ? 'success' : 'error',
                                'message': res.data.message,
                            });
                        }).catch(err => {
                            this.$message({
                               'type': 'error',
                               'message': '重新下单失败，服务器错误！',
                            });
                        });
                    }
                });
            },
            handleResetForm(formName) {
                this.$refs[formName].resetFields();
            },
            handleOrderTab(tab, event) {
                if (tab.name == 2) {

                }
                // 订单操作日志
                if (tab.name == 3) {
                    axios.post(this.orderLogApi, {trade_no:this.tradeNo}).then(res => {
                        this.logData = res.data;
                    });
                }
            },
            // 删除补款单号
            removeDomain(item) {
                let index = this.form.domains.indexOf(item);
                if (index !== -1) {
                    this.form.domains.splice(index, 1)
                }
            },
            // 添加补款单号
            addDomain() {
                this.form.domains.push({
                    value: '',
                    key: Date.now()
                });
            },
            handleDayHour() {
                for(let i = 0; i<=90; i++) {
                    let day = [];
                    for (let i = 0; i<=24; i++) {
                        day.push({
                            value:i,
                            label:i + '小时',
                        })
                    }
                    this.dayHourOptions.push({
                        value:i,
                        label:i + '天',
                        children:day,
                    })
                }
            }
        },
        created() {
            this.handleFromGameRegionServerOptions();
            this.handleFromData();
            this.handleDayHour();
        }
    }
</script>

<style lang="less">
    .el-col {
        border-radius: 4px;
    }
    .grid-content {
        border-radius: 4px;
        min-height: 36px;
    }
    .game-leveling-order-repeat .el-card__body {
        padding: 20px 20px 10px;
    }
    .game-leveling-order-repeat .el-card {
        border-radius: 0;
        border: 1px solid #ebeef5;
        background-color: #fff;
        overflow: hidden;
        color: #303133;
        -webkit-transition: none;
        transition: none;
    }
    .game-leveling-order-repeat .el-card__header {
        padding: 10px 20px;
    }
    .game-leveling-order-repeat .footer {
        height: 60px;background-color: #fff;
        position: fixed;bottom: 0;width:100%;
        /*box-shadow:inset 0px 15px 15px -15px rgba(0, 0, 0, 0.1);*/
        /*!*-webkit-box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);*!*/
        box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
    }
</style>