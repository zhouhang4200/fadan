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
                                                                    <el-input type="age" v-model="form.title" autocomplete="off">

                                                                    </el-input>
                                                                </el-col>
                                                            <el-col :span="1">
                                                                <el-tooltip  placement="top">
                                                                    <div slot="content">多行信息<br/>第二行信息</div>
                                                                    <i class="el-icon-question"></i>
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
                                                                <el-cascader
                                                                        :options="dayHourOptions"
                                                                        v-model="form.day_hour">
                                                                </el-cascader>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="代练要求模版">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-select v-model="form" placeholder="请选择">
                                                                    <!--<el-option-->
                                                                    <!--v-for="item in options"-->
                                                                    <!--:key="item.value"-->
                                                                    <!--:label="item.label"-->
                                                                    <!--:value="item.value">-->
                                                                    <!--</el-option>-->
                                                                </el-select>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
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
                                                                        v-model="form.amount">
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
                                                                        :rows="2"
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
                                                                        v-model="form.player_phone">
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
                                                                <el-input
                                                                        type="input"
                                                                        placeholder="请输入内容"
                                                                        v-model="form.user_qq">
                                                                </el-input>
                                                                <!---->
                                                                <!--<el-autocomplete-->
                                                                <!--class="inline-input"-->
                                                                <!--v-model="form.user_qq"-->
                                                                <!--:fetch-suggestions="handleUserQQOptions"-->
                                                                <!--placeholder="请输入内容"-->
                                                                <!--@select="handleSelect"-->
                                                                <!--&gt;</el-autocomplete>-->
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
                                                            <el-col :span="1">
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
                                                                <el-input v-model="domain.value">
                                                                    <el-button  slot="append" @click.prevent="removeDomain(domain)">删除</el-button>
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1"></el-col>
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
    </div>
</template>

<script>
    export default {
        name: "GameLevelingCreate",
        props:[
            'orderCreateApi',
            'gameRegionServerApi',
            'gameLevelingTypesApi',
            'taobaoOrderApi',
            'tid',
        ],
        data() {
            return {
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
                form: {
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
                    user_qq:'', // 商家QQ
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
                        {type: 'array', required: true, message: '请选择代练天/小时',  trigger: 'change'},
                    ],
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
                        { required: true, message: '请输入代练价格', trigger: 'change' }
                    ],
                    source_amount: [
                        {
                            validator:(rule, value, callback)=>{
                                if(value != ""){
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
                    player_phone: [
                        { required: true, message: '请输入无家电话', trigger: 'blur' },
                        { min: 3, max: 5, message: '长度在 3 到 5 个字符', trigger: 'blur' }
                    ],
                    user_qq: [
                        { required: true, message: '请选择商户QQ', trigger: 'blur' },
                    ],
                    price_increase_step: [
                        {
                            validator:(rule, value, callback)=>{
                                if(value != ""){
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
                                if(value != ""){
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
                dataTab:"1",
                taobaoData: [],
                orderTemplate:'',
            };
        },
        methods: {
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
            handleUserQQOptions() {

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
                axios.post(this.taobaoOrderApi, {tid:this.tid}).then(res => {
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
            }
        },
        created() {
            this.handleFromGameRegionServerOptions();
            this.handelTaobaoData();
        }
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