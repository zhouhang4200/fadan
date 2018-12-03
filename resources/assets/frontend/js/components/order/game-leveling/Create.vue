<template>
    <div class="game-leveling-order-create">
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

                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-cascader
                                                                        :filterable=true
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
                                                                <el-select v-model="form.game_leveling_type_id" placeholder="请选择">
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
                                                                <el-input type="input" v-model.number="form.game_role" autocomplete="off"></el-input>
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
                                                                <el-input type="input" v-model="form.game_account" autocomplete="off"></el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="游戏密码" prop="game_password">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input type="input" v-model="form.game_password" autocomplete="off"></el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
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
                                                                    <div slot="content">王者荣耀标题规范示例：黄金3（2星）-钻石1 （3星） 铭文：129</div>
                                                                    <span class="icon-button">
                                                                        <i class="el-icon-question" ></i>
                                                                    </span>
                                                                </el-tooltip>
                                                            </el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="接单密码" prop="take_order_password">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input type="input" v-model="form.take_order_password" autocomplete="off"></el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="代练天/小时" prop="day_hour">
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
                                                    <el-form-item  label="代练说明"  prop="explain">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        type="textarea"
                                                                        :rows="3"
                                                                        placeholder="请输入内容"
                                                                        v-model="form.explain">
                                                                    <el-button slot="append" icon="el-icon-search"></el-button>
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="代练要求"  prop="requirement">
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
                                                    <el-form-item label="代练价格"  prop="amount">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        type="input"
                                                                        placeholder="请输入内容"
                                                                        v-model.number="form.amount">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="来源价格"  prop="source_amount">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        type="input"
                                                                        placeholder="请输入内容"
                                                                        v-model.number="form.source_amount">
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
                                                                        :rows="2"
                                                                        placeholder="请输入内容"
                                                                        v-model.number="form.security_deposit">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1">
                                                                <el-tooltip  placement="top">
                                                                    <div slot="content">安全保证金是指对上家游戏账号安全进行保障时下家所需预先支付的保证形式的费用。<br/>当在代练过程中出现账号安全问题，即以双方协商或客服仲裁的部分或全部金额赔付给上家。<br/>（安全问题包括游戏内虚拟道具的安全，例如：符文、角色经验、胜点、负场经下家代练后不增反减、私自与号主联系、下家使用第三方软件带来的风险）</div>
                                                                    <span class="icon-button">
                                                                        <i class="el-icon-question" ></i>
                                                                    </span>
                                                                </el-tooltip>
                                                            </el-col>
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
                                                                        v-model.number="form.efficiency_deposit">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1">
                                                                <el-tooltip  placement="top">
                                                                    <div slot="content">效率保证金是指对上家的代练要求进行效率保障时下家所需预先支付的保证形式的费用。<br/>当下家未在规定时间内完成代练要求，即以双方协商或客服仲裁的部分或全部金额赔付给上家。<br/>（代练要求包括：下家在规定时间内没有完成上家的代练要求，接单4小时内没有上号，代练时间过四分之一但代练进度未达六分之一，下家原因退单，下家未及时上传代练截图）</div>
                                                                    <span class="icon-button">
                                                                        <i class="el-icon-question" ></i>
                                                                    </span>
                                                                </el-tooltip>
                                                            </el-col>
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
                                                <el-col :span="12">
                                                    <el-form-item label="商户QQ" prop="user_qq">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-select
                                                                        @change="handleBusinessmanQQIdChange"
                                                                        v-model.number="form.businessmanQQId"
                                                                        placeholder="请选择">
                                                                    <el-option
                                                                            v-for="item in businessmanQQOptions"
                                                                            :key="item.id"
                                                                            :label="item.name + '-' + item.content"
                                                                            :value="item.id">
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
                                        <div  class="text item">
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item label="加价幅度"  prop="price_increase_step">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        type="input"
                                                                        v-model.number="form.price_increase_step"
                                                                        autocomplete="off">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1">
                                                                <el-tooltip  placement="top">
                                                                    <div slot="content">设置后，若一小时仍无人接单，将自动补款所填金额，每小时补款一次</div>
                                                                    <span class="icon-button">
                                                                        <i class="el-icon-question" ></i>
                                                                    </span>
                                                                </el-tooltip>
                                                            </el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="加价上限"  prop="price_ceiling">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        type="input"
                                                                        v-model.number="form.price_ceiling"
                                                                        autocomplete="off">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1">
                                                                <el-tooltip  placement="top">
                                                                    <div slot="content">自动加价将不超过该价格</div>
                                                                    <span class="icon-button">
                                                                        <i class="el-icon-question" ></i>
                                                                    </span>
                                                                </el-tooltip>
                                                            </el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <!--<el-row>-->
                                                <!--<el-col :span="12">-->

                                                    <!--<el-form-item label="补款单号"  prop="take_order_password">-->
                                                        <!--<el-row :gutter="10">-->
                                                            <!--<el-col :span="22">-->
                                                                <!--<el-input-->
                                                                        <!--type="input"-->
                                                                        <!--v-model="form.take_order_password"-->
                                                                        <!--autocomplete="off">-->
                                                                <!--</el-input>-->
                                                            <!--</el-col>-->
                                                            <!--<el-col :span="1">-->
                                                                <!--<i class="el-icon-circle-plus"-->
                                                                   <!--@click.prevent="addDomain(domain)">-->
                                                                <!--</i>-->
                                                            <!--</el-col>-->
                                                        <!--</el-row>-->
                                                    <!--</el-form-item>-->

                                                    <!--<el-form-item-->
                                                            <!--v-for="(domain, index) in form.domains"-->
                                                            <!--:label="'补款单号' + (index +1)"-->
                                                            <!--:key="domain.key"-->
                                                            <!--:prop="'domains.' + index + '.value'"-->
                                                            <!--:rules="{required: true, message: '补款单号不能为空', trigger: 'blur'}">-->
                                                        <!--<el-row :gutter="10">-->
                                                            <!--<el-col :span="22">-->
                                                                <!--<el-input v-model="domain.value">-->
                                                                    <!--<el-button  slot="append" @click.prevent="removeDomain(domain)">删除</el-button>-->
                                                                <!--</el-input>-->
                                                            <!--</el-col>-->
                                                            <!--<el-col :span="1"></el-col>-->
                                                        <!--</el-row>-->

                                                    <!--</el-form-item>-->
                                                <!--</el-col>-->
                                                <!--<el-col :span="12">-->

                                                <!--</el-col>-->
                                            <!--</el-row>-->
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
                        <el-tabs v-model="dataTab">
                            <el-tab-pane label="淘宝数据" name="1">
                                <el-table
                                        :data="taobaoData"
                                        :show-header=false
                                        border
                                        style="width: 100%">
                                    <el-table-column
                                            prop="name"
                                            width="120">
                                    </el-table-column>
                                    <el-table-column
                                            prop="value">
                                        <template slot-scope="scope">
                                            <span v-html="scope.row.value"></span>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </el-tab-pane>
                            <el-tab-pane label="发单模板" name="2">
                                <el-input
                                        type="textarea"
                                        rows="20"
                                        v-model="orderTemplate" ></el-input>
                                <div style="margin-top: 15px">
                                    <el-button type="primary" >解析模板</el-button>
                                    <el-button @click="handleOrderTemplateGuide">使用说明</el-button>
                                </div>
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
                        <el-button type="primary" @click="handleSubmitForm('form')">确定下单</el-button>
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
        name: "GameLevelingCreate",
        components: {
            GameLevelingRequirement,
            BusinessmanQQ,
        },
        props:[
            'tid',
        ],
        data() {
            var isPhone = (rule, value, callback) => {
                let phone=/^(13[012356789][0-9]{8}|15[012356789][0-9]{8}|18[02356789][0-9]{8}|147[0-9]{8}|1349[0-9]{7})$/;
                if (!phone.test(value)) {
                    callback(new Error('请填写正确的手机号！'));
                } else {
                    callback();
                }
            };
            var mustOverZero = (rule, value, callback) => {
                let isNumber=/^([1-9]\d*|0)(\.\d{1,2})?$/;
                if (value && !isNumber.test(value)) {
                    callback(new Error('请输入大于0的数字值，支持2位小数!'));
                } else {
                    callback();
                }
            };
            var overZeroInt = (rule, value, callback) => {
                let isNumber=/^[1-9]\d*$/;
                if (value && !isNumber.test(value)) {
                    callback(new Error('请输入大于0的整数值!'));
                } else {
                    callback();
                }
            };
            return {
                gameLevelingRequirementVisible:false,
                gameLevelingRequirementOptions:[],
                businessmanQQVisible:false,
                businessmanQQOptions:[],
                gameRegionServerOptions: [], // 游戏/区/服 选项
                dayHourOptions: [],
                dayOptions:[],
                hourOptions:[],
                gameLevelingTypeOptions:[], // 游戏代练类型 选项
                form: {
                    game_region_server: [], // 选择的 游戏/区/服
                    day_hour:[], // 选择的代练天/小时
                    day:0, // 选择的代练天/小时
                    hour:1, // 选择的代练天/小时
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
                    user_qq:'', // 商家QQ
                    domains: [],
                    remark: '',
                    gameLevelingRequirementId:'',
                    businessmanQQId:'',
                },
                rules: {
                    game_leveling_type_id:[
                        { required: true, message: '请选择代练类型', trigger: 'change' },
                    ],
                    game_role:[
                        { required: true, message: '请输入游戏角色', trigger: 'blur' },
                    ],
                    game_account:[
                        { required: true, message: '请输入游戏账号', trigger: 'blur' },
                    ],
                    game_password:[
                        { required: true, message: '请输入游戏密码', trigger: 'blur' },
                    ],
                    title:[
                        { required: true, message: '请输入代练标题', trigger: 'blur' },
                        { min: 3, max: 35, message: '长度在 3 到 35 个字符', trigger: 'blur' }
                    ],
                    day_hour:[
                        // {type: 'array', required: true, message: '请选择代练天/小时',  trigger: 'change'},
                        {
                            validator:(rule, value, callback)=>{
                                if(this.form.day == 0 && this.form.hour == 0){
                                    callback(new Error("代练天数与小时不能都为0"));
                                }else{
                                    callback();
                                }
                            },
                            trigger:'change'
                        },
                    ],
                    // day_hour:[
                    //     {type: 'array', required: true, message: '请选择代练天/小时',  trigger: 'change'},
                    // ],
                    game_region_server:[
                        {type: 'array', required: true, message: '请选择游戏/区/服', trigger: 'change' },
                    ],
                    explain:[
                        { required: true, message: '请输入代练说明', trigger: 'blur' },
                    ],
                    requirement:[
                        { required: true, message: '请输入代练要求', trigger: 'blur' },
                    ],
                    amount: [
                        { required: true, message: '请输入代练价格', trigger: 'change' },
                        { validator: mustOverZero, trigger: 'blur' }
                    ],
                    source_amount: [
                        { validator: mustOverZero, trigger: 'blur' }
                    ],
                    efficiency_deposit: [
                        { required: true, message: '请输入效率保证金', trigger: 'change' },
                        { validator: mustOverZero, trigger: 'blur' }
                    ],
                    security_deposit: [
                        { required: true, message: '请输入安全保证金', trigger: 'change' },
                        { validator: mustOverZero, trigger: 'blur' }
                    ],
                    player_phone: [
                        { required: true, message: '请输入玩家电话', trigger: 'blur' },
                        { validator: isPhone, trigger: 'blur' }
                    ],
                    user_qq: [
                        { required: true, message: '请选择商户QQ', trigger: 'blur' },
                    ],
                    price_increase_step: [
                        { validator: overZeroInt, trigger: 'blur' }
                    ],
                    price_ceiling: [
                        { validator: mustOverZero, trigger: 'blur' }
                    ],

                },
                dataTab:"1",
                taobaoData: [],
                orderTemplate:'',
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
                               'message': '下单失败，服务器错误！',
                            });
                        });
                    }
                });
            },
            handleResetForm(formName) {
                this.$refs[formName].resetFields();
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
            // 模板使用说明
            handleOrderTemplateGuide() {
                this.$alert('1.选择“游戏”后会自动显示对应模板。<br>' +
                    '2.将模版复制，发给号主填写。<br>' +
                    '3.粘贴号主填写好的模版，粘贴至模板输入框内。<br>' +
                    '4.点击“解析模板”按钮将资料导入至左侧表格内，点击“发布”按钮，即可创建订单。', '使用说明', {
                    confirmButtonText: '确定',
                    dangerouslyUseHTMLString: true,
                });
            },
            handelTaobaoData() {
                this.$api.taobaoOrderShow({tid:this.tid}).then(res => {
                   this.taobaoData = [
                       {
                           name: '店铺名',
                           value: res.data.seller_nick,
                       },
                       {
                           name: '天猫单号',
                           value: res.data.tid,
                       },
                       {
                           name: '订单状态',
                           value: res.data.trade_status,
                       },
                       {
                           name: '买家旺旺',
                           value: res.data.buyer_nick,
                       }, {
                           name: '购买单价',
                           value: res.data.price,
                       },
                       {
                           name: '购买数量',
                           value: res.data.num,
                       },
                       {
                           name: '实付金额',
                           value: res.data.payment,
                       },
                       {
                           name: '所在区/服',
                           value: res.data.region_server,
                       },
                       {
                           name: '角色名称',
                           value: res.data.role,
                       },
                       {
                           name: '买家留言',
                           value: res.data.buyer_message,
                       },
                       {
                           name: '下单时间',
                           value: res.data.created,
                       }
                   ];
                }).catch(err => {
                    
                });
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
            handleAutoChoseTemplate() {
                let vm = this;
                this.businessmanQQOptions.forEach(function (item) {
                    if (item.game_id === 0 && item.status === 1) {
                        vm.form.businessmanQQId = item.id;
                    }
                    if (item.game_id === vm.form.game_region_server[0]) {
                        vm.form.businessmanQQId = item.id;
                    }
                    if (item.game_id === vm.form.game_region_server[0] && item.status === 1) {
                        vm.form.businessmanQQId = item.id;
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
                this.handleBusinessmanQQIdChange();
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
            handleBusinessmanQQIdChange() {
                let vm = this;
                this.businessmanQQOptions.forEach(function (item) {
                    if (item.id === vm.form.businessmanQQId) {
                        vm.form.user_qq = item.content;
                        return false;
                    }
                });
            }
        },
        created() {
            this.handleFromGameRegionServerOptions();
            this.handelTaobaoData();
            this.handleDayOption();
            this.handleHourOption();
            this.businessmanQQOption();
            this.gameLevelingRequirementOption();
        },
    }
</script>

<style lang="less">
    .el-col {
        border-radius: 4px;
    }
    .bg-purple-dark {
        background: #99a9bf;
    }
    .bg-purple {
        background: #d3dce6;
    }
    .bg-purple-light {
        background: #e5e9f2;
    }
    .grid-content {
        border-radius: 4px;
        min-height: 36px;
    }
    .game-leveling-order-create .el-card__body {
        padding: 20px 20px 10px;
    }
    .game-leveling-order-create .el-card {
        border-radius: 0;
        border: 1px solid #ebeef5;
        background-color: #fff;
        overflow: hidden;
        color: #303133;
        -webkit-transition: none;
        transition: none;
    }
    .game-leveling-order-create .el-card__header {
        padding: 10px 20px;
    }
    .game-leveling-order-create .footer {
        height: 60px;background-color: #fff;
        position: fixed;bottom: 0;width:100%;
        /*box-shadow:inset 0px 15px 15px -15px rgba(0, 0, 0, 0.1);*/
        /*!*-webkit-box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);*!*/
        box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
    }
</style>