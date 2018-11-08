<template>
    <div class="main content">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">
                <el-col :span="4">
                    <el-form-item label="订单号">
                        <el-input v-model="searchParams.order_no"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="7">
                    <el-form-item label="发送手机">
                        <el-input v-model="searchParams.client_phone"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="handleSearch">查询</el-button>
                        <el-button type="primary" @click="exportExcel">导出</el-button>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
        <el-table
                id="order"
                :height="tableHeight"
                :data="tableData"
                border
                style="width: 100%; margin-top: 1px">
            <el-table-column
                    prop="foreign_order_no"
                    label="订单号"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="client_phone"
                    label="发送手机"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="content"
                    label="发送内容"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="date"
                    label="发送时间"
                    width="200">
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
    </div>
</template>

<script>
    import FileSaver from 'file-saver';
    import XLSX from 'xlsx';
    export default {
        // 初始化数据
        created () {
            this.handleTableData();
            this.handleTableHeight();
            window.addEventListener('resize', this.handleTableHeight);
        },
        destroyed() {
            window.removeEventListener('resize', this.handleTableHeight);
        },
        methods:{
            // 表格高度计算
            handleTableHeight() {
                this.tableHeight = window.innerHeight - 318;
            },
            // 导出
            exportExcel() {
                /* generate workbook object from table */
                let wb = XLSX.utils.table_to_book(document.querySelector('#order'));
                /* get binary string as output */
                let wbout = XLSX.write(wb, { bookType: 'xlsx', bookSST: true, type: 'array' });
                try {
                    FileSaver.saveAs(new Blob([wbout], { type: 'application/octet-stream' }), '短信统计.xlsx');
                } catch (e)
                {
                    if (typeof console !== 'undefined')
                        console.log(e, wbout)
                }
                return wbout;
            },
            // 表格加载数据
            handleTableData(){
                this.searchParams.date=this.$route.params.date;
                this.$api.StatisticMessageShowDataList(this.searchParams).then(res => {
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
            handleCurrentChange(page) {
                this.searchParams.page = page;
                this.handleTableData();
            },
            handleSearch() {
                this.handleTableData();
            },
        },
        data() {
            return {
                tableHeight: 0,
                // 表单查找和表单数据
                tableData: [],
                UserArr:[],
                searchParams:{
                    date:'',
                    order_no:'',
                    client_phone:'',
                    page:1
                },
                TotalPage:0,
            }
        }
    }
</script>