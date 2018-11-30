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
                                        <div class="text item">
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item
                                                            label="游戏/区/服"
                                                            prop="game_region_server">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-cascader
                                                                        @change="handleFromGameLevelingTypeIdOptions"
                                                                        :options="gameRegionServerOptions"
                                                                        v-model="form.game_region_server">
                                                                </el-cascader>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">

                                                    <el-form-item label="代练类型" prop="game_leveling_type_id">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
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
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>

                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="角色名称" prop="game_role">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        
                                                                        type="input"
                                                                        v-model.number="form.game_role"
                                                                        autocomplete="off"></el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>

                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="游戏账号" prop="game_account">

                                                        <el-row :gutter="10">
                                                            <el-col :span="22">

                                                                <el-input
                                                                        
                                                                        type="input"
                                                                        v-model="form.game_account"
                                                                        autocomplete="off"></el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>

                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="游戏密码" prop="game_password">

                                                        <el-row>
                                                            <el-col :span="22">
                                                                <el-input
                                                                        
                                                                        type="input"
                                                                        v-model="form.game_password"
                                                                        autocomplete="off"></el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>

                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                        </div>
                                    </el-card>

                                    <el-card class="box-card">
                                        <div class="text item">
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item
                                                            label="代练标题"
                                                            prop="title">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        
                                                                        type="age"
                                                                        v-model="form.title"
                                                                        autocomplete="off">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1">
                                                                <el-tooltip  placement="top">
                                                                    <div slot="content">多行信息<br/>第二行信息</div>
                                                                    <span class="icon-button">
                                                                        <i class="el-icon-question" ></i>
                                                                    </span>
                                                                </el-tooltip>
                                                            </el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>

                                                <el-col :span="12">
                                                    <el-form-item
                                                            label="接单密码"
                                                            prop="take_order_password">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        
                                                                        type="input"
                                                                        v-model="form.take_order_password"
                                                                        autocomplete="off"></el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="代练天/小时">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-row :gutter="10">
                                                                    <el-col :span="12">
                                                                        <el-select
                                                                                
                                                                                v-model="form.day"
                                                                                filterable
                                                                                placeholder="请选择">
                                                                            <el-option
                                                                                    v-for="item in dayOptions"
                                                                                    :key="item.value"
                                                                                    :label="item.label"
                                                                                    :value="item.value">
                                                                            </el-option>
                                                                        </el-select>
                                                                    </el-col>
                                                                    <el-col :span="12">
                                                                        <el-select
                                                                                
                                                                                v-model="form.hour"
                                                                                filterable
                                                                                placeholder="请选择">
                                                                            <el-option
                                                                                    v-for="item in hourOptions"
                                                                                    :key="item.value"
                                                                                    :label="item.label"
                                                                                    :value="item.value">
                                                                            </el-option>
                                                                        </el-select>
                                                                    </el-col>
                                                                </el-row>

                                                            </el-col>
                                                            <el-col :span="1">

                                                            </el-col>
                                                        </el-row>
                                                    </el-form-item>

                                                </el-col>
                                                <el-col :span="12">

                                                    <el-form-item label="代练要求模版">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-select
                                                                        @change="handleGameLevelingRequirementIdChange"
                                                                        v-model="form.gameLevelingRequirementId"
                                                                        placeholder="请选择">
                                                                    <el-option
                                                                            v-for="item in gameLevelingRequirementOptions"
                                                                            :key="item.id"
                                                                            :label="item.name"
                                                                            :value="item.id">
                                                                    </el-option>
                                                                </el-select>
                                                            </el-col>
                                                            <el-col :span="1">
                                                                <span class="icon-button" @click="handleGameLevelingRequirementVisible({visible:true})">
                                                                    <i class="el-icon-circle-plus" ></i>
                                                                </span>
                                                            </el-col>
                                                        </el-row>
                                                    </el-form-item>

                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="代练说明" prop="explain">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        
                                                                        type="textarea"
                                                                        :rows="3"
                                                                        placeholder="请输入内容"
                                                                        v-model="form.explain">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="代练要求" prop="requirement">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        type="textarea"
                                                                        :rows="3"
                                                                        placeholder="请输入内容"
                                                                        v-model="form.requirement">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="代练价格" prop="amount">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        
                                                                        type="input"
                                                                        placeholder="请输入内容"
                                                                        v-model="form.amount">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1" class="icon-button">

                                                            </el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="来源价格" prop="source_amount">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        
                                                                        type="input"
                                                                        placeholder="请输入内容"
                                                                        v-model="form.source_amount">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="安全保证金" prop="security_deposit">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">

                                                                <el-input
                                                                        
                                                                        type="input"
                                                                        placeholder="请输入内容"
                                                                        v-model="form.security_deposit">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="效率保证金" prop="efficiency_deposit">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        
                                                                        type="input"
                                                                        placeholder="请输入内容"
                                                                        v-model="form.efficiency_deposit">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="玩家电话" prop="player_phone">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        type="input"
                                                                        placeholder="请输入内容"
                                                                        v-model.number="form.player_phone">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <!---->
                                                <el-col :span="12">
                                                    <el-form-item label="商户QQ" prop="user_qq">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-select
                                                                        v-model="form.user_qq"
                                                                        placeholder="请选择">
                                                                    <el-option
                                                                            v-for="item in businessmanQQOptions"
                                                                            :key="item.id"
                                                                            :label="item.name + '-' + item.content"
                                                                            :value="item.content">
                                                                    </el-option>
                                                                </el-select>
                                                            </el-col>
                                                            <el-col :span="1">
                                                               <span class="icon-button" @click="handleBusinessmanQQVisible({visible:true})">
                                                                    <i class="el-icon-circle-plus" ></i>
                                                                </span>
                                                            </el-col>
                                                        </el-row>
                                                    </el-form-item>

                                                </el-col>
                                            </el-row>
                                        </div>
                                    </el-card>

                                    <el-card class="box-card">
                                        <div class="text item">
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="加价幅度" prop="price_increase_step">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        type="input"
                                                                        v-model="form.price_increase_step"
                                                                        autocomplete="off">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="加价上限" prop="price_ceiling">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        type="input"
                                                                        v-model="form.price_ceiling"
                                                                        autocomplete="off">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">

                                                </el-col>
                                                <el-col :span="12">

                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="客服备注">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        type="textarea"
                                                                        :rows="2"
                                                                        placeholder="请输入内容"
                                                                        v-model="form.remark">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
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

        <GameLevelingRequirement v-if="gameLevelingRequirementVisible"
                                 @handleGameLevelingRequirementVisible="handleGameLevelingRequirementVisible">
        </GameLevelingRequirement>

        <BusinessmanQQ v-if="businessmanQQVisible"
                       @handleBusinessmanQQVisible="handleBusinessmanQQVisible">
        </BusinessmanQQ>

    </div>
</template>

<script>
    import GameLevelingRequirement from './GameLevelingRequirement';
    import BusinessmanQQ from './BusinessmanQQ';

    export default {
        name: "GameLevelingRepeat",
        components: {
            GameLevelingRequirement,
            BusinessmanQQ,
        },
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
            var isPhone = (rule, value, callback) => {
                let phone=/^(13[012356789][0-9]{8}|15[012356789][0-9]{8}|18[02356789][0-9]{8}|147[0-9]{8}|1349[0-9]{7})$/;
                if (!phone.test(value)) {
                    callback(new Error('请填写正确的手机号！'));
                } else {
                    callback();
                }
            };
            return {
                tradeNo:this.$route.query.trade_no,
                gameLevelingRequirementVisible:false,
                gameLevelingRequirementOptions:[],
                businessmanQQVisible:false,
                businessmanQQOptions:[],
                dayOptions:[],
                hourOptions:[],
                gameRegionServerOptions: [], // 游戏/区/服 选项
                dayHourOptions:[],
                gameLevelingTypeOptions:[], // 游戏代练类型 选项
                form: {
                    trade_no: '',
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
                    day:0,
                    hour:1,
                    gameLevelingRequirementId:'',
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
                        { validator: isPhone, trigger: 'blur' }
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
            // 设置添加代练要求模版是否显示
            handleGameLevelingRequirementVisible(data) {
                this.gameLevelingRequirementVisible = data.visible;
                if (data.visible == false) {

                }
            },
            // 设置添加商户QQ是否显示
            handleBusinessmanQQVisible(data) {
                this.businessmanQQVisible = data.visible;
                if (data.visible == false) {

                }
            },
            handleFromData() {
                this.$api.gameLevelingOrderEdit({trade_no: this.tradeNo}).then(res => {
                        this.form.status = res.status;
                        this.form.channel_order_trade_no = res.channel_order_trade_no;
                        this.form.game_leveling_order_consult = res.game_leveling_order_consult;
                        this.form.game_leveling_order_complain = res.game_leveling_order_complain;
                        this.form.game_region_server =  [  // 选择的 游戏/区/服
                            res.game_id,
                            res.game_region_id,
                            res.game_server_id,
                        ];
                        this.handleFromGameLevelingTypeIdOptions();
                        this.form.day_hour = [   // 选择的代练天/小时
                            res.day,
                            res.hour,
                        ];
                        this.form.game_id = res.game_id; // 游戏ID
                        this.form.game_region_id =  res.game_region_id; // 游戏区ID
                        this.form.game_server_id =  res.game_server_id;// 游戏服务器ID
                        this.form.game_leveling_type_id =  res.game_leveling_type_id; // 代练类型ID
                        this.form.amount = res.amount; // 代练金额
                        this.form.source_amount = res.source_amount !== '0.00' ? res.source_amount : ''; // 来源价格
                        this.form.security_deposit = res.security_deposit; // 安全保证金
                        this.form.efficiency_deposit = res.efficiency_deposit; // 效率保证金
                        this.form.title = res.title; //代练标题
                        this.form.game_role = res.game_role; // 游戏角色
                        this.form.game_account = res.game_account; // 游戏账号
                        this.form.game_password = res.game_password; // 游戏密码
                        this.form.price_increase_step = res.price_increase_step !== '0.00' ? res.price_increase_step : ''; // 自动加价步长
                        this.form.price_ceiling = res.price_ceiling !== '0.00' ? res.price_ceiling : ''; // 自动加价上限
                        this.form.explain = res.game_leveling_order_detail.explain; // 代练说明
                        this.form.requirement = res.game_leveling_order_detail.requirement; // 代练要求
                        this.form.take_order_password = res.take_order_password; // 接单密码
                        this.form.player_phone = res.game_leveling_order_detail.player_phone; // 玩家电话
                        this.form.user_qq = res.game_leveling_order_detail.user_qq; // 商家qq
                        this.form.remark =  res.remark;
                        this.form.domains = [];

                        this.taobaoData = [
                            {
                                name: '店铺名',
                                value: res.taobao_data.seller_nick,
                            },
                            {
                                name: '天猫单号',
                                value: res.taobao_data.tid,
                            },
                            {
                                name: '订单状态',
                                value: res.taobao_data.trade_status,
                            },
                            {
                                name: '买家旺旺',
                                value: res.taobao_data.buyer_nick,
                            }, {
                                name: '购买单价',
                                value: res.taobao_data.price,
                            },
                            {
                                name: '购买数量',
                                value: res.taobao_data.num,
                            },
                            {
                                name: '实付金额',
                                value: res.taobao_data.payment,
                            },
                            {
                                name: '所在区/服',
                                value: res.taobao_data.region_server,
                            },
                            {
                                name: '角色名称',
                                value: res.taobao_data.role,
                            },
                            {
                                name: '买家留言',
                                value: res.taobao_data.buyer_message,
                            },
                            {
                                name: '下单时间',
                                value: res.taobao_data.created,
                            }
                        ];
                }).catch(err => {
                });
            },
            handleFromGameRegionServerOptions() {
                this.$api.gameRegionServer().then(res => {
                    this.gameRegionServerOptions = res.data;
                }).catch(err => {
                });
            },
            handleFromGameLevelingTypeIdOptions(val) {
                this.$api.gameLevelingTypes({
                    'game_id' : this.form.game_region_server[2]
                }).then(res => {
                    this.gameLevelingTypeOptions = res.data;
                }).catch(err => {
                });
                this.handleAutoChoseTemplate();
            },
            handleSubmitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.form.game_id = this.form.game_region_server[0];
                        this.form.game_region_id = this.form.game_region_server[1];
                        this.form.game_server_id = this.form.game_region_server[2];
                        this.$api.gameLevelingOrderCreate(this.form).then(res => {
                            this.$message({
                                'type': res.status == 1 ? 'success' : 'error',
                                'message': res.message,
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
            handleDayOption() {
                for (let i = 0; i<=90; i++) {
                    this.dayOptions.push({
                        value:i,
                        label:i + '天',
                    })
                }
            },
            handleHourOption() {
                for (let i = 0; i<=24; i++) {
                    this.hourOptions.push({
                        value:i,
                        label:i + '小时',
                    })
                }
            },
            // 商户QQ选项
            businessmanQQOption() {
                this.$api.businessmanContactTemplate().then(res => {
                    this.businessmanQQOptions = res.data;
                });
            },
            // 游戏代练要求选项
            gameLevelingRequirementOption() {
                this.$api.gameLevelingRequirementTemplate().then(res => {
                    this.gameLevelingRequirementOptions = res.data;
                });
            },
            // 选择游戏后自动选择对应的代练模板与联系QQ
            handleAutoChoseTemplate() {
                let vm = this;
                this.businessmanQQOptions.forEach(function (item) {
                    if (item.game_id === 0 && item.status === 1) {
                        vm.form.user_qq = item.content;
                    }
                    if (item.game_id === vm.form.game_region_server[0]) {
                        vm.form.user_qq = item.content;
                    }
                    if (item.game_id === vm.form.game_region_server[0] && item.status === 1) {
                        vm.form.user_qq = item.content;
                    }
                });
                this.gameLevelingRequirementOptions.forEach(function (item) {

                    if (item.game_id === 0 && item.status === 1) {
                        vm.form.gameLevelingRequirementId = item.id;
                    }
                    if (item.game_id === vm.form.game_region_server[0]) {
                        vm.form.gameLevelingRequirementId = item.id;
                    }
                    if (item.game_id === vm.form.game_region_server[0] && item.status === 1) {
                        vm.form.gameLevelingRequirementId = item.id;
                    }
                });
                this.handleGameLevelingRequirementIdChange();
            },
            handleGameLevelingRequirementIdChange() {
                let vm = this;
                this.gameLevelingRequirementOptions.forEach(function (item) {
                    if (item.id === vm.form.gameLevelingRequirementId) {
                        vm.form.requirement = item.content;
                        return false;
                    }
                });
            },
        },
        mounted() {
            this.handleFromData();
            this.businessmanQQOption();
            this.gameLevelingRequirementOption();
            this.handleFromGameRegionServerOptions();
            this.handleDayOption();
            this.handleHourOption();
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