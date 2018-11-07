<template>
    <div class="main content amount-flow">
        <el-tabs v-model="activeName" @tab-click="handleClick">
            <el-tab-pane label="自动加价设置" name="markup">
                <el-alert
                        style="margin-bottom: 15px"
                        title="操作提示: “自动加价”功能可以自动给“未接单”状态的订单增加代练费。"
                        type="success"
                        :closable="false">
                </el-alert>
                <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
                    <el-row :gutter="16">
                        <el-col :span="5">
                            <el-form-item>
                                <el-button
                                        type="primary"
                                        size="small"
                                        @click="markupAdd()">新增</el-button>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </el-form>
                <el-table
                        :data="tableData"
                        border
                        style="width: 100%">
                    <el-table-column
                            prop="markup_amount"
                            label="价格区间"
                            width="200">
                        <template slot-scope="scope">
                            0 < 发单价 <= {{ scope.row.markup_amount }}
                        </template>
                    </el-table-column>
                    <el-table-column
                            prop="markup_time"
                            label="加价开始时间"
                            width="200">
                    </el-table-column>
                    <el-table-column
                            prop="markup_type"
                            label="加价类型"
                            width="200">
                        <template slot-scope="scope">
                            {{ type[scope.row.markup_type] }}
                        </template>
                    </el-table-column>
                    <el-table-column
                            prop="markup_money"
                            label="增加金额"
                            width="200">
                    </el-table-column>
                    <el-table-column
                            prop="markup_frequency"
                            label="加价频率"
                            width="200">
                    </el-table-column>
                    <el-table-column
                            prop="markup_number"
                            label="加价次数限制"
                            width="200">
                    </el-table-column>
                    <el-table-column
                            label="操作"
                            width="">
                        <template slot-scope="scope">
                            <el-button
                                    type="primary"
                                    size="small"
                                    @click="markupUpdate(scope.row)">编辑</el-button>
                            <el-button
                                    type="primary"
                                    size="small"
                                    @click="markupDelete(scope.row.id)">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <el-pagination
                        style="margin-top: 25px"
                        @current-change="handleCurrentChange"
                        :current-page.sync="searchParams.page"
                        :page-size="15"
                        layout="prev, pager, next, jumper"
                        :total="TotalPage">
                </el-pagination>
                <el-dialog :title="title" :visible.sync="dialogFormVisible">
                    <el-form :model="form" ref="form" :rules="rules" label-width="80px">
                        <el-form-item label="发单价" prop="markup_amount" >
                            <el-row :gutter="10">
                                <el-col :span="20">
                                    <el-input v-model.number="form.markup_amount" autocomplete="off"></el-input>
                                </el-col>
                                <el-col :span="2">
                                    <el-tooltip class="item" effect="dark" content="请填写正整数值" placement="right-start">
                                        <i class="el-icon-question"></i>
                                    </el-tooltip>
                                </el-col>
                            </el-row>
                        </el-form-item>
                        <el-form-item label="加价开始时间(m)" prop="markup_time">
                            <el-row :gutter="10">
                                <el-col :span="20">
                                    <el-input v-model.number="form.markup_time" autocomplete="off"></el-input>
                                </el-col>
                                <el-col :span="2">
                                    <el-tooltip class="item" effect="dark" content="订单上架后第1次加价的时间，请填写正整数值，可以为0" placement="right-start">
                                        <i class="el-icon-question"></i>
                                    </el-tooltip>
                                </el-col>
                            </el-row>
                        </el-form-item>
                        <el-form-item label="加价类型" prop="markup_type">
                            <el-row :gutter="10">
                                <el-col :span="20">
                                    <el-radio v-model="form.markup_type" :label=0 autocomplete="off">绝对值</el-radio>
                                    <el-radio v-model="form.markup_type" :label=1 autocomplete="off">百分比</el-radio>
                                </el-col>
                                <el-col :span="2">
                                    <el-tooltip class="item" effect="dark" placement="right-start">
                                        <div slot="content">选择“绝对值”，则“增加值”中填写的值为增加的金额；选择“百分比”,
                                            <br/>则“增加值”中填写的值（百分数）乘以订单代练价格为增加的金额，所填写的值均为正整数或带2位小数</div>
                                        <i class="el-icon-question"></i>
                                    </el-tooltip>
                                </el-col>
                            </el-row>
                        </el-form-item>
                        <el-form-item label="增加金额" prop="markup_money">
                            <el-row :gutter="10">
                                <el-col :span="20">
                                    <el-input v-model.number="form.markup_money" autocomplete="off"></el-input>
                                </el-col>
                                <el-col :span="2">
                                    <el-tooltip class="item" effect="dark" content="请填写正整数值" placement="right-start">
                                        <i class="el-icon-question"></i>
                                    </el-tooltip>
                                </el-col>
                            </el-row>
                        </el-form-item>
                        <el-form-item label="加价频率" prop="markup_frequency">
                            <el-row :gutter="10">
                                <el-col :span="20">
                                    <el-input v-model.number="form.markup_frequency" autocomplete="off"></el-input>
                                </el-col>
                                <el-col :span="2">
                                    <el-tooltip class="item" effect="dark" content="请填写正整数值" placement="right-start">
                                        <i class="el-icon-question"></i>
                                    </el-tooltip>
                                </el-col>
                            </el-row>
                        </el-form-item>
                        <el-form-item label="加价次数限制" prop="markup_number">
                            <el-row :gutter="10">
                                <el-col :span="20">
                                    <el-input v-model.number="form.markup_number" autocomplete="off"></el-input>
                                </el-col>
                                <el-col :span="2">
                                    <el-tooltip class="item" effect="dark" content="填写0为无次数限制" placement="right-start">
                                        <i class="el-icon-question"></i>
                                    </el-tooltip>
                                </el-col>
                            </el-row>
                        </el-form-item>
                        <el-form-item>
                            <el-button v-if="isAdd" type="primary" @click="submitFormAdd('form')">确认添加</el-button>
                            <el-button v-if="isUpdate" type="primary" @click="submitFormUpdate('form')">确认修改</el-button>
                            <el-button @click="markupCancel('form')">取消</el-button>
                        </el-form-item>
                    </el-form>
                </el-dialog>
            </el-tab-pane>
            <el-tab-pane label="发布渠道设置" name="channel">
                <el-alert
                        style="margin-bottom: 15px"
                        title="操作提示：发布渠道设置可以控制发布的订单所能转单的平台，每种游戏至少选择一家平台。"
                        type="success"
                        :closable="false">
                </el-alert>
                <template>
                    <el-table
                            :data="tableDataChannel"
                            border
                            style="width: 100%">
                            <el-table-column
                                    prop="game"
                                    label="游戏"
                                    width="200">
                                <template slot-scope="scope">
                                    {{ scope.row.name }}
                                </template>
                            </el-table-column>
                            <el-table-column
                                    prop="channel"
                                    label="发布渠道"
                                    width="">
                                <template slot-scope="scope">
                                    <el-checkbox-group v-model="scope.row.hasModel" @change="switchChange(scope.row.id, scope.row.name, scope.row.hasModel)">
                                        <el-checkbox v-for="item in scope.row.allChannel" :key="item.id"  :label="item.id">{{ item.name }}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </el-table-column>
                    </el-table>
                </template>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
    export default {
        props: [
            'SettingMarkupDataListApi',
            'SettingMarkupAddApi',
            'SettingMarkupUpdateApi',
            'SettingMarkupDeleteApi',
            'SettingMarkupTimeApi',
            'SettingMarkupTimeApi',
            'SettingChannelDataListApi',
            'SettingChannelSwitchApi',
        ],
        methods: {
            // 新增按钮
            markupAdd() {
                this.dialogFormVisible = true;
                this.isAdd = true;
                this.isUpdate = false;
                this.title = "新增";
                this.$refs.form.resetFields();
            },
            // 编辑按钮
            markupUpdate(row) {
                this.dialogFormVisible = true;
                this.form = row;
                this.isAdd = false;
                this.title = "修改";
                this.isUpdate = true;
            },
            // 取消按钮
            markupCancel(formName) {
                this.dialogFormVisible = false;
            },
            handleClick(tab, event) {
            },
            // 添加
            submitFormAdd(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        axios.post(this.SettingMarkupAddApi, this.form).then(res => {
                            this.$message({
                                showClose: true,
                                type: res.data.status == 1 ? 'success' : 'error',
                                message: res.data.message
                            });
                            this.handleTableData();
                        }).catch(err => {
                            this.$alert('获取数据失败, 请重试!', '提示', {
                                confirmButtonText: '确定',
                                callback: action => {
                                }
                            });
                        });
                    } else {
                        return false;
                    }
                });
            },
            // 修改
            submitFormUpdate(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        axios.post(this.SettingMarkupUpdateApi, this.form).then(res => {
                            this.$message({
                                showClose: true,
                                type: res.data.status == 1 ? 'success' : 'error',
                                message: res.data.message
                            });
                            this.handleTableData();
                        }).catch(err => {
                            this.$alert('获取数据失败, 请重试!', '提示', {
                                confirmButtonText: '确定',
                                callback: action => {
                                }
                            });
                        });
                    } else {
                        return false;
                    }
                });
            },
            // 删除
            markupDelete(id) {
                axios.post(this.SettingMarkupDeleteApi, {id: id}).then(res => {
                    this.$message({
                        showClose: true,
                        type: res.data.status == 1 ? 'success' : 'error',
                        message: res.data.message
                    });
                    this.handleTableData();
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            // 加载数据
            handleTableData() {
                axios.post(this.SettingMarkupDataListApi, this.searchParams).then(res => {
                    this.tableData = res.data.data;
                    this.TotalPage = res.data.total;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            // 数据
            handleTableDataChannel(){
                axios.post(this.SettingChannelDataListApi).then(res => {
                    this.tableDataChannel = res.data;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            handleSearch() {
                this.handleTableData();
            },
            handleCurrentChange(page) {
                this.searchParams.page = page;
                this.handleTableData();
            },
            // 开关选择
            switchChange(gameId, gameName, platformIds){
                axios.post(this.SettingChannelSwitchApi, {game_id:gameId, game_name:gameName, thirds:platformIds}).then(res => {
                    this.$message({
                        showClose: true,
                        type: res.data.status == 1 ? 'success' : 'error',
                        message: res.data.message
                    });
                    this.handleTableData();
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            }
        },
        created () {
            this.handleTableData();
            this.handleTableDataChannel();
        },
        data() {
            let greaterZero = (rule, value, callback) => {
                if (parseInt(value) < 0) {
                    callback(new Error('不可填写负数!'));
                }
                callback();
            };
            return {
                // channelGroup:[1, 3, 4, 5],
                channelGroup:[],
                title:'新增',
                activeName:'markup',
                isAdd:true,
                isUpdate:false,
                dialogFormVisible:false,
                rules:{
                    markup_amount:[{ validator: greaterZero, trigger: 'blur' },{ required: true, message: '不能为空'}],
                    hour:[{ validator: greaterZero, trigger: 'blur' },{ required: true, message: '不能为空'}, { type: 'number', message:'必须为数字'}],
                    minute:[{ validator: greaterZero, trigger: 'blur' },{ required: true, message: '不能为空'}, { type: 'number', message:'必须为数字'}],
                    markup_type:[{ validator: greaterZero, trigger: 'blur' },{ required: true, message: '不能为空'}, { type: 'number', message:'必须为数字'}],
                    markup_money:[{ validator: greaterZero, trigger: 'blur' },{ required: true, message: '不能为空'}],
                    markup_frequency:[{ validator: greaterZero, trigger: 'blur' },{ required: true, message: '不能为空'}, { type: 'number', message:'必须为数字'}],
                    markup_number:[{ validator: greaterZero, trigger: 'blur' },{ required: true, message: '不能为空'}, { type: 'number', message:'必须为数字'}],
                },
                tableData: [],
                tableDataChannel:[],
                searchParams:{
                    page:1,
                },
                TotalPage:0,
                type:{
                    0:'绝对值',
                    1:'百分比'
                },
                form:{
                    id:'',
                    markup_amount:'',
                    markup_time:'',
                    markup_type:0,
                    markup_money:'',
                    markup_frequency:'',
                    markup_number:'',
                },
                channelForm:{
                    channel:''
                }
            }
        },
        mounted() {
            this.$cookieStore.setCookie('menu', '4');
            this.$cookieStore.setCookie('submenu', '4-4');
        },
    }
</script>