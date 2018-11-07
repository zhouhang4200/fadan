<template>
    <div class="main content amount-flow">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">
                <el-col :span="5">
                    <el-form-item label="状态">
                        <el-select v-model="searchParams.status" placeholder="请选择">
                            <el-option v-for="(value, key) in StatusArr" :key="key" :value="key" :label="value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="7">
                    <el-form-item label="日期">
                        <el-date-picker
                                v-model="searchParams.date"
                                type="daterange"
                                align="right"
                                unlink-panels
                                format="yyyy-MM-dd"
                                value-format="yyyy-MM-dd"
                                range-separator="至"
                                start-placeholder="开始日期"
                                end-placeholder="结束日期">
                        </el-date-picker>
                    </el-form-item>
                </el-col>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">查询</el-button>
                </el-form-item>

                <el-button type="primary" @click="dialogFormVisible = true">余额提现</el-button>
                <el-dialog title="余额提现" :visible.sync="dialogFormVisible">
                <el-form :model="ruleForm" status-icon :rules="rules" ref="ruleForm" class="demo-ruleForm">
                            <el-form-item label="提现金额" prop="fee">
                                <el-input type="text" v-model.number="ruleForm.fee" @CanWithdraw="CanWithdraw" :placeholder="placeString"></el-input>
                            </el-form-item>
                            <el-form-item label="备注说明">
                                <el-input type="text" v-model="ruleForm.remark" placeholder="可为空"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-button type="primary" @click="submitForm('ruleForm')">提交</el-button>
                                <el-button @click="resetForm('ruleForm')">重置</el-button>
                            </el-form-item>
                        </el-form>
                </el-dialog>
            </el-row>
        </el-form>
        <el-table
                :data="tableData"
                border
                style="width: 100%">
            <el-table-column
                    prop="no"
                    label="提现单号"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="fee"
                    label="提现金额"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="status"
                    label="状态"
                    width="200">
                <template slot-scope="scope">
                    {{ StatusArr["" + scope.row.status] }}
                </template>
            </el-table-column>
            <el-table-column
                    prop="remark"
                    label="备注"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="created_at"
                    label="创建时间"
                    width="200">
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
    </div>
</template>

<script>
    export default {
        props: [
            'MyWithdrawApi',
            'CanWithdrawApi',
            'CreateWithdrawApi'
        ],
        // 初始化数据
        created () {
            this.handleTableData();
            this.CanWithdraw();
        },
        methods:{
            // 表格加载数据
            handleTableData(){
                axios.post(this.MyWithdrawApi, this.searchParams).then(res => {
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
            handleCurrentChange(page) {
                this.searchParams.page = page;
                this.handleTableData();
            },
            handleSearch() {
                this.handleTableData();
            },
            // 我的提现里面input框输入的说明文字
            CanWithdraw() {
                axios.post(this.CanWithdrawApi).then(res => {
                    this.placeString = '可以提现金额: '+res.data;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            // 表单提交
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        axios.post(this.CreateWithdrawApi, this.ruleForm).then(res => {
                            if (res.data.status > 0) {
                                this.$message({
                                    showClose: true,
                                    message: '发送成功!',
                                    type: 'success'
                                });
                            } else {
                                this.$message({
                                    showClose: true,
                                    message: '发送失败:'+res.data.message,
                                    type: 'error'
                                });
                            }
                        }).catch(err => {
                            this.$message({
                                showClose: true,
                                message: '服务器错误,发送失败!',
                                type: 'error'
                            });
                        });
                        this.dialogFormVisible = false;
                    } else {
                        return false;
                    }
                });
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();
            }
        },
        data() {
            // 表单验证
            var checkFee = (rule, value, callback) => {
                setTimeout(() => {
                    if (!Number.isInteger(value)) {
                        callback(new Error('请输入数字值'));
                    } else {
                        callback();
                    }
                }, 1000);
            };
            return {
                // 表单提交规则和数据
                ruleForm: {
                    fee: '',
                    remark: '',
                },
                rules: {
                    fee: [{ required: true, message: '必填项不可为空!', trigger: 'blur'}, { validator: checkFee, trigger: 'blur' }]
                },
                dialogFormVisible: false,
                // 表单查找和表单数据
                tableData: [],
                StatusArr:{
                    1:'申请中',
                    2:'填单提现完成',
                    3:'拒绝',
                    4:'待审核',
                    5:'待确认',
                    6:'办款中',
                    7:'提现成功',
                    8:'提现失败'
                },
                searchParams:{
                    date:'',
                    status:'',
                    page:1
                },
                TotalPage:0,
                // 申请提现输入框提示语句
                placeString:'',
            }
        }
    }
</script>