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
        props: [
            'MyAssetApi',
        ],
        created () {
            this.handleTableData();
        },
        methods:{
            // 加载数据
            handleTableData(){
                axios.post(this.MyAssetApi).then(res => {
                    console.log(res);
                    this.tableData = res.data;
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
        }
    }
</script>