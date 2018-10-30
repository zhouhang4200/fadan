<template>
    <div class="main content amount-flow">
        <el-table
                :data="tableData"
                border
                style="width: 100%">
            <el-table-column
                    prop="id"
                    label="用户ID"
                    width="180">
            </el-table-column>
            <el-table-column
                    prop="name"
                    label="用户名"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="email"
                    label="邮箱"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="created_at"
                    label="注册时间">
            </el-table-column>
            <el-table-column
                    prop="order"
                    label="操作"
                    width="180">
                <template slot-scope="scope">
                    <el-button v-if="scope.row.id > 0"
                               type="primary"
                               size="small"
                               @click="edit(scope.row.id)">编辑</el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script>
    export default {
        props: [
            'AccountMineDataListApi',
        ],
        methods: {
            // 加载数据
            handleTableData(){
                axios.post(this.AccountMineDataListApi, {}).then(res => {
                    this.tableData = res.data;
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
        },
        data() {
            return {
                tableData: [],
            }
        }
    }
</script>