webpackJsonp([3],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyComplain.vue":
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

/* harmony default export */ __webpack_exports__["default"] = ({
    name: "ApplyComplain",
    props: ['tradeNo'],
    computed: {
        // getVisible() {
        //     return this.$store.state.applyComplainVisible;
        // }
        // 如果上传图片等于3张时就隐藏增加图片按钮
        uploadExceedLimit: function uploadExceedLimit() {
            return [this.form.images.length == 3 ? 'exceed' : ''];
        }
    },
    data: function data() {
        var reasonRule = function reasonRule(rule, value, callback) {
            if (value.length > 50) {
                callback(new Error('申请仲裁原因不得大于50字！'));
            } else {
                callback();
            }
        };
        return {
            fileReader: '',
            dialogImageUrl: '',
            dialogVisible: false,
            form: {
                pic1: '',
                pic2: '',
                pic3: '',
                reason: '',
                images: [],
                trade_no: this.tradeNo
            },
            complainRule: {
                reason: [{ required: true, message: '必填项不能为空' }, { validator: reasonRule, trigger: 'blur' }]
            }
        };
    },

    methods: {
        handleBeforeClose: function handleBeforeClose() {
            this.$emit("handleApplyComplainVisible", { "visible": false });
        },
        handleSubmitForm: function handleSubmitForm(formName) {
            var _this = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this.form.pic1 = _this.form.images[0];
                    _this.form.pic2 = _this.form.images[1];
                    _this.form.pic3 = _this.form.images[2];
                    _this.form.images = [];
                    _this.$api.gameLevelingOrderApplyComplain(_this.form).then(function (res) {
                        _this.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
                            // 关闭窗口
                            _this.$emit("handleApplyComplainVisible", { "visible": false });
                        }
                    }).catch(function (err) {
                        _this.$message({
                            type: 'error',
                            message: '操作失败'
                        });
                    });
                } else {
                    return false;
                }
            });
        },
        handleResetForm: function handleResetForm(formName) {
            this.$refs[formName].resetFields();
        },

        // 预览图片
        handleUploadPreview: function handleUploadPreview(file) {
            var h = this.$createElement;
            this.$msgbox({
                message: h('img', { attrs: { src: file.url } }),
                showCancelButton: false,
                cancelButtonText: false,
                showConfirmButton: false,
                customClass: 'preview-image'
            });
        },

        // 删除图片
        handleUploadRemove: function handleUploadRemove(file, fileList) {
            var index = this.form.images.indexOf(file.response);
            this.form.images.splice(index, 1);
        },
        handleUploadFile: function handleUploadFile(options) {
            var _this2 = this;

            var file = options.file;
            if (file) {
                this.fileReader.readAsDataURL(file);
            }
            this.fileReader.onload = function () {
                _this2.form.images.push(_this2.fileReader.result);
                _this2.$refs.image.clearValidate();
            };
        }
    },
    watch: {
        // getVisible(val) {
        //     this.visible = val;
        // }
    },
    mounted: function mounted() {
        this.fileReader = new FileReader();
    }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyConsult.vue":
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

/* harmony default export */ __webpack_exports__["default"] = ({
    name: "ApplyConsult",
    props: ['tradeNo', 'amount', 'securityDeposit', 'efficiencyDeposit'],
    computed: {
        // getVisible() {
        //     return this.$store.state.applyConsultVisible;
        // }
    },
    data: function data() {
        var _this = this;

        var mustOverZero = function mustOverZero(rule, value, callback) {
            var grep = /^([1-9]\d*|0)(\.\d{1,2})?$/;
            if (!grep.test(value)) {
                callback(new Error('金额必须大于0，支持2位小数!'));
            } else {
                callback();
            }
        };
        var amountRule = function amountRule(rule, value, callback) {
            if (value > _this.form.amount) {
                callback(new Error('填写赔偿代练费不得超过订单代练费！'));
            } else {
                callback();
            }
        };
        var depositRule = function depositRule(rule, value, callback) {
            var total = Number(_this.form.security_deposit) + Number(_this.form.efficiency_deposit);
            if (value > total) {
                callback(new Error('填写赔偿双金不得大于订单双金！'));
            } else {
                callback();
            }
        };
        var reasonRule = function reasonRule(rule, value, callback) {
            if (value.length > 50) {
                callback(new Error('申请协商原因不得大于50字！'));
            } else {
                callback();
            }
        };
        return {
            fileReader: '',
            visible: false,
            dialogImageUrl: '',
            dialogVisible: false,
            form: {
                reason: '',
                payment_amount: '',
                payment_deposit: '',
                trade_no: this.tradeNo,
                amount: this.amount,
                security_deposit: this.securityDeposit,
                efficiency_deposit: this.efficiencyDeposit
            },
            consultRules: {
                payment_amount: [{ required: true, message: '必填项不能为空' }, { validator: mustOverZero, trigger: 'blur' }, { validator: amountRule, trigger: 'blur' }],
                payment_deposit: [{ required: true, message: '必填项不能为空' }, { validator: mustOverZero, trigger: 'blur' }, { validator: depositRule, trigger: 'blur' }],
                reason: [{ required: true, message: '必填项不能为空' }, { validator: reasonRule, trigger: 'blur' }]
            }
        };
    },

    methods: {
        handleBeforeClose: function handleBeforeClose() {
            this.$emit("handleApplyConsultVisible", { "visible": false });
        },
        handleSubmitForm: function handleSubmitForm(formName) {
            var _this2 = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this2.$api.gameLevelingOrderApplyConsult(_this2.form).then(function (res) {
                        _this2.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });

                        if (res.status == 1) {
                            // 关闭窗口
                            _this2.$emit("handleApplyConsultVisible", { "visible": false });
                        }
                    }).catch(function (err) {
                        _this2.$message({
                            type: 'error',
                            message: '操作失败'
                        });
                    });
                }
            });
        },
        handleResetForm: function handleResetForm(formName) {
            this.$refs[formName].resetFields();
        }
    },
    watch: {
        // getVisible(val) {
        //     this.visible = val;
        // }
    }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/List.vue":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__ApplyComplain__ = __webpack_require__("./resources/assets/frontend/js/components/order/game-leveling/ApplyComplain.vue");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__ApplyComplain___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__ApplyComplain__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__ApplyConsult__ = __webpack_require__("./resources/assets/frontend/js/components/order/game-leveling/ApplyConsult.vue");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__ApplyConsult___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__ApplyConsult__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    components: {
        ApplyComplain: __WEBPACK_IMPORTED_MODULE_0__ApplyComplain___default.a,
        ApplyConsult: __WEBPACK_IMPORTED_MODULE_1__ApplyConsult___default.a
    },
    props: ['pageTitle'],
    computed: {
        tableDataEmpty: function tableDataEmpty() {
            // return [
            //     this.tableData.length === 0 ? ' el-table_empty' : '',
            // ]
        }
    },
    data: function data() {
        return {
            editRemark: [],
            tradeNo: '',
            amount: 0,
            securityDeposit: 0,
            efficiencyDeposit: 0,
            applyConsultVisible: false,
            applyComplainVisible: false,
            statusQuantity: [],
            platformOptions: [{ key: 0, value: '所有平台' }, { key: 5, value: '丸子代练' }, { key: 1, value: '91代练' }, { key: 3, value: '蚂蚁代练' }],
            platformMap: {
                5: '丸子代练',
                1: '91代练',
                4: 'DD373',
                3: '蚂蚁代练'
            },
            gameLevelingTypeOptions: [],
            gameOptions: [],
            search: {
                status: '',
                order_no: '',
                buyer_nick: '',
                game_id: '',
                game_leveling_type_id: '',
                user_id: '',
                platform_id: 0,
                start_created_at: '',
                created_at: '',
                page: 1
            },
            statusMap: {
                1: '未接单',
                13: '代练中',
                14: '待验收',
                15: '撤销中',
                16: '仲裁中',
                17: '异常',
                18: '已锁定',
                19: '已撤销',
                20: '已结算',
                21: '已仲裁',
                22: '已下架',
                23: '强制撤销',
                24: '已撤单'
            },
            tableLoading: false,
            tableHeight: 0,
            tableDataTotal: 0,
            tableData: []
        };
    },

    methods: {
        // 设置仲裁窗口是否显示
        handleApplyComplainVisible: function handleApplyComplainVisible(data) {
            this.applyComplainVisible = data.visible;
            if (data.visible == false) {
                this.handleTableData();
            }
        },

        // 设置协商窗口是否显示
        handleApplyConsultVisible: function handleApplyConsultVisible(data) {
            this.applyConsultVisible = data.visible;
            if (data.visible == false) {
                this.handleTableData();
            }
        },

        // 表格高度计算
        handleTableHeight: function handleTableHeight() {
            this.tableHeight = window.innerHeight - 366;
        },

        // 获取订单状态数量
        handleStatusQuantity: function handleStatusQuantity() {
            var _this = this;

            this.$api.gameLevelingOrderStatusQuantity({}).then(function (res) {
                _this.statusQuantity = res;
            });
        },

        // 加载订单数据
        handleTableData: function handleTableData() {
            var _this2 = this;

            this.tableLoading = true;
            this.$api.gameLevelingOrder(this.search).then(function (res) {
                _this2.tableData = res.data.items;
                _this2.tableDataTotal = res.data.total;
                _this2.tableLoading = false;
            });
            this.handleStatusQuantity();
        },

        // 加载游戏选项
        handleGameOptions: function handleGameOptions() {
            var _this3 = this;

            this.$api.games().then(function (res) {
                _this3.gameOptions = res.data;
            });
        },

        // 搜索
        handleSearch: function handleSearch() {
            this.handleTableData();
        },

        // 切换页码
        handleParamsPage: function handleParamsPage(page) {
            this.search.page = page;
            this.handleTableData();
        },

        // 切换状态tab
        handleParamsStatus: function handleParamsStatus() {
            this.handleTableData();
        },

        // 选择游戏后加载代练类型
        handleSearchGameId: function handleSearchGameId() {
            var _this4 = this;

            if (this.search.game_id) {
                this.$api.gameLevelingTypes({
                    'game_id': this.search.game_id
                }).then(function (res) {
                    _this4.gameLevelingTypeOptions = res.data;
                });
            } else {
                this.gameLevelingTypeOptions = [];
            }
        },

        // 撤单
        handleDelete: function handleDelete(row) {
            var _this5 = this;

            this.$confirm('您确定要"撤单"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this5.$api.gameLevelingOrderDelete({
                    'trade_no': row.trade_no
                }).then(function (res) {
                    _this5.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this5.handleTableData();
                    }
                }).catch(function (err) {
                    _this5.$message({
                        message: '操作失败',
                        type: 'error'
                    });
                });
            });
        },

        // 上架
        handleOnSale: function handleOnSale(row) {
            var _this6 = this;

            this.$confirm('您确定要"上架"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this6.$api.gameLevelingOrderOnSale({
                    'trade_no': row.trade_no
                }).then(function (res) {
                    _this6.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this6.handleTableData();
                    }
                }).catch(function (err) {
                    _this6.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 下架
        handleOffSale: function handleOffSale(row) {
            var _this7 = this;

            this.$confirm('您确定要"下架"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this7.$api.gameLevelingOrderOffSale({
                    'trade_no': row.trade_no
                }).then(function (res) {
                    _this7.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this7.handleTableData();
                    }
                }).catch(function (err) {
                    _this7.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 申请仲裁
        handleApplyComplain: function handleApplyComplain(row) {
            this.tradeNo = row.trade_no;
            this.applyComplainVisible = true;
        },

        // 取消仲裁
        handleCancelComplain: function handleCancelComplain(row) {
            var _this8 = this;

            this.$confirm('您确定要"取消仲裁"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this8.$api.gameLevelingOrderCancelComplain({
                    'trade_no': row.trade_no
                }).then(function (res) {
                    _this8.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this8.handleTableData();
                    }
                }).catch(function (err) {
                    _this8.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 查看图片
        handleApplyCompleteImage: function handleApplyCompleteImage(row) {
            var _this9 = this;

            // 请求图片
            this.$api.gameLevelingOrderApplyCompleteImage({
                'trade_no': row.trade_no
            }).then(function (res) {
                if (res.status == 1) {
                    var h = _this9.$createElement;
                    var item = [];
                    res.content.forEach(function (val) {
                        item.push(h('el-carousel-item', null, [h('img', {
                            attrs: {
                                src: val['url'],
                                class: 'avatar'
                            }
                        }, '')]));
                    });

                    _this9.$msgbox({
                        title: '查看验收图片',
                        message: h('el-carousel', null, item),
                        showCancelButton: true,
                        confirmButtonText: '确定',
                        cancelButtonText: '取消'
                    });
                } else {
                    _this9.$message({
                        type: 'error',
                        message: res.message
                    });
                }
            }).catch(function (err) {
                _this9.$message({
                    type: 'error',
                    message: '操作失败'
                });
            });
        },

        // 完成验收
        handleComplete: function handleComplete(row) {
            var _this10 = this;

            this.$confirm('您确定要"完成验收"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this10.$api.gameLevelingOrderComplete({
                    'trade_no': row.trade_no
                }).then(function (res) {
                    _this10.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this10.handleTableData();
                    }
                }).catch(function (err) {
                    _this10.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 申请撤销
        handleApplyConsult: function handleApplyConsult(row) {
            this.tradeNo = row.trade_no;
            this.amount = row.amount;
            this.securityDeposit = row.security_deposit;
            this.efficiencyDeposit = row.efficiency_deposit;
            this.applyConsultVisible = true;
        },

        // 取消撤销
        handleCancelConsult: function handleCancelConsult(row) {
            var _this11 = this;

            this.$confirm('您确定要"取消撤销"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this11.$api.gameLevelingOrderCancelConsult({
                    'trade_no': row.trade_no
                }).then(function (res) {
                    _this11.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this11.handleTableData();
                    }
                }).catch(function (err) {
                    _this11.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 同意撤销
        handleAgreeConsult: function handleAgreeConsult(row) {
            var _this12 = this;

            var str = row.consult_describe + " ，确认 同意撤销 吗?";

            this.$confirm(str, '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this12.$api.gameLevelingOrderAgreeConsult({
                    'trade_no': row.trade_no
                }).then(function (res) {
                    _this12.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this12.handleTableData();
                    }
                }).catch(function (err) {
                    _this12.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 不同意撤销
        handleRejectConsult: function handleRejectConsult(row) {
            var _this13 = this;

            this.$confirm('您确定"不同意撤销"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this13.$api.gameLevelingOrderRejectConsult({
                    'trade_no': row.trade_no
                }).then(function (res) {
                    _this13.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this13.handleTableData();
                    }
                }).catch(function (err) {
                    _this13.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 锁定
        handleLock: function handleLock(row) {
            var _this14 = this;

            this.$confirm('您确定要"锁定"订单吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this14.$api.gameLevelingOrderLock({
                    'trade_no': row.trade_no
                }).then(function (res) {
                    _this14.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this14.handleTableData();
                    }
                }).catch(function (err) {
                    _this14.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 取消锁定
        handleCancelLock: function handleCancelLock(row) {
            var _this15 = this;

            this.$confirm('您确定要"取消锁定"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this15.$api.gameLevelingOrderCancelLock({
                    'trade_no': row.trade_no
                }).then(function (res) {
                    _this15.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this15.handleTableData();
                    }
                }).catch(function (err) {
                    _this15.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 重置表单
        handleResetForm: function handleResetForm() {
            this.search = {
                status: '',
                order_no: '',
                buyer_nick: '',
                game_id: '',
                game_leveling_type_id: '',
                user_id: '',
                platform_id: 0,
                start_created_at: '',
                created_at: '',
                page: 1
            };
            this.handleTableData();
        },
        handleCellMouseEnter: function handleCellMouseEnter(row, column, cell, event) {
            if (column.property === 'user_remark') {
                row.remark_edit = true;
            }
        },
        handleCellMouseLeave: function handleCellMouseLeave(row, column, cell, event) {
            if (column.property === 'user_remark') {
                row.remark_edit = false;
                // if (row.game_leveling_order_detail.user_remark !== '') {
                this.$api.gameLevelingOrderUserRemark({
                    trade_no: row.trade_no,
                    user_remark: row.game_leveling_order_detail.user_remark }).then(function (res) {});
                // }
            }
        }
    },
    created: function created() {
        this.handleTableHeight();
        this.handleTableData();
        this.handleGameOptions();
        window.addEventListener('resize', this.handleTableHeight);
    }
});

/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-11fc571e\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/less-loader/dist/cjs.js!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/List.vue":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.game-leveling-order-tab .el-tabs__item {\n  font-weight: normal;\n}\n.game-leveling-order-table .el-button {\n  width: 80px;\n}\n.el-table_empty .el-table__empty-block {\n  width: auto !important;\n}\n.game-leveling-order .el-table--small th,\n.game-leveling-order .el-table--small td {\n  padding: 2px 0;\n}\n.avatar {\n  width: 100%;\n  height: 100%;\n  display: block;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4f069d1e\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyConsult.vue":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.avatar-uploader .el-upload[data-v-4f069d1e] {\n    border: 1px dashed #d9d9d9;\n    border-radius: 6px;\n    cursor: pointer;\n    position: relative;\n    overflow: hidden;\n}\n.avatar-uploader .el-upload[data-v-4f069d1e]:hover {\n    border-color: #409EFF;\n}\n.avatar-uploader-icon[data-v-4f069d1e] {\n    font-size: 28px;\n    color: #8c939d;\n    width: 178px;\n    height: 178px;\n    line-height: 178px;\n    text-align: center;\n}\n.avatar[data-v-4f069d1e] {\n    width: 178px;\n    height: 178px;\n    display: block;\n}\n\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ec590898\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyComplain.vue":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.avatar-uploader .el-upload[data-v-ec590898] {\n    border: 1px dashed #d9d9d9;\n    border-radius: 6px;\n    cursor: pointer;\n    position: relative;\n    overflow: hidden;\n}\n.avatar-uploader .el-upload[data-v-ec590898]:hover {\n    border-color: #409EFF;\n}\n.avatar-uploader-icon[data-v-ec590898] {\n    font-size: 28px;\n    color: #8c939d;\n    width: 178px;\n    height: 178px;\n    line-height: 178px;\n    text-align: center;\n}\n.avatar[data-v-ec590898] {\n    width: 178px;\n    height: 178px;\n    display: block;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-11fc571e\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/List.vue":
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    {
      staticClass: "main content game-leveling-order",
      class: _vm.tableDataEmpty
    },
    [
      _c(
        "el-form",
        {
          staticClass: "search-form-inline",
          attrs: { inline: true, model: _vm.search, size: "small" }
        },
        [
          _c(
            "el-row",
            { attrs: { gutter: 16 } },
            [
              _c(
                "el-col",
                { attrs: { span: 6 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "订单单号", prop: "name" } },
                    [
                      _c("el-input", {
                        model: {
                          value: _vm.search.order_no,
                          callback: function($$v) {
                            _vm.$set(_vm.search, "order_no", $$v)
                          },
                          expression: "search.order_no"
                        }
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
                { attrs: { span: 6 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "玩家旺旺", prop: "name" } },
                    [
                      _c("el-input", {
                        model: {
                          value: _vm.search.buyer_nick,
                          callback: function($$v) {
                            _vm.$set(_vm.search, "buyer_nick", $$v)
                          },
                          expression: "search.buyer_nick"
                        }
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
                { attrs: { span: 6 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "代练游戏", prop: "name" } },
                    [
                      _c(
                        "el-select",
                        {
                          attrs: { placeholder: "请选择" },
                          on: { change: _vm.handleSearchGameId },
                          model: {
                            value: _vm.search.game_id,
                            callback: function($$v) {
                              _vm.$set(_vm.search, "game_id", $$v)
                            },
                            expression: "search.game_id"
                          }
                        },
                        [
                          _c("el-option", {
                            key: "0",
                            attrs: { label: "所有游戏", value: "0" }
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
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-col",
                { attrs: { span: 6 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "代练类型", prop: "name" } },
                    [
                      _c(
                        "el-select",
                        {
                          attrs: { placeholder: "请选择" },
                          model: {
                            value: _vm.search.game_leveling_type_id,
                            callback: function($$v) {
                              _vm.$set(_vm.search, "game_leveling_type_id", $$v)
                            },
                            expression: "search.game_leveling_type_id"
                          }
                        },
                        _vm._l(_vm.gameLevelingTypeOptions, function(item) {
                          return _c("el-option", {
                            key: item.name,
                            attrs: { label: item.name, value: item.id }
                          })
                        })
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
            { attrs: { gutter: 16 } },
            [
              _c(
                "el-col",
                { attrs: { span: 6 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "发单客服" } },
                    [
                      _c("el-input", {
                        attrs: { placeholder: "发单客服" },
                        model: {
                          value: _vm.search.user_id,
                          callback: function($$v) {
                            _vm.$set(_vm.search, "user_id", $$v)
                          },
                          expression: "search.user_id"
                        }
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
                { attrs: { span: 6 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "代练平台" } },
                    [
                      _c(
                        "el-select",
                        {
                          attrs: { placeholder: "请选择" },
                          model: {
                            value: _vm.search.platform_id,
                            callback: function($$v) {
                              _vm.$set(_vm.search, "platform_id", $$v)
                            },
                            expression: "search.platform_id"
                          }
                        },
                        _vm._l(_vm.platformOptions, function(item) {
                          return _c("el-option", {
                            key: item.key,
                            attrs: { label: item.value, value: item.key }
                          })
                        })
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
                { attrs: { span: 6 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "发布时间", prop: "name" } },
                    [
                      _c("el-date-picker", {
                        attrs: {
                          type: "daterange",
                          align: "right",
                          "unlink-panels": "",
                          "range-separator": "至",
                          "start-placeholder": "开始日期",
                          "end-placeholder": "结束日期",
                          format: "yyyy-MM-dd",
                          "value-format": "yyyy-MM-dd"
                        },
                        model: {
                          value: _vm.search.created_at,
                          callback: function($$v) {
                            _vm.$set(_vm.search, "created_at", $$v)
                          },
                          expression: "search.created_at"
                        }
                      })
                    ],
                    1
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-button",
                { attrs: { type: "primary" }, on: { click: _vm.handleSearch } },
                [_vm._v("查询")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                {
                  attrs: { type: "primary" },
                  on: { click: _vm.handleResetForm }
                },
                [_vm._v("重置")]
              )
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-tabs",
        {
          staticClass: "game-leveling-order-tab",
          attrs: { size: "small" },
          on: { "tab-click": _vm.handleParamsStatus },
          model: {
            value: _vm.search.status,
            callback: function($$v) {
              _vm.$set(_vm.search, "status", $$v)
            },
            expression: "search.status"
          }
        },
        [
          _c("el-tab-pane", { attrs: { name: "0" } }, [
            _c("span", { attrs: { slot: "label" }, slot: "label" }, [
              _vm._v("\n                全部\n            ")
            ])
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "1" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("\n                未接单\n                "),
                this.statusQuantity[1] != undefined
                  ? _c("el-badge", { attrs: { value: this.statusQuantity[1] } })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "13" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("\n                代练中\n                "),
                this.statusQuantity[13] != undefined
                  ? _c("el-badge", {
                      attrs: { value: this.statusQuantity[13] }
                    })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "14" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("\n                待验收\n                "),
                this.statusQuantity[14] != undefined
                  ? _c("el-badge", {
                      attrs: { value: this.statusQuantity[14] }
                    })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "15" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("\n                撤销中\n                "),
                this.statusQuantity[15] != undefined
                  ? _c("el-badge", {
                      attrs: { value: this.statusQuantity[15] }
                    })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "16" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("\n                仲裁中\n                "),
                this.statusQuantity[16] != undefined
                  ? _c("el-badge", {
                      attrs: { value: this.statusQuantity[16] }
                    })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "99" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("\n                淘宝退款中\n                "),
                this.statusQuantity[99] != undefined
                  ? _c("el-badge", {
                      attrs: { value: this.statusQuantity[99] }
                    })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "17" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("\n                异常\n                "),
                this.statusQuantity[17] != undefined
                  ? _c("el-badge", {
                      attrs: { value: this.statusQuantity[17] }
                    })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "18" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("\n                已锁定\n                "),
                this.statusQuantity[18] != undefined
                  ? _c("el-badge", {
                      attrs: { value: this.statusQuantity[18] }
                    })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "19" } }, [
            _c("span", { attrs: { slot: "label" }, slot: "label" }, [
              _vm._v("\n                已撤销\n            ")
            ])
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "20" } }, [
            _c("span", { attrs: { slot: "label" }, slot: "label" }, [
              _vm._v("\n                已结算\n            ")
            ])
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "21" } }, [
            _c("span", { attrs: { slot: "label" }, slot: "label" }, [
              _vm._v("\n                已仲裁\n            ")
            ])
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "22" } }, [
            _c("span", { attrs: { slot: "label" }, slot: "label" }, [
              _vm._v("\n                已下架\n            ")
            ])
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "24" } }, [
            _c("span", { attrs: { slot: "label" }, slot: "label" }, [
              _vm._v("\n                已撤单\n            ")
            ])
          ])
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-table",
        {
          directives: [
            {
              name: "loading",
              rawName: "v-loading",
              value: _vm.tableLoading,
              expression: "tableLoading"
            }
          ],
          staticClass: "game-leveling-order-table",
          staticStyle: { width: "100%", height: "800px" },
          attrs: { height: _vm.tableHeight, data: _vm.tableData, border: "" },
          on: {
            "cell-mouse-enter": _vm.handleCellMouseEnter,
            "cell-mouse-leave": _vm.handleCellMouseLeave
          }
        },
        [
          _c("el-table-column", {
            attrs: {
              fixed: "",
              prop: "trade_no",
              label: "订单号",
              width: "250"
            },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _c(
                      "router-link",
                      {
                        attrs: {
                          to: {
                            name: "gameLevelingOrderShow",
                            query: { trade_no: scope.row.trade_no }
                          }
                        }
                      },
                      [
                        _c("div", { staticStyle: { "margin-left": "10px" } }, [
                          _vm._v(
                            "淘宝：" + _vm._s(scope.row.channel_order_trade_no)
                          )
                        ]),
                        _vm._v(" "),
                        _c("div", { staticStyle: { "margin-left": "10px" } }, [
                          _vm._v(
                            _vm._s(_vm.platformMap[scope.row.platform_id]) +
                              "：" +
                              _vm._s(scope.row.platform_trade_no)
                          )
                        ])
                      ]
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "status", label: "订单状态", width: "70" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(_vm.statusMap[scope.row.status]) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "province", label: "玩家旺旺", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(scope.row.buyer_nick) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "user_remark", label: "客服备注", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    scope.row.remark_edit
                      ? _c("el-input", {
                          attrs: { type: "textarea" },
                          model: {
                            value:
                              scope.row.game_leveling_order_detail.user_remark,
                            callback: function($$v) {
                              _vm.$set(
                                scope.row.game_leveling_order_detail,
                                "user_remark",
                                $$v
                              )
                            },
                            expression:
                              "scope.row.game_leveling_order_detail.user_remark"
                          }
                        })
                      : _c("span", [
                          _vm._v(
                            "\n                    " +
                              _vm._s(
                                scope.row.game_leveling_order_detail.user_remark
                              ) +
                              "\n                "
                          )
                        ])
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "title", label: "代练标题", width: "300" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "zip", label: "游戏/区/服", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _c("div", [
                      _vm._v(
                        _vm._s(scope.row.game_leveling_order_detail.game_name)
                      )
                    ]),
                    _vm._v(" "),
                    _c("div", [
                      _vm._v(
                        _vm._s(
                          scope.row.game_leveling_order_detail.game_region_name
                        )
                      )
                    ]),
                    _vm._v(" "),
                    _c("div", [
                      _vm._v(
                        _vm._s(
                          scope.row.game_leveling_order_detail.game_server_name
                        )
                      )
                    ])
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "game_role", label: "角色名称", width: "120" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "game_name", label: "账号/密码", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _c("div", [_vm._v(_vm._s(scope.row.game_account))]),
                    _vm._v(" "),
                    _c("div", [_vm._v(_vm._s(scope.row.game_password))])
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "amount", label: "代练价格", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(Number(scope.row.amount)) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "zip", label: "效率/安全保证金", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _c("div", [
                      _vm._v(_vm._s(Number(scope.row.security_deposit)))
                    ]),
                    _vm._v(" "),
                    _c("div", [
                      _vm._v(_vm._s(Number(scope.row.efficiency_deposit)))
                    ])
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "zip", label: "发单/接单时间", width: "140" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _c("div", [_vm._v(_vm._s(scope.row.created_at))]),
                    _vm._v(" "),
                    _c("div", [_vm._v(_vm._s(scope.row.take_at))])
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "zip", label: "代练时间", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(scope.row.day) +
                        " 天 " +
                        _vm._s(scope.row.hour) +
                        " 小时\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "left_time", label: "剩余时间", width: "120" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "zip", label: "打手QQ/电话", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _c("div", [
                      _vm._v(
                        _vm._s(
                          scope.row.game_leveling_order_detail.hatchet_man_phone
                        )
                      )
                    ]),
                    _vm._v(" "),
                    _c("div", [
                      _vm._v(
                        _vm._s(
                          scope.row.game_leveling_order_detail.hatchet_man_qq
                        )
                      )
                    ])
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "zip", label: "号主电话", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(
                          scope.row.game_leveling_order_detail.player_phone
                        ) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "zip", label: "来源价格", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(Number(scope.row.source_amount)) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "pay_amount", label: "支付代练费用", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(Number(scope.row.pay_amount)) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "get_amount", label: "获得赔偿金额", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(Number(scope.row.get_amount)) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "get_poundage", label: "手续费", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(Number(scope.row.get_poundage)) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "profit", label: "最终支付金额", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(Number(scope.row.profit)) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "zip", label: "发单客服", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(scope.row.game_leveling_order_detail.username) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { fixed: "right", label: "操作", width: "200" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    scope.row.status == 1
                      ? _c(
                          "div",
                          [
                            _c(
                              "el-button",
                              {
                                attrs: { size: "small" },
                                on: {
                                  click: function($event) {
                                    _vm.handleDelete(scope.row)
                                  }
                                }
                              },
                              [_vm._v("撤单\n                    ")]
                            ),
                            _vm._v(" "),
                            _c(
                              "el-button",
                              {
                                attrs: { size: "small", type: "primary" },
                                on: {
                                  click: function($event) {
                                    _vm.handleOffSale(scope.row)
                                  }
                                }
                              },
                              [_vm._v("下架\n                    ")]
                            )
                          ],
                          1
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    scope.row.status == 13
                      ? _c(
                          "div",
                          [
                            _c(
                              "el-button",
                              {
                                attrs: { size: "small" },
                                on: {
                                  click: function($event) {
                                    _vm.handleApplyConsult(scope.row)
                                  }
                                }
                              },
                              [_vm._v("协商撤销\n                    ")]
                            ),
                            _vm._v(" "),
                            _c(
                              "el-button",
                              {
                                attrs: { size: "small", type: "primary" },
                                on: {
                                  click: function($event) {
                                    _vm.handleApplyComplain(scope.row)
                                  }
                                }
                              },
                              [_vm._v("申请仲裁\n                    ")]
                            )
                          ],
                          1
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    scope.row.status == 14
                      ? _c(
                          "div",
                          [
                            _c(
                              "el-button",
                              {
                                attrs: { size: "small" },
                                on: {
                                  click: function($event) {
                                    _vm.handleApplyCompleteImage(scope.row)
                                  }
                                }
                              },
                              [_vm._v("查看图片")]
                            ),
                            _vm._v(" "),
                            _c(
                              "el-button",
                              {
                                attrs: { size: "small", type: "primary" },
                                on: {
                                  click: function($event) {
                                    _vm.handleComplete(scope.row)
                                  }
                                }
                              },
                              [_vm._v("完成验收")]
                            )
                          ],
                          1
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    scope.row.status == 15
                      ? _c(
                          "div",
                          [
                            scope.row.game_leveling_order_consult.initiator ==
                              1 &&
                            scope.row.game_leveling_order_consult.status == 1
                              ? _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleCancelConsult(scope.row)
                                      }
                                    }
                                  },
                                  [_vm._v("取消撤销\n                    ")]
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            scope.row.game_leveling_order_consult.initiator ==
                              2 &&
                            scope.row.game_leveling_order_consult.status == 1
                              ? _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleAgreeConsult(scope.row)
                                      }
                                    }
                                  },
                                  [_vm._v("同意撤销\n                    ")]
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            _c(
                              "el-button",
                              {
                                attrs: { size: "small", type: "primary" },
                                on: {
                                  click: function($event) {
                                    _vm.handleApplyComplain(scope.row)
                                  }
                                }
                              },
                              [_vm._v("申请仲裁")]
                            )
                          ],
                          1
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    scope.row.status == 16
                      ? _c(
                          "div",
                          [
                            scope.row.game_leveling_order_complain.initiator ==
                              1 &&
                            scope.row.game_leveling_order_complain.status == 1
                              ? _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleCancelComplain(scope.row)
                                      }
                                    }
                                  },
                                  [_vm._v("取消仲裁\n                    ")]
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            scope.row.game_leveling_order_consult &&
                            scope.row.game_leveling_order_consult.initiator ==
                              2 &&
                            scope.row.game_leveling_order_consult.status == 1
                              ? _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small", type: "primary" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleAgreeConsult(scope.row)
                                      }
                                    }
                                  },
                                  [_vm._v("同意撤销\n                    ")]
                                )
                              : _vm._e()
                          ],
                          1
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    scope.row.status == 17
                      ? _c(
                          "div",
                          [
                            _c(
                              "el-button",
                              {
                                attrs: { size: "small" },
                                on: {
                                  click: function($event) {
                                    _vm.handleLock(scope.row)
                                  }
                                }
                              },
                              [_vm._v("锁定\n                    ")]
                            ),
                            _vm._v(" "),
                            _c(
                              "el-button",
                              {
                                attrs: { size: "small", type: "primary" },
                                on: {
                                  click: function($event) {
                                    _vm.handleApplyConsult(scope.row)
                                  }
                                }
                              },
                              [_vm._v("协商撤销\n                    ")]
                            )
                          ],
                          1
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    scope.row.status == 18
                      ? _c(
                          "div",
                          [
                            _c(
                              "el-button",
                              {
                                attrs: { size: "small" },
                                on: {
                                  click: function($event) {
                                    _vm.handleCancelLock(scope.row)
                                  }
                                }
                              },
                              [_vm._v("取消锁定\n                    ")]
                            ),
                            _vm._v(" "),
                            _c(
                              "el-button",
                              {
                                attrs: { size: "small", type: "primary" },
                                on: {
                                  click: function($event) {
                                    _vm.handleApplyConsult(scope.row)
                                  }
                                }
                              },
                              [_vm._v("协商撤销\n                    ")]
                            )
                          ],
                          1
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    scope.row.status == 22
                      ? _c(
                          "div",
                          [
                            _c(
                              "el-button",
                              {
                                attrs: { size: "small" },
                                on: {
                                  click: function($event) {
                                    _vm.handleOnSale(scope.row)
                                  }
                                }
                              },
                              [_vm._v("上架")]
                            ),
                            _vm._v(" "),
                            _c(
                              "el-button",
                              {
                                attrs: { size: "small", type: "primary" },
                                on: {
                                  click: function($event) {
                                    _vm.handleDelete(scope.row)
                                  }
                                }
                              },
                              [_vm._v("撤单")]
                            )
                          ],
                          1
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    [19, 20, 21, 22, 23, 24].indexOf(scope.row.status) != -1
                      ? _c(
                          "div",
                          [
                            _c(
                              "router-link",
                              {
                                attrs: {
                                  to: {
                                    name: "gameLevelingOrderRepeat",
                                    query: { trade_no: scope.row.trade_no }
                                  }
                                }
                              },
                              [
                                _c("el-button", { attrs: { size: "small" } }, [
                                  _vm._v(
                                    "\n                            重发\n                        "
                                  )
                                ])
                              ],
                              1
                            )
                          ],
                          1
                        )
                      : _vm._e()
                  ]
                }
              }
            ])
          })
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "block", staticStyle: { "margin-top": "15px" } },
        [
          _c("el-pagination", {
            attrs: {
              background: "",
              "current-page": _vm.search.page,
              "page-size": 20,
              layout: "total, prev, pager, next, jumper",
              total: _vm.tableDataTotal
            },
            on: {
              "current-change": _vm.handleParamsPage,
              "update:currentPage": function($event) {
                _vm.$set(_vm.search, "page", $event)
              }
            }
          })
        ],
        1
      ),
      _vm._v(" "),
      _vm.applyComplainVisible
        ? _c("ApplyComplain", {
            attrs: { tradeNo: _vm.tradeNo },
            on: { handleApplyComplainVisible: _vm.handleApplyComplainVisible }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm.applyConsultVisible
        ? _c("ApplyConsult", {
            attrs: {
              tradeNo: _vm.tradeNo,
              amount: _vm.amount,
              securityDeposit: _vm.securityDeposit,
              efficiencyDeposit: _vm.efficiencyDeposit
            },
            on: { handleApplyConsultVisible: _vm.handleApplyConsultVisible }
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
    require("vue-hot-reload-api")      .rerender("data-v-11fc571e", module.exports)
  }
}

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-4f069d1e\",\"hasScoped\":true,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyConsult.vue":
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "el-dialog",
    {
      attrs: {
        width: "40%",
        title: "申请撤销",
        visible: true,
        "before-close": _vm.handleBeforeClose
      }
    },
    [
      _c(
        "el-form",
        {
          ref: "form",
          staticClass: "demo-ruleForm",
          attrs: {
            model: _vm.form,
            rules: _vm.consultRules,
            "label-width": "204px"
          }
        },
        [
          _c("el-alert", {
            staticStyle: { "margin-bottom": "15px" },
            attrs: {
              title:
                "双方友好协商撤单，若有分歧可以在订单中留言或申请客服介入；若申请成功，此单将被锁定，若双方取消撤单会退回至原有状态。",
              type: "success",
              closable: false
            }
          }),
          _vm._v(" "),
          _c(
            "el-form-item",
            { attrs: { label: "我已支付代练费（元）" } },
            [
              _c("el-input", {
                attrs: { type: "input", disabled: true },
                model: {
                  value: _vm.form.amount,
                  callback: function($$v) {
                    _vm.$set(_vm.form, "amount", $$v)
                  },
                  expression: "form.amount"
                }
              })
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "el-form-item",
            {
              attrs: { label: "我愿意支付代练费（元）", prop: "payment_amount" }
            },
            [
              _c("el-input", {
                attrs: { type: "input" },
                model: {
                  value: _vm.form.payment_amount,
                  callback: function($$v) {
                    _vm.$set(_vm.form, "payment_amount", _vm._n($$v))
                  },
                  expression: "form.payment_amount"
                }
              })
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "el-form-item",
            { attrs: { label: "对方已预付安全保证金（元）" } },
            [
              _c("el-input", {
                attrs: { type: "input", disabled: true },
                model: {
                  value: _vm.form.security_deposit,
                  callback: function($$v) {
                    _vm.$set(_vm.form, "security_deposit", $$v)
                  },
                  expression: "form.security_deposit"
                }
              })
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "el-form-item",
            { attrs: { label: "对方已预付效率保证金（元）" } },
            [
              _c("el-input", {
                attrs: { type: "input", disabled: true },
                model: {
                  value: _vm.form.efficiency_deposit,
                  callback: function($$v) {
                    _vm.$set(_vm.form, "efficiency_deposit", $$v)
                  },
                  expression: "form.efficiency_deposit"
                }
              })
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "el-form-item",
            { attrs: { label: "需要对方赔付保证金", prop: "payment_deposit" } },
            [
              _c("el-input", {
                attrs: { type: "input" },
                model: {
                  value: _vm.form.payment_deposit,
                  callback: function($$v) {
                    _vm.$set(_vm.form, "payment_deposit", _vm._n($$v))
                  },
                  expression: "form.payment_deposit"
                }
              })
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "el-form-item",
            { attrs: { label: "撤销理由", prop: "reason" } },
            [
              _c("el-input", {
                attrs: { type: "textarea", rows: 5 },
                model: {
                  value: _vm.form.reason,
                  callback: function($$v) {
                    _vm.$set(_vm.form, "reason", $$v)
                  },
                  expression: "form.reason"
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
                  on: {
                    click: function($event) {
                      _vm.handleSubmitForm("form")
                    }
                  }
                },
                [_vm._v("提交")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                {
                  on: {
                    click: function($event) {
                      _vm.handleResetForm("form")
                    }
                  }
                },
                [_vm._v("重置")]
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
    require("vue-hot-reload-api")      .rerender("data-v-4f069d1e", module.exports)
  }
}

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-ec590898\",\"hasScoped\":true,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyComplain.vue":
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "el-dialog",
    {
      attrs: {
        title: "申请仲裁",
        visible: true,
        "before-close": _vm.handleBeforeClose
      }
    },
    [
      _c(
        "el-form",
        {
          ref: "form",
          staticClass: "demo-ruleForm",
          attrs: {
            model: _vm.form,
            rules: _vm.complainRule,
            "label-width": "100px"
          }
        },
        [
          _c(
            "el-form-item",
            {
              ref: "image",
              attrs: {
                label: "仲裁证据",
                prop: "images",
                rules: [
                  {
                    required: true,
                    message: "最少上传一张图片",
                    trigger: "change"
                  }
                ]
              }
            },
            [
              _c(
                "el-upload",
                {
                  class: _vm.uploadExceedLimit,
                  attrs: {
                    action: "action",
                    "list-type": "picture-card",
                    limit: 3,
                    "on-preview": _vm.handleUploadPreview,
                    "on-remove": _vm.handleUploadRemove,
                    "http-request": _vm.handleUploadFile
                  }
                },
                [_c("i", { staticClass: "el-icon-plus" })]
              ),
              _vm._v(" "),
              _c(
                "el-dialog",
                {
                  attrs: { visible: _vm.dialogVisible },
                  on: {
                    "update:visible": function($event) {
                      _vm.dialogVisible = $event
                    }
                  }
                },
                [
                  _c("img", {
                    staticStyle: { "z-index": "2000" },
                    attrs: {
                      width: "100%",
                      modal: false,
                      "modal-append-to-body": false,
                      src: _vm.dialogImageUrl
                    }
                  })
                ]
              )
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "el-form-item",
            { attrs: { label: "仲裁原因", prop: "reason" } },
            [
              _c("el-input", {
                attrs: { type: "textarea", rows: 8 },
                model: {
                  value: _vm.form.reason,
                  callback: function($$v) {
                    _vm.$set(_vm.form, "reason", $$v)
                  },
                  expression: "form.reason"
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
                  on: {
                    click: function($event) {
                      _vm.handleSubmitForm("form")
                    }
                  }
                },
                [_vm._v("提交")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                {
                  on: {
                    click: function($event) {
                      _vm.handleResetForm("form")
                    }
                  }
                },
                [_vm._v("重置")]
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
    require("vue-hot-reload-api")      .rerender("data-v-ec590898", module.exports)
  }
}

/***/ }),

/***/ "./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-11fc571e\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/less-loader/dist/cjs.js!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/List.vue":
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__("./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-11fc571e\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/less-loader/dist/cjs.js!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/List.vue");
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__("./node_modules/vue-style-loader/lib/addStylesClient.js")("4233db82", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-11fc571e\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./List.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-11fc571e\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./List.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ "./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4f069d1e\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyConsult.vue":
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__("./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4f069d1e\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyConsult.vue");
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__("./node_modules/vue-style-loader/lib/addStylesClient.js")("478f99ec", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4f069d1e\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ApplyConsult.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4f069d1e\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ApplyConsult.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ "./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ec590898\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyComplain.vue":
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__("./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ec590898\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyComplain.vue");
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__("./node_modules/vue-style-loader/lib/addStylesClient.js")("ac5ad76c", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ec590898\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ApplyComplain.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ec590898\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ApplyComplain.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/order/game-leveling/ApplyComplain.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__("./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ec590898\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyComplain.vue")
}
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyComplain.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-ec590898\",\"hasScoped\":true,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyComplain.vue")
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-ec590898"
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
Component.options.__file = "resources/assets/frontend/js/components/order/game-leveling/ApplyComplain.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-ec590898", Component.options)
  } else {
    hotAPI.reload("data-v-ec590898", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ "./resources/assets/frontend/js/components/order/game-leveling/ApplyConsult.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__("./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4f069d1e\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyConsult.vue")
}
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyConsult.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-4f069d1e\",\"hasScoped\":true,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyConsult.vue")
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-4f069d1e"
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
Component.options.__file = "resources/assets/frontend/js/components/order/game-leveling/ApplyConsult.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-4f069d1e", Component.options)
  } else {
    hotAPI.reload("data-v-4f069d1e", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ "./resources/assets/frontend/js/components/order/game-leveling/List.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__("./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-11fc571e\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/less-loader/dist/cjs.js!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/List.vue")
}
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/List.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-11fc571e\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/List.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/order/game-leveling/List.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-11fc571e", Component.options)
  } else {
    hotAPI.reload("data-v-11fc571e", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});