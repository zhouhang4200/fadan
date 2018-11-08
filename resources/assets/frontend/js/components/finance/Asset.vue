<template>
    <div class="main content ">
        <el-table
                :data="tableData"
                border
                style="width: 100%">
            <el-table-column
                    prop="name"
                    label="名称"
                    width="100">
            </el-table-column>
            <el-table-column
                    prop="amount"
                    label="金额"
                    width="200">
            </el-table-column>
        </el-table>
    </div>
</template>

<script>
    export default {
        created () {
            this.handleTableData();
        },
        methods:{
            // 加载数据
            handleTableData(){
                this.$api.FinanceMyAssetDataList().then(res => {
                    this.tableData = res;
                }).catch(err => {
                    this.$alert('获取数据失败, 请重试!', '提示', {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                });
            },
        },
        data() {
            return {
                tableData: []
            }
        },
        mounted() {
            this.$cookieStore.setCookie('menu', '2');
            this.$cookieStore.setCookie('submenu', '2-1');
        },
    }
</script>