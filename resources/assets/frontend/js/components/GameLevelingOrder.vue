<style scoped>
    .ivu-form-item {
        width: 100%;
    }
</style>

<template>
    <div id="game-leveling-order-table">
        <Form  inline  :label-width="60" label-position="left" :model="searchParams">
            <Row :gutter="16">
                <Col span="6">
                    <FormItem label="订单号">
                        <Input placeholder="输入订单号" v-model="searchParams.order_no"></Input>
                    </FormItem>
                </Col>
                <Col span="6">
                    <FormItem label="玩家旺旺">
                        <Input style="width:100%" placeholder="输入玩家旺旺" v-model="searchParams.buyer_nick"></Input>
                    </FormItem>
                </Col>
                <Col span="6">
                    <FormItem label="代练游戏">
                        <Select>
                            <Option value="beijing">New York</Option>
                            <Option value="shanghai">London</Option>
                            <Option value="shenzhen">Sydney</Option>
                        </Select>
                    </FormItem>
                </Col>
                <Col span="6">
                    <FormItem label="代练类型">
                        <Select>
                            <Option value="beijing">New York</Option>
                            <Option value="shanghai">London</Option>
                            <Option value="shenzhen">Sydney</Option>
                        </Select>
                    </FormItem>
                </Col>
            </Row>
            <Row :gutter="16">
                <Col span="6">
                    <FormItem label="发单客服">
                        <Select>
                            <Option value="beijing">New York</Option>
                            <Option value="shanghai">London</Option>
                            <Option value="shenzhen">Sydney</Option>
                        </Select>
                    </FormItem>
                </Col>
                <Col span="6">
                    <FormItem label="发单平台">
                        <Select>
                            <Option value="beijing">New York</Option>
                            <Option value="shanghai">London</Option>
                            <Option value="shenzhen">Sydney</Option>
                        </Select>
                    </FormItem>
                </Col>
                <Col span="6">
                    <FormItem label="发单平台">
                        <DatePicker type="daterange" placement="bottom-end" placeholder="Select date" style="width: 100%"></DatePicker>
                    </FormItem>
                </Col>
                <Button type="primary" icon="ios-search" @click="search('searchParams')">搜索</Button>
                <Button type="primary" icon="ios-exit">导出</Button>

            </Row>

        </Form>
        <Tabs value="name1" size="small" @on-click="changeStatus">
            <TabPane :label="tabLabels.s1" name="1" ></TabPane>
            <TabPane label="未接单" name="2" @on-click="changeStatus"></TabPane>
            <TabPane label="代练中" name="3"></TabPane>
            <TabPane label="待验收" name="4"></TabPane>
            <TabPane label="撤销中" name="5"></TabPane>
            <TabPane label="淘宝退款中" name="6"></TabPane>
            <TabPane label="异常" name="7"></TabPane>
            <TabPane label="锁定" name="name8"></TabPane>
            <TabPane label="已撤销" name="name9"></TabPane>
            <TabPane label="已结算" name="name10"></TabPane>
            <TabPane label="已仲裁" name="name11"></TabPane>
            <TabPane label="已下架" name="name12"></TabPane>
            <TabPane label="已撤单" name="name13"></TabPane>
        </Tabs>
        <Table :loading="loading" border :height="tableHeight" :columns="tableColumns" :data="tableData"></Table>
        <div class="page"  style="margin-top: 10px">
            <Page :total="tableDataTotal"  show-elevator prev-text="上一页" next-text="下一页" :page-size=20 show-total @on-change="changePage" />
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'pageTitle',
            'gameLevelingOrderApi',
            'gameLevelingOrderDeleteApi',
        ],
        data() {
            return {
                loading: false,
                tabLabels: {
                    s1: (h) => {
                    return h('div', [
                    h('span', {
                        props: {
                            type: 'primary',
                            size: 'small'
                        },

                    }, '全部'),
                    h('Badge', {
                        style: {
                            marginLeft: '5px'
                        },
                        props: {
                            count: 3,
                            offset:[
                                2
                            ]
                        }
                    })
                ])
                    }
                },
                statusMap: {
                    s1: '未接单',
                    s13: '代练中',
                    s14: '待验收',
                    s15: '撤销中',
                    s16: '仲裁中',
                    s17: '异常',
                    s18: '已锁定',
                    s19: '已撤销',
                    s20: '已结算',
                    s21: '已仲裁',
                    s22: '已下架',
                    s23: '强制撤销',
                    s24: '已撤单',
                },
                tableHeight: 0,
                tableColumns:[
                    {
                        title: '订单号',
                        key: 'status',
                        width: 250,
                        fixed:'left',
                        render: (h, params) => {
                            return h('div', [
                                h('p', '渠道：' + params.row.status),
                                h('span', '平台：' + params.row.status)
                            ]);
                        }
                    },
                    {
                        title: '订单状态',
                        key: 'status',
                        width: 100,
                        render: (h, params) => {
                            return h('span', this.statusMap['s' + params.row.status]);
                        }
                    },
                    {
                        title: '玩家旺旺',
                        key: 'buyer_nick',
                        width: 100
                    },
                    {
                        title: '客服备注',
                        key: 'city',
                        width: 100
                    },
                    {
                        title: '代练标题',
                        key: 'title',
                        width: 200
                    },
                    {
                        title: '游戏/区/服',
                        key: 'zip',
                        width: 100,
                        render: (h, params) => {
                            return h('div', [
                                h('p',   params.row.game_name),
                                h('span', params.row.game_name)
                            ]);
                        }
                    },
                    {
                        title: '角色名称',
                        key: 'game_role',
                        width: 100
                    },
                    {
                        title: '账号/密码',
                        key: 'zip',
                        width: 100,
                        render: (h, params) => {
                            return h('div', [
                                h('p',   params.row.game_account),
                                h('span', params.row.game_password)
                            ]);
                        }
                    },
                    {
                        title: '代练价格',
                        key: 'amount',
                        width: 100
                    },
                    {
                        title: '效率/安全保证金',
                        key: 'zip',
                        width: 100,
                        render: (h, params) => {
                            return params.row.efficiency_deposit + '/' + params.row.security_deposit;
                        }
                    },
                    {
                        title: '发单/接单时间',
                        key: 'zip',
                        width: 100,
                        render: (h, params) => {
                            return params.row.created_at + '/' + params.row.take_at;
                        }
                    },
                    {
                        title: '代练时间',
                        key: 'zip',
                        width: 100,
                        render: (h, params) => {
                            return params.row.day + '天' + params.row.hour  + '小时';
                        }
                    },
                    {
                        title: '剩余时间',
                        key: 'zip',
                        width: 100
                    },
                    {
                        title: '打手QQ电话',
                        key: 'zip',
                        width: 100
                    },
                    {
                        title: '号主电话',
                        key: 'zip',
                        width: 100
                    },
                    {
                        title: '来源价格',
                        key: 'source_price',
                        width: 100
                    },
                    {
                        title: '支付代练费用',
                        key: 'zip',
                        width: 100
                    },
                    {
                        title: '获得赔偿金额',
                        key: 'zip',
                        width: 100
                    },
                    {
                        title: '手续费',
                        key: 'zip',
                        width: 100
                    },
                    {
                        title: '最终支付金额',
                        key: 'zip',
                        width: 100
                    },
                    {
                        title: '发单客服',
                        key: 'zip',
                        width: 100
                    },
                    {
                        title: '操作',
                        key: 'action',
                        width: 140,
                        fixed: 'right',
                        // align: 'center',
                        render: (h, params) => {
                            // 撤单 下架
                            if (params.row.status == 1) {
                                return h('div', [
                                    h('Button', {
                                        props: {
                                            type: 'primary',
                                            size: 'small'
                                        },
                                        style: {
                                            marginRight: '10px'
                                        },
                                        on: {
                                            click: () => {
                                                this.delete(params.index)
                                            }
                                        }
                                    }, '撤单'),
                                    h('Button', {
                                        props: {
                                            size: 'small',
                                        },
                                        on: {
                                            click: () => {
                                                this.offSale(params.index)
                                            }
                                        }
                                    }, '下架')
                                ]);
                            }
                            // 撤销 申请仲裁
                            if (params.row.status == 13) {
                                return h('div', [
                                    h('Button', {
                                        props: {
                                            type: 'primary',
                                            size: 'small'
                                        },
                                        style: {
                                            marginRight: '10px'
                                        },
                                        on: {
                                            click: () => {
                                                this.applyConsult(params.index)
                                            }
                                        }
                                    }, '撤销'),
                                    h('Button', {
                                        props: {
                                            size: 'small',
                                        },
                                        style: {
                                            display:(params.row.status == 2) ? "none" : "inline-block",
                                        },
                                        on: {
                                            click: () => {
                                                this.applyComplain(params.index)
                                            }
                                        }
                                    }, '申请仲裁')
                                ]);
                            }
                            // 查看图片 完成验收
                            if (params.row.status == 14) {
                                return h('div', [
                                    h('Button', {
                                        props: {
                                            type: 'primary',
                                            size: 'small'
                                        },
                                        style: {
                                            marginRight: '10px'
                                        },
                                        on: {
                                            click: () => {
                                                this.applyCompleteImage(params.index)
                                            }
                                        }
                                    }, '查看图片'),
                                    h('Button', {
                                        props: {
                                            size: 'small',
                                        },
                                        on: {
                                            click: () => {
                                                this.complete(params.index)
                                            }
                                        }
                                    }, '完成验收')
                                ]);
                            }
                            // 取消撤销/同意撤销 申请仲裁
                            if (params.row.status == 15) {
                                return h('div', [
                                    h('Button', {
                                        props: {
                                            type: 'primary',
                                            size: 'small'
                                        },
                                        style: {
                                            marginRight: '10px'
                                        },
                                        on: {
                                            click: () => {
                                                this.show(params.index)
                                            }
                                        }
                                    }, '撤单'),
                                    h('Button', {
                                        props: {
                                            size: 'small',
                                        },
                                        on: {
                                            click: () => {
                                                this.applyComplain(params.index)
                                            }
                                        }
                                    }, '申请仲裁')
                                ]);
                            }
                            // 取消仲裁  同意撤销
                            if (params.row.status == 16) {
                                return h('div', [
                                    h('Button', {
                                        props: {
                                            type: 'primary',
                                            size: 'small'
                                        },
                                        style: {
                                            marginRight: '10px'
                                        },
                                        on: {
                                            click: () => {
                                                this.cancelComplain(params.index)
                                            }
                                        }
                                    }, '取消仲裁'),
                                    h('Button', {
                                        props: {
                                            size: 'small',
                                        },
                                        on: {
                                            click: () => {
                                                this.agreeConsult(params.index)
                                            }
                                        }
                                    }, '同意撤销')
                                ]);
                            }
                            // 锁定  撤销
                            if (params.row.status == 17) {
                                return h('div', [
                                    h('Button', {
                                        props: {
                                            type: 'primary',
                                            size: 'small'
                                        },
                                        style: {
                                            marginRight: '10px'
                                        },
                                        on: {
                                            click: () => {
                                                this.lock(params.index)
                                            }
                                        }
                                    }, '锁定'),
                                    h('Button', {
                                        props: {
                                            size: 'small',
                                        },
                                        on: {
                                            click: () => {
                                                this.cancelLock(params.index)
                                            }
                                        }
                                    }, '撤销')
                                ]);
                            }
                            // 取消锁定  撤销
                            if (params.row.status == 18) {
                                return h('div', [
                                    h('Button', {
                                        props: {
                                            type: 'primary',
                                            size: 'small'
                                        },
                                        style: {
                                            marginRight: '10px'
                                        },
                                        on: {
                                            click: () => {
                                                this.show(params.index)
                                            }
                                        }
                                    }, '取消锁定'),
                                    h('Button', {
                                        props: {
                                            size: 'small',
                                        },
                                        on: {
                                            click: () => {
                                                this.remove(params.index)
                                            }
                                        }
                                    }, '撤销')
                                ]);
                            }
                            // 上架 撤单
                            if (params.row.status == 22) {
                                return h('div', [
                                    h('Button', {
                                        props: {
                                            type: 'primary',
                                            size: 'small'
                                        },
                                        style: {
                                            marginRight: '10px'
                                        },
                                        on: {
                                            click: () => {
                                                this.show(params.index)
                                            }
                                        }
                                    }, '上架'),
                                    h('Button', {
                                        props: {
                                            size: 'small',
                                        },
                                        on: {
                                            click: () => {
                                                this.remove(params.index)
                                            }
                                        }
                                    }, '撤单')
                                ]);
                            }
                            // 重发
                            if (params.row.status == 19 || 20 || 21 || 23 || 24) {
                                return h('div', [
                                    h('Button', {
                                        props: {
                                            type: 'primary',
                                            size: 'small'
                                        },
                                        on: {
                                            click: () => {
                                                this.show(params.index)
                                            }
                                        }
                                    }, '重发')
                                ]);
                            }
                        }
                    }
                ],
                tableData:[],
                searchParams:{
                    status:'',
                    order_no:'',
                    buyer_nick:'',
                    game_id:'',
                    game_leveling_type_id:'',
                    user_id:'',
                    platform_id:'',
                    start_created_at:'',
                    end_created_at:'',
                    page:1,
                },
                tableDataTotal:0,
            }
        },
        methods: {
            setPageTitle() {
                this.$store.commit('setPageTitle',{pageTitle:this.pageTitle})
            },
            setTableHeight() {
                this.tableHeight = window.innerHeight - 380;
            },
            getTableData(){
                this.loading = true;
                axios.post(this.gameLevelingOrderApi, this.searchParams).then(res => {
                    this.tableData = res.data.data;
                    this.tableDataTotal = res.data.total;
                    this.loading = false;
                }).catch(err => {
                    this.$Message.error('获取数据失败, 请重试!');
                });
            },
            search(){
                this.getTableData();
            },
            changePage(page){
                this.searchParams.page = page;
                this.getTableData();
            },
            changeStatus(status) {
                this.searchParams.status = status;
                this.getTableData();
            },
            // 查看订单
            show(index) {
                this.$Modal.info({
                    title: 'User Info',
                    content: `Name：${this.tableData[index].status}<br>Age：${this.tableData[index].title}<br>Address：${this.tableData[index].amount}`
                })
            },
            // 撤单
            delete(index) {
                this.$Modal.confirm({
                    title: '操作提示',
                    content: '您确定要"撤单"吗？',
                    loading: true,
                    onOk: () => {
                        this.$Modal.remove();

                        axios.post(this.gameLevelingOrderDeleteApi, {
                            'trade_no' : this.tableData[index].trade_no
                        }).then(res => {
                            // this.getTableData();
                        }).catch(err => {
                            // this.loading = false;

                            this.$customModal('sss');

                            // this.$Modal.confirm({
                            //     content:'操作失败请稍后再试',
                            //     // loading: true,
                            //     onOk: () => {
                            //         this.$Modal.remove();
                            //     }
                            // });

                            // this.$Message.error('操作失败请稍后再试！');
                        });

                    },
                    onCancel: () => {
                        this.$test('sss');
                        this.$Message.info('Clicked cancel');
                    }
                });
            },
            // 上架
            onSale(index) {

            },
            // 下架
            offSale(index) {
                this.$Modal.confirm({
                    title: '操作提示',
                    content: '您确定要"下架"吗？',
                    onOk: () => {
                        this.tableData.splice(index, 1);
                        this.getTableData();
                    },
                    onCancel: () => {
                        // this.$test('sss');
                        this.$Message.info('Clicked cancel');
                    }
                });
            },
            // 申请仲裁
            applyComplain(index) {
                this.$test('sss');
            },
            // 取消仲裁
            cancelComplain(index) {
                this.$Modal.confirm({
                    title: '操作提示',
                    content: '您确定要"取消仲裁"吗？',
                    onOk: () => {
                        this.tableData.splice(index, 1);
                        this.getTableData();
                    },
                    onCancel: () => {
                        // this.$test('sss');
                        this.$Message.info('Clicked cancel');
                    }
                });
            },
            // 查看图片
            applyCompleteImage(index) {

            },
            // 完成验收
            complete(index) {
                this.$Modal.confirm({
                    title: '操作提示',
                    content: '您确定要"完成验收"吗？',
                    onOk: () => {
                        this.tableData.splice(index, 1);
                        this.getTableData();
                    },
                    onCancel: () => {
                        // this.$test('sss');
                        this.$Message.info('Clicked cancel');
                    }
                });
            },
            // 申请撤销
            applyConsult(index) {

            },
            // 取消撤销
            cancelConsult(index) {
                this.$Modal.confirm({
                    title: '操作提示',
                    content: '您确定要"取消撤销"吗？',
                    onOk: () => {
                        this.tableData.splice(index, 1);
                        this.getTableData();
                    },
                    onCancel: () => {
                        // this.$test('sss');
                        this.$Message.info('Clicked cancel');
                    }
                });
            },
            // 同意撤销
            agreeConsult(index) {
                this.$Modal.confirm({
                    title: '操作提示',
                    content: '您确定要"同意撤销"吗？',
                    onOk: () => {
                        this.tableData.splice(index, 1);
                        this.getTableData();
                    },
                    onCancel: () => {
                        // this.$test('sss');
                        this.$Message.info('Clicked cancel');
                    }
                });
            },
            // 不同意撤销
            refuseConsult(index) {
                this.$Modal.confirm({
                    title: '操作提示',
                    content: '您确定要"不同意撤销"吗？',
                    onOk: () => {
                        this.tableData.splice(index, 1);
                        this.getTableData();
                    },
                    onCancel: () => {
                        // this.$test('sss');
                        this.$Message.info('Clicked cancel');
                    }
                });
            },
            // 锁定
            lock(index) {
                this.$Modal.confirm({
                    title: '操作提示',
                    content: '您确定要"锁定"吗？',
                    onOk: () => {
                        this.tableData.splice(index, 1);
                        this.getTableData();
                    },
                    onCancel: () => {
                        // this.$test('sss');
                        this.$Message.info('Clicked cancel');
                    }
                });
            },
            // 取消锁定
            cancelLock(index) {
                this.$Modal.confirm({
                    title: '操作提示',
                    content: '您确定要"取消锁定"吗？',
                    onOk: () => {
                        this.tableData.splice(index, 1);
                        this.getTableData();
                    },
                    onCancel: () => {
                        // this.$test('sss');
                        this.$Message.info('Clicked cancel');
                    }
                });
            },
            // 重发
            repeat(index) {

            },
            remove (index) {
                this.$Modal.confirm({
                    title: '操作提示',
                    content: '您确定要删除吗？',
                    onOk: () => {
                        this.tableData.splice(index, 1);
                        this.getTableData();
                    },
                    onCancel: () => {
                        this.$test('sss');
                        this.$Message.info('Clicked cancel');
                    }
                });
            },
        },
        created() {
            this.setPageTitle();
            this.setTableHeight();
            this.getTableData();
            window.addEventListener('resize', this.setTableHeight);
        },
        destroyed() {
            window.removeEventListener('resize', this.setTableHeight)
        },
    }
</script>
