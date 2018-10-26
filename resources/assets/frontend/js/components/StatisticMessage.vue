<template>
    <div class="main content">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">
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
                    <el-button type="primary" @click="exportExcel">导出</el-button>
                </el-form-item>
            </el-row>
        </el-form>
        <el-table
                id="order"
                :data="tableData"
                border
                style="width: 100%">
            <el-table-column
                    prop="date"
                    label="发布时间"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="count"
                    label="发送条数"
                    width="">
            </el-table-column>
            <el-table-column
                    prop="id"
                    label="操作"
                    width="150">
                <template slot-scope="scope">
                        <el-button v-if="scope.row.id > 0"
                                type="primary"
                                size="small"
                                @click="show(scope.row.date)">详情</el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination
                @current-change="handleCurrentChange"
                :current-page.sync="searchParams.page"
                :page-size="15"
                layout="prev, pager, next, jumper"
                :total="TotalPage">
        </el-pagination>
    </div>
</template>

<script>
    import FileSaver from 'file-saver';
    import XLSX from 'xlsx';
    export default {
        props: [
            'StatisticMessageDataListApi',
            'StatisticMessageShowApi',
        ],
        // 初始化数据
        created () {
            this.handleTableData();
        },
        methods:{
            // 详情页
            show(date) {
                window.location.href=this.StatisticMessageShowApi+'?date='+date
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
                axios.post(this.StatisticMessageDataListApi, this.searchParams).then(res => {
                    this.tableData = res.data.data;
                    this.TotalPage = res.data.total;
                    console.log(res.data.total);
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