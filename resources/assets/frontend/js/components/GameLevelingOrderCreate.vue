<template>
    <div class="game-leveling-order-create">
        <div class="main">
            <el-row :gutter="10">
                <el-form ref="form" :model="form" label-width="100px">
                    <el-col :span="16">
                        <div class="grid-content bg-purple">
                            <el-card class="box-card">
                                <div slot="header" class="clearfix">
                                    <span>订单信息</span>
                                </div>
                                <div  class="text item">
                                    <el-row>
                                        <el-col :span="12">
                                            <el-form-item  label="游戏/区/服" prop="gameRegionServer">
                                                <el-cascader
                                                        :options="form.gameRegionServerOptions"
                                                        v-model="form.gameRegionServer"
                                                ></el-cascader>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">
                                            <el-form-item label="代练类型" prop="game_leveling_type_id">
                                                <el-select v-model="form.game_leveling_type_id" placeholder="请选择">
                                                    <el-option
                                                            v-for="item in options"
                                                            :key="item.value"
                                                            :label="item.label"
                                                            :value="item.value">
                                                    </el-option>
                                                </el-select>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                    <el-row>
                                        <el-col :span="12">
                                            <el-form-item label="角色名称" prop="game_role">
                                                <el-input type="input" v-model.number="form.game_role" autocomplete="off"></el-input>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                    <el-row>
                                        <el-col :span="12">
                                            <el-form-item label="游戏账号" prop="game_account">
                                                <el-input type="input" v-model="form.game_account" autocomplete="off"></el-input>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">
                                            <el-form-item label="游戏密码" prop="game_password">
                                                <el-input type="input" v-model="form.game_password" autocomplete="off"></el-input>
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
                                                <el-input type="age" v-model="form.title" autocomplete="off"></el-input>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">

                                        </el-col>
                                    </el-row>
                                    <el-row>
                                        <el-col :span="12">
                                            <el-form-item label="代练天/小时" prop="gameRegionServer">
                                                <el-cascader
                                                        :options="form.gameRegionServerOptions"
                                                        v-model="form.gameRegionServer"
                                                ></el-cascader>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">
                                            <el-form-item label="代练要求模版"  prop="requirement">
                                                <el-select v-model="form.requirement" placeholder="请选择">
                                                    <el-option
                                                            v-for="item in options"
                                                            :key="item.value"
                                                            :label="item.label"
                                                            :value="item.value">
                                                    </el-option>
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
                                                        :rows="2"
                                                        placeholder="请输入内容"
                                                        v-model="form.source_amount">
                                                </el-input>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                    <el-row>
                                        <el-col :span="12">
                                            <el-form-item label="安全保证金">
                                                <el-input
                                                        type="input"
                                                        :rows="2"
                                                        placeholder="请输入内容"
                                                        v-model="form">
                                                </el-input>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">
                                            <el-form-item label="效率保证金">
                                                <el-input
                                                        type="input"
                                                        placeholder="请输入内容"
                                                        v-model="textarea">
                                                </el-input>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                    <el-row>
                                        <el-col :span="12">
                                            <el-form-item label="玩家电话" prop="player_phone">
                                                <el-input
                                                        type="input"
                                                        :rows="2"
                                                        placeholder="请输入内容"
                                                        v-model="form.player_phone">
                                                </el-input>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">
                                            <el-form-item label="商户QQ" prop="player_phone">
                                                <el-input
                                                        type="input"
                                                        placeholder="请输入内容"
                                                        v-model="textarea">
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
                                                        type="age"
                                                        v-model="form.price_increase_step"
                                                        autocomplete="off">
                                                </el-input>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">
                                            <el-form-item label="加价上限"  prop="price_ceiling">
                                                <el-input
                                                        type="age"
                                                        v-model="form.price_ceiling"
                                                        autocomplete="off">
                                                </el-input>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                    <el-row>
                                        <el-col :span="12">
                                            <el-form-item label="接单密码"  prop="take_order_password">
                                                <el-input
                                                        type="input"
                                                        v-model="form.take_order_password"
                                                        autocomplete="off">
                                                </el-input>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">
                                            <el-form-item label="代练类型"  prop="game_leveling_type_id">
                                                <el-select v-model="form.game_leveling_type_id" placeholder="请选择">
                                                    <el-option
                                                            v-for="item in options"
                                                            :key="item.value"
                                                            :label="item.label"
                                                            :value="item.value">
                                                    </el-option>
                                                </el-select>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                    <el-row>
                                        <el-col :span="12">
                                            <el-form-item label="代练说明">
                                                <el-input
                                                        type="textarea"
                                                        :rows="2"
                                                        placeholder="请输入内容"
                                                        v-model="textarea">
                                                </el-input>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">
                                            <el-form-item label="代练要求">
                                                <el-input
                                                        type="textarea"
                                                        :rows="2"
                                                        placeholder="请输入内容"
                                                        v-model="form.requirement">
                                                </el-input>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                </div>
                            </el-card>
                        </div>
                    </el-col>
                </el-form>
                <el-col :span="8">
                    <div class="grid-content bg-purple">
                        <el-card class="box-card">
                            <div slot="header" class="clearfix">
                                <span>淘宝数据</span>
                            </div>
                            <el-table
                                    :data="tableData"
                                    border
                                    style="width: 100%">
                                <el-table-column
                                        prop="date"
                                        label="日期"
                                        width="180">
                                </el-table-column>
                                <el-table-column
                                        prop="name"
                                        label="姓名"
                                        width="180">
                                </el-table-column>
                                <el-table-column
                                        prop="address"
                                        label="地址">
                                </el-table-column>
                            </el-table>
                        </el-card>
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
    </div>
</template>

<script>
    export default {
        name: "GameLevelingCreate",
        props:[
            'gameRegionServerApi',
        ],
        data() {
            return {
                form: {
                    age:0,
                    gameRegionServerOptions: [], // 游戏/区/服数据
                    gameRegionServer: [],
                    dayHour:[], // 代练天小时
                    game_id: 0, // 游戏ID
                    region_id: 0, // 游戏区ID
                    service_id: 0, // 游戏服务器ID
                    game_leveling_type_id: 0, // 代练类型ID
                    amount:'', // 代练金额
                    source_amount:'', // 安全保证金
                    efficiency_deposit:'', // 效率保证金
                    title:'', //代练标题
                    game_role:'', // 游戏角色
                    game_account:'', // 游戏账叼
                    game_password:'', // 游戏密码
                    price_increase_step:'', // 自动加价步长
                    price_ceiling:'', // 自动加价上限
                    explain:'', // 代练说明
                    requirement:'', // 代练要求
                    take_order_password:'', // 接单密码
                    player_phone:'', // 玩家电话
                },
                rules: {
                    player_phone: [
                        { required: true, message: '请输入活动名称', trigger: 'blur' },
                        { min: 3, max: 5, message: '长度在 3 到 5 个字符', trigger: 'blur' }
                    ],
                    region: [
                        { required: true, message: '请选择活动区域', trigger: 'change' }
                    ],
                    date1: [
                        { type: 'date', required: true, message: '请选择日期', trigger: 'change' }
                    ],
                    date2: [
                        { type: 'date', required: true, message: '请选择时间', trigger: 'change' }
                    ],
                    type: [
                        { type: 'array', required: true, message: '请至少选择一个活动性质', trigger: 'change' }
                    ],
                    resource: [
                        { required: true, message: '请选择活动资源', trigger: 'change' }
                    ],
                    desc: [
                        { required: true, message: '请填写活动形式', trigger: 'blur' }
                    ]
                },
                tableData: [{
                    date: '2016-05-02',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1518 弄'
                }, {
                    date: '2016-05-04',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1517 弄'
                }, {
                    date: '2016-05-01',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1519 弄'
                }, {
                    date: '2016-05-03',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1516 弄'
                }]
            };
        },
        methods: {
            handleFromGameRegionServerOptions() {
                axios.post(this.gameRegionServerApi).then(res => {
                    this.form.gameRegionServerOptions = res.data;
                }).catch(err => {
                });
            },
            handleSubmitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        alert('submit!');
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            handleResetForm(formName) {
                this.$refs[formName].resetFields();
            }
        },
        created() {
            this.handleFromGameRegionServerOptions();
        }
    }
</script>

<style>
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