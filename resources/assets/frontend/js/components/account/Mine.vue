<template>
    <div class="main content amount-flow">
        <el-form ref="editForm" :model="editForm" label-width="80px">
            <el-form-item label="账号">
                <el-input v-model="editForm.name" :disabled="true"></el-input>
            </el-form-item>
            <el-form-item label="邮箱">
                <el-input v-model="editForm.email" :disabled="true" placeholder=""></el-input>
            </el-form-item>
            <el-form-item label="密码">
                <el-input v-model="editForm.password" placeholder="不填写则为原密码"></el-input>
            </el-form-item>
            <el-form-item label="代练">
                <template slot-scope="scope">
                    <el-radio v-model=editForm.type :label=1>接单</el-radio>
                    <el-radio v-model=editForm.type :label=2>发单</el-radio>
                </template>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="handleUpdate()">修改</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>

<script>
    export default {
        methods: {
            handleUpdate() {
                this.$api.AccountMineUpdate(this.editForm).then(res => {
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
            handleForm(){
                this.$api.AccountMineForm().then(res => {
                    this.editForm = res;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
        },
        created () {
            this.handleForm();
        },
        data() {
            return {
                dialogFormVisible: false,
                editForm: {
                    'name':'',
                    'email':'',
                    'type':'',
                    'password':''
                }
            }
        }
    }
</script>