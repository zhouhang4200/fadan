@extends('frontend.v1.layouts.app')

@section('title', '工作台-代练订单')

@section('css')
    <link rel="stylesheet" href="//unpkg.com/iview/dist/styles/iview.css">
@endsection

@section('main')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div id="app">

                <div class="layui-card-body">

                    <i-form ref="formInline" :model="formInline" :rules="ruleInline" inline>
                        <form-item prop="user">
                            <i-input type="text" v-model="formInline.user" placeholder="Username">
                                <icon type="ios-person-outline" slot="prepend"></icon>
                            </i-input>
                        </form-item>
                        <form-item prop="password">
                            <i-input type="password" v-model="formInline.password" placeholder="Password">
                                <icon type="ios-lock-outline" slot="prepend"></icon>
                            </i-input>
                        </form-item>
                        <form-item>
                            <i-button type="primary" @click="handleSubmit('formInline')">Signin</i-button>
                        </form-item>
                    </i-form>

                    <i-table :height="tableHeight" border :columns="columns2" :data="data4"></i-table>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.bootcss.com/vue/2.5.16/vue.js"></script>
    <script src="https://unpkg.com/iview/dist/iview.min.js"></script>
    <script>

        new Vue({
            el: '#app',
            data:
                {
                    formInline: {
                        user: '',
                        password: ''
                    },
                    ruleInline: {
                        user: [
                            { required: true, message: 'Please fill in the user name', trigger: 'blur' }
                        ],
                        password: [
                            { required: true, message: 'Please fill in the password.', trigger: 'blur' },
                            { type: 'string', min: 6, message: 'The password length cannot be less than 6 bits', trigger: 'blur' }
                        ]
                    },
                    tableHeight:0,
                    columns2: [
                        {
                            title: '订单号',
                            key: 'name',
                            width: 300,
                            fixed:left,
                            render: (h, params) => {
                                return h('div', [
                                    h('p', '渠道：' + params.row.name),
                                    h('span', '平台：' + params.row.name)
                                ]);
                            }
                        },
                        {
                            title: '订单状态',
                            key: 'age',
                            width: 100,

                        },
                        {
                            title: '玩家旺旺',
                            key: 'province',
                            width: 100
                        },
                        {
                            title: '客服备注',
                            key: 'city',
                            width: 100
                        },
                        {
                            title: '代练标题',
                            key: 'address',
                            width: 200
                        },
                        {
                            title: '游戏/区/服',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '角色名称',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '账号/密码',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '代练价格',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '效率/安全保证金',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '发单/接单时间',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '代练时间',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '剩余时间',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '打手QQ电话',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '号主电话',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '来源价格',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '支付代练费用',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '获得赔偿金额',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '手续费',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '最终支付金额',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '发单客服',
                            key: 'zip',
                            width: 100
                        },
                        {   title: '操作',
                            key: 'zip',
                            width: 100,
                            fixed: 'right'
                        }
                    ],
                    data4: [
                        {
                            name: '1',
                            age: 2,
                            address: '2',
                            province: '3',
                            city: '4',
                            5: 5,
                            6: 100000,
                            7: 100000,
                            8: 100000,
                            9: 100000,
                            10: 100000,
                            11: 100000,
                            12: 100000,
                            13: 100000,
                            14: 100000,
                            15: 100000,
                            16: 100000,
                            17: 100000,
                            18: 100000,
                            19: 100000,
                            20: 100000,
                            21: 100000
                        }

                    ]
                },
            methods: {
                getHeight() {
                    this.tableHeight = window.innerHeight - 340;
                }
            },
            created() {
                window.addEventListener('resize', this.getHeight);
                this.getHeight()
            },
            destroyed() {
                window.removeEventListener('resize', this.getHeight)
            }

        });
    </script>

@endSection
