<template>
    <div class="main content amount-flow">
        <template>
            <el-alert
                    style="margin-bottom: 15px"
                    title="操作提示: 添加了某一淘宝/天猫商品，则会自动获取该商品对应的订单，未添加商品则无法获取商品对应订单。请确保添加商品之前，已进行店铺授权。"
                    type="success"
                    :closable="false">
            </el-alert>
            <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
                <el-row :gutter="16">
                    <el-col :span="5">
                        <el-form-item label="淘宝商品ID">
                            <el-input v-model="searchParams.foreign_goods_id"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-form-item>
                        <el-button type="primary" @click="handleSearch">查询</el-button>
                        <el-button
                                type="primary"
                                size="small"
                                @click="goodsAdd()">新增</el-button>
                    </el-form-item>
                </el-row>
            </el-form>
            <el-table
                    :data="tableData"
                    border
                    style="width: 100%">
                <el-table-column
                        prop="seller_nick"
                        label="店铺"
                        width="150">
                </el-table-column>
                <el-table-column
                        prop="foreign_goods_id"
                        label="淘宝商品ID"
                        width="150">
                </el-table-column>
                <el-table-column
                        prop="game_name"
                        label="绑定游戏"
                        width="150">
                </el-table-column>
                <el-table-column
                        prop="remark"
                        label="备注"
                        width="">
                </el-table-column>
                <el-table-column
                        prop="delivery"
                        label="提验自动发货"
                        width="180">
                    <template slot-scope="scope">
                        <el-switch v-model=scope.row.delivery @change="handleSwitch($event, scope.row)" active-text="启用"
                                   inactive-text="禁用" :active-value=1 :inactive-value=2></el-switch>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="created_at"
                        label="添加时间"
                        width="180">
                </el-table-column>
                <el-table-column
                        prop="updated_at"
                        label="更新时间"
                        width="180">
                </el-table-column>
                <el-table-column
                        label="操作"
                        width="200">
                    <template slot-scope="scope">
                        <el-button
                                type="primary"
                                size="small"
                                @click="goodsEdit(scope.row)">编辑</el-button>
                        <el-button
                                type="primary"
                                size="small"
                                @click="goodsDelete(scope.row.id)">删除</el-button>
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
                    <el-form-item label="*店铺" prop="seller_nick">
                        <el-select v-model="form.seller_nick" placeholder="请选择">
                            <el-option v-for="value of sellerNicks" :value="value" :key="value"  :label="value">{{value}}</el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="*绑定游戏" prop="game_id">
                        <el-select v-model="form.game_id" placeholder="请选择">
                            <el-option v-for="item in games" :value="item.id" :key="item.id"  :label="item.name">{{ item.name }}</el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="*淘宝链接" prop="foreign_goods_id">
                        <el-input v-model="form.foreign_goods_id" name="foreign_goods_id" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="*备注信息" prop="remark">
                        <el-input type="textarea" v-model="form.remark"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button v-if="isAdd" type="primary" @click="submitFormAdd('form')">确认添加</el-button>
                        <el-button v-if="isUpdate" type="primary" @click="submitFormUpdate('form')">确认修改</el-button>
                        <el-button @click="goodsCancel('form')">取消</el-button>
                    </el-form-item>
                </el-form>
            </el-dialog>
        </template>
    </div>
</template>

<script>
    export default {
        props: [
            'SettingGoodsDataListApi',
            'SettingGoodsGameApi',
            'SettingGoodsSellerNickApi',
            'SettingGoodsAddApi',
            'SettingGoodsUpdateApi',
            'SettingGoodsDeleteApi',
            'SettingGoodsDeliveryApi',
        ],
        methods: {
            // 新增
            goodsAdd() {
                this.dialogFormVisible = true;
                this.isAdd=true;
                this.isUpdate=false;
                this.title="新增";
                this.$refs.form.resetFields();
            },
            // 编辑按钮
            goodsEdit(row) {
                this.dialogFormVisible = true;
                this.form=row;
                this.isAdd=false;
                this.title="修改";
                this.isUpdate=true;
            },
            // 取消按钮
            goodsCancel(formName) {
                this.dialogFormVisible = false;
            },
            // 添加
            submitFormAdd(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        axios.post(this.SettingGoodsAddApi, this.form).then(res => {
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
                        axios.post(this.SettingGoodsUpdateApi, this.form).then(res => {
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
            goodsDelete(id) {
                axios.post(this.SettingGoodsDeleteApi, {id:id}).then(res => {
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
            handleTableData(){
                axios.post(this.SettingGoodsDataListApi, this.searchParams).then(res => {

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
            handleSearch() {

                this.handleTableData();
            },
            handleCurrentChange(page) {

                this.searchParams.page = page;
                this.handleTableData();
            },
            // 开关状态
            handleSwitch(value, row) {
                axios.post(this.SettingGoodsDeliveryApi, {delivery:value, id:row.id}).then(res => {
                    this.$message({
                        showClose: true,
                        type: res.data.status == 1 ? 'success' : 'error',
                        message: res.data.message
                    });
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            // 游戏
            game(){
                axios.post(this.SettingGoodsGameApi).then(res => {
                    this.games=res.data;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            // 商铺
            sellerNick(){
                axios.post(this.SettingGoodsSellerNickApi).then(res => {
                    this.sellerNicks=res.data;
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
            this.$store.commit('handleOpenMenu', '4');
            this.$store.commit('handleOpenSubmenu', '4-1');
            this.handleTableData();
            this.sellerNick();
            this.game();
        },
        data() {
            var checkHas = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('必填项不能为空!'));
                }
                callback();
            };
            return {
                games:[],
                sellerNicks:[],
                title:'新增',
                isAdd:true,
                isUpdate:false,
                dialogFormVisible:false,
                rules:{
                    seller_nick:[{ validator: checkHas, trigger: 'blur' }],
                    foreign_goods_id:[{ validator: checkHas, trigger: 'blur' }],
                    game_id:[{ validator: checkHas, trigger: 'blur' }],
                    remark:[{ validator: checkHas, trigger: 'blur' }],
                },
                tableData: [],
                searchParams:{
                    page:1,
                    foreign_goods_id:'',
                },
                TotalPage:0,
                form:{
                    id:'',
                    game_name:'',
                    seller_nick:'',
                    foreign_goods_id:'',
                    game_id:'',
                    remark:'',
                    delivery:'',
                    created_at:'',
                    updated_at:'',
                }
            }
        }
    }
</script>