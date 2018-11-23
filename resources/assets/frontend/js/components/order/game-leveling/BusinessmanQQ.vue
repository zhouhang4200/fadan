<template>

    <el-dialog
            title="添加商户联系QQ"
            width="60%"
            :visible=true
            :before-close="handleBeforeClose">
        <el-row :gutter="10">
            <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                <el-table
                        height="300"
                        border
                        :data="tableData">
                    <el-table-column
                            prop="name"
                            label="名称">
                    </el-table-column>

                    <el-table-column
                            prop="tag"
                            label="操作"
                            width="160">
                        <template slot-scope="scope">
                            <el-button type="primary" @click="handleEdit(scope.row)">修改</el-button>
                            <el-button type="danger" @click="handleDelete(scope.row)">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </el-col>
            <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                <el-form ref="form" :model="form" label-width="80px">
                    <el-form-item label="游戏" prop="game_id">
                        <el-select
                                filterable
                                v-model="form.game_id"
                                placeholder="请选择游戏">
                            <el-option
                                    label="通用模板"
                                    :value="0">
                            </el-option>
                            <el-option
                                    v-for="item in gameOptions"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="模版名" prop="name">
                        <el-input v-model="form.name"></el-input>
                    </el-form-item>

                    <el-form-item label="是否默认"  prop="status">
                        <el-switch
                                active-value="1"
                                inactive-value="0"
                                v-model="form.status">
                        </el-switch>
                    </el-form-item>

                    <el-form-item label="联系QQ" prop="content">
                        <el-input
                                v-model="form.content">
                        </el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-button type="primary" @click="handleSubmitForm">确定</el-button>
                        <el-button type="primary" @click="handleFormRest">清空</el-button>
                    </el-form-item>

                </el-form>
            </el-col>
        </el-row>
    </el-dialog>

</template>

<script>
    export default {
        name: "BusinessmanQQ",
        data() {
            return {
                dialogVisible: false,
                form: {
                    id: 0,
                    game_id: '',
                    name: '',
                    status: 0,
                    content: '',
                },
                gameOptions:[],
                tableData:[],
            };
        },
        methods: {
            handleBeforeClose() {
                this.$emit("handleBusinessmanQQVisible", {"visible":false});
            },
            handleSubmitForm() {
                this.$api.businessmanContactTemplateStore(this.form).then(res => {
                    if (res.status == 1) {
                        this.handleFormRest();
                        this.$message.success(res.message);
                        this.handleTableData();
                    }
                });
            },
            handleTableData() {
                this.$api.businessmanContactTemplate().then(res => {
                    this.tableData = res.data;
                });
            },
            handleGameOptions() {
                this.$api.games().then(res => {
                    this.gameOptions = res.data;
                });
            },
            handleDelete(row) {
                this.$confirm('此操作将永久删除, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$api.businessmanContactTemplateDelete({id:row.id}).then(res => {
                        if (res.status == 1) {
                            this.$message.success('删除成功');
                            this.handleTableData();
                        }
                    });
                }).catch(() => {

                });
            },
            handleEdit(row) {
                this.$api.businessmanContactTemplateShow({id:row.id}).then(res => {
                    this.form.id = res.data.id;
                    this.form.game_id = res.data.game_id;
                    this.form.name = res.data.name;
                    this.form.content = res.data.content;
                    this.form.status = res.data.status.toString();
                });
            },
            handleFormRest() {
                this.$refs.form.resetFields();
                this.form.id = 0;
            }
        },
        created() {
            this.handleTableData();
            this.handleGameOptions();
        }
    }
</script>

<style scoped>

</style>