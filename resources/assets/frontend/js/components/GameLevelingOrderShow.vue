<template>
    <div class="game-leveling-order-create">
        <div class="main">
            <el-row :gutter="10">
                <el-form ref="form"  :rules="rules" :model="form" label-width="120px">
                    <el-col :span="16" :style="{'margin-bottom': displayFooter ? '60px' : '15px'}">
                        <div class="grid-content bg-purple" style="padding: 15px;background-color: #fff" >
                            <el-tabs v-model="orderTab" @tab-click="handleOrderTab">
                                <el-tab-pane label="订单信息" name="1">

                                    <el-card class="box-card">
                                        <div  class="text item">
                                            <el-row >
                                                <el-col :span="12">
                                                    <el-form-item
                                                            label="游戏/区/服"
                                                            prop="game_region_server">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-cascader
                                                                        :disabled="fieldDisabled"
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
                                                                        :disabled="fieldDisabled"
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
                                            <el-row >
                                                <el-col :span="12">
                                                    <el-form-item label="角色名称" prop="game_role">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        :disabled="fieldDisabled"
                                                                        type="input"
                                                                        v-model.number="form.game_role"
                                                                        autocomplete="off"></el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>

                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                            <el-row >
                                                <el-col :span="12">
                                                    <el-form-item label="游戏账号" prop="game_account">

                                                        <el-row :gutter="10">
                                                            <el-col :span="22">

                                                                <el-input
                                                                        :disabled="fieldDisabled"
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
                                                                        :disabled="fieldDisabled"
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
                                        <div  class="text item">
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item
                                                            label="代练标题"
                                                            prop="title">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        :disabled="fieldDisabled"
                                                                        type="input"
                                                                        v-model="form.title"
                                                                        autocomplete="off">
                                                                    <el-tooltip slot="append" placement="top">
                                                                        <div slot="content">多行信息<br/>第二行信息</div>
                                                                        <el-button><i class="el-icon-question"></i></el-button>
                                                                    </el-tooltip>
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
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
                                                                        :disabled="fieldDisabled"
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
                                                    <el-form-item label="代练天/小时" prop="day_hour">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-cascader
                                                                        :disabled="fieldDisabled"
                                                                        :options="dayHourOptions"
                                                                        v-model="form.day_hour">
                                                                </el-cascader>
                                                            </el-col>
                                                            <el-col :span="1" class="icon-button">
                                                                <i class="el-icon-circle-plus-outline"
                                                                   @click.prevent="handleAddDayHour()">
                                                                </i>
                                                            </el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="代练要求模版">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-select
                                                                        :disabled="fieldDisabled"
                                                                        v-model="form"
                                                                        placeholder="请选择">
                                                                </el-select>
                                                            </el-col>
                                                            <el-col :span="1" class="icon-button">
                                                                <i class="el-icon-circle-plus-outline"></i>
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
                                                                        :disabled="fieldDisabled"
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
                                                    <el-form-item label="代练要求"  prop="requirement">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        :disabled="fieldDisabled"
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
                                                                        :disabled="fieldDisabled"
                                                                        type="input"
                                                                        placeholder="请输入内容"
                                                                        v-model="form.amount">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1" class="icon-button">
                                                                <i class="el-icon-circle-plus-outline"
                                                                   @click.prevent="handleAddAmount()">
                                                                </i>
                                                            </el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="来源价格"  prop="source_amount">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        :disabled="fieldDisabled"
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
                                                                        :disabled="fieldDisabled"
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
                                                                        :disabled="fieldDisabled"
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
                                                                        :disabled="fieldDisabled"
                                                                        type="input"
                                                                        placeholder="请输入内容"
                                                                        v-model="form.player_phone">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <!--:disabled="fieldDisabled"-->
                                                <el-col :span="12">
                                                    <el-form-item label="商户QQ" prop="user_qq">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">

                                                                <el-input
                                                                        :disabled="fieldDisabled"
                                                                        type="input"
                                                                        placeholder="请输入内容"
                                                                        v-model="form.user_qq">
                                                                </el-input>
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
                                                    <el-form-item label="加价幅度"  prop="price_increase_step">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        :disabled="fieldDisabled"
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
                                                    <el-form-item label="加价上限"  prop="price_ceiling">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        :disabled="fieldDisabled"
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

                                                    <el-form-item label="补款单号"  prop="take_order_password">

                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        type="input"
                                                                        v-model="form.take_order_password"
                                                                        autocomplete="off">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1" class="icon-button">
                                                                <i class="el-icon-circle-plus-outline"
                                                                   @click.prevent="addDomain(domain)">
                                                                </i>
                                                            </el-col>
                                                        </el-row>

                                                    </el-form-item>

                                                    <el-form-item
                                                            v-for="(domain, index) in form.domains"
                                                            :label="'补款单号' + (index +1)"
                                                            :key="domain.key"
                                                            :prop="'domains.' + index + '.value'"
                                                            :rules="{required: true, message: '补款单号不能为空', trigger: 'blur'}">
                                                            <el-row :gutter="10">
                                                                <el-col :span="22">
                                                                    <el-input v-model="domain.value"></el-input>
                                                                </el-col>
                                                                <el-col :span="1" class="icon-button">
                                                                    <i class="el-icon-remove-outline"
                                                                       @click.prevent="removeDomain(domain)">
                                                                    </i>
                                                                </el-col>
                                                            </el-row>
                                                    </el-form-item>
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
                                <el-tab-pane label="仲裁证据" name="2">

                                </el-tab-pane>
                                <el-tab-pane label="操作记录" name="3">

                                    <el-table
                                            :data="logData"
                                            border
                                            style="width: 100%;margin-bottom: 15px">
                                        <el-table-column
                                                prop="username"
                                                label="操作人"
                                                width="180">
                                        </el-table-column>
                                        <el-table-column
                                                prop="name"
                                                label="操作名"
                                                width="180">
                                        </el-table-column>
                                        <el-table-column
                                                prop="description"
                                                label="描述">
                                        </el-table-column>
                                        <el-table-column
                                                prop="created_at"
                                                label="时间">
                                        </el-table-column>
                                    </el-table>
                                </el-tab-pane>
                            </el-tabs>
                        </div>
                    </el-col>
                </el-form>
                <el-col :span="8">
                    <div class="grid-content bg-purple" style="padding: 15px;background-color: #fff" >
                        <el-tabs v-model="dataTab">
                            <el-tab-pane label="平台数据" name="1">
                                <el-table
                                            :data="platformData"
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
                            <el-tab-pane label="淘宝数据" name="2">
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
        <div class="footer" v-if="displayFooter">
            <el-row>
                <el-col :span="16">
                    <div style="text-align: center;line-height: 60px;">

                        <el-button v-if="(form.status == 1 || form.status == 22)"
                                   type="primary"
                                   @click="handleSubmitForm('form')"
                                   style="margin-right: 8px">确认修改</el-button>

                        <!--未接单 1 -->
                        <span v-if="form.status == 1">
                            <el-button
                                    size="small"
                                    @click="handleDelete()">撤单</el-button>
                            <el-button
                                    size="small"
                                    type="primary" @click="handleOffSale()">下架</el-button>
                        </span>

                        <!--代练中 13 -->
                        <span v-if="form.status == 13">
                            <el-button
                                    size="small"
                                    @click="handleApplyConsult()">撤销</el-button>
                            <el-button
                                    size="small"
                                    type="primary" @click="handleApplyComplain()">仲裁</el-button>
                        </span>

                        <!--待验收 14 -->
                        <span v-if="form.status == 14">
                            <el-button
                                    size="small"
                                    @click="handleComplete()">完成</el-button>
                            <el-button
                                    size="small"
                                    @click="handleApplyConsult()">撤销</el-button>
                            <el-button
                                    size="small"
                                    type="primary" @click="handleApplyComplain()">仲裁</el-button>
                            <el-button
                                    size="small"
                                    @click="handleLock()">锁定</el-button>
                        </span>

                        <!--撤销中 15 -->
                        <span v-if="form.status == 15">
                            <el-button
                                    v-if="(this.form.game_leveling_order_consult.initiator == 1 && this.form.game_leveling_order_consult.status == 1)"
                                    size="small"
                                    @click="handleCancelConsult()">取消撤销</el-button>
                            <el-button
                                    v-if="(this.form.game_leveling_order_consult.initiator == 2 && this.form.game_leveling_order_consult.status == 1)"
                                    size="small"
                                    @click="handleAgreeConsult()">同意撤销</el-button>
                            <el-button
                                    size="small"
                                    type="primary" @click="handleApplyComplain()">仲裁</el-button>
                        </span>

                        <!--仲裁中 16 -->
                        <span v-if="form.status == 16">
                            <el-button
                                    size="small"
                                    type="primary" @click="handleCancelComplain()">取消仲裁</el-button>
                        </span>

                        <!--异常 17 -->
                        <span v-if="form.status == 17">
                            <el-button
                                    size="small"
                                    type="primary" @click="handleApplyConsult()">撤销</el-button>
                            <el-button
                                    size="small"
                                    @click="handleLock()">锁定</el-button>
                        </span>

                        <!--已锁定 18 -->
                        <span v-if="form.status == 18">
                            <el-button
                                    size="small"
                                    @click="handleCancelLock()">取消锁定</el-button>
                            <el-button
                                    size="small"
                                    type="primary"
                                    @click="handleApplyConsult()">撤销</el-button>
                        </span>

                        <!--已下架 22 -->
                        <span v-if="form.status == 22">
                            <el-button
                                    size="small"
                                    type="primary"
                                    @click="handleOnSale()">上架</el-button>
                        </span>

                        <!-- 19, 20, 21, 22, 23, 24 -->
                        <span v-if="([19, 20, 21, 23, 24].indexOf(form.status)) != -1">
                            <el-button
                                    size="small"
                                    type="primary"
                                    @click="handleRepeatOrder()">重发</el-button>
                        </span>

                    </div>
                </el-col>
            </el-row>
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

<script>
    import ApplyComplain from './ApplyComplain';
    import ApplyConsult from './ApplyConsult';
    export default {
        name: "GameLevelingShow",
        components: {
            ApplyComplain,
            ApplyConsult,
        },
        props:[
            'tradeNo',
            'orderEditApi',
            'orderUpdateApi',
            'orderRepeatApi',
            'orderLogApi',
            'orderAddAmountApi',
            'orderAddDayHourApi',
            'gameRegionServerApi',
            'gameLevelingTypesApi',
            'deleteApi',
            'onSaleApi',
            'offSaleApi',
            'applyConsultApi',
            'applyComplainApi',
            'cancelComplainApi',
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
            fieldDisabled() {
                if (this.form.status == 1 || this.form.status == 22) {
                    return  false;
                } else {
                    return true;
                }
            },
            displayFooter() {
                if (this.orderTab == "1") {
                    return true;
                } else {
                    return false;
                }
            }
        },
        data() {
            return {
                amount:0,
                securityDeposit:0,
                efficiencyDeposit:0,
                applyConsultVisible:false,
                applyComplainVisible:false,
                orderTab:"1",
                dataTab:"1",
                gameRegionServerOptions: [], // 游戏/区/服 选项
                dayHourOptions: [  // 天数/小时  选项
                    {
                        value: 1,
                        label: '1天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 2,
                        label: '2天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 3,
                        label: '3天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 4,
                        label: '4天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 5,
                        label: '5天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 6,
                        label: '6天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 7,
                        label: '7天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 8,
                        label: '8天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 9,
                        label: '9天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 10,
                        label: '10天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 11,
                        label: '11天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 12,
                        label: '12天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 13,
                        label: '13天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 14,
                        label: '14天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 15,
                        label: '15天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 16,
                        label: '16天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 17,
                        label: '17天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 18,
                        label: '18天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 19,
                        label: '19天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 20,
                        label: '1天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 21,
                        label: '21天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 22,
                        label: '22天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 23,
                        label: '23天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 24,
                        label: '24天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 25,
                        label: '25天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 26,
                        label: '26天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 27,
                        label: '27天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 28,
                        label: '28天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 29,
                        label: '29天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                    {
                        value: 30,
                        label: '30天',
                        children: [
                            {
                                value: 0,
                                label: '0小时',
                            },
                            {
                                value: 1,
                                label: '1小时',
                            },
                            {
                                value: 2,
                                label: '2小时',
                            },
                            {
                                value: 3,
                                label: '4小时',
                            },
                            {
                                value: 4,
                                label: '4小时',
                            },
                            {
                                value: 5,
                                label: '5小时',
                            },
                            {
                                value: 6,
                                label: '6小时',
                            }, {
                                value: 7,
                                label: '7小时',
                            },
                            {
                                value: 8,
                                label: '8小时',
                            },
                            {
                                value: 9,
                                label: '9小时',
                            },
                            {
                                value: 10,
                                label: '10小时',
                            }, {
                                value: 11,
                                label: '11小时',
                            },
                            {
                                value: 12,
                                label: '12小时',
                            }, {
                                value: 13,
                                label: '13小时',
                            }, {
                                value: 14,
                                label: '14小时',
                            },
                            {
                                value: 15,
                                label: '15小时',
                            }, {
                                value: 16,
                                label: '16小时',
                            },
                            {
                                value: 17,
                                label: '17小时',
                            },
                            {
                                value: 18,
                                label: '18小时',
                            },
                            {
                                value: 19,
                                label: '18小时',
                            },
                            {
                                value: 20,
                                label: '20小时',
                            }, {
                                value: 21,
                                label: '21小时',
                            },
                            {
                                value: 22,
                                label: '22小时',
                            },
                            {
                                value: 23,
                                label: '23小时',
                            },
                            {
                                value: 24,
                                label: '24小时',
                            },
                        ]
                    },
                ],
                gameLevelingTypeOptions:[], // 游戏代练类型 选项
                addDay:0, // 增加的天数
                addHour:0, // 增加的小时
                form: {
                    trade_no:this.tradeNo,
                    status:0,
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
                status:{
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
                platform:{
                    5:'丸子代练',
                    1:'91代练',
                    3:'蚂蚁代练',
                },
                platformData: [],
                taobaoData:[],
                logData:[]
            };
        },
        methods: {
            handleFromStatus() {
                return (this.form.status == 1 || this.form.status == 22) ? false : false;
            },
            handleFromData() {
                axios.post(this.orderEditApi, {trade_no: this.tradeNo}).then(res => {
                        this.amount = res.data.amount;
                        this.securityDeposit = res.data.security_deposit;
                        this.efficiencyDeposit = res.data.efficiency_deposit;
                        this.form.status = res.data.status;
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
                        // 平台数据
                        this.platformData = [
                            {
                                name: '平台单号',
                                value: res.data.trade_no,
                            },
                            {
                                name: '订单状态',
                                value: this.status[res.data.status]
                            },
                            {
                                name: '接单平台',
                                value: this.platform[res.data.platform_id]
                            },
                            {
                                name: '打手呢称',
                                value: res.data.game_leveling_order_detail.hatchet_man_name
                            },
                            {
                                name: '打手电话',
                                value: res.data.game_leveling_order_detail.hatchet_man_phone
                            },
                            {
                                name: '打手QQ',
                                value: res.data.game_leveling_order_detail.hatchet_man_qq
                            },
                            {
                                name: '剩余代练时间',
                                value: res.data.left_time
                            },
                            {
                                name: '发布时间',
                                value: res.data.created_at
                            },
                            {
                                name: '接单时间',
                                value: res.data.take_at
                            },
                            {
                                name: '提验时间',
                                value: res.data.apply_complete_at
                            },
                            {
                                name: '结算时间',
                                value: res.data.complete_at
                            },
                            {
                                name: '发单客服',
                                value: res.data.game_leveling_order_detail.username
                            },
                            {
                                name: '撤销说明',
                                value: res.data.consult_describe
                            },
                            {
                                name: '仲裁说明',
                                value: res.data.complain_describe
                            },
                            {
                                name: '支付代练费用',
                                value: res.data.pay_amount
                            },
                            {
                                name: '获得赔偿金额',
                                value: res.data.get_amount
                            },
                            {
                                name: '手续费',
                                value: res.data.get_poundage
                            },
                            {
                                name: '最终支付金额',
                                value: res.data.complain_amount
                            },
                        ];
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
                        axios.post(this.orderUpdateApi, this.form).then(res => {
                            this.$message({
                                'type': res.data.status == 1 ? 'success' : 'error',
                                'message': res.data.message,
                            });
                        }).catch(err => {
                            this.$message({
                               'type': 'error',
                               'message': '修改订单失败，服务器错误！',
                            });
                        });
                    }
                });
            },
            handleResetForm(formName) {
                this.$refs[formName].resetFields();
            },
            // 增加代练价格
            handleAddAmount() {
                this.$prompt('请输入需要加增加的价格', '增加代练价格', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    inputPattern: /^[0-9]+.?[0-9]*$/,
                    inputErrorMessage: '代练价格只能为数字'
                }).then(({ value }) => {
                    // 发送加价请求 value 为写入的值
                    axios.post(this.orderAddAmountApi, {
                        trade_no:this.form.trade_no,
                        amount:value
                    }).then(res => {
                        this.$message({
                            'type': res.data.status == 1 ? 'success' : 'error',
                            'message': res.data.message,
                        });
                    }).catch(err => {
                        this.$message({
                            'type': 'error',
                            'message': '加价失败，服务器错误！',
                        });
                    });
                });
            },
            // 增加天数与小时
            handleAddDayHour() {
                const h = this.$createElement;
                const currentThis = this;
                this.$msgbox({
                    title: '选择增加的天数与小时',
                    message: h('div', null, [
                        h('el-cascader', {
                            props: {
                                options:this.dayHourOptions,
                            },
                            on:{
                                'change' (addDayHour) {
                                    currentThis.addDay = addDayHour[0];
                                    currentThis.addHour = addDayHour[1];
                                }
                            }
                        }, ''),
                    ]),
                    showCancelButton: true,
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    beforeClose: (action, instance, done) => {
                        if (action == 'confirm') {
                            // 发送加天与小时请求
                            axios.post(this.orderAddDayHourApi, {
                                trade_no:this.form.trade_no,
                                day:this.addDay,
                                hour:this.addHour
                            }).then(res => {
                                this.$message({
                                    'type': res.data.status == 1 ? 'success' : 'error',
                                    'message': res.data.message,
                                });
                                if (res.data.status == 1) {
                                    done();
                                }
                            }).catch(err => {
                                this.$message({
                                    'type': 'error',
                                    'message': '加时失败，服务器错误！',
                                });
                            });
                        } else {
                            done();
                        }
                    }
                });
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
            // 撤单
            handleDelete(row) {
                this.$confirm('您确定要"撤单"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.deleteApi, {
                        'trade_no' : this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleFromData();
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
                        'trade_no' : this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleFromData();
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
                        'trade_no' : this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleFromData();
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
                this.tradeNo = this.form.trade_no;
                this.applyComplainVisible = true;
            },
            // 取消仲裁
            handleCancelComplain(row) {
                this.$confirm('您确定要"取消仲裁"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.cancelComplainApi, {
                        'trade_no' : this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleFromData();
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
            handleApplyCompleteImage() {
                const h = this.$createElement;
                const currentThis = this;
                this.$msgbox({
                    title: '查看验收图片',
                    message: h('el-carousel', {
                            // props: {
                            //     options:this.dayHourOptions,
                            // },
                    }, '<h3>3</h3>'),
                    showCancelButton: true,
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    beforeClose: (action, instance, done) => {
                        if (action == 'confirm') {
                            // 发送加天与小时请求
                            axios.post(this.orderAddDayHourApi, {
                                trade_no:this.form.trade_no,
                                day:this.addDay,
                                hour:this.addHour
                            }).then(res => {
                                this.$message({
                                    'type': res.data.status == 1 ? 'success' : 'error',
                                    'message': res.data.message,
                                });
                                if (res.data.status == 1) {
                                    done();
                                }
                            }).catch(err => {
                                this.$message({
                                    'type': 'error',
                                    'message': '加时失败，服务器错误！',
                                });
                            });
                        } else {
                            done();
                        }
                    }
                });
            },
            // 完成验收
            handleComplete(row) {
                this.$confirm('您确定要"完成验收"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.completeApi, {
                        'trade_no' : this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleFromData();
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
                this.tradeNo = this.form.trade_no;
                this.amount = this.amount;
                this.securityDeposit = this.securityDeposit;
                this.efficiencyDeposit = this.efficiencyDeposit;
                this.applyConsultVisible = true;
            },
            // 设置仲裁窗口是否显示
            handleApplyComplainVisible(data) {
                this.applyComplainVisible = data.visible;
                if (data.visible == false) {
                    this.handleFromData();
                }
            },
            // 设置协商窗口是否显示
            handleApplyConsultVisible(data) {
                this.applyConsultVisible = data.visible;
                if (data.visible == false) {
                    this.handleFromData();
                }
            },
            // 取消撤销
            handleCancelConsult() {
                this.$confirm('您确定要"取消撤销"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.cancelConsultApi, {
                        'trade_no' : this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleFromData();
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
                        'trade_no' : this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleFromData();
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
                        'trade_no' : this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleFromData();
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
            handleLock() {
                this.$confirm('您确定要"锁定"订单吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post(this.lockApi, {
                        'trade_no' : this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleFromData();
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
                    axios.post(this.cancelLockApi, {
                        'trade_no' : this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.data.status == 1 ? 'success' : 'error',
                            message: res.data.message
                        });

                        if(res.data.status == 1) {
                            this.handleFromData();
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
            handleRepeatOrder() {
                location.href= this.orderRepeatApi + '/' + this.tradeNo;
            },
        },
        created() {
            this.handleFromGameRegionServerOptions();
            this.handleFromData();
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