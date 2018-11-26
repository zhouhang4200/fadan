<template>
    <div class="game-leveling-order-create">
        <div class="main">
            <el-row :gutter="10">
                <el-form ref="form" :rules="rules" :model="form" label-width="120px">
                    <el-col :span="16" :style="{'margin-bottom': displayFooter ? '60px' : '15px'}">
                        <div class="grid-content bg-purple"
                             style="padding: 15px;background-color: #fff;position: relative">
                            <el-tabs v-model="orderTab" @tab-click="handleOrderTab">
                                <el-tab-pane label="订单信息" name="1">

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
                                            <el-row>
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
                                            <el-row>
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
                                        <div class="text item">
                                            <el-row>
                                                <el-col :span="12">
                                                    <el-form-item
                                                            label="代练标题"
                                                            prop="title">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-input
                                                                        :disabled="fieldDisabled"
                                                                        type="age"
                                                                        v-model="form.title"
                                                                        autocomplete="off">
                                                                </el-input>
                                                            </el-col>
                                                            <el-col :span="1">
                                                                <el-tooltip placement="top">
                                                                    <div slot="content">多行信息<br/>第二行信息</div>
                                                                    <span class="icon-button">
                                                                        <i class="el-icon-question"></i>
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
                                                    <el-form-item label="代练天/小时">
                                                        <el-row :gutter="10">
                                                            <el-col :span="22">
                                                                <el-row :gutter="10">
                                                                    <el-col :span="12">
                                                                        <el-select
                                                                                :disabled="fieldDisabled"
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
                                                                                :disabled="fieldDisabled"
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
                                                                <span class="icon-button"
                                                                      v-if="(form.status == 13 || form.status ==  14 || form.status == 17)"
                                                                      @click.prevent="addTimeDialogVisible = true">
                                                                     <i class="el-icon-circle-plus-outline"></i>
                                                                </span>
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
                                                                <span class="icon-button"
                                                                      @click="handleGameLevelingRequirementVisible({visible:true})">
                                                                    <i class="el-icon-circle-plus"></i>
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
                                                    <el-form-item label="代练要求" prop="requirement">
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
                                                    <el-form-item label="代练价格" prop="amount">
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
                                                                <i class="el-icon-circle-plus"
                                                                   v-if="(form.status == 13 || form.status ==  14 || form.status == 17)"
                                                                   @click.prevent="handleAddAmount()">
                                                                </i>
                                                            </el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :span="12">
                                                    <el-form-item label="来源价格" prop="source_amount">
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

                                                                <el-select
                                                                        :disabled="fieldDisabled"
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
                                                               <span class="icon-button"
                                                                     @click="handleBusinessmanQQVisible({visible:true})">
                                                                    <i class="el-icon-circle-plus"></i>
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
                                                    <el-form-item label="加价上限" prop="price_ceiling">
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

                                                    <!--<el-form-item label="补款单号" prop="take_order_password">-->

                                                    <!--<el-row :gutter="10">-->
                                                    <!--<el-col :span="22">-->
                                                    <!--<el-input-->
                                                    <!--type="input"-->
                                                    <!--v-model="form.take_order_password"-->
                                                    <!--autocomplete="off">-->
                                                    <!--</el-input>-->
                                                    <!--</el-col>-->
                                                    <!--<el-col :span="1" class="icon-button">-->
                                                    <!--<i class="el-icon-circle-plus-outline"-->
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
                                                    <!--<el-input v-model="domain.value"></el-input>-->
                                                    <!--</el-col>-->
                                                    <!--<el-col :span="1" class="icon-button">-->
                                                    <!--<i class="el-icon-remove-outline"-->
                                                    <!--@click.prevent="removeDomain(domain)">-->
                                                    <!--</i>-->
                                                    <!--</el-col>-->
                                                    <!--</el-row>-->
                                                    <!--</el-form-item>-->
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

                                    <el-table
                                            :data="complainDesData"
                                            :stripe=true
                                            :border=true
                                            style="width: 100%">
                                        <el-table-column
                                                prop="who"
                                                label="申请仲裁"
                                                width="180">
                                        </el-table-column>
                                        <el-table-column
                                                prop="created_at"
                                                label="申请时间"
                                                width="180">
                                        </el-table-column>
                                        <el-table-column
                                                prop="content"
                                                label="仲裁理由">
                                        </el-table-column>
                                    </el-table>
                                    <p></p>
                                    <el-row :gutter="12"
                                            v-if="complainDesData.length">
                                        <el-col :span="8">
                                            <el-card
                                                    v-if="complainDesData[0].pic1"
                                                    :style="{
                                                backgroundImage:'url(' + complainDesData[0].pic1 + ')',
                                                height:'150px',
                                                backgroundSize: 'cover',
                                                width: '100%',
                                                display:'block',
                                              }" @click.native="handleOpenImage(complainDesData[0].pic1)">
                                            </el-card>
                                        </el-col>
                                        <el-col :span="8">
                                            <el-card
                                                    v-if="complainDesData[0].pic2"
                                                    :style="{
                                                backgroundImage:'url(' + complainDesData[0].pic2 + ')',
                                                height:'150px',
                                                backgroundSize: 'cover',
                                                width: '100%',
                                            }"
                                                    @click.native="handleOpenImage(complainDesData[0].pic2)">
                                            </el-card>
                                        </el-col>
                                        <el-col :span="8">
                                            <el-card
                                                    v-if="complainDesData[0].pic3"
                                                    :style="{
                                                backgroundImage:'url(' + complainDesData[0].pic3 + ')',
                                                height:'150px',
                                                backgroundSize: 'cover',
                                                width: '100%',
                                            }"
                                                    @click.native="handleOpenImage(complainDesData[0].pic3)">
                                            </el-card>
                                        </el-col>
                                    </el-row>
                                    <p></p>
                                    <el-table
                                            :data="complainMessageData"
                                            :stripe=true
                                            :border=true
                                            style="width: 100%">
                                        <el-table-column
                                                prop="who"
                                                label="留言方">
                                        </el-table-column>
                                        <el-table-column
                                                prop="content"
                                                label="留言说明">
                                        </el-table-column>
                                        <el-table-column
                                                prop="created_at"
                                                label="留言时间">
                                        </el-table-column>
                                        <el-table-column
                                                prop="address"
                                                label="留言证据"
                                                width="80">
                                            <template slot-scope="scope">
                                                <el-button icon="el-icon-search"
                                                           v-if="scope.row.pic"
                                                           @click.native="handleOpenImage(scope.row.pic)"></el-button>
                                            </template>
                                        </el-table-column>
                                    </el-table>
                                    <p></p>
                                    <el-form :model="complainMessageForm" ref="complainMessageForm" label-width="100px"
                                             class="demo-ruleForm">
                                        <el-form-item
                                                label="留言内容"
                                                :rules="[{ required: true, message: '留言内容不能为空'}]">
                                            <el-input
                                                    type="textarea"
                                                    :rows="6"
                                                    v-model="complainMessageForm.reason">
                                            </el-input>
                                        </el-form-item>
                                        <el-form-item label="上传证据">
                                            <el-upload action="action"
                                                       :class="complainMessageImageExceedLimit"
                                                       list-type="picture-card"
                                                       :limit="1"
                                                       :on-preview="handleUploadPreview"
                                                       :on-remove="handleRemoveComplainMessageImage"
                                                       :http-request="handleUploadComplainMessageImage">
                                                <i class="el-icon-plus"></i>
                                            </el-upload>
                                        </el-form-item>
                                        <el-form-item>
                                            <el-button type="primary" @click="handleAddComplainMessageForm()">提交
                                            </el-button>
                                        </el-form-item>
                                    </el-form>
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
                            <el-button type="primary" icon="el-icon-document"
                                       style="position: absolute; right: 15px; top:15px"
                                       @click="handleOpenChat">
                                订单留言
                            </el-button>
                            <el-button @click="handleApplyCompleteImage"
                                       type="primary" 
                                       icon="el-icon-search"
                                       style="position: absolute; right: 125px; top:15px">查看图片
                            </el-button>
                        </div>
                    </el-col>
                </el-form>
                <el-col :span="8">
                    <div class="grid-content bg-purple" style="padding: 15px;background-color: #fff">
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
                        <!--v-if="(form.status == 1 || form.status == 22)"-->
                        <el-button
                                   type="primary"
                                   @click="handleSubmitForm('form')"
                                   style="margin-right: 8px">确认修改
                        </el-button>

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
                            <router-link :to="{name:'gameLevelingOrderRepeat', query:{trade_no:$route.query.trade_no}}">
                                <el-button
                                        size="small"
                                        type="primary"
                                        >
                                    重发</el-button>
                             </router-link>
                             <el-button
                                     size="small"
                                     type="primary"
                                     @click="handleBusinessmanComplainVisible()">投诉</el-button>
                        </span>

                    </div>
                </el-col>
            </el-row>
        </div>

        <div id="chat">
            <el-dialog
                    title="订单留言"
                    :visible="chatVisible" :before-close="handleCloseChat">
                <div class="chat-main">
                    <ul style="padding: 0">
                        <li v-for="item in chatData" :class="[item.sender == '您' ? 'chat-mine' : 'chat-user']">
                            <div class="chat-user">
                                <img src="/frontend/v2/images/message_avatar.jpg">
                                <cite>
                                    <i>{{ item.send_time }} </i> {{ item.sender}}
                                </cite>
                            </div>
                            <div class="chat-text">{{ item.send_content}}</div>
                        </li>
                    </ul>
                </div>
                <el-form :model="form">
                    <el-input
                            type="textarea"
                            :rows="5"
                            v-model="chatForm.content">
                    </el-input>
                </el-form>
                <div slot="footer" class="dialog-footer">
                    <el-button type="primary" @click="handleChatForm">发送留言</el-button>
                </div>
            </el-dialog>
        </div>

        <template>
            <el-dialog
                    title="订单投诉"
                    :before-close="handleBusinessmanComplainVisible"
                    :visible=businessmanComplainVisible>
                <el-form
                        :model="businessmanComplainForm"
                         ref="businessmanComplainForm"
                         label-width="110px"
                         class="demo-ruleForm">
                    <el-form-item label="证据截图"
                                  prop="images"
                                  :rules="[
                                     { required: true, message: '最少上传一张图片', trigger: 'change'}
                                    ]"
                                  ref="image">
                        <el-upload :class="businessmanComplainImageExceedLimit"
                                   action="action"
                                   list-type="picture-card"
                                   :limit="3"
                                   :on-remove="handleRemoveBusinessmanComplainImage"
                                   :http-request="handleUploadBusinessmanComplainImage">
                            <i class="el-icon-plus"></i>
                        </el-upload>

                        <el-dialog :visiblec="businessmanComplainForm.dialogVisible">
                            <img width="100%"
                                 :src="businessmanComplainForm.dialogImageUrl">
                        </el-dialog>
                    </el-form-item>

                    <el-form-item prop="amount"
                                  :rules="[
                                      { required: true, message: '赔偿金额不能为空', trigger: 'blur'},
                                      { type: 'number', message: '赔偿金额必须为数字值', trigger: 'blur'}
                                  ]"
                                  label="要求赔偿金额">
                        <el-input type="input"
                                  :rows="8"
                                  v-model.number="businessmanComplainForm.amount"></el-input>
                    </el-form-item>

                    <el-form-item label="投诉原因"
                                  :rules="[
                                      { required: true, message: '投诉原因不能为空'},
                                  ]"
                                  prop="reason">
                        <el-input type="textarea"
                                  :rows="8"
                                  v-model="businessmanComplainForm.reason"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary"
                                   @click="handleSubmitBusinessmanComplainForm('businessmanComplainForm')">提交
                        </el-button>
                    </el-form-item>
                </el-form>
            </el-dialog>
        </template>

        <ApplyComplain v-if="applyComplainVisible"
                       :tradeNo="tradeNo"
                       @handleApplyComplainVisible="handleApplyComplainVisible">
        </ApplyComplain>

        <ApplyConsult v-if="applyConsultVisible"
                      :tradeNo="tradeNo"
                      :amount="amount"
                      :securityDeposit="securityDeposit"
                      :efficiencyDeposit="efficiencyDeposit"
                      @handleApplyConsultVisible="handleApplyConsultVisible">
        </ApplyConsult>

        <GameLevelingRequirement v-if="gameLevelingRequirementVisible"
                                 @handleGameLevelingRequirementVisible="handleGameLevelingRequirementVisible">
        </GameLevelingRequirement>

        <BusinessmanQQ v-if="businessmanQQVisible"
                       @handleBusinessmanQQVisible="handleBusinessmanQQVisible">
        </BusinessmanQQ>

        <el-dialog

                top="35vh"
                custom-class="add-time-dialog"
                title="增加代练时间"
                :visible.sync="addTimeDialogVisible">

            <el-form ref="addTimeForm" :rules="addTimeFormRules" :model="addTimeForm" label-width="80px">
                <el-form-item
                        prop="day"
                        label="天">
                    <el-input v-model.number="addTimeForm.day"></el-input>
                </el-form-item>

                <el-form-item
                        prop="hour"
                        label="小时">
                    <el-input v-model.number="addTimeForm.hour"></el-input>
                </el-form-item>

                <el-form-item>
                    <el-button @click="addTimeDialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="handleAddDayHour">确认</el-button>
                </el-form-item>

            </el-form>
        </el-dialog>
    </div>
</template>

<script>
    import ApplyComplain from './ApplyComplain';
    import ApplyConsult from './ApplyConsult';
    import GameLevelingRequirement from './GameLevelingRequirement';
    import BusinessmanQQ from './BusinessmanQQ';

    export default {
        name: "GameLevelingShow",
        components: {
            ApplyComplain,
            ApplyConsult,
            GameLevelingRequirement,
            BusinessmanQQ,
        },
        computed: {
            fieldDisabled() {
                if (this.form.status === 1 || this.form.status === 22) {
                    return false;
                } else {
                    return true;
                }
            },
            displayFooter() {
                if (this.orderTab === "1") {
                    return true;
                } else {
                    return false;
                }
            },
            // 商户投诉图片上传数量限制 3 张
            businessmanComplainImageExceedLimit() {
                return [
                    this.businessmanComplainForm.images.length === 3 ? 'exceed' : ' '
                ];
            },
            // 仲裁证据补充图片上传数量限制 1 张
            complainMessageImageExceedLimit() {
                return [
                    this.complainMessageForm.pic !== '' ? 'exceed' : ' '
                ];
            },

        },
        data() {
            return {
                tradeNo: this.$route.query.trade_no,
                gameLevelingRequirementVisible: false,
                gameLevelingRequirementOptions: [],
                businessmanQQVisible: false,
                businessmanQQOptions: [],
                dayOptions: [],
                hourOptions: [],
                fileReader: '',
                amount: 0,
                securityDeposit: 0,
                efficiencyDeposit: 0,
                chatVisible: false,
                applyConsultVisible: false, // 申请协商
                applyComplainVisible: false, // 申请仲裁
                businessmanComplainVisible: false, // 商户投诉
                orderTab: "1",
                dataTab: "1",
                gameRegionServerOptions: [], // 游戏/区/服 选项
                dayHourOptions: [],  // 天数/小时  选项
                gameLevelingTypeOptions: [], // 游戏代练类型 选项
                addTimeForm: {
                    day: 0, // 增加的天数
                    hour: 0, // 增加的小时
                },
                addTimeFormRules: {
                    day: [
                        {validator: (rule, value, callback) => {
                                if (this.addTimeForm.day === "" && this.addTimeForm.hour === "") {
                                    callback(new Error("加价天数与小时不能都为空"));
                                } else if ((/^[1-9][0-9]*$/).test(value) == false && value != 0) {
                                    callback(new Error("加价小时不能为小数"));
                                } else {
                                    callback();
                                }
                            }, trigger: ['change', 'blur']},
                    ],
                    hour: [
                        {validator: (rule, value, callback) => {
                                if (this.addTimeForm.day === 0 && this.addTimeForm.hour === 0) {
                                    callback(new Error("加价天数与小时不能都为0"));
                                } else if (this.addTimeForm.day === "" && this.addTimeForm.hour === "") {
                                    callback(new Error("加价天数与小时不能都为空"));
                                } else if ((/^[1-9][0-9]*$/).test(value) == false && value != 0) {
                                    callback(new Error("加价小时不能为小数"));
                                } else {
                                    callback();
                                }
                            }, trigger: ['change', 'blur']},
                    ],
                },
                addTimeDialogVisible: false,
                chatData: [],
                businessmanComplainForm: {
                    trade_no: this.$route.query.trade_no,
                    images: [],
                    amount: '',
                    reason: '',
                    dialogVisible: false,
                    dialogImageUrl: '',
                },
                complainMessageForm: {
                    trade_no: this.$route.query.trade_no,
                    reason: '',
                    pic: '',
                    dialogVisible: false,
                    dialogImageUrl: '',
                },
                chatForm: {
                    trade_no: this.$route.query.trade_no,
                    content: '',
                },
                form: {
                    trade_no: this.$route.query.trade_no,
                    status: 0,
                    game_leveling_order_consult: [],
                    game_leveling_order_complain: [],
                    game_region_server: [], // 选择的 游戏/区/服
                    day_hour: [], // 选择的代练天/小时
                    game_id: 0, // 游戏ID
                    game_region_id: 0, // 游戏区ID
                    game_server_id: 0, // 游戏服务器ID
                    game_leveling_type_id: '', // 代练类型ID
                    amount: '', // 代练金额
                    source_amount: '', // 来源价格
                    security_deposit: '', // 安全保证金
                    efficiency_deposit: '', // 效率保证金
                    title: '', //代练标题
                    game_role: '', // 游戏角色
                    game_account: '', // 游戏账号
                    game_password: '', // 游戏密码
                    price_increase_step: '', // 自动加价步长
                    price_ceiling: '', // 自动加价上限
                    explain: '', // 代练说明
                    requirement: '', // 代练要求
                    take_order_password: '', // 接单密码
                    player_phone: '', // 玩家电话
                    user_qq: '', // 商户qq
                    domains: [],
                    remark: '',
                    day: 0,
                    hour: 1,
                    gameLevelingRequirementId: '',
                },
                rules: {
                    game_leveling_type_id: [
                        {required: true, message: '请选择代练类型', trigger: 'change'},
                    ],
                    game_role: [
                        {required: true, message: '请输入游戏角色', trigger: 'blur'},
                    ],
                    game_account: [
                        {required: true, message: '请输入游戏账号', trigger: 'change'},
                    ],
                    game_password: [
                        {required: true, message: '请输入游戏密码', trigger: 'change'},
                    ],
                    title: [
                        {required: true, message: '请输入代练标题', trigger: 'change'},
                        {min: 3, max: 35, message: '长度在 3 到 35 个字符', trigger: 'change'}
                    ],
                    day_hour: [
                        {type: 'array', required: true, message: '请选择代练天/小时', trigger: 'change'},
                    ],
                    game_region_server: [
                        {type: 'array', required: true, message: '请选择游戏/区/服', trigger: 'change'},
                    ],
                    explain: [
                        {required: true, message: '请输入代练说明', trigger: 'change'},
                    ],
                    requirement: [
                        {required: true, message: '请输入代练要求', trigger: 'change'},
                    ],
                    amount: [
                        {required: true, message: '请输入代练价格', trigger: 'change'}
                    ],
                    source_amount: [
                        { validator: (rule, value, callback) => {
                                if ((/^[+]{0,1}(\d+)$|^[+]{0,1}(\d+\.\d+)$/).test(value) == false) {
                                    callback(new Error("来源价格必须为数字值"));
                                } else {
                                    callback();
                                }
                            }, trigger: 'blur'},
                    ],
                    efficiency_deposit: [
                        {required: true, message: '请输入效率保证金', trigger: 'change'}
                    ],
                    security_deposit: [
                        {required: true, message: '请输入安全保证金', trigger: 'change'}
                    ],
                    user_qq: [
                        {required: true, message: '请输入商户QQ号', trigger: 'change'}
                    ],
                    player_phone: [
                        {required: true, message: '请输入无家电话', trigger: 'blur'},
                        {min: 3, max: 5, message: '长度在 3 到 5 个字符', trigger: 'blur'}
                    ],
                    price_increase_step: [
                        {
                            validator: (rule, value, callback) => {
                                if (value != "" && value != undefined) {
                                    if ((/^[+]{0,1}(\d+)$|^[+]{0,1}(\d+\.\d+)$/).test(value) == false) {
                                        callback(new Error("加价幅度必须为数字值"));
                                    } else {
                                        callback();
                                    }
                                } else {
                                    callback();
                                }
                            },
                            trigger: 'blur'
                        },
                    ],
                    price_ceiling: [
                        {
                            validator: (rule, value, callback) => {
                                if (value != "" && value != undefined) {
                                    if ((/^[+]{0,1}(\d+)$|^[+]{0,1}(\d+\.\d+)$/).test(value) == false) {
                                        callback(new Error("加价上限必须为数字值"));
                                    } else {
                                        callback();
                                    }
                                } else {
                                    callback();
                                }
                            },
                            trigger: 'blur'
                        },
                    ],

                },
                status: {
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
                platform: {
                    5: '丸子代练',
                    1: '91代练',
                    3: '蚂蚁代练',
                },
                platformData: [],
                taobaoData: [],
                logData: [],
                complainDesData: [],
                complainMessageData: []
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
            handleFromStatus() {
                return (this.form.status == 1 || this.form.status == 22) ? false : false;
            },
            handleFromData() {
                this.$api.gameLevelingOrderEdit({trade_no: this.$route.query.trade_no}).then(res => {
                    this.trade_no = res.trade_no;
                    this.amount = res.amount;
                    this.securityDeposit = res.security_deposit;
                    this.efficiencyDeposit = res.efficiency_deposit;
                    this.form.status = res.status;
                    this.form.game_leveling_order_consult = res.game_leveling_order_consult;
                    this.form.game_leveling_order_complain = res.game_leveling_order_complain;
                    this.form.game_region_server = [  // 选择的 游戏/区/服
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
                    this.form.game_region_id = res.game_region_id; // 游戏区ID
                    this.form.game_server_id = res.game_server_id;// 游戏服务器ID
                    this.form.game_leveling_type_id = res.game_leveling_type_id; // 代练类型ID
                    this.form.amount = res.amount; // 代练金额
                    this.form.source_amount = res.source_amount; // 来源价格
                    this.form.security_deposit = res.security_deposit; // 安全保证金
                    this.form.efficiency_deposit = res.efficiency_deposit; // 效率保证金
                    this.form.title = res.title; //代练标题
                    this.form.game_role = res.game_role; // 游戏角色
                    this.form.game_account = res.game_account; // 游戏账号
                    this.form.game_password = res.game_password; // 游戏密码
                    this.form.price_increase_step = res.price_increase_step != '0.0000' ? res.price_increase_step : ''; // 自动加价步长
                    this.form.price_ceiling = res.price_ceiling != '0.0000' ? res.price_ceiling : ''; // 自动加价上限
                    this.form.explain = res.game_leveling_order_detail.explain; // 代练说明
                    this.form.requirement = res.game_leveling_order_detail.requirement; // 代练要求
                    this.form.take_order_password = res.take_order_password; // 接单密码
                    this.form.player_phone = res.game_leveling_order_detail.player_phone; // 玩家电话
                    this.form.user_qq = res.game_leveling_order_detail.user_qq; // 商家qq
                    this.form.remark = res.remark;
                    this.form.domains = [];
                    // 平台数据
                    this.platformData = [
                        {
                            name: '平台单号',
                            value: res.trade_no,
                        },
                        {
                            name: '订单状态',
                            value: this.status[res.status]
                        },
                        {
                            name: '接单平台',
                            value: this.platform[res.platform_id]
                        },
                        {
                            name: '打手呢称',
                            value: res.game_leveling_order_detail.hatchet_man_name
                        },
                        {
                            name: '打手电话',
                            value: res.game_leveling_order_detail.hatchet_man_phone
                        },
                        {
                            name: '打手QQ',
                            value: res.game_leveling_order_detail.hatchet_man_qq
                        },
                        {
                            name: '剩余代练时间',
                            value: res.left_time
                        },
                        {
                            name: '发布时间',
                            value: res.created_at
                        },
                        {
                            name: '接单时间',
                            value: res.take_at
                        },
                        {
                            name: '提验时间',
                            value: res.apply_complete_at
                        },
                        {
                            name: '结算时间',
                            value: res.complete_at
                        },
                        {
                            name: '发单客服',
                            value: res.game_leveling_order_detail.username
                        },
                        {
                            name: '撤销说明',
                            value: res.consult_describe
                        },
                        {
                            name: '仲裁说明',
                            value: res.complain_describe
                        },
                        {
                            name: '支付代练费用',
                            value: res.pay_amount
                        },
                        {
                            name: '获得赔偿金额',
                            value: res.get_amount
                        },
                        {
                            name: '手续费',
                            value: res.get_poundage
                        },
                        {
                            name: '最终支付金额',
                            value: res.complain_amount
                        },
                    ];
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
                    'game_id': this.form.game_region_server[2]
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

                        this.$api.gameLevelingOrderUpdate(this.form).then(res => {
                            this.$message({
                                'type': res.status == 1 ? 'success' : 'error',
                                'message': res.message,
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
                }).then(({value}) => {
                    // 发送加价请求 value 为写入的值
                    this.$api.gameLevelingOrderAddAmount({
                        trade_no: this.form.trade_no,
                        amount: value
                    }).then(res => {
                        this.$message({
                            'type': res.status == 1 ? 'success' : 'error',
                            'message': res.message,
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
                this.$refs.addTimeForm.validate((valid) => {
                    if (valid) {
                        // 发送加天与小时请求
                        this.$api.gameLevelingOrderAddDayHour({
                            trade_no: this.form.trade_no,
                            day: this.addDay,
                            hour: this.addHour
                        }).then(res => {
                            this.$message({
                                'type': res.status == 1 ? 'success' : 'error',
                                'message': res.message,
                            });
                            if (res.status == 1) {
                                this.addTimeDialogVisible = false;
                            }
                        }).catch(err => {
                            this.$message({
                                'type': 'error',
                                'message': '加时失败，服务器错误！',
                            });
                        });
                    }
                });
            },
            handleOrderTab(tab, event) {
                if (tab.name == 2) {
                    this.handleComplainData();
                }
                // 订单操作日志
                if (tab.name == 3) {
                    this.$api.gameLevelingOrderLog({trade_no: this.$route.query.trade_no}).then(res => {
                        this.logData = res;
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
                    this.$api.gameLevelingOrderDelete({
                        'trade_no': this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                    this.$api.gameLevelingOrderOnSale({
                        'trade_no': this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                    this.$api.gameLevelingOrderOffSale({
                        'trade_no': this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                this.$route.query.trade_no = this.form.trade_no;
                this.applyComplainVisible = true;
            },
            // 取消仲裁
            handleCancelComplain(row) {
                this.$confirm('您确定要"取消仲裁"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$api.gameLevelingOrderCancelComplain({
                        'trade_no': this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                // 请求图片
                this.$api.gameLevelingOrderApplyCompleteImage({
                    'trade_no': this.form.trade_no
                }).then(res => {
                    if (res.status == 1) {
                        const h = this.$createElement;
                        let item = [];
                        res.content.forEach(function (val) {
                            item.push(h('el-carousel-item', null, [
                                h('img', {
                                    attrs: {
                                        src: val['url']
                                    }
                                }, '')
                            ]))
                        });

                        this.$msgbox({
                            title: '查看验收图片',
                            message: h('el-carousel', null, item),
                            showCancelButton: true,
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                        });
                    } else {
                        this.$message({
                            type: 'error',
                            message: res.message
                        });
                    }
                }).catch(err => {
                    this.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });

                // const h = this.$createElement;
                // const currentThis = this;
                // this.$msgbox({
                //     title: '查看验收图片',
                //     message: h('el-carousel', {
                //         // props: {
                //         //     options:this.dayHourOptions,
                //         // },
                //     }, '<h3>3</h3>'),
                //     showCancelButton: true,
                //     confirmButtonText: '确定',
                //     cancelButtonText: '取消',
                //     beforeClose: (action, instance, done) => {
                //         if (action == 'confirm') {
                //             // 发送加天与小时请求
                //             this.$api.gameLevelingOrderAddDayHour({
                //                 trade_no: this.form.trade_no,
                //                 day: this.addDay,
                //                 hour: this.addHour
                //             }).then(res => {
                //                 this.$message({
                //                     'type': res.status == 1 ? 'success' : 'error',
                //                     'message': res.message,
                //                 });
                //                 if (res.status == 1) {
                //                     done();
                //                 }
                //             }).catch(err => {
                //                 this.$message({
                //                     'type': 'error',
                //                     'message': '加时失败，服务器错误！',
                //                 });
                //             });
                //         } else {
                //             done();
                //         }
                //     }
                // });
            },
            // 完成验收
            handleComplete(row) {
                this.$confirm('您确定要"完成验收"吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$api.gameLevelingOrderComplete({
                        'trade_no': this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                this.$route.query.trade_no = this.form.trade_no;
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
                    this.$api.gameLevelingOrderCancelConsult({
                        'trade_no': this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                    this.$api.gameLevelingOrderAgreeConsult({
                        'trade_no': this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                    this.$api.gameLevelingOrderRejectConsult({
                        'trade_no': this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                    this.$api.gameLevelingOrderLock({
                        'trade_no': this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                    this.$api.gameLevelingOrderCancelLock({
                        'trade_no': this.form.trade_no
                    }).then(res => {
                        this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
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
                location.href = this.orderRepeatApi + '/' + this.$route.query.trade_no;
            },
            // 查看图片大图
            handleOpenImage(src) {
                const h = this.$createElement;
                this.$msgbox({
                    center: true,
                    showConfirmButton: false,
                    customClass: 'preview-image',
                    message: h('img', {attrs: {src: src}}, '')
                });
            },
            // 获取仲裁数据
            handleComplainData() {
                this.$api.gameLevelingOrderComplainInfo({trade_no: this.$route.query.trade_no}).then(res => {
                    if (res.content.length > 0) {
                        this.complainDesData = [res.content.detail];
                        this.complainMessageData = res.content.info;
                    }
                });
            },
            // 添加仲裁留言
            handleAddComplainMessageForm() {
                this.$api.gameLevelingOrderAddComplainInfo(this.complainMessageForm).then(res => {
                    if (res.status == 1) {
                        this.$message.success('发送成功');
                        this.complainMessageForm.reason = '';
                        this.handleComplainData();
                    }
                });
            },
            // 预览图片
            handleUploadPreview(file) {
                const h = this.$createElement;
                this.$msgbox({
                    message: h('img', {attrs: {src: file.url}}),
                    showCancelButton: false,
                    cancelButtonText: false,
                    showConfirmButton: false,
                    customClass: 'preview-image'
                });
            },
            // 仲裁证据补充图片删除
            handleRemoveComplainMessageImage() {
                this.complainMessageForm.pic = '';
            },
            // 仲裁证据补充图片上传
            handleUploadComplainMessageImage(options) {
                let file = options.file;
                if (file) {
                    this.fileReader.readAsDataURL(file)
                }
                this.fileReader.onload = () => {
                    this.complainMessageForm.pic = this.fileReader.result;
                }
            },
            // 打开聊天窗口
            handleOpenChat() {
                this.chatVisible = true;
                this.handleChatData();
            },
            // 加载聊天数据
            handleChatData() {
                this.$api.gameLevelingOrderMessage(this.chatForm).then(res => {
                    if (res.status == 1) {
                        this.chatData = res.content;

                        setTimeout(() => {
                            let chatWindowsHeight = document.querySelector(".chat-main").scrollHeight;
                            this.$nextTick(() => {
                                document.querySelector(".chat-main").scrollTop = chatWindowsHeight;
                            });
                            this.chatForm.content = '';
                        }, 80);
                    }
                });
            },
            // 发送聊天数据
            handleChatForm() {
                this.$api.gameLevelingOrderSendMessage(this.chatForm).then(res => {
                    if (res.status == 1) {
                        this.handleChatData();
                    }
                });
            },
            // 关闭聊天窗口
            handleCloseChat() {
                this.chatVisible = false;
            },
            // 投诉弹窗
            handleBusinessmanComplainVisible() {
                if (this.businessmanComplainVisible == false) {
                    return this.businessmanComplainVisible = true;
                } else {
                    return this.businessmanComplainVisible = false;
                }
            },
            // 提交商户投诉表单
            handleSubmitBusinessmanComplainForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.$api.gameLevelingOrderBusinessmanComplainStore(this.businessmanComplainForm).then(res => {
                            this.$message({
                                type: res.status == 1 ? 'success' : 'error',
                                message: res.message,
                            });
                            if (res.status == 1) {
                                this.businessmanComplainVisible = false;
                                this.handleFromData();
                            }
                        });
                    }
                });
            },
            // 上传商户投诉图片
            handleUploadBusinessmanComplainImage(options) {
                let file = options.file;
                if (file) {
                    this.fileReader.readAsDataURL(file)
                }
                this.fileReader.onload = () => {
                    this.businessmanComplainForm.images.push(this.fileReader.result);
                    this.$refs.image.clearValidate();
                };
            },
            // 删除图片
            handleRemoveBusinessmanComplainImage(file, fileList) {
                let index = this.businessmanComplainForm.images.indexOf(file.response);
                this.businessmanComplainForm.images.splice(index, 1);
            },
            handleDayOption() {
                for (let i = 0; i <= 90; i++) {
                    this.dayOptions.push({
                        value: i,
                        label: i + '天',
                    })
                }
            },
            handleHourOption() {
                for (let i = 0; i <= 24; i++) {
                    this.hourOptions.push({
                        value: i,
                        label: i + '小时',
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
        created() {

        },
        mounted() {
            this.businessmanQQOption();
            this.gameLevelingRequirementOption();
            this.handleFromData();
            this.handleFromGameRegionServerOptions();
            this.handleDayOption();
            this.handleHourOption();
            this.fileReader = new FileReader()
        }
    }
</script>

<style lang="less">
    .add-time-dialog {
        border-radius: 4px;
        width: 420px;
    }
    .add-time-dialog .el-dialog__body {
        padding: 10px 20px 5px;
        text-align: right;
    }

    .img-box {
        width: auto;
        padding: 0;
        background-color: transparent;
        border: none;
        box-shadow: none;
    }

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
        height: 60px;
        background-color: #fff;
        position: fixed;
        bottom: 0;
        width: 100%;
        /*box-shadow:inset 0px 15px 15px -15px rgba(0, 0, 0, 0.1);*/
        /*!*-webkit-box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);*!*/
        box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
    }

    #chat .el-dialog__header {
        background-color: #efefef;
    }

    #chat .el-dialog__header .el-dialog__title {
        font-size: 16px;
    }

    #chat .el-dialog__body {
        padding: 0 20px 20px 20px;
    }

    #chat .el-dialog__footer {
        padding: 0 20px 10px;
        padding-top: 0;
    }

    .chat-title {
        position: absolute;
        top: -80px;
        height: 80px;
    }

    .chat-main {
        height: 350px;
        overflow-x: hidden;
        overflow-y: auto;
    }

    .chat-main ul .chat-mine {
        text-align: right;
        padding-left: 0;
        padding-right: 60px;
    }

    .chat-main ul .chat-mine .chat-user {
        position: absolute;
        left: auto;
        right: 3px;
    }

    .chat-main ul .chat-mine .chat-user img {
        width: 40px;
        height: 40px;
        border-radius: 100%;
    }

    .chat-main ul .chat-mine .chat-user cite {
        left: auto;
        right: 60px;
        text-align: right;
    }

    .chat-main ul .chat-mine .chat-user cite i {
        padding-left: 0;
        padding-right: 15px;
    }

    .chat-main ul .chat-mine .chat-text {
        margin-left: 0;
        text-align: left;
        background-color: #5FB878;
        color: #fff;
    }

    .chat-main ul .chat-mine .chat-text:after {
        left: auto;
        right: -10px;
        border-top-color: #5FB878;
    }

    .chat-main ul li {
        position: relative;
        font-size: 0;
        margin-bottom: 10px;
        padding-left: 60px;
        min-height: 68px;
    }

    .chat-main ul li .chat-user {
        display: inline-block;
        vertical-align: top;
        font-size: 14px;
        position: absolute;
        left: 3px;
    }

    .chat-main ul li .chat-user img {
        width: 40px;
        height: 40px;
        border-radius: 100%;
    }

    .chat-main ul li .chat-user cite {
        position: absolute;
        left: 60px;
        top: -2px;
        width: 500px;
        line-height: 24px;
        font-size: 12px;
        white-space: nowrap;
        color: #999;
        text-align: left;
        font-style: normal;
    }

    .chat-main ul li .chat-user cite i {
        padding-left: 15px;
        font-style: normal;
    }

    .chat-main ul li .chat-text {
        position: relative;
        line-height: 22px;
        margin-top: 25px;
        padding: 8px 15px;
        background-color: #e2e2e2;
        border-radius: 3px;
        color: #333;
        word-break: break-all;
        max-width: 462px \9;
        display: inline-block;
        vertical-align: top;
        font-size: 14px;
    }

    .chat-main ul li .chat-text:after {
        content: '';
        position: absolute;
        left: -10px;
        top: 13px;
        width: 0;
        height: 0;
        border-style: solid dashed dashed;
        border-color: #e2e2e2 transparent transparent;
        overflow: hidden;
        border-width: 10px;
    }

    .exceed .el-upload {
        display: none;
    }

</style>