<template>
    <div class="main content amount-flow">
        <template>
            <el-alert
                    style="margin-bottom: 15px"
                    title="操作提示: 编辑自动发送短信模板，可在打手接单/完成订单/提交验收/提交撤销/提交仲裁时自动发送短信提醒用户。"
                    type="success"
                    :closable="false">
            </el-alert>
            <el-table
                    :data="tableData"
                    :height="tableHeight"
                    border
                    style="width: 100%; margin-top: 1px">
                <el-table-column
                        prop="name"
                        label="短信名称"
                        width="150">
                </el-table-column>
                <el-table-column
                        prop="contents"
                        label="短信内容"
                        width="">
                </el-table-column>
                <el-table-column
                        prop="purpose"
                        label="发送场景"
                        width="150">
                    <template slot-scope="scope">
                        {{ purpose[scope.row.purpose] }}
                    </template>
                </el-table-column>
                <el-table-column
                        prop="status"
                        label="状态"
                        width="150">
                    <template slot-scope="scope">
                        <el-switch v-model=scope.row.status @change="handleSwitch($event, scope.row)" active-text="启用"
                                   inactive-text="禁用" :active-value=1 :inactive-value=0></el-switch>
                    </template>
                </el-table-column>
                <el-table-column
                        label="操作"
                        width="150">
                    <template slot-scope="scope">
                        <el-button
                                type="primary"
                                size="small"
                                @click="ShowEditForm(scope.row)">编辑</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <el-pagination
                    style="margin-top: 25px"
                    background
                    @current-change="handleCurrentChange"
                    :current-page.sync="searchParams.page"
                    :page-size="15"
                    layout="total, prev, pager, next, jumper"
                    :total="TotalPage">
            </el-pagination>
            <el-dialog title="短信模板编辑" :visible.sync="dialogFormVisible">
                <el-form :model="form" ref="form" :rules="rules" label-width="80px">
                    <el-form-item label="短信名称" prop="name">
                        <el-input v-model="form.name" name="name" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="短信内容" prop="contents">
                        <el-input v-model="form.contents" name="contents" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="submitForm('form')">确认修改</el-button>
                        <el-button @click="dialogFormVisible = false">取消</el-button>
                    </el-form-item>
                </el-form>
            </el-dialog>
        </template>
    </div>
</template>

<script>
    export default {
        methods: {
            // 编辑按钮
            ShowEditForm(row) {
                this.dialogFormVisible = true;
                this.form=JSON.parse(JSON.stringify(row));
            },
            // 修改
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.$api.SettingMessageUpdate(this.form).then(res => {
                            this.$message({
                                showClose: true,
                                type: res.status == 1 ? 'success' : 'error',
                                message: res.message
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
            // 加载数据
            handleTableData(){
                this.$api.SettingMessageDataList(this.searchParams).then(res => {
                    this.tableData = res.data;
                    this.TotalPage = res.total;
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
                this.$api.SettingMessageStatus({status:value, id:row.id}).then(res => {
                    this.$message({
                        showClose: true,
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
            // 表格高度计算
            handleTableHeight() {
                this.tableHeight = window.innerHeight - 318;
            },
        },
        created () {
            this.handleTableData();
            this.handleTableHeight();
            window.addEventListener('resize', this.handleTableHeight);
        },
        destroyed() {
            window.removeEventListener('resize', this.handleTableHeight);
        },
        data() {
            var checkHas = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('必填项不能为空!'));
                }
                callback();
            };
            return {
                tableHeight: 0,
                dialogFormVisible:false,
                rules:{
                    name:[{ required: true, message:'必填项不可为空!', trigger: 'blur' }],
                    contents:[{ required: true, message:'必填项不可为空!', trigger: 'blur' }],
                },
                tableData: [],
                purpose:{
                    1:'被接单提示',
                    2:'已完成提示',
                    3:'待验收提示',
                    4:'撤销中提示',
                    5:'仲裁中提示'
                },
                searchParams:{
                    page:1,
                },
                TotalPage:0,
                form:{
                    id:'',
                    name:'',
                    contents:''
                }
            }
        }
    }
</script>