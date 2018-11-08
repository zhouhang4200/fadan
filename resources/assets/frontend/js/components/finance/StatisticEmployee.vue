<template>
    <div class="main content">
        <el-form :inline="true" :model="searchParams" class="search-form-inline" size="small">
            <el-row :gutter="16">
                <el-col :span="4">
                    <el-form-item label="姓名">
                        <el-select v-model="searchParams.username" placeholder="请选择">
                            <el-option v-for="result of UserArr" v-bind:value="result.id" :key="result.id" :label="result.username"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
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
                id="employee"
                :data="tableData"
                border
                style="width: 100%; margin-top: 1px">
            <el-table-column
                    prop="username"
                    label="员工"
                    width="300">
            </el-table-column>
            <el-table-column
                    prop="all_count"
                    label="发布数量"
                    width="300">
            </el-table-column>
            <el-table-column
                    prop="all_original_price"
                    label="来源价格"
                    width="300">
            </el-table-column>
            <el-table-column
                    prop="all_price"
                    label="发布价格"
                    width="300">
            </el-table-column>
            <el-table-column
                    prop="subtract_price"
                    label="来源/发布差价"
                    width="">
            </el-table-column>
        </el-table>
        <el-pagination
                style="margin-top: 20px"
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
        // 初始化数据
        created () {
            this.handleTableData();
            this.StatisticEmployeeUser();
        },
        methods:{
            // 导出
            exportExcel () {
                /* generate workbook object from table */
                let wb = XLSX.utils.table_to_book(document.querySelector('#employee'));
                /* get binary string as output */
                let wbout = XLSX.write(wb, { bookType: 'xlsx', bookSST: true, type: 'array' });
                try {
                    FileSaver.saveAs(new Blob([wbout], { type: 'application/octet-stream' }), '员工统计.xlsx');
                } catch (e)
                {
                    if (typeof console !== 'undefined')
                        console.log(e, wbout)
                }
                return wbout
            },
            // 表格加载数据
            handleTableData(){
                this.$api.StatisticEmployeeDataList(this.searchParams).then(res => {
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
            // 所有的员工
            StatisticEmployeeUser() {
                this.$api.StatisticEmployeeUser().then(res => {
                    this.UserArr = res;

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
                // 表单查找和表单数据
                tableData: [],
                UserArr:[],
                searchParams:{
                    date:'',
                    username:'',
                    page:1,
                },
                TotalPage:0,
            }
        }
    }
</script>