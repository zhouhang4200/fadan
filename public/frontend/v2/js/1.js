webpackJsonp([1],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanQQ.vue":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
    name: "BusinessmanQQ",
    data: function data() {
        return {
            dialogVisible: false,
            form: {
                id: 0,
                game_id: '',
                name: '',
                status: 0,
                content: ''
            },
            gameOptions: [],
            tableData: []
        };
    },

    methods: {
        handleBeforeClose: function handleBeforeClose() {
            this.$emit("handleBusinessmanQQVisible", { "visible": false });
        },
        handleSubmitForm: function handleSubmitForm() {
            var _this = this;

            this.$api.businessmanContactTemplateStore(this.form).then(function (res) {
                if (res.status == 1) {
                    _this.handleFormRest();
                    _this.$message.success(res.message);
                    _this.handleTableData();
                }
            });
        },
        handleTableData: function handleTableData() {
            var _this2 = this;

            this.$api.businessmanContactTemplate().then(function (res) {
                _this2.tableData = res.data;
            });
        },
        handleGameOptions: function handleGameOptions() {
            var _this3 = this;

            this.$api.games().then(function (res) {
                _this3.gameOptions = res.data;
            });
        },
        handleDelete: function handleDelete(row) {
            var _this4 = this;

            this.$confirm('此操作将永久删除, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this4.$api.businessmanContactTemplateDelete({ id: row.id }).then(function (res) {
                    if (res.status == 1) {
                        _this4.$message.success('删除成功');
                        _this4.handleTableData();
                    }
                });
            }).catch(function () {});
        },
        handleEdit: function handleEdit(row) {
            var _this5 = this;

            this.$api.businessmanContactTemplateShow({ id: row.id }).then(function (res) {
                _this5.form.id = res.data.id;
                _this5.form.game_id = res.data.game_id;
                _this5.form.name = res.data.name;
                _this5.form.content = res.data.content;
                _this5.form.status = res.data.status.toString();
            });
        },
        handleFormRest: function handleFormRest() {
            this.$refs.form.resetFields();
            this.form.id = 0;
        }
    },
    created: function created() {
        this.handleTableData();
        this.handleGameOptions();
    }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/GameLevelingRequirement.vue":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
    name: "GameLevelingRequirement",
    data: function data() {
        return {
            dialogVisible: false,
            form: {
                id: 0,
                game_id: '',
                name: '',
                status: 0,
                content: ''
            },
            gameOptions: [],
            tableData: []
        };
    },

    methods: {
        handleBeforeClose: function handleBeforeClose() {
            this.$emit("handleGameLevelingRequirementVisible", { "visible": false });
        },
        handleSubmitForm: function handleSubmitForm() {
            var _this = this;

            this.$api.gameLevelingRequirementTemplateStore(this.form).then(function (res) {
                if (res.status == 1) {
                    _this.handleFormRest();
                    _this.$message.success(res.message);
                    _this.handleTableData();
                }
            });
        },
        handleTableData: function handleTableData() {
            var _this2 = this;

            this.$api.gameLevelingRequirementTemplate().then(function (res) {
                _this2.tableData = res.data;
            });
        },
        handleGameOptions: function handleGameOptions() {
            var _this3 = this;

            this.$api.games().then(function (res) {
                _this3.gameOptions = res.data;
            });
        },
        handleDelete: function handleDelete(row) {
            var _this4 = this;

            this.$confirm('此操作将永久删除, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this4.$api.gameLevelingRequirementTemplateDelete({ id: row.id }).then(function (res) {
                    if (res.status == 1) {
                        _this4.$message.success('删除成功');
                        _this4.handleTableData();
                    }
                });
            }).catch(function () {});
        },
        handleEdit: function handleEdit(row) {
            var _this5 = this;

            this.$api.gameLevelingRequirementTemplateShow({ id: row.id }).then(function (res) {
                _this5.form.id = res.data.id;
                _this5.form.game_id = res.data.game_id;
                _this5.form.name = res.data.name;
                _this5.form.content = res.data.content;
                _this5.form.status = res.data.status.toString();
            });
        },
        handleFormRest: function handleFormRest() {
            this.$refs.form.resetFields();
            this.form.id = 0;
        }
    },
    created: function created() {
        this.handleTableData();
        this.handleGameOptions();
    }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/Repeat.vue":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__GameLevelingRequirement__ = __webpack_require__("./resources/assets/frontend/js/components/order/game-leveling/GameLevelingRequirement.vue");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__GameLevelingRequirement___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__GameLevelingRequirement__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__BusinessmanQQ__ = __webpack_require__("./resources/assets/frontend/js/components/order/game-leveling/BusinessmanQQ.vue");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__BusinessmanQQ___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__BusinessmanQQ__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//




/* harmony default export */ __webpack_exports__["default"] = ({
    name: "GameLevelingRepeat",
    components: {
        GameLevelingRequirement: __WEBPACK_IMPORTED_MODULE_0__GameLevelingRequirement___default.a,
        BusinessmanQQ: __WEBPACK_IMPORTED_MODULE_1__BusinessmanQQ___default.a
    },
    computed: {
        displayFooter: function displayFooter() {
            var status = [19, 20, 21, 22, 23, 24];
            if (this.tabCurrent == "1" && status.indexOf(this.form.status) == -1) {
                return true;
            } else if (status.indexOf(this.form.status) != -1) {
                return false;
            } else {
                return false;
            }
        }
    },
    data: function data() {
        var isPhone = function isPhone(rule, value, callback) {
            var phone = /^1[3|4|5|7|8][0-9]\d{8}$/;
            if (!phone.test(value)) {
                callback(new Error('请填写正确的手机号！'));
            } else {
                callback();
            }
        };
        var mustOverZero = function mustOverZero(rule, value, callback) {
            var isNumber = /^([1-9]\d*|0)(\.\d{1,2})?$/;
            if (value && !isNumber.test(value)) {
                callback(new Error('请输入大于0的数字值，支持2位小数!'));
            } else {
                callback();
            }
        };
        var overZeroInt = function overZeroInt(rule, value, callback) {
            var isNumber = /^[1-9]\d*$/;
            if (value && !isNumber.test(value)) {
                callback(new Error('请输入大于0的整数值!'));
            } else {
                callback();
            }
        };
        return {
            tradeNo: this.$route.query.trade_no,
            gameLevelingRequirementVisible: false,
            gameLevelingRequirementOptions: [],
            businessmanQQVisible: false,
            businessmanQQOptions: [],
            dayOptions: [],
            hourOptions: [],
            gameRegionServerOptions: [], // 游戏/区/服 选项
            dayHourOptions: [],
            gameLevelingTypeOptions: [], // 游戏代练类型 选项
            form: {
                trade_no: '',
                status: 0,
                channel_order_trade_no: '',
                game_leveling_order_consult: [],
                game_leveling_order_complain: [],
                game_region_server: [], // 选择的 游戏/区/服
                day_hour: [], // 选择的代练天/小时
                game_id: 0, // 游戏ID
                game_region_id: 0, // 游戏区ID
                game_server_id: 0, // 游戏服务器ID
                game_leveling_type_id: '', // 代练类型ID
                amount: '', // 代练金额
                source_amount: '', // 来源价格
                security_deposit: '', // 安全保证金
                efficiency_deposit: '', // 效率保证金
                title: '', //代练标题
                game_role: '', // 游戏角色
                game_account: '', // 游戏账号
                game_password: '', // 游戏密码
                price_increase_step: '', // 自动加价步长
                price_ceiling: '', // 自动加价上限
                explain: '', // 代练说明
                requirement: '', // 代练要求
                take_order_password: '', // 接单密码
                player_phone: '', // 玩家电话
                user_qq: '', // 商户qq
                domains: [],
                remark: '',
                day: 0,
                hour: 1,
                gameLevelingRequirementId: ''
            },
            rules: {
                game_leveling_type_id: [{ required: true, message: '请选择代练类型', trigger: 'change' }],
                game_role: [{ required: true, message: '请输入游戏角色', trigger: 'blur' }],
                game_account: [{ required: true, message: '请输入游戏账号', trigger: 'change' }],
                game_password: [{ required: true, message: '请输入游戏密码', trigger: 'change' }],
                title: [{ required: true, message: '请输入代练标题', trigger: 'change' }, { min: 3, max: 35, message: '长度在 3 到 35 个字符', trigger: 'change' }],
                day_hour: [{ type: 'array', required: true, message: '请选择代练天/小时', trigger: 'change' }],
                game_region_server: [{ type: 'array', required: true, message: '请选择游戏/区/服', trigger: 'change' }],
                explain: [{ required: true, message: '请输入代练说明', trigger: 'change' }],
                requirement: [{ required: true, message: '请输入代练要求', trigger: 'change' }],
                amount: [{ required: true, message: '请输入代练价格', trigger: 'change' }, { validator: mustOverZero, trigger: 'blur' }],
                source_amount: [{ validator: mustOverZero, trigger: 'blur' }],
                efficiency_deposit: [{ required: true, message: '请输入效率保证金', trigger: 'change' }, { validator: mustOverZero, trigger: 'blur' }],
                security_deposit: [{ required: true, message: '请输入安全保证金', trigger: 'change' }, { validator: mustOverZero, trigger: 'blur' }],
                user_qq: [{ required: true, message: '请输入商户QQ号', trigger: 'change' }],
                player_phone: [{ required: true, message: '请输入玩家电话', trigger: 'blur' }, { validator: isPhone, trigger: 'blur' }],
                price_increase_step: [{ validator: overZeroInt, trigger: 'blur' }],
                price_ceiling: [{ validator: mustOverZero, trigger: 'blur' }]

            },
            taobaoData: []
        };
    },

    methods: {
        // 设置添加代练要求模版是否显示
        handleGameLevelingRequirementVisible: function handleGameLevelingRequirementVisible(data) {
            this.gameLevelingRequirementVisible = data.visible;
            if (data.visible == false) {}
        },

        // 设置添加商户QQ是否显示
        handleBusinessmanQQVisible: function handleBusinessmanQQVisible(data) {
            this.businessmanQQVisible = data.visible;
            if (data.visible == false) {}
        },
        handleFromData: function handleFromData() {
            var _this = this;

            this.$api.gameLevelingOrderEdit({ trade_no: this.tradeNo }).then(function (res) {
                _this.form.status = res.status;
                _this.form.channel_order_trade_no = res.channel_order_trade_no;
                _this.form.game_leveling_order_consult = res.game_leveling_order_consult;
                _this.form.game_leveling_order_complain = res.game_leveling_order_complain;
                _this.form.game_region_server = [// 选择的 游戏/区/服
                res.game_id, res.game_region_id, res.game_server_id];
                _this.handleFromGameLevelingTypeIdOptions();
                _this.form.day_hour = [// 选择的代练天/小时
                res.day, res.hour];
                _this.form.game_id = res.game_id; // 游戏ID
                _this.form.game_region_id = res.game_region_id; // 游戏区ID
                _this.form.game_server_id = res.game_server_id; // 游戏服务器ID
                _this.form.game_leveling_type_id = res.game_leveling_type_id; // 代练类型ID
                _this.form.amount = res.amount; // 代练金额
                _this.form.source_amount = res.source_amount !== '0.00' ? res.source_amount : ''; // 来源价格
                _this.form.security_deposit = res.security_deposit; // 安全保证金
                _this.form.efficiency_deposit = res.efficiency_deposit; // 效率保证金
                _this.form.title = res.title; //代练标题
                _this.form.game_role = res.game_role; // 游戏角色
                _this.form.game_account = res.game_account; // 游戏账号
                _this.form.game_password = res.game_password; // 游戏密码
                _this.form.price_increase_step = res.price_increase_step !== '0.00' ? res.price_increase_step : ''; // 自动加价步长
                _this.form.price_ceiling = res.price_ceiling !== '0.00' ? res.price_ceiling : ''; // 自动加价上限
                _this.form.explain = res.game_leveling_order_detail.explain; // 代练说明
                _this.form.requirement = res.game_leveling_order_detail.requirement; // 代练要求
                _this.form.take_order_password = res.take_order_password; // 接单密码
                _this.form.player_phone = res.game_leveling_order_detail.player_phone; // 玩家电话
                _this.form.user_qq = res.game_leveling_order_detail.user_qq; // 商家qq
                _this.form.remark = res.remark;
                _this.form.domains = [];

                _this.taobaoData = [{
                    name: '店铺名',
                    value: res.taobao_data.seller_nick
                }, {
                    name: '天猫单号',
                    value: res.taobao_data.tid
                }, {
                    name: '订单状态',
                    value: res.taobao_data.trade_status
                }, {
                    name: '买家旺旺',
                    value: res.taobao_data.buyer_nick
                }, {
                    name: '购买单价',
                    value: res.taobao_data.price
                }, {
                    name: '购买数量',
                    value: res.taobao_data.num
                }, {
                    name: '实付金额',
                    value: res.taobao_data.payment
                }, {
                    name: '所在区/服',
                    value: res.taobao_data.region_server
                }, {
                    name: '角色名称',
                    value: res.taobao_data.role
                }, {
                    name: '买家留言',
                    value: res.taobao_data.buyer_message
                }, {
                    name: '下单时间',
                    value: res.taobao_data.created
                }];
            }).catch(function (err) {});
        },
        handleFromGameRegionServerOptions: function handleFromGameRegionServerOptions() {
            var _this2 = this;

            this.$api.gameRegionServer().then(function (res) {
                _this2.gameRegionServerOptions = res.data;
            }).catch(function (err) {});
        },
        handleFromGameLevelingTypeIdOptions: function handleFromGameLevelingTypeIdOptions(val) {
            var _this3 = this;

            this.$api.gameLevelingTypes({
                'game_id': this.form.game_region_server[2]
            }).then(function (res) {
                _this3.gameLevelingTypeOptions = res.data;
            }).catch(function (err) {});
            this.handleAutoChoseTemplate();
        },
        handleSubmitForm: function handleSubmitForm(formName) {
            var _this4 = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this4.form.game_id = _this4.form.game_region_server[0];
                    _this4.form.game_region_id = _this4.form.game_region_server[1];
                    _this4.form.game_server_id = _this4.form.game_region_server[2];
                    _this4.$api.gameLevelingOrderCreate(_this4.form).then(function (res) {
                        _this4.$message({
                            'type': res.status == 1 ? 'success' : 'error',
                            'message': res.message
                        });
                    }).catch(function (err) {
                        _this4.$message({
                            'type': 'error',
                            'message': '重新下单失败，服务器错误！'
                        });
                    });
                }
            });
        },
        handleResetForm: function handleResetForm(formName) {
            this.$refs[formName].resetFields();
        },
        handleDayOption: function handleDayOption() {
            for (var i = 0; i <= 90; i++) {
                this.dayOptions.push({
                    value: i,
                    label: i + '天'
                });
            }
        },
        handleHourOption: function handleHourOption() {
            for (var i = 0; i <= 24; i++) {
                this.hourOptions.push({
                    value: i,
                    label: i + '小时'
                });
            }
        },

        // 商户QQ选项
        businessmanQQOption: function businessmanQQOption() {
            var _this5 = this;

            this.$api.businessmanContactTemplate().then(function (res) {
                _this5.businessmanQQOptions = res.data;
            });
        },

        // 游戏代练要求选项
        gameLevelingRequirementOption: function gameLevelingRequirementOption() {
            var _this6 = this;

            this.$api.gameLevelingRequirementTemplate().then(function (res) {
                _this6.gameLevelingRequirementOptions = res.data;
            });
        },

        // 选择游戏后自动选择对应的代练模板与联系QQ
        handleAutoChoseTemplate: function handleAutoChoseTemplate() {
            var vm = this;
            this.businessmanQQOptions.forEach(function (item) {
                if (item.game_id === 0 && item.status === 1) {
                    vm.form.user_qq = item.content;
                }
                if (item.game_id === vm.form.game_region_server[0]) {
                    vm.form.user_qq = item.content;
                }
                if (item.game_id === vm.form.game_region_server[0] && item.status === 1) {
                    vm.form.user_qq = item.content;
                }
            });
            this.gameLevelingRequirementOptions.forEach(function (item) {

                if (item.game_id === 0 && item.status === 1) {
                    vm.form.gameLevelingRequirementId = item.id;
                }
                if (item.game_id === vm.form.game_region_server[0]) {
                    vm.form.gameLevelingRequirementId = item.id;
                }
                if (item.game_id === vm.form.game_region_server[0] && item.status === 1) {
                    vm.form.gameLevelingRequirementId = item.id;
                }
            });
            this.handleGameLevelingRequirementIdChange();
        },
        handleGameLevelingRequirementIdChange: function handleGameLevelingRequirementIdChange() {
            var vm = this;
            this.gameLevelingRequirementOptions.forEach(function (item) {
                if (item.id === vm.form.gameLevelingRequirementId) {
                    vm.form.requirement = item.content;
                    return false;
                }
            });
        }
    },
    mounted: function mounted() {
        this.handleFromData();
        this.businessmanQQOption();
        this.gameLevelingRequirementOption();
        this.handleFromGameRegionServerOptions();
        this.handleDayOption();
        this.handleHourOption();
    }
});

/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3cdccb30\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/GameLevelingRequirement.vue":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-6188fe12\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanQQ.vue":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-a7a12fa4\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/less-loader/dist/cjs.js!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/Repeat.vue":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.el-col {\n  border-radius: 4px;\n}\n.grid-content {\n  border-radius: 4px;\n  min-height: 36px;\n}\n.game-leveling-order-repeat .el-card__body {\n  padding: 20px 20px 10px;\n}\n.game-leveling-order-repeat .el-card {\n  border-radius: 0;\n  border: 1px solid #ebeef5;\n  background-color: #fff;\n  overflow: hidden;\n  color: #303133;\n  -webkit-transition: none;\n  transition: none;\n}\n.game-leveling-order-repeat .el-card__header {\n  padding: 10px 20px;\n}\n.game-leveling-order-repeat .footer {\n  height: 60px;\n  background-color: #fff;\n  position: fixed;\n  bottom: 0;\n  width: 100%;\n  /*box-shadow:inset 0px 15px 15px -15px rgba(0, 0, 0, 0.1);*/\n  /*!*-webkit-box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);*!*/\n  -webkit-box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);\n          box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-3cdccb30\",\"hasScoped\":true,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/GameLevelingRequirement.vue":
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "el-dialog",
    {
      attrs: {
        title: "添加代练要求模版",
        width: "60%",
        visible: true,
        "before-close": _vm.handleBeforeClose
      }
    },
    [
      _c(
        "el-row",
        { attrs: { gutter: 10 } },
        [
          _c(
            "el-col",
            { attrs: { xs: 12, sm: 12, md: 12, lg: 12, xl: 12 } },
            [
              _c(
                "el-table",
                { attrs: { height: "300", border: "", data: _vm.tableData } },
                [
                  _c("el-table-column", {
                    attrs: { prop: "name", label: "名称" }
                  }),
                  _vm._v(" "),
                  _c("el-table-column", {
                    attrs: { prop: "tag", label: "操作", width: "160" },
                    scopedSlots: _vm._u([
                      {
                        key: "default",
                        fn: function(scope) {
                          return [
                            _c(
                              "el-button",
                              {
                                attrs: { type: "primary" },
                                on: {
                                  click: function($event) {
                                    _vm.handleEdit(scope.row)
                                  }
                                }
                              },
                              [_vm._v("修改")]
                            ),
                            _vm._v(" "),
                            _c(
                              "el-button",
                              {
                                attrs: { type: "danger" },
                                on: {
                                  click: function($event) {
                                    _vm.handleDelete(scope.row)
                                  }
                                }
                              },
                              [_vm._v("删除")]
                            )
                          ]
                        }
                      }
                    ])
                  })
                ],
                1
              )
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "el-col",
            { attrs: { xs: 12, sm: 12, md: 12, lg: 12, xl: 12 } },
            [
              _c(
                "el-form",
                {
                  ref: "form",
                  attrs: { model: _vm.form, "label-width": "80px" }
                },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "游戏", prop: "game_id" } },
                    [
                      _c(
                        "el-select",
                        {
                          attrs: { filterable: "", placeholder: "请选择游戏" },
                          model: {
                            value: _vm.form.game_id,
                            callback: function($$v) {
                              _vm.$set(_vm.form, "game_id", $$v)
                            },
                            expression: "form.game_id"
                          }
                        },
                        [
                          _c("el-option", {
                            attrs: { label: "通用模板", value: 0 }
                          }),
                          _vm._v(" "),
                          _vm._l(_vm.gameOptions, function(item) {
                            return _c("el-option", {
                              key: item.id,
                              attrs: { label: item.name, value: item.id }
                            })
                          })
                        ],
                        2
                      )
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    { attrs: { label: "模版名", prop: "name" } },
                    [
                      _c("el-input", {
                        model: {
                          value: _vm.form.name,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "name", $$v)
                          },
                          expression: "form.name"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    { attrs: { label: "是否默认", prop: "status" } },
                    [
                      _c("el-switch", {
                        attrs: { "active-value": "1", "inactive-value": "0" },
                        model: {
                          value: _vm.form.status,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "status", $$v)
                          },
                          expression: "form.status"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    { attrs: { label: "模版内容", prop: "content" } },
                    [
                      _c("el-input", {
                        attrs: { rows: "5", type: "textarea" },
                        model: {
                          value: _vm.form.content,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "content", $$v)
                          },
                          expression: "form.content"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    [
                      _c(
                        "el-button",
                        {
                          attrs: { type: "primary" },
                          on: { click: _vm.handleSubmitForm }
                        },
                        [_vm._v("确定")]
                      ),
                      _vm._v(" "),
                      _c(
                        "el-button",
                        {
                          attrs: { type: "primary" },
                          on: { click: _vm.handleFormRest }
                        },
                        [_vm._v("清空")]
                      )
                    ],
                    1
                  )
                ],
                1
              )
            ],
            1
          )
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-3cdccb30", module.exports)
  }
}

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-6188fe12\",\"hasScoped\":true,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanQQ.vue":
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "el-dialog",
    {
      attrs: {
        title: "添加商户联系QQ",
        width: "60%",
        visible: true,
        "before-close": _vm.handleBeforeClose
      }
    },
    [
      _c(
        "el-row",
        { attrs: { gutter: 10 } },
        [
          _c(
            "el-col",
            { attrs: { xs: 12, sm: 12, md: 12, lg: 12, xl: 12 } },
            [
              _c(
                "el-table",
                { attrs: { height: "300", border: "", data: _vm.tableData } },
                [
                  _c("el-table-column", {
                    attrs: { prop: "name", label: "名称" }
                  }),
                  _vm._v(" "),
                  _c("el-table-column", {
                    attrs: { prop: "tag", label: "操作", width: "160" },
                    scopedSlots: _vm._u([
                      {
                        key: "default",
                        fn: function(scope) {
                          return [
                            _c(
                              "el-button",
                              {
                                attrs: { type: "primary" },
                                on: {
                                  click: function($event) {
                                    _vm.handleEdit(scope.row)
                                  }
                                }
                              },
                              [_vm._v("修改")]
                            ),
                            _vm._v(" "),
                            _c(
                              "el-button",
                              {
                                attrs: { type: "danger" },
                                on: {
                                  click: function($event) {
                                    _vm.handleDelete(scope.row)
                                  }
                                }
                              },
                              [_vm._v("删除")]
                            )
                          ]
                        }
                      }
                    ])
                  })
                ],
                1
              )
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "el-col",
            { attrs: { xs: 12, sm: 12, md: 12, lg: 12, xl: 12 } },
            [
              _c(
                "el-form",
                {
                  ref: "form",
                  attrs: { model: _vm.form, "label-width": "80px" }
                },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "游戏", prop: "game_id" } },
                    [
                      _c(
                        "el-select",
                        {
                          attrs: { filterable: "", placeholder: "请选择游戏" },
                          model: {
                            value: _vm.form.game_id,
                            callback: function($$v) {
                              _vm.$set(_vm.form, "game_id", $$v)
                            },
                            expression: "form.game_id"
                          }
                        },
                        [
                          _c("el-option", {
                            attrs: { label: "通用模板", value: 0 }
                          }),
                          _vm._v(" "),
                          _vm._l(_vm.gameOptions, function(item) {
                            return _c("el-option", {
                              key: item.id,
                              attrs: { label: item.name, value: item.id }
                            })
                          })
                        ],
                        2
                      )
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    { attrs: { label: "模版名", prop: "name" } },
                    [
                      _c("el-input", {
                        model: {
                          value: _vm.form.name,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "name", $$v)
                          },
                          expression: "form.name"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    { attrs: { label: "是否默认", prop: "status" } },
                    [
                      _c("el-switch", {
                        attrs: { "active-value": "1", "inactive-value": "0" },
                        model: {
                          value: _vm.form.status,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "status", $$v)
                          },
                          expression: "form.status"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    { attrs: { label: "联系QQ", prop: "content" } },
                    [
                      _c("el-input", {
                        model: {
                          value: _vm.form.content,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "content", $$v)
                          },
                          expression: "form.content"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    [
                      _c(
                        "el-button",
                        {
                          attrs: { type: "primary" },
                          on: { click: _vm.handleSubmitForm }
                        },
                        [_vm._v("确定")]
                      ),
                      _vm._v(" "),
                      _c(
                        "el-button",
                        {
                          attrs: { type: "primary" },
                          on: { click: _vm.handleFormRest }
                        },
                        [_vm._v("清空")]
                      )
                    ],
                    1
                  )
                ],
                1
              )
            ],
            1
          )
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-6188fe12", module.exports)
  }
}

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-a7a12fa4\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/Repeat.vue":
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "game-leveling-order-repeat" },
    [
      _c(
        "div",
        { staticClass: "main" },
        [
          _c(
            "el-row",
            { attrs: { gutter: 10 } },
            [
              _c(
                "el-form",
                {
                  ref: "form",
                  attrs: {
                    rules: _vm.rules,
                    model: _vm.form,
                    "label-width": "120px"
                  }
                },
                [
                  _c(
                    "el-col",
                    {
                      staticStyle: { "margin-bottom": "60px" },
                      attrs: { span: 16 }
                    },
                    [
                      _c(
                        "div",
                        {
                          staticClass: "grid-content bg-purple",
                          staticStyle: {
                            padding: "15px",
                            "background-color": "#fff"
                          }
                        },
                        [
                          _c(
                            "el-tabs",
                            [
                              _c(
                                "el-tab-pane",
                                { attrs: { label: "订单信息" } },
                                [
                                  _c("el-card", { staticClass: "box-card" }, [
                                    _c(
                                      "div",
                                      { staticClass: "text item" },
                                      [
                                        _c(
                                          "el-row",
                                          [
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "游戏/区/服",
                                                      prop: "game_region_server"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-cascader", {
                                                              attrs: {
                                                                options:
                                                                  _vm.gameRegionServerOptions
                                                              },
                                                              on: {
                                                                change:
                                                                  _vm.handleFromGameLevelingTypeIdOptions
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .game_region_server,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "game_region_server",
                                                                    $$v
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.game_region_server"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c("el-col", {
                                                          attrs: { span: 1 }
                                                        })
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            ),
                                            _vm._v(" "),
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "代练类型",
                                                      prop:
                                                        "game_leveling_type_id"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c(
                                                              "el-select",
                                                              {
                                                                attrs: {
                                                                  placeholder:
                                                                    "请选择"
                                                                },
                                                                model: {
                                                                  value:
                                                                    _vm.form
                                                                      .game_leveling_type_id,
                                                                  callback: function(
                                                                    $$v
                                                                  ) {
                                                                    _vm.$set(
                                                                      _vm.form,
                                                                      "game_leveling_type_id",
                                                                      $$v
                                                                    )
                                                                  },
                                                                  expression:
                                                                    "form.game_leveling_type_id"
                                                                }
                                                              },
                                                              _vm._l(
                                                                _vm.gameLevelingTypeOptions,
                                                                function(item) {
                                                                  return _c(
                                                                    "el-option",
                                                                    {
                                                                      key:
                                                                        item.id,
                                                                      attrs: {
                                                                        label:
                                                                          item.name,
                                                                        value:
                                                                          item.id
                                                                      }
                                                                    }
                                                                  )
                                                                }
                                                              )
                                                            )
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c("el-col", {
                                                          attrs: { span: 1 }
                                                        })
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            )
                                          ],
                                          1
                                        ),
                                        _vm._v(" "),
                                        _c(
                                          "el-row",
                                          [
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "角色名称",
                                                      prop: "game_role"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type: "input",
                                                                autocomplete:
                                                                  "off"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .game_role,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "game_role",
                                                                    _vm._n($$v)
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.game_role"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c("el-col", {
                                                          attrs: { span: 1 }
                                                        })
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            )
                                          ],
                                          1
                                        ),
                                        _vm._v(" "),
                                        _c(
                                          "el-row",
                                          [
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "游戏账号",
                                                      prop: "game_account"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type: "input",
                                                                autocomplete:
                                                                  "off"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .game_account,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "game_account",
                                                                    $$v
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.game_account"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c("el-col", {
                                                          attrs: { span: 1 }
                                                        })
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            ),
                                            _vm._v(" "),
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "游戏密码",
                                                      prop: "game_password"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type: "input",
                                                                autocomplete:
                                                                  "off"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .game_password,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "game_password",
                                                                    $$v
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.game_password"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c("el-col", {
                                                          attrs: { span: 1 }
                                                        })
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            )
                                          ],
                                          1
                                        )
                                      ],
                                      1
                                    )
                                  ]),
                                  _vm._v(" "),
                                  _c("el-card", { staticClass: "box-card" }, [
                                    _c(
                                      "div",
                                      { staticClass: "text item" },
                                      [
                                        _c(
                                          "el-row",
                                          [
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "代练标题",
                                                      prop: "title"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type: "age",
                                                                autocomplete:
                                                                  "off"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .title,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "title",
                                                                    $$v
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.title"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 1 }
                                                          },
                                                          [
                                                            _c(
                                                              "el-tooltip",
                                                              {
                                                                attrs: {
                                                                  placement:
                                                                    "top"
                                                                }
                                                              },
                                                              [
                                                                _c(
                                                                  "div",
                                                                  {
                                                                    attrs: {
                                                                      slot:
                                                                        "content"
                                                                    },
                                                                    slot:
                                                                      "content"
                                                                  },
                                                                  [
                                                                    _vm._v(
                                                                      "王者荣耀标题规范示例：黄金3（2星）-钻石1 （3星） 铭文：129"
                                                                    )
                                                                  ]
                                                                ),
                                                                _vm._v(" "),
                                                                _c(
                                                                  "span",
                                                                  {
                                                                    staticClass:
                                                                      "icon-button"
                                                                  },
                                                                  [
                                                                    _c("i", {
                                                                      staticClass:
                                                                        "el-icon-question"
                                                                    })
                                                                  ]
                                                                )
                                                              ]
                                                            )
                                                          ],
                                                          1
                                                        )
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            ),
                                            _vm._v(" "),
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "接单密码",
                                                      prop:
                                                        "take_order_password"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type: "input",
                                                                autocomplete:
                                                                  "off"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .take_order_password,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "take_order_password",
                                                                    $$v
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.take_order_password"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c("el-col", {
                                                          attrs: { span: 1 }
                                                        })
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            )
                                          ],
                                          1
                                        ),
                                        _vm._v(" "),
                                        _c(
                                          "el-row",
                                          [
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "代练天/小时"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c(
                                                              "el-row",
                                                              {
                                                                attrs: {
                                                                  gutter: 10
                                                                }
                                                              },
                                                              [
                                                                _c(
                                                                  "el-col",
                                                                  {
                                                                    attrs: {
                                                                      span: 12
                                                                    }
                                                                  },
                                                                  [
                                                                    _c(
                                                                      "el-select",
                                                                      {
                                                                        attrs: {
                                                                          filterable:
                                                                            "",
                                                                          placeholder:
                                                                            "请选择"
                                                                        },
                                                                        model: {
                                                                          value:
                                                                            _vm
                                                                              .form
                                                                              .day,
                                                                          callback: function(
                                                                            $$v
                                                                          ) {
                                                                            _vm.$set(
                                                                              _vm.form,
                                                                              "day",
                                                                              $$v
                                                                            )
                                                                          },
                                                                          expression:
                                                                            "form.day"
                                                                        }
                                                                      },
                                                                      _vm._l(
                                                                        _vm.dayOptions,
                                                                        function(
                                                                          item
                                                                        ) {
                                                                          return _c(
                                                                            "el-option",
                                                                            {
                                                                              key:
                                                                                item.value,
                                                                              attrs: {
                                                                                label:
                                                                                  item.label,
                                                                                value:
                                                                                  item.value
                                                                              }
                                                                            }
                                                                          )
                                                                        }
                                                                      )
                                                                    )
                                                                  ],
                                                                  1
                                                                ),
                                                                _vm._v(" "),
                                                                _c(
                                                                  "el-col",
                                                                  {
                                                                    attrs: {
                                                                      span: 12
                                                                    }
                                                                  },
                                                                  [
                                                                    _c(
                                                                      "el-select",
                                                                      {
                                                                        attrs: {
                                                                          filterable:
                                                                            "",
                                                                          placeholder:
                                                                            "请选择"
                                                                        },
                                                                        model: {
                                                                          value:
                                                                            _vm
                                                                              .form
                                                                              .hour,
                                                                          callback: function(
                                                                            $$v
                                                                          ) {
                                                                            _vm.$set(
                                                                              _vm.form,
                                                                              "hour",
                                                                              $$v
                                                                            )
                                                                          },
                                                                          expression:
                                                                            "form.hour"
                                                                        }
                                                                      },
                                                                      _vm._l(
                                                                        _vm.hourOptions,
                                                                        function(
                                                                          item
                                                                        ) {
                                                                          return _c(
                                                                            "el-option",
                                                                            {
                                                                              key:
                                                                                item.value,
                                                                              attrs: {
                                                                                label:
                                                                                  item.label,
                                                                                value:
                                                                                  item.value
                                                                              }
                                                                            }
                                                                          )
                                                                        }
                                                                      )
                                                                    )
                                                                  ],
                                                                  1
                                                                )
                                                              ],
                                                              1
                                                            )
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c("el-col", {
                                                          attrs: { span: 1 }
                                                        })
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            ),
                                            _vm._v(" "),
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "代练要求模版"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c(
                                                              "el-select",
                                                              {
                                                                attrs: {
                                                                  placeholder:
                                                                    "请选择"
                                                                },
                                                                on: {
                                                                  change:
                                                                    _vm.handleGameLevelingRequirementIdChange
                                                                },
                                                                model: {
                                                                  value:
                                                                    _vm.form
                                                                      .gameLevelingRequirementId,
                                                                  callback: function(
                                                                    $$v
                                                                  ) {
                                                                    _vm.$set(
                                                                      _vm.form,
                                                                      "gameLevelingRequirementId",
                                                                      $$v
                                                                    )
                                                                  },
                                                                  expression:
                                                                    "form.gameLevelingRequirementId"
                                                                }
                                                              },
                                                              _vm._l(
                                                                _vm.gameLevelingRequirementOptions,
                                                                function(item) {
                                                                  return _c(
                                                                    "el-option",
                                                                    {
                                                                      key:
                                                                        item.id,
                                                                      attrs: {
                                                                        label:
                                                                          item.name,
                                                                        value:
                                                                          item.id
                                                                      }
                                                                    }
                                                                  )
                                                                }
                                                              )
                                                            )
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 1 }
                                                          },
                                                          [
                                                            _c(
                                                              "span",
                                                              {
                                                                staticClass:
                                                                  "icon-button",
                                                                on: {
                                                                  click: function(
                                                                    $event
                                                                  ) {
                                                                    _vm.handleGameLevelingRequirementVisible(
                                                                      {
                                                                        visible: true
                                                                      }
                                                                    )
                                                                  }
                                                                }
                                                              },
                                                              [
                                                                _c("i", {
                                                                  staticClass:
                                                                    "el-icon-circle-plus"
                                                                })
                                                              ]
                                                            )
                                                          ]
                                                        )
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            )
                                          ],
                                          1
                                        ),
                                        _vm._v(" "),
                                        _c(
                                          "el-row",
                                          [
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "代练说明",
                                                      prop: "explain"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type:
                                                                  "textarea",
                                                                rows: 3,
                                                                placeholder:
                                                                  "请输入内容"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .explain,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "explain",
                                                                    $$v
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.explain"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c("el-col", {
                                                          attrs: { span: 1 }
                                                        })
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            ),
                                            _vm._v(" "),
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "代练要求",
                                                      prop: "requirement"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type:
                                                                  "textarea",
                                                                rows: 3,
                                                                placeholder:
                                                                  "请输入内容"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .requirement,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "requirement",
                                                                    $$v
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.requirement"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c("el-col", {
                                                          attrs: { span: 1 }
                                                        })
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            )
                                          ],
                                          1
                                        ),
                                        _vm._v(" "),
                                        _c(
                                          "el-row",
                                          [
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "代练价格",
                                                      prop: "amount"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type: "input",
                                                                placeholder:
                                                                  "请输入内容"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .amount,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "amount",
                                                                    _vm._n($$v)
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.amount"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c("el-col", {
                                                          staticClass:
                                                            "icon-button",
                                                          attrs: { span: 1 }
                                                        })
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            ),
                                            _vm._v(" "),
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "来源价格",
                                                      prop: "source_amount"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type: "input",
                                                                placeholder:
                                                                  "请输入内容"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .source_amount,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "source_amount",
                                                                    _vm._n($$v)
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.source_amount"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c("el-col", {
                                                          attrs: { span: 1 }
                                                        })
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            )
                                          ],
                                          1
                                        ),
                                        _vm._v(" "),
                                        _c(
                                          "el-row",
                                          [
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "安全保证金",
                                                      prop: "security_deposit"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type: "input",
                                                                placeholder:
                                                                  "请输入内容"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .security_deposit,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "security_deposit",
                                                                    _vm._n($$v)
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.security_deposit"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 1 }
                                                          },
                                                          [
                                                            _c(
                                                              "el-tooltip",
                                                              {
                                                                attrs: {
                                                                  placement:
                                                                    "top"
                                                                }
                                                              },
                                                              [
                                                                _c(
                                                                  "div",
                                                                  {
                                                                    attrs: {
                                                                      slot:
                                                                        "content"
                                                                    },
                                                                    slot:
                                                                      "content"
                                                                  },
                                                                  [
                                                                    _vm._v(
                                                                      "安全保证金是指对上家游戏账号安全进行保障时下家所需预先支付的保证形式的费用。"
                                                                    ),
                                                                    _c("br"),
                                                                    _vm._v(
                                                                      "当在代练过程中出现账号安全问题，即以双方协商或客服仲裁的部分或全部金额赔付给上家。"
                                                                    ),
                                                                    _c("br"),
                                                                    _vm._v(
                                                                      "（安全问题包括游戏内虚拟道具的安全，例如：符文、角色经验、胜点、负场经下家代练后不增反减、私自与号主联系、下家使用第三方软件带来的风险）"
                                                                    )
                                                                  ]
                                                                ),
                                                                _vm._v(" "),
                                                                _c(
                                                                  "span",
                                                                  {
                                                                    staticClass:
                                                                      "icon-button"
                                                                  },
                                                                  [
                                                                    _c("i", {
                                                                      staticClass:
                                                                        "el-icon-question"
                                                                    })
                                                                  ]
                                                                )
                                                              ]
                                                            )
                                                          ],
                                                          1
                                                        )
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            ),
                                            _vm._v(" "),
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "效率保证金",
                                                      prop: "efficiency_deposit"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type: "input",
                                                                placeholder:
                                                                  "请输入内容"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .efficiency_deposit,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "efficiency_deposit",
                                                                    _vm._n($$v)
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.efficiency_deposit"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 1 }
                                                          },
                                                          [
                                                            _c(
                                                              "el-tooltip",
                                                              {
                                                                attrs: {
                                                                  placement:
                                                                    "top"
                                                                }
                                                              },
                                                              [
                                                                _c(
                                                                  "div",
                                                                  {
                                                                    attrs: {
                                                                      slot:
                                                                        "content"
                                                                    },
                                                                    slot:
                                                                      "content"
                                                                  },
                                                                  [
                                                                    _vm._v(
                                                                      "效率保证金是指对上家的代练要求进行效率保障时下家所需预先支付的保证形式的费用。"
                                                                    ),
                                                                    _c("br"),
                                                                    _vm._v(
                                                                      "当下家未在规定时间内完成代练要求，即以双方协商或客服仲裁的部分或全部金额赔付给上家。"
                                                                    ),
                                                                    _c("br"),
                                                                    _vm._v(
                                                                      "（代练要求包括：下家在规定时间内没有完成上家的代练要求，接单4小时内没有上号，代练时间过四分之一但代练进度未达六分之一，下家原因退单，下家未及时上传代练截图）"
                                                                    )
                                                                  ]
                                                                ),
                                                                _vm._v(" "),
                                                                _c(
                                                                  "span",
                                                                  {
                                                                    staticClass:
                                                                      "icon-button"
                                                                  },
                                                                  [
                                                                    _c("i", {
                                                                      staticClass:
                                                                        "el-icon-question"
                                                                    })
                                                                  ]
                                                                )
                                                              ]
                                                            )
                                                          ],
                                                          1
                                                        )
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            )
                                          ],
                                          1
                                        ),
                                        _vm._v(" "),
                                        _c(
                                          "el-row",
                                          [
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "玩家电话",
                                                      prop: "player_phone"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type: "input",
                                                                placeholder:
                                                                  "请输入内容"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .player_phone,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "player_phone",
                                                                    _vm._n($$v)
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.player_phone"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c("el-col", {
                                                          attrs: { span: 1 }
                                                        })
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            ),
                                            _vm._v(" "),
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "商户QQ",
                                                      prop: "user_qq"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c(
                                                              "el-select",
                                                              {
                                                                attrs: {
                                                                  placeholder:
                                                                    "请选择"
                                                                },
                                                                model: {
                                                                  value:
                                                                    _vm.form
                                                                      .user_qq,
                                                                  callback: function(
                                                                    $$v
                                                                  ) {
                                                                    _vm.$set(
                                                                      _vm.form,
                                                                      "user_qq",
                                                                      _vm._n(
                                                                        $$v
                                                                      )
                                                                    )
                                                                  },
                                                                  expression:
                                                                    "form.user_qq"
                                                                }
                                                              },
                                                              _vm._l(
                                                                _vm.businessmanQQOptions,
                                                                function(item) {
                                                                  return _c(
                                                                    "el-option",
                                                                    {
                                                                      key:
                                                                        item.id,
                                                                      attrs: {
                                                                        label:
                                                                          item.name +
                                                                          "-" +
                                                                          item.content,
                                                                        value:
                                                                          item.content
                                                                      }
                                                                    }
                                                                  )
                                                                }
                                                              )
                                                            )
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 1 }
                                                          },
                                                          [
                                                            _c(
                                                              "span",
                                                              {
                                                                staticClass:
                                                                  "icon-button",
                                                                on: {
                                                                  click: function(
                                                                    $event
                                                                  ) {
                                                                    _vm.handleBusinessmanQQVisible(
                                                                      {
                                                                        visible: true
                                                                      }
                                                                    )
                                                                  }
                                                                }
                                                              },
                                                              [
                                                                _c("i", {
                                                                  staticClass:
                                                                    "el-icon-circle-plus"
                                                                })
                                                              ]
                                                            )
                                                          ]
                                                        )
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            )
                                          ],
                                          1
                                        )
                                      ],
                                      1
                                    )
                                  ]),
                                  _vm._v(" "),
                                  _c("el-card", { staticClass: "box-card" }, [
                                    _c(
                                      "div",
                                      { staticClass: "text item" },
                                      [
                                        _c(
                                          "el-row",
                                          [
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "加价幅度",
                                                      prop:
                                                        "price_increase_step"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type: "input",
                                                                autocomplete:
                                                                  "off"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .price_increase_step,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "price_increase_step",
                                                                    $$v
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.price_increase_step"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 1 }
                                                          },
                                                          [
                                                            _c(
                                                              "el-tooltip",
                                                              {
                                                                attrs: {
                                                                  placement:
                                                                    "top"
                                                                }
                                                              },
                                                              [
                                                                _c(
                                                                  "div",
                                                                  {
                                                                    attrs: {
                                                                      slot:
                                                                        "content"
                                                                    },
                                                                    slot:
                                                                      "content"
                                                                  },
                                                                  [
                                                                    _vm._v(
                                                                      "设置后，若一小时仍无人接单，将自动补款所填金额，每小时补款一次"
                                                                    )
                                                                  ]
                                                                ),
                                                                _vm._v(" "),
                                                                _c(
                                                                  "span",
                                                                  {
                                                                    staticClass:
                                                                      "icon-button"
                                                                  },
                                                                  [
                                                                    _c("i", {
                                                                      staticClass:
                                                                        "el-icon-question"
                                                                    })
                                                                  ]
                                                                )
                                                              ]
                                                            )
                                                          ],
                                                          1
                                                        )
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            ),
                                            _vm._v(" "),
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: {
                                                      label: "加价上限",
                                                      prop: "price_ceiling"
                                                    }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type: "input",
                                                                autocomplete:
                                                                  "off"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .price_ceiling,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "price_ceiling",
                                                                    $$v
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.price_ceiling"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 1 }
                                                          },
                                                          [
                                                            _c(
                                                              "el-tooltip",
                                                              {
                                                                attrs: {
                                                                  placement:
                                                                    "top"
                                                                }
                                                              },
                                                              [
                                                                _c(
                                                                  "div",
                                                                  {
                                                                    attrs: {
                                                                      slot:
                                                                        "content"
                                                                    },
                                                                    slot:
                                                                      "content"
                                                                  },
                                                                  [
                                                                    _vm._v(
                                                                      "自动加价将不超过该价格"
                                                                    )
                                                                  ]
                                                                ),
                                                                _vm._v(" "),
                                                                _c(
                                                                  "span",
                                                                  {
                                                                    staticClass:
                                                                      "icon-button"
                                                                  },
                                                                  [
                                                                    _c("i", {
                                                                      staticClass:
                                                                        "el-icon-question"
                                                                    })
                                                                  ]
                                                                )
                                                              ]
                                                            )
                                                          ],
                                                          1
                                                        )
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            )
                                          ],
                                          1
                                        ),
                                        _vm._v(" "),
                                        _c(
                                          "el-row",
                                          [
                                            _c("el-col", {
                                              attrs: { span: 12 }
                                            }),
                                            _vm._v(" "),
                                            _c("el-col", {
                                              attrs: { span: 12 }
                                            })
                                          ],
                                          1
                                        ),
                                        _vm._v(" "),
                                        _c(
                                          "el-row",
                                          [
                                            _c(
                                              "el-col",
                                              { attrs: { span: 12 } },
                                              [
                                                _c(
                                                  "el-form-item",
                                                  {
                                                    attrs: { label: "客服备注" }
                                                  },
                                                  [
                                                    _c(
                                                      "el-row",
                                                      { attrs: { gutter: 10 } },
                                                      [
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 22 }
                                                          },
                                                          [
                                                            _c("el-input", {
                                                              attrs: {
                                                                type:
                                                                  "textarea",
                                                                rows: 2,
                                                                placeholder:
                                                                  "请输入内容"
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.form
                                                                    .remark,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.$set(
                                                                    _vm.form,
                                                                    "remark",
                                                                    $$v
                                                                  )
                                                                },
                                                                expression:
                                                                  "form.remark"
                                                              }
                                                            })
                                                          ],
                                                          1
                                                        ),
                                                        _vm._v(" "),
                                                        _c("el-col", {
                                                          attrs: { span: 1 }
                                                        })
                                                      ],
                                                      1
                                                    )
                                                  ],
                                                  1
                                                )
                                              ],
                                              1
                                            ),
                                            _vm._v(" "),
                                            _c("el-col", {
                                              attrs: { span: 12 }
                                            })
                                          ],
                                          1
                                        )
                                      ],
                                      1
                                    )
                                  ])
                                ],
                                1
                              )
                            ],
                            1
                          )
                        ],
                        1
                      )
                    ]
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c("el-col", { attrs: { span: 8 } }, [
                _c(
                  "div",
                  {
                    staticClass: "grid-content bg-purple",
                    staticStyle: { padding: "15px", "background-color": "#fff" }
                  },
                  [
                    _c(
                      "el-tabs",
                      [
                        _c(
                          "el-tab-pane",
                          { attrs: { label: "淘宝数据" } },
                          [
                            _c(
                              "el-table",
                              {
                                staticStyle: { width: "100%" },
                                attrs: {
                                  data: _vm.taobaoData,
                                  "show-header": false,
                                  border: ""
                                }
                              },
                              [
                                _c("el-table-column", {
                                  attrs: {
                                    prop: "name",
                                    label: "",
                                    width: "120"
                                  }
                                }),
                                _vm._v(" "),
                                _c("el-table-column", {
                                  attrs: { prop: "value", label: "" },
                                  scopedSlots: _vm._u([
                                    {
                                      key: "default",
                                      fn: function(scope) {
                                        return [
                                          _c("span", {
                                            domProps: {
                                              innerHTML: _vm._s(scope.row.value)
                                            }
                                          })
                                        ]
                                      }
                                    }
                                  ])
                                })
                              ],
                              1
                            )
                          ],
                          1
                        )
                      ],
                      1
                    )
                  ],
                  1
                )
              ])
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "footer" },
        [
          _c(
            "el-row",
            [
              _c("el-col", { attrs: { span: 16 } }, [
                _c(
                  "div",
                  {
                    staticStyle: {
                      "text-align": "center",
                      "line-height": "60px"
                    }
                  },
                  [
                    _c(
                      "el-button",
                      {
                        staticStyle: { "margin-right": "8px" },
                        attrs: { type: "primary" },
                        on: {
                          click: function($event) {
                            _vm.handleSubmitForm("form")
                          }
                        }
                      },
                      [_vm._v("确认下单")]
                    )
                  ],
                  1
                )
              ])
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _vm.gameLevelingRequirementVisible
        ? _c("GameLevelingRequirement", {
            on: {
              handleGameLevelingRequirementVisible:
                _vm.handleGameLevelingRequirementVisible
            }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm.businessmanQQVisible
        ? _c("BusinessmanQQ", {
            on: { handleBusinessmanQQVisible: _vm.handleBusinessmanQQVisible }
          })
        : _vm._e()
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-a7a12fa4", module.exports)
  }
}

/***/ }),

/***/ "./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3cdccb30\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/GameLevelingRequirement.vue":
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__("./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3cdccb30\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/GameLevelingRequirement.vue");
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__("./node_modules/vue-style-loader/lib/addStylesClient.js")("0b6f96e0", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3cdccb30\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./GameLevelingRequirement.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3cdccb30\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./GameLevelingRequirement.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ "./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-6188fe12\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanQQ.vue":
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__("./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-6188fe12\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanQQ.vue");
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__("./node_modules/vue-style-loader/lib/addStylesClient.js")("160847de", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-6188fe12\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./BusinessmanQQ.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-6188fe12\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./BusinessmanQQ.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ "./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-a7a12fa4\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/less-loader/dist/cjs.js!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/Repeat.vue":
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__("./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-a7a12fa4\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/less-loader/dist/cjs.js!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/Repeat.vue");
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__("./node_modules/vue-style-loader/lib/addStylesClient.js")("58988a5c", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-a7a12fa4\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Repeat.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-a7a12fa4\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Repeat.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/order/game-leveling/BusinessmanQQ.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__("./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-6188fe12\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanQQ.vue")
}
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanQQ.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-6188fe12\",\"hasScoped\":true,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanQQ.vue")
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-6188fe12"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/frontend/js/components/order/game-leveling/BusinessmanQQ.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-6188fe12", Component.options)
  } else {
    hotAPI.reload("data-v-6188fe12", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ "./resources/assets/frontend/js/components/order/game-leveling/GameLevelingRequirement.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__("./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3cdccb30\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/GameLevelingRequirement.vue")
}
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/GameLevelingRequirement.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-3cdccb30\",\"hasScoped\":true,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/GameLevelingRequirement.vue")
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-3cdccb30"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/frontend/js/components/order/game-leveling/GameLevelingRequirement.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-3cdccb30", Component.options)
  } else {
    hotAPI.reload("data-v-3cdccb30", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ "./resources/assets/frontend/js/components/order/game-leveling/Repeat.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__("./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-a7a12fa4\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/less-loader/dist/cjs.js!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/Repeat.vue")
}
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/Repeat.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-a7a12fa4\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/Repeat.vue")
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/frontend/js/components/order/game-leveling/Repeat.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-a7a12fa4", Component.options)
  } else {
    hotAPI.reload("data-v-a7a12fa4", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});