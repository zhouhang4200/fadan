<template>
    <div class="main content">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">
                <el-col :span="5">
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
                <el-col :span="4">
                    <el-form-item>
                        <el-button type="primary" @click="handleSearch">查询</el-button>
                        <el-button type="primary" @click="exportExcel">导出</el-button>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
        <el-table
                id="order"
                :data="tableData"
                :height="tableHeight"
                border
                style="width: 100%; margin-top: 1px">
            <el-table-column
                    prop="date"
                    label="发布时间"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="count"
                    label="发送条数"
                    width="200">
            </el-table-column>
            <el-table-column
                    prop="id"
                    label="操作"
                    width="200">
                <template slot-scope="scope">
                    <router-link :to="{name:'financeStatisticMessageShow', params: {date:scope.row.date}}">
                        <el-button type="primary" size="small" >详情</el-button>
                    </router-link>
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
            // 详情页
            show(date) {
                window.location.href='/v2/statistic/message-show?date='+date
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
                return wbout
            },
            // 表格加载数据
            handleTableData(){
                this.$api.StatisticMessageDataList(this.searchParams).then(res => {
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
                    page:1,
                },
                TotalPage:0,
            }
        }
    }
</script>