webpackJsonp([0],{

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

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/Show.vue":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__ApplyComplain__ = __webpack_require__("./resources/assets/frontend/js/components/order/game-leveling/ApplyComplain.vue");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__ApplyComplain___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__ApplyComplain__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__ApplyConsult__ = __webpack_require__("./resources/assets/frontend/js/components/order/game-leveling/ApplyConsult.vue");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__ApplyConsult___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__ApplyConsult__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__GameLevelingRequirement__ = __webpack_require__("./resources/assets/frontend/js/components/order/game-leveling/GameLevelingRequirement.vue");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__GameLevelingRequirement___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2__GameLevelingRequirement__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__BusinessmanQQ__ = __webpack_require__("./resources/assets/frontend/js/components/order/game-leveling/BusinessmanQQ.vue");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__BusinessmanQQ___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3__BusinessmanQQ__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    name: "GameLevelingShow",
    components: {
        ApplyComplain: __WEBPACK_IMPORTED_MODULE_0__ApplyComplain___default.a,
        ApplyConsult: __WEBPACK_IMPORTED_MODULE_1__ApplyConsult___default.a,
        GameLevelingRequirement: __WEBPACK_IMPORTED_MODULE_2__GameLevelingRequirement___default.a,
        BusinessmanQQ: __WEBPACK_IMPORTED_MODULE_3__BusinessmanQQ___default.a
    },
    computed: {
        fieldDisabled: function fieldDisabled() {
            if (this.form.status === 1 || this.form.status === 22) {
                return false;
            } else {
                return true;
            }
        },
        isComplain: function isComplain() {
            if (this.form.status === 16 || this.form.status === 21) {
                return false;
            } else {
                return true;
            }
        },
        displayFooter: function displayFooter() {
            if (this.orderTab === "1") {
                return true;
            } else {
                return false;
            }
        },

        // 商户投诉图片上传数量限制 3 张
        businessmanComplainImageExceedLimit: function businessmanComplainImageExceedLimit() {
            return [this.businessmanComplainForm.images.length === 3 ? 'exceed' : ' '];
        },

        // 仲裁证据补充图片上传数量限制 1 张
        complainMessageImageExceedLimit: function complainMessageImageExceedLimit() {
            return [this.complainMessageForm.pic !== '' ? 'exceed' : ' '];
        }
    },
    data: function data() {
        var _this = this;

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
            fileReader: '',
            amount: 0,
            securityDeposit: 0,
            efficiencyDeposit: 0,
            chatVisible: false,
            applyConsultVisible: false, // 申请协商
            applyComplainVisible: false, // 申请仲裁
            businessmanComplainVisible: false, // 商户投诉
            orderTab: "1",
            dataTab: "1",
            gameRegionServerOptions: [], // 游戏/区/服 选项
            dayHourOptions: [], // 天数/小时  选项
            gameLevelingTypeOptions: [], // 游戏代练类型 选项
            addTimeForm: {
                day: 0, // 增加的天数
                hour: 0 // 增加的小时
            },
            addTimeFormRules: {
                day: [{ validator: function validator(rule, value, callback) {
                        if (_this.addTimeForm.day === "" && _this.addTimeForm.hour === "") {
                            callback(new Error("加价天数与小时不能都为空"));
                        } else if (/^[1-9][0-9]*$/.test(value) == false && value != 0) {
                            callback(new Error("加价小时不能为小数"));
                        } else {
                            callback();
                        }
                    }, trigger: ['change', 'blur'] }],
                hour: [{ validator: function validator(rule, value, callback) {
                        if (_this.addTimeForm.day === 0 && _this.addTimeForm.hour === 0) {
                            callback(new Error("加价天数与小时不能都为0"));
                        } else if (_this.addTimeForm.day === "" && _this.addTimeForm.hour === "") {
                            callback(new Error("加价天数与小时不能都为空"));
                        } else if (/^[1-9][0-9]*$/.test(value) == false && value != 0) {
                            callback(new Error("加价小时不能为小数"));
                        } else {
                            callback();
                        }
                    }, trigger: ['change', 'blur'] }]
            },
            addTimeDialogVisible: false,
            chatData: [],
            businessmanComplainForm: {
                trade_no: this.$route.query.trade_no,
                images: [],
                amount: '',
                reason: '',
                dialogVisible: false,
                dialogImageUrl: ''
            },
            complainMessageForm: {
                trade_no: this.$route.query.trade_no,
                reason: '',
                pic: '',
                dialogVisible: false,
                dialogImageUrl: ''
            },
            chatForm: {
                trade_no: this.$route.query.trade_no,
                content: ''
            },
            form: {
                trade_no: this.$route.query.trade_no,
                status: 0,
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
                gameLevelingRequirementId: '',
                consult_describe: ''
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
            status: {
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
            platform: {
                5: '丸子代练',
                1: '91代练',
                3: '蚂蚁代练'
            },
            platformData: [],
            taobaoData: [],
            logData: [],
            complainDesData: [],
            complainImage: {
                pic1: '',
                pic2: '',
                pic3: ''
            },
            complainMessageData: []
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
        handleFromStatus: function handleFromStatus() {
            return this.form.status == 1 || this.form.status == 22 ? false : false;
        },
        handleFromData: function handleFromData() {
            var _this2 = this;

            this.$api.gameLevelingOrderEdit({ trade_no: this.$route.query.trade_no }).then(function (res) {
                _this2.trade_no = res.trade_no;
                _this2.amount = res.amount;
                _this2.securityDeposit = res.security_deposit;
                _this2.efficiencyDeposit = res.efficiency_deposit;
                _this2.form.status = res.status;
                _this2.form.game_leveling_order_consult = res.game_leveling_order_consult;
                _this2.form.game_leveling_order_complain = res.game_leveling_order_complain;
                _this2.form.game_region_server = [// 选择的 游戏/区/服
                res.game_id, res.game_region_id, res.game_server_id];
                _this2.handleFromGameLevelingTypeIdOptions();
                _this2.form.day_hour = [// 选择的代练天/小时
                res.day, res.hour];
                _this2.form.day = res.day;
                _this2.form.hour = res.hour;
                _this2.form.game_id = res.game_id; // 游戏ID
                _this2.form.game_region_id = res.game_region_id; // 游戏区ID
                _this2.form.game_server_id = res.game_server_id; // 游戏服务器ID
                _this2.form.game_leveling_type_id = res.game_leveling_type_id; // 代练类型ID
                _this2.form.amount = res.amount; // 代练金额
                _this2.form.source_amount = res.source_amount; // 来源价格
                _this2.form.security_deposit = res.security_deposit; // 安全保证金
                _this2.form.efficiency_deposit = res.efficiency_deposit; // 效率保证金
                _this2.form.title = res.title; //代练标题
                _this2.form.game_role = res.game_role; // 游戏角色
                _this2.form.game_account = res.game_account; // 游戏账号
                _this2.form.game_password = res.game_password; // 游戏密码
                _this2.form.price_increase_step = res.price_increase_step != '0.0000' ? Number(res.price_increase_step) : ''; // 自动加价步长
                _this2.form.price_ceiling = res.price_ceiling != '0.0000' ? Number(res.price_ceiling) : ''; // 自动加价上限
                _this2.form.explain = res.game_leveling_order_detail.explain; // 代练说明
                _this2.form.requirement = res.game_leveling_order_detail.requirement; // 代练要求
                _this2.form.take_order_password = res.take_order_password; // 接单密码
                _this2.form.player_phone = res.game_leveling_order_detail.player_phone; // 玩家电话
                _this2.form.user_qq = res.game_leveling_order_detail.user_qq; // 商家qq
                _this2.form.remark = res.game_leveling_order_detail.user_remark;
                _this2.form.domains = [];
                _this2.form.consult_describe = res.consult_describe;
                // 平台数据
                _this2.platformData = [{
                    name: '平台单号',
                    value: res.trade_no
                }, {
                    name: '订单状态',
                    value: _this2.status[res.status]
                }, {
                    name: '接单平台',
                    value: _this2.platform[res.platform_id]
                }, {
                    name: '打手呢称',
                    value: res.game_leveling_order_detail.hatchet_man_name
                }, {
                    name: '打手电话',
                    value: res.game_leveling_order_detail.hatchet_man_phone
                }, {
                    name: '打手QQ',
                    value: res.game_leveling_order_detail.hatchet_man_qq
                }, {
                    name: '剩余代练时间',
                    value: res.left_time
                }, {
                    name: '发布时间',
                    value: res.created_at
                }, {
                    name: '接单时间',
                    value: res.take_at
                }, {
                    name: '提验时间',
                    value: res.apply_complete_at
                }, {
                    name: '结算时间',
                    value: res.complete_at
                }, {
                    name: '发单客服',
                    value: res.game_leveling_order_detail.username
                }, {
                    name: '撤销说明',
                    value: res.consult_describe
                }, {
                    name: '仲裁说明',
                    value: res.complain_describe
                }, {
                    name: '支付代练费用',
                    value: res.pay_amount
                }, {
                    name: '获得赔偿金额',
                    value: res.get_amount
                }, {
                    name: '手续费',
                    value: res.get_poundage
                }, {
                    name: '最终支付金额',
                    value: res.complain_amount
                }];
                _this2.taobaoData = [{
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
            var _this3 = this;

            this.$api.gameRegionServer().then(function (res) {
                _this3.gameRegionServerOptions = res.data;
            }).catch(function (err) {});
        },
        handleFromGameLevelingTypeIdOptions: function handleFromGameLevelingTypeIdOptions(val) {
            var _this4 = this;

            this.$api.gameLevelingTypes({
                'game_id': this.form.game_region_server[2]
            }).then(function (res) {
                _this4.gameLevelingTypeOptions = res.data;
            }).catch(function (err) {});
            this.handleAutoChoseTemplate();
        },
        handleSubmitForm: function handleSubmitForm(formName) {
            var _this5 = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this5.form.game_id = _this5.form.game_region_server[0];
                    _this5.form.game_region_id = _this5.form.game_region_server[1];
                    _this5.form.game_server_id = _this5.form.game_region_server[2];

                    _this5.$api.gameLevelingOrderUpdate(_this5.form).then(function (res) {
                        _this5.$message({
                            'type': res.status == 1 ? 'success' : 'error',
                            'message': res.message
                        });
                    }).catch(function (err) {
                        _this5.$message({
                            'type': 'error',
                            'message': '修改订单失败，服务器错误！'
                        });
                    });
                }
            });
        },
        handleResetForm: function handleResetForm(formName) {
            this.$refs[formName].resetFields();
        },

        // 增加代练价格
        handleAddAmount: function handleAddAmount() {
            var _this6 = this;

            this.$prompt('请输入需要加增加的价格', '增加代练价格', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                inputPattern: /^[0-9]+.?[0-9]*$/,
                inputErrorMessage: '代练价格只能为数字'
            }).then(function (_ref) {
                var value = _ref.value;

                // 发送加价请求 value 为写入的值
                _this6.$api.gameLevelingOrderAddAmount({
                    trade_no: _this6.form.trade_no,
                    amount: value
                }).then(function (res) {
                    _this6.$message({
                        'type': res.status == 1 ? 'success' : 'error',
                        'message': res.message
                    });
                }).catch(function (err) {
                    _this6.$message({
                        'type': 'error',
                        'message': '加价失败，服务器错误！'
                    });
                });
            });
        },

        // 增加天数与小时
        handleAddDayHour: function handleAddDayHour() {
            var _this7 = this;

            this.$refs.addTimeForm.validate(function (valid) {
                if (valid) {
                    // 发送加天与小时请求
                    _this7.$api.gameLevelingOrderAddDayHour({
                        trade_no: _this7.form.trade_no,
                        day: _this7.addTimeForm.day,
                        hour: _this7.addTimeForm.hour
                    }).then(function (res) {
                        _this7.$message({
                            'type': res.status == 1 ? 'success' : 'error',
                            'message': res.message
                        });
                        if (res.status == 1) {
                            _this7.addTimeDialogVisible = false;
                        }
                    }).catch(function (err) {
                        _this7.$message({
                            'type': 'error',
                            'message': '加时失败，服务器错误！'
                        });
                    });
                }
            });
        },
        handleOrderTab: function handleOrderTab(tab, event) {
            var _this8 = this;

            if (tab.name == 2) {
                this.handleComplainData();
            }
            // 订单操作日志
            if (tab.name == 3) {
                this.$api.gameLevelingOrderLog({ trade_no: this.$route.query.trade_no }).then(function (res) {
                    _this8.logData = res;
                });
            }
        },

        // 删除补款单号
        removeDomain: function removeDomain(item) {
            var index = this.form.domains.indexOf(item);
            if (index !== -1) {
                this.form.domains.splice(index, 1);
            }
        },

        // 添加补款单号
        addDomain: function addDomain() {
            this.form.domains.push({
                value: '',
                key: Date.now()
            });
        },

        // 撤单
        handleDelete: function handleDelete(row) {
            var _this9 = this;

            this.$confirm('您确定要"撤单"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this9.$api.gameLevelingOrderDelete({
                    'trade_no': _this9.form.trade_no
                }).then(function (res) {
                    _this9.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this9.handleFromData();
                    }
                }).catch(function (err) {
                    _this9.$message({
                        message: '操作失败',
                        type: 'error'
                    });
                });
            });
        },

        // 上架
        handleOnSale: function handleOnSale(row) {
            var _this10 = this;

            this.$confirm('您确定要"上架"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this10.$api.gameLevelingOrderOnSale({
                    'trade_no': _this10.form.trade_no
                }).then(function (res) {
                    _this10.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this10.handleFromData();
                    }
                }).catch(function (err) {
                    _this10.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 下架
        handleOffSale: function handleOffSale(row) {
            var _this11 = this;

            this.$confirm('您确定要"下架"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this11.$api.gameLevelingOrderOffSale({
                    'trade_no': _this11.form.trade_no
                }).then(function (res) {
                    _this11.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this11.handleFromData();
                    }
                }).catch(function (err) {
                    _this11.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 申请仲裁
        handleApplyComplain: function handleApplyComplain(row) {
            this.$route.query.trade_no = this.form.trade_no;
            this.applyComplainVisible = true;
        },

        // 取消仲裁
        handleCancelComplain: function handleCancelComplain(row) {
            var _this12 = this;

            this.$confirm('您确定要"取消仲裁"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this12.$api.gameLevelingOrderCancelComplain({
                    'trade_no': _this12.form.trade_no
                }).then(function (res) {
                    _this12.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this12.handleFromData();
                    }
                }).catch(function (err) {
                    _this12.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 查看图片
        handleApplyCompleteImage: function handleApplyCompleteImage() {
            var _this13 = this;

            // 请求图片
            this.$api.gameLevelingOrderApplyCompleteImage({
                'trade_no': this.form.trade_no
            }).then(function (res) {
                if (res.status == 1) {
                    var h = _this13.$createElement;
                    var item = [];
                    res.content.forEach(function (val) {
                        item.push(h('el-carousel-item', null, [h('img', {
                            attrs: {
                                src: val['url'],
                                class: 'avatar'
                            }
                        }, '')]));
                    });

                    _this13.$msgbox({
                        title: '查看验收图片',
                        message: h('el-carousel', null, item),
                        showCancelButton: true,
                        confirmButtonText: '确定',
                        cancelButtonText: '取消'
                    });
                } else {
                    _this13.$message({
                        type: 'error',
                        message: res.message
                    });
                }
            }).catch(function (err) {
                _this13.$message({
                    type: 'error',
                    message: '操作失败'
                });
            });

            // const h = this.$createElement;
            // const currentThis = this;
            // this.$msgbox({
            //     title: '查看验收图片',
            //     message: h('el-carousel', {
            //         // props: {
            //         //     options:this.dayHourOptions,
            //         // },
            //     }, '<h3>3</h3>'),
            //     showCancelButton: true,
            //     confirmButtonText: '确定',
            //     cancelButtonText: '取消',
            //     beforeClose: (action, instance, done) => {
            //         if (action == 'confirm') {
            //             // 发送加天与小时请求
            //             this.$api.gameLevelingOrderAddDayHour({
            //                 trade_no: this.form.trade_no,
            //                 day: this.addDay,
            //                 hour: this.addHour
            //             }).then(res => {
            //                 this.$message({
            //                     'type': res.status == 1 ? 'success' : 'error',
            //                     'message': res.message,
            //                 });
            //                 if (res.status == 1) {
            //                     done();
            //                 }
            //             }).catch(err => {
            //                 this.$message({
            //                     'type': 'error',
            //                     'message': '加时失败，服务器错误！',
            //                 });
            //             });
            //         } else {
            //             done();
            //         }
            //     }
            // });
        },

        // 完成验收
        handleComplete: function handleComplete(row) {
            var _this14 = this;

            this.$confirm('您确定要"完成验收"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this14.$api.gameLevelingOrderComplete({
                    'trade_no': _this14.form.trade_no
                }).then(function (res) {
                    _this14.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this14.handleFromData();
                    }
                }).catch(function (err) {
                    _this14.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 申请撤销
        handleApplyConsult: function handleApplyConsult(row) {
            this.$route.query.trade_no = this.form.trade_no;
            this.amount = this.amount;
            this.securityDeposit = this.securityDeposit;
            this.efficiencyDeposit = this.efficiencyDeposit;
            this.applyConsultVisible = true;
        },

        // 设置仲裁窗口是否显示
        handleApplyComplainVisible: function handleApplyComplainVisible(data) {
            this.applyComplainVisible = data.visible;
            if (data.visible == false) {
                this.handleFromData();
            }
        },

        // 设置协商窗口是否显示
        handleApplyConsultVisible: function handleApplyConsultVisible(data) {
            this.applyConsultVisible = data.visible;
            if (data.visible == false) {
                this.handleFromData();
            }
        },

        // 取消撤销
        handleCancelConsult: function handleCancelConsult() {
            var _this15 = this;

            this.$confirm('您确定要"取消撤销"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this15.$api.gameLevelingOrderCancelConsult({
                    'trade_no': _this15.form.trade_no
                }).then(function (res) {
                    _this15.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this15.handleFromData();
                    }
                }).catch(function (err) {
                    _this15.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 同意撤销
        handleAgreeConsult: function handleAgreeConsult(row) {
            var _this16 = this;

            var str = this.form.consult_describe + " ，确认 同意撤销 吗?";

            this.$confirm(str, '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this16.$api.gameLevelingOrderAgreeConsult({
                    'trade_no': _this16.form.trade_no
                }).then(function (res) {
                    _this16.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this16.handleFromData();
                    }
                }).catch(function (err) {
                    _this16.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 不同意撤销
        handleRejectConsult: function handleRejectConsult(row) {
            var _this17 = this;

            this.$confirm('您确定"不同意撤销"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this17.$api.gameLevelingOrderRejectConsult({
                    'trade_no': _this17.form.trade_no
                }).then(function (res) {
                    _this17.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this17.handleFromData();
                    }
                }).catch(function (err) {
                    _this17.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 锁定
        handleLock: function handleLock() {
            var _this18 = this;

            this.$confirm('您确定要"锁定"订单吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this18.$api.gameLevelingOrderLock({
                    'trade_no': _this18.form.trade_no
                }).then(function (res) {
                    _this18.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this18.handleFromData();
                    }
                }).catch(function (err) {
                    _this18.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 取消锁定
        handleCancelLock: function handleCancelLock(row) {
            var _this19 = this;

            this.$confirm('您确定要"取消锁定"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this19.$api.gameLevelingOrderCancelLock({
                    'trade_no': _this19.form.trade_no
                }).then(function (res) {
                    _this19.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });

                    if (res.status == 1) {
                        _this19.handleFromData();
                    }
                }).catch(function (err) {
                    _this19.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 重新下单
        handleRepeatOrder: function handleRepeatOrder() {
            location.href = this.orderRepeatApi + '/' + this.$route.query.trade_no;
        },

        // 查看图片大图
        handleOpenImage: function handleOpenImage(src) {
            var h = this.$createElement;
            this.$msgbox({
                center: true,
                showConfirmButton: false,
                customClass: 'preview-image',
                message: h('img', { attrs: { src: src } }, '')
            });
        },

        // 获取仲裁数据
        handleComplainData: function handleComplainData() {
            var _this20 = this;

            this.$api.gameLevelingOrderComplainInfo({ trade_no: this.$route.query.trade_no }).then(function (res) {
                if (res.detail) {
                    _this20.complainDesData = [res.detail];
                }
                if (_this20.complainDesData[0].pic1) {
                    _this20.complainImage.pic1 = _this20.complainDesData[0].pic1;
                }
                if (_this20.complainDesData[0].pic2) {
                    _this20.complainImage.pic2 = _this20.complainDesData[0].pic2;
                }
                if (_this20.complainDesData[0].pic3) {
                    _this20.complainImage.pic3 = _this20.complainDesData[0].pic3;
                }
                if (res.info) {
                    _this20.complainMessageData = res.info;
                }
            });
        },

        // 添加仲裁留言
        handleAddComplainMessageForm: function handleAddComplainMessageForm() {
            var _this21 = this;

            this.$api.gameLevelingOrderAddComplainInfo(this.complainMessageForm).then(function (res) {
                if (res.status == 1) {
                    _this21.$message.success('发送成功');
                    _this21.complainMessageForm.reason = '';
                    _this21.handleComplainData();
                }
            });
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

        // 仲裁证据补充图片删除
        handleRemoveComplainMessageImage: function handleRemoveComplainMessageImage() {
            this.complainMessageForm.pic = '';
        },

        // 仲裁证据补充图片上传
        handleUploadComplainMessageImage: function handleUploadComplainMessageImage(options) {
            var _this22 = this;

            var file = options.file;
            if (file) {
                this.fileReader.readAsDataURL(file);
            }
            this.fileReader.onload = function () {
                _this22.complainMessageForm.pic = _this22.fileReader.result;
            };
        },

        // 打开聊天窗口
        handleOpenChat: function handleOpenChat() {
            this.chatVisible = true;
            this.handleChatData();
        },

        // 加载聊天数据
        handleChatData: function handleChatData() {
            var _this23 = this;

            this.$api.gameLevelingOrderMessage(this.chatForm).then(function (res) {
                if (res.status == 1) {
                    _this23.chatData = res.content;

                    setTimeout(function () {
                        var chatWindowsHeight = document.querySelector(".chat-main").scrollHeight;
                        _this23.$nextTick(function () {
                            document.querySelector(".chat-main").scrollTop = chatWindowsHeight;
                        });
                        _this23.chatForm.content = '';
                    }, 80);
                }
            });
        },

        // 发送聊天数据
        handleChatForm: function handleChatForm() {
            var _this24 = this;

            this.$api.gameLevelingOrderSendMessage(this.chatForm).then(function (res) {
                if (res.status == 1) {
                    _this24.handleChatData();
                }
            });
        },

        // 关闭聊天窗口
        handleCloseChat: function handleCloseChat() {
            this.chatVisible = false;
        },

        // 投诉弹窗
        handleBusinessmanComplainVisible: function handleBusinessmanComplainVisible() {
            if (this.businessmanComplainVisible == false) {
                return this.businessmanComplainVisible = true;
            } else {
                return this.businessmanComplainVisible = false;
            }
        },

        // 提交商户投诉表单
        handleSubmitBusinessmanComplainForm: function handleSubmitBusinessmanComplainForm(formName) {
            var _this25 = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this25.$api.gameLevelingOrderBusinessmanComplainStore(_this25.businessmanComplainForm).then(function (res) {
                        _this25.$message({
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });
                        if (res.status == 1) {
                            _this25.businessmanComplainVisible = false;
                            _this25.handleFromData();
                        }
                    });
                }
            });
        },

        // 上传商户投诉图片
        handleUploadBusinessmanComplainImage: function handleUploadBusinessmanComplainImage(options) {
            var _this26 = this;

            var file = options.file;
            if (file) {
                this.fileReader.readAsDataURL(file);
            }
            this.fileReader.onload = function () {
                _this26.businessmanComplainForm.images.push(_this26.fileReader.result);
                _this26.$refs.image.clearValidate();
            };
        },

        // 删除图片
        handleRemoveBusinessmanComplainImage: function handleRemoveBusinessmanComplainImage(file, fileList) {
            var index = this.businessmanComplainForm.images.indexOf(file.response);
            this.businessmanComplainForm.images.splice(index, 1);
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
            var _this27 = this;

            this.$api.businessmanContactTemplate().then(function (res) {
                _this27.businessmanQQOptions = res.data;
            });
        },

        // 游戏代练要求选项
        gameLevelingRequirementOption: function gameLevelingRequirementOption() {
            var _this28 = this;

            this.$api.gameLevelingRequirementTemplate().then(function (res) {
                _this28.gameLevelingRequirementOptions = res.data;
            });
        },
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
    created: function created() {},
    mounted: function mounted() {
        this.businessmanQQOption();
        this.gameLevelingRequirementOption();
        this.handleFromData();
        this.handleFromGameRegionServerOptions();
        this.handleDayOption();
        this.handleHourOption();
        this.fileReader = new FileReader();
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

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4f069d1e\",\"scoped\":true,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/ApplyConsult.vue":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.avatar-uploader .el-upload[data-v-4f069d1e] {\n    border: 1px dashed #d9d9d9;\n    border-radius: 6px;\n    cursor: pointer;\n    position: relative;\n    overflow: hidden;\n}\n.avatar-uploader .el-upload[data-v-4f069d1e]:hover {\n    border-color: #409EFF;\n}\n.avatar-uploader-icon[data-v-4f069d1e] {\n    font-size: 28px;\n    color: #8c939d;\n    width: 178px;\n    height: 178px;\n    line-height: 178px;\n    text-align: center;\n}\n.avatar[data-v-4f069d1e] {\n    width: 178px;\n    height: 178px;\n    display: block;\n}\n\n", ""]);

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

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-daca9160\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/less-loader/dist/cjs.js!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/Show.vue":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.add-time-dialog {\n  border-radius: 4px;\n  width: 420px;\n}\n.add-time-dialog .el-dialog__body {\n  padding: 10px 20px 5px;\n  text-align: right;\n}\n.img-box {\n  width: auto;\n  padding: 0;\n  background-color: transparent;\n  border: none;\n  -webkit-box-shadow: none;\n          box-shadow: none;\n}\n.el-col {\n  border-radius: 4px;\n}\n.grid-content {\n  border-radius: 4px;\n  min-height: 36px;\n}\n.game-leveling-order-create .el-card__body {\n  padding: 20px 20px 10px;\n}\n.game-leveling-order-create .el-card {\n  border-radius: 0;\n  border: 1px solid #ebeef5;\n  background-color: #fff;\n  overflow: hidden;\n  color: #303133;\n  -webkit-transition: none;\n  transition: none;\n}\n.game-leveling-order-create .el-card__header {\n  padding: 10px 20px;\n}\n.game-leveling-order-create .footer {\n  height: 60px;\n  background-color: #fff;\n  position: fixed;\n  bottom: 0;\n  width: 100%;\n  /*box-shadow:inset 0px 15px 15px -15px rgba(0, 0, 0, 0.1);*/\n  /*!*-webkit-box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);*!*/\n  -webkit-box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);\n          box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);\n}\n#chat .el-dialog__header {\n  background-color: #efefef;\n}\n#chat .el-dialog__header .el-dialog__title {\n  font-size: 16px;\n}\n#chat .el-dialog__body {\n  padding: 0 20px 20px 20px;\n}\n#chat .el-dialog__footer {\n  padding: 0 20px 10px;\n  padding-top: 0;\n}\n.chat-title {\n  position: absolute;\n  top: -80px;\n  height: 80px;\n}\n.chat-main {\n  height: 350px;\n  overflow-x: hidden;\n  overflow-y: auto;\n}\n.chat-main ul .chat-mine {\n  text-align: right;\n  padding-left: 0;\n  padding-right: 60px;\n}\n.chat-main ul .chat-mine .chat-user {\n  position: absolute;\n  left: auto;\n  right: 3px;\n}\n.chat-main ul .chat-mine .chat-user img {\n  width: 40px;\n  height: 40px;\n  border-radius: 100%;\n}\n.chat-main ul .chat-mine .chat-user cite {\n  left: auto;\n  right: 60px;\n  text-align: right;\n}\n.chat-main ul .chat-mine .chat-user cite i {\n  padding-left: 0;\n  padding-right: 15px;\n}\n.chat-main ul .chat-mine .chat-text {\n  margin-left: 0;\n  text-align: left;\n  background-color: #5FB878;\n  color: #fff;\n}\n.chat-main ul .chat-mine .chat-text:after {\n  left: auto;\n  right: -10px;\n  border-top-color: #5FB878;\n}\n.chat-main ul li {\n  position: relative;\n  font-size: 0;\n  margin-bottom: 10px;\n  padding-left: 60px;\n  min-height: 68px;\n}\n.chat-main ul li .chat-user {\n  display: inline-block;\n  vertical-align: top;\n  font-size: 14px;\n  position: absolute;\n  left: 3px;\n}\n.chat-main ul li .chat-user img {\n  width: 40px;\n  height: 40px;\n  border-radius: 100%;\n}\n.chat-main ul li .chat-user cite {\n  position: absolute;\n  left: 60px;\n  top: -2px;\n  width: 500px;\n  line-height: 24px;\n  font-size: 12px;\n  white-space: nowrap;\n  color: #999;\n  text-align: left;\n  font-style: normal;\n}\n.chat-main ul li .chat-user cite i {\n  padding-left: 15px;\n  font-style: normal;\n}\n.chat-main ul li .chat-text {\n  position: relative;\n  line-height: 22px;\n  margin-top: 25px;\n  padding: 8px 15px;\n  background-color: #e2e2e2;\n  border-radius: 3px;\n  color: #333;\n  word-break: break-all;\n  max-width: 462px \\9;\n  display: inline-block;\n  vertical-align: top;\n  font-size: 14px;\n}\n.chat-main ul li .chat-text:after {\n  content: '';\n  position: absolute;\n  left: -10px;\n  top: 13px;\n  width: 0;\n  height: 0;\n  border-style: solid dashed dashed;\n  border-color: #e2e2e2 transparent transparent;\n  overflow: hidden;\n  border-width: 10px;\n}\n.exceed .el-upload {\n  display: none;\n}\n.avatar {\n  width: 100%;\n  height: 100%;\n  display: block;\n}\n", ""]);

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

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-daca9160\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/Show.vue":
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "game-leveling-order-create" },
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
                      style: {
                        "margin-bottom": _vm.displayFooter ? "60px" : "15px"
                      },
                      attrs: { span: 16 }
                    },
                    [
                      _c(
                        "div",
                        {
                          staticClass: "grid-content bg-purple",
                          staticStyle: {
                            padding: "15px",
                            "background-color": "#fff",
                            position: "relative"
                          }
                        },
                        [
                          _c(
                            "el-tabs",
                            {
                              on: { "tab-click": _vm.handleOrderTab },
                              model: {
                                value: _vm.orderTab,
                                callback: function($$v) {
                                  _vm.orderTab = $$v
                                },
                                expression: "orderTab"
                              }
                            },
                            [
                              _c(
                                "el-tab-pane",
                                { attrs: { label: "订单信息", name: "1" } },
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                                                                  disabled:
                                                                    _vm.fieldDisabled,
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                                                            [
                                                              _vm.form
                                                                .status ===
                                                                13 ||
                                                              _vm.form
                                                                .status ===
                                                                14 ||
                                                              _vm.form
                                                                .status ===
                                                                17 ||
                                                              _vm.form
                                                                .status === 18
                                                                ? _c(
                                                                    "div",
                                                                    [
                                                                      _c(
                                                                        "el-input",
                                                                        {
                                                                          attrs: {
                                                                            type:
                                                                              "input",
                                                                            autocomplete:
                                                                              "off"
                                                                          },
                                                                          model: {
                                                                            value:
                                                                              _vm
                                                                                .form
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
                                                                        }
                                                                      )
                                                                    ],
                                                                    1
                                                                  )
                                                                : _c(
                                                                    "div",
                                                                    [
                                                                      _c(
                                                                        "el-input",
                                                                        {
                                                                          attrs: {
                                                                            disabled:
                                                                              _vm.fieldDisabled,
                                                                            type:
                                                                              "input",
                                                                            autocomplete:
                                                                              "off"
                                                                          },
                                                                          model: {
                                                                            value:
                                                                              _vm
                                                                                .form
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
                                                                        }
                                                                      )
                                                                    ],
                                                                    1
                                                                  )
                                                            ]
                                                          ],
                                                          2
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                                                                          disabled:
                                                                            _vm.fieldDisabled,
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
                                                                          disabled:
                                                                            _vm.fieldDisabled,
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
                                                        _c(
                                                          "el-col",
                                                          {
                                                            attrs: { span: 1 }
                                                          },
                                                          [
                                                            _vm.form.status ==
                                                              13 ||
                                                            _vm.form.status ==
                                                              14 ||
                                                            _vm.form.status ==
                                                              17
                                                              ? _c(
                                                                  "span",
                                                                  {
                                                                    staticClass:
                                                                      "icon-button",
                                                                    on: {
                                                                      click: function(
                                                                        $event
                                                                      ) {
                                                                        $event.preventDefault()
                                                                        _vm.addTimeDialogVisible = true
                                                                      }
                                                                    }
                                                                  },
                                                                  [
                                                                    _c("i", {
                                                                      staticClass:
                                                                        "el-icon-circle-plus-outline"
                                                                    })
                                                                  ]
                                                                )
                                                              : _vm._e()
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
                                                                  disabled:
                                                                    _vm.fieldDisabled,
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
                                                            _vm.form.status ===
                                                            1
                                                              ? _c(
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
                                                              : _vm._e()
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                                                                    $$v
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
                                                        _c(
                                                          "el-col",
                                                          {
                                                            staticClass:
                                                              "icon-button",
                                                            attrs: { span: 1 }
                                                          },
                                                          [
                                                            _vm.form.status ==
                                                              13 ||
                                                            _vm.form.status ==
                                                              14 ||
                                                            _vm.form.status ==
                                                              17
                                                              ? _c("i", {
                                                                  staticClass:
                                                                    "el-icon-circle-plus",
                                                                  on: {
                                                                    click: function(
                                                                      $event
                                                                    ) {
                                                                      $event.preventDefault()
                                                                      _vm.handleAddAmount()
                                                                    }
                                                                  }
                                                                })
                                                              : _vm._e()
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                                                                    $$v
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                                                                    $$v
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                                                                    $$v
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                                                                  disabled:
                                                                    _vm.fieldDisabled,
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
                                                            _vm.form.status ===
                                                            1
                                                              ? _c(
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
                                                              : _vm._e()
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                                                                disabled:
                                                                  _vm.fieldDisabled,
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
                              ),
                              _vm._v(" "),
                              _c(
                                "el-tab-pane",
                                {
                                  attrs: {
                                    label: "仲裁证据",
                                    disabled: _vm.isComplain,
                                    name: "2"
                                  }
                                },
                                [
                                  _c(
                                    "el-table",
                                    {
                                      staticStyle: { width: "100%" },
                                      attrs: {
                                        data: _vm.complainDesData,
                                        stripe: true,
                                        border: true
                                      }
                                    },
                                    [
                                      _c("el-table-column", {
                                        attrs: {
                                          prop: "who",
                                          label: "申请仲裁",
                                          width: "180"
                                        }
                                      }),
                                      _vm._v(" "),
                                      _c("el-table-column", {
                                        attrs: {
                                          prop: "created_at",
                                          label: "申请时间",
                                          width: "180"
                                        }
                                      }),
                                      _vm._v(" "),
                                      _c("el-table-column", {
                                        attrs: {
                                          prop: "content",
                                          label: "仲裁理由"
                                        }
                                      })
                                    ],
                                    1
                                  ),
                                  _vm._v(" "),
                                  _c("p"),
                                  _vm._v(" "),
                                  _c(
                                    "el-row",
                                    { attrs: { gutter: 12 } },
                                    [
                                      _vm.complainImage.pic1
                                        ? _c(
                                            "el-col",
                                            { attrs: { span: 8 } },
                                            [
                                              _vm.complainImage.pic1
                                                ? _c("el-card", {
                                                    style: {
                                                      backgroundImage:
                                                        "url(" +
                                                        _vm.complainImage.pic1 +
                                                        ")",
                                                      height: "150px",
                                                      backgroundSize: "cover",
                                                      width: "100%",
                                                      display: "block"
                                                    },
                                                    nativeOn: {
                                                      click: function($event) {
                                                        _vm.handleOpenImage(
                                                          _vm.complainImage.pic1
                                                        )
                                                      }
                                                    }
                                                  })
                                                : _vm._e()
                                            ],
                                            1
                                          )
                                        : _vm._e(),
                                      _vm._v(" "),
                                      _vm.complainImage.pic2
                                        ? _c(
                                            "el-col",
                                            { attrs: { span: 8 } },
                                            [
                                              _vm.complainImage.pic2
                                                ? _c("el-card", {
                                                    style: {
                                                      backgroundImage:
                                                        "url(" +
                                                        _vm.complainImage.pic2 +
                                                        ")",
                                                      height: "150px",
                                                      backgroundSize: "cover",
                                                      width: "100%"
                                                    },
                                                    nativeOn: {
                                                      click: function($event) {
                                                        _vm.handleOpenImage(
                                                          _vm.complainImage.pic2
                                                        )
                                                      }
                                                    }
                                                  })
                                                : _vm._e()
                                            ],
                                            1
                                          )
                                        : _vm._e(),
                                      _vm._v(" "),
                                      _vm.complainImage.pic3
                                        ? _c(
                                            "el-col",
                                            { attrs: { span: 8 } },
                                            [
                                              _vm.complainImage.pic3
                                                ? _c("el-card", {
                                                    style: {
                                                      backgroundImage:
                                                        "url(" +
                                                        _vm.complainDesData[0]
                                                          .pic3 +
                                                        ")",
                                                      height: "150px",
                                                      backgroundSize: "cover",
                                                      width: "100%"
                                                    },
                                                    nativeOn: {
                                                      click: function($event) {
                                                        _vm.handleOpenImage(
                                                          _vm.complainDesData[0]
                                                            .pic3
                                                        )
                                                      }
                                                    }
                                                  })
                                                : _vm._e()
                                            ],
                                            1
                                          )
                                        : _vm._e()
                                    ],
                                    1
                                  ),
                                  _vm._v(" "),
                                  _c("p"),
                                  _vm._v(" "),
                                  _c(
                                    "el-table",
                                    {
                                      staticStyle: { width: "100%" },
                                      attrs: {
                                        data: _vm.complainMessageData,
                                        stripe: true,
                                        border: true
                                      }
                                    },
                                    [
                                      _c("el-table-column", {
                                        attrs: { prop: "who", label: "留言方" }
                                      }),
                                      _vm._v(" "),
                                      _c("el-table-column", {
                                        attrs: {
                                          prop: "content",
                                          label: "留言说明"
                                        }
                                      }),
                                      _vm._v(" "),
                                      _c("el-table-column", {
                                        attrs: {
                                          prop: "created_at",
                                          label: "留言时间"
                                        }
                                      }),
                                      _vm._v(" "),
                                      _c("el-table-column", {
                                        attrs: {
                                          prop: "address",
                                          label: "留言证据",
                                          width: "80"
                                        },
                                        scopedSlots: _vm._u([
                                          {
                                            key: "default",
                                            fn: function(scope) {
                                              return [
                                                scope.row.pic
                                                  ? _c("el-button", {
                                                      attrs: {
                                                        icon: "el-icon-search"
                                                      },
                                                      nativeOn: {
                                                        click: function(
                                                          $event
                                                        ) {
                                                          _vm.handleOpenImage(
                                                            scope.row.pic
                                                          )
                                                        }
                                                      }
                                                    })
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
                                  _c("p"),
                                  _vm._v(" "),
                                  _c(
                                    "el-form",
                                    {
                                      ref: "complainMessageForm",
                                      staticClass: "demo-ruleForm",
                                      attrs: {
                                        model: _vm.complainMessageForm,
                                        "label-width": "100px"
                                      }
                                    },
                                    [
                                      _c(
                                        "el-form-item",
                                        {
                                          attrs: {
                                            label: "留言内容",
                                            rules: [
                                              {
                                                required: true,
                                                message: "留言内容不能为空"
                                              }
                                            ]
                                          }
                                        },
                                        [
                                          _c("el-input", {
                                            attrs: {
                                              type: "textarea",
                                              rows: 6
                                            },
                                            model: {
                                              value:
                                                _vm.complainMessageForm.reason,
                                              callback: function($$v) {
                                                _vm.$set(
                                                  _vm.complainMessageForm,
                                                  "reason",
                                                  $$v
                                                )
                                              },
                                              expression:
                                                "complainMessageForm.reason"
                                            }
                                          })
                                        ],
                                        1
                                      ),
                                      _vm._v(" "),
                                      _c(
                                        "el-form-item",
                                        { attrs: { label: "上传证据" } },
                                        [
                                          _c(
                                            "el-upload",
                                            {
                                              class:
                                                _vm.complainMessageImageExceedLimit,
                                              attrs: {
                                                action: "action",
                                                "list-type": "picture-card",
                                                limit: 1,
                                                "on-preview":
                                                  _vm.handleUploadPreview,
                                                "on-remove":
                                                  _vm.handleRemoveComplainMessageImage,
                                                "http-request":
                                                  _vm.handleUploadComplainMessageImage
                                              }
                                            },
                                            [
                                              _c("i", {
                                                staticClass: "el-icon-plus"
                                              })
                                            ]
                                          )
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
                                                  _vm.handleAddComplainMessageForm()
                                                }
                                              }
                                            },
                                            [
                                              _vm._v(
                                                "提交\n                                        "
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
                              ),
                              _vm._v(" "),
                              _c(
                                "el-tab-pane",
                                { attrs: { label: "操作记录", name: "3" } },
                                [
                                  _c(
                                    "el-table",
                                    {
                                      staticStyle: {
                                        width: "100%",
                                        "margin-bottom": "15px"
                                      },
                                      attrs: { data: _vm.logData, border: "" }
                                    },
                                    [
                                      _c("el-table-column", {
                                        attrs: {
                                          prop: "username",
                                          label: "操作人",
                                          width: "180"
                                        }
                                      }),
                                      _vm._v(" "),
                                      _c("el-table-column", {
                                        attrs: {
                                          prop: "name",
                                          label: "操作名",
                                          width: "180"
                                        }
                                      }),
                                      _vm._v(" "),
                                      _c("el-table-column", {
                                        attrs: {
                                          prop: "description",
                                          label: "描述"
                                        }
                                      }),
                                      _vm._v(" "),
                                      _c("el-table-column", {
                                        attrs: {
                                          prop: "created_at",
                                          label: "时间"
                                        }
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
                            "el-button",
                            {
                              staticStyle: {
                                position: "absolute",
                                right: "15px",
                                top: "15px"
                              },
                              attrs: {
                                type: "primary",
                                icon: "el-icon-document"
                              },
                              on: { click: _vm.handleOpenChat }
                            },
                            [
                              _vm._v(
                                "\n                            订单留言\n                        "
                              )
                            ]
                          ),
                          _vm._v(" "),
                          _vm.form.status === 14
                            ? _c(
                                "el-button",
                                {
                                  staticStyle: {
                                    position: "absolute",
                                    right: "125px",
                                    top: "15px"
                                  },
                                  attrs: {
                                    type: "primary",
                                    icon: "el-icon-search"
                                  },
                                  on: { click: _vm.handleApplyCompleteImage }
                                },
                                [_vm._v("查看图片\n                        ")]
                              )
                            : _vm._e()
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
                      {
                        model: {
                          value: _vm.dataTab,
                          callback: function($$v) {
                            _vm.dataTab = $$v
                          },
                          expression: "dataTab"
                        }
                      },
                      [
                        _c(
                          "el-tab-pane",
                          { attrs: { label: "平台数据", name: "1" } },
                          [
                            _c(
                              "el-table",
                              {
                                staticStyle: { width: "100%" },
                                attrs: {
                                  data: _vm.platformData,
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
                        ),
                        _vm._v(" "),
                        _c(
                          "el-tab-pane",
                          { attrs: { label: "淘宝数据", name: "2" } },
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
      _vm.displayFooter
        ? _c(
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
                        _vm.form.status == 1 ||
                        _vm.form.status == 13 ||
                        _vm.form.status == 14 ||
                        _vm.form.status == 17 ||
                        _vm.form.status == 18 ||
                        _vm.form.status == 22 ||
                        _vm.form.status == 22
                          ? _c(
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
                              [_vm._v("确认修改\n                    ")]
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.form.status == 1
                          ? _c(
                              "span",
                              [
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleDelete()
                                      }
                                    }
                                  },
                                  [_vm._v("撤单")]
                                ),
                                _vm._v(" "),
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small", type: "primary" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleOffSale()
                                      }
                                    }
                                  },
                                  [_vm._v("下架")]
                                )
                              ],
                              1
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.form.status == 13
                          ? _c(
                              "span",
                              [
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleApplyConsult()
                                      }
                                    }
                                  },
                                  [_vm._v("撤销")]
                                ),
                                _vm._v(" "),
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small", type: "primary" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleApplyComplain()
                                      }
                                    }
                                  },
                                  [_vm._v("仲裁")]
                                )
                              ],
                              1
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.form.status == 14
                          ? _c(
                              "span",
                              [
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleComplete()
                                      }
                                    }
                                  },
                                  [_vm._v("完成")]
                                ),
                                _vm._v(" "),
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleApplyConsult()
                                      }
                                    }
                                  },
                                  [_vm._v("撤销")]
                                ),
                                _vm._v(" "),
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small", type: "primary" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleApplyComplain()
                                      }
                                    }
                                  },
                                  [_vm._v("仲裁")]
                                ),
                                _vm._v(" "),
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleLock()
                                      }
                                    }
                                  },
                                  [_vm._v("锁定")]
                                )
                              ],
                              1
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.form.status == 15
                          ? _c(
                              "span",
                              [
                                this.form.game_leveling_order_consult
                                  .initiator == 1 &&
                                this.form.game_leveling_order_consult.status ==
                                  1
                                  ? _c(
                                      "el-button",
                                      {
                                        attrs: { size: "small" },
                                        on: {
                                          click: function($event) {
                                            _vm.handleCancelConsult()
                                          }
                                        }
                                      },
                                      [_vm._v("取消撤销")]
                                    )
                                  : _vm._e(),
                                _vm._v(" "),
                                this.form.game_leveling_order_consult
                                  .initiator == 2 &&
                                this.form.game_leveling_order_consult.status ==
                                  1
                                  ? _c(
                                      "el-button",
                                      {
                                        attrs: { size: "small" },
                                        on: {
                                          click: function($event) {
                                            _vm.handleAgreeConsult()
                                          }
                                        }
                                      },
                                      [_vm._v("同意撤销")]
                                    )
                                  : _vm._e(),
                                _vm._v(" "),
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small", type: "primary" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleApplyComplain()
                                      }
                                    }
                                  },
                                  [_vm._v("仲裁")]
                                )
                              ],
                              1
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.form.status == 16
                          ? _c(
                              "span",
                              [
                                this.form.game_leveling_order_consult &&
                                this.form.game_leveling_order_consult
                                  .initiator == 2 &&
                                this.form.game_leveling_order_consult.status ==
                                  1
                                  ? _c(
                                      "el-button",
                                      {
                                        attrs: { size: "small" },
                                        on: {
                                          click: function($event) {
                                            _vm.handleAgreeConsult()
                                          }
                                        }
                                      },
                                      [_vm._v("同意撤销")]
                                    )
                                  : _vm._e(),
                                _vm._v(" "),
                                this.form.game_leveling_order_complain
                                  .initiator == 1 &&
                                this.form.game_leveling_order_complain.status ==
                                  1
                                  ? _c(
                                      "el-button",
                                      {
                                        attrs: {
                                          size: "small",
                                          type: "primary"
                                        },
                                        on: {
                                          click: function($event) {
                                            _vm.handleCancelComplain()
                                          }
                                        }
                                      },
                                      [_vm._v("取消仲裁")]
                                    )
                                  : _vm._e()
                              ],
                              1
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.form.status == 17
                          ? _c(
                              "span",
                              [
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small", type: "primary" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleApplyConsult()
                                      }
                                    }
                                  },
                                  [_vm._v("撤销")]
                                ),
                                _vm._v(" "),
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleLock()
                                      }
                                    }
                                  },
                                  [_vm._v("锁定")]
                                )
                              ],
                              1
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.form.status == 18
                          ? _c(
                              "span",
                              [
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleCancelLock()
                                      }
                                    }
                                  },
                                  [_vm._v("取消锁定")]
                                ),
                                _vm._v(" "),
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small", type: "primary" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleApplyConsult()
                                      }
                                    }
                                  },
                                  [_vm._v("撤销")]
                                )
                              ],
                              1
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.form.status == 22
                          ? _c(
                              "span",
                              [
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small", type: "primary" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleOnSale()
                                      }
                                    }
                                  },
                                  [_vm._v("上架")]
                                )
                              ],
                              1
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        [19, 20, 21, 23, 24].indexOf(_vm.form.status) != -1
                          ? _c(
                              "span",
                              [
                                _c(
                                  "router-link",
                                  {
                                    attrs: {
                                      to: {
                                        name: "gameLevelingOrderRepeat",
                                        query: {
                                          trade_no: _vm.$route.query.trade_no
                                        }
                                      }
                                    }
                                  },
                                  [
                                    _c(
                                      "el-button",
                                      {
                                        attrs: {
                                          size: "small",
                                          type: "primary"
                                        }
                                      },
                                      [_vm._v("重发")]
                                    )
                                  ],
                                  1
                                ),
                                _vm._v(" "),
                                _c(
                                  "el-button",
                                  {
                                    attrs: { size: "small", type: "primary" },
                                    on: {
                                      click: function($event) {
                                        _vm.handleBusinessmanComplainVisible()
                                      }
                                    }
                                  },
                                  [_vm._v("投诉")]
                                )
                              ],
                              1
                            )
                          : _vm._e()
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
        : _vm._e(),
      _vm._v(" "),
      _c(
        "div",
        { attrs: { id: "chat" } },
        [
          _c(
            "el-dialog",
            {
              attrs: {
                title: "订单留言",
                visible: _vm.chatVisible,
                "before-close": _vm.handleCloseChat
              }
            },
            [
              _c("div", { staticClass: "chat-main" }, [
                _c(
                  "ul",
                  { staticStyle: { padding: "0" } },
                  _vm._l(_vm.chatData, function(item) {
                    return _c(
                      "li",
                      {
                        class: [item.sender == "您" ? "chat-mine" : "chat-user"]
                      },
                      [
                        _c("div", { staticClass: "chat-user" }, [
                          _c("img", {
                            attrs: {
                              src: "/frontend/v2/images/message_avatar.jpg"
                            }
                          }),
                          _vm._v(" "),
                          _c("cite", [
                            _c("i", [_vm._v(_vm._s(item.send_time) + " ")]),
                            _vm._v(
                              " " +
                                _vm._s(item.sender) +
                                "\n                            "
                            )
                          ])
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "chat-text" }, [
                          _vm._v(_vm._s(item.send_content))
                        ])
                      ]
                    )
                  })
                )
              ]),
              _vm._v(" "),
              _c(
                "el-form",
                { attrs: { model: _vm.form } },
                [
                  _c("el-input", {
                    attrs: { type: "textarea", rows: 5 },
                    model: {
                      value: _vm.chatForm.content,
                      callback: function($$v) {
                        _vm.$set(_vm.chatForm, "content", $$v)
                      },
                      expression: "chatForm.content"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "div",
                {
                  staticClass: "dialog-footer",
                  attrs: { slot: "footer" },
                  slot: "footer"
                },
                [
                  _c(
                    "el-button",
                    {
                      attrs: { type: "primary" },
                      on: { click: _vm.handleChatForm }
                    },
                    [_vm._v("发送留言")]
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
      [
        _c(
          "el-dialog",
          {
            attrs: {
              title: "订单投诉",
              "before-close": _vm.handleBusinessmanComplainVisible,
              visible: _vm.businessmanComplainVisible
            }
          },
          [
            _c(
              "el-form",
              {
                ref: "businessmanComplainForm",
                staticClass: "demo-ruleForm",
                attrs: {
                  model: _vm.businessmanComplainForm,
                  "label-width": "110px"
                }
              },
              [
                _c(
                  "el-form-item",
                  {
                    ref: "image",
                    attrs: {
                      label: "证据截图",
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
                        class: _vm.businessmanComplainImageExceedLimit,
                        attrs: {
                          action: "action",
                          "list-type": "picture-card",
                          limit: 3,
                          "on-remove": _vm.handleRemoveBusinessmanComplainImage,
                          "http-request":
                            _vm.handleUploadBusinessmanComplainImage
                        }
                      },
                      [_c("i", { staticClass: "el-icon-plus" })]
                    ),
                    _vm._v(" "),
                    _c(
                      "el-dialog",
                      {
                        attrs: {
                          visiblec: _vm.businessmanComplainForm.dialogVisible
                        }
                      },
                      [
                        _c("img", {
                          attrs: {
                            width: "100%",
                            src: _vm.businessmanComplainForm.dialogImageUrl
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
                  {
                    attrs: {
                      prop: "amount",
                      rules: [
                        {
                          required: true,
                          message: "赔偿金额不能为空",
                          trigger: "blur"
                        },
                        {
                          type: "number",
                          message: "赔偿金额必须为数字值",
                          trigger: "blur"
                        }
                      ],
                      label: "要求赔偿金额"
                    }
                  },
                  [
                    _c("el-input", {
                      attrs: { type: "input", rows: 8 },
                      model: {
                        value: _vm.businessmanComplainForm.amount,
                        callback: function($$v) {
                          _vm.$set(
                            _vm.businessmanComplainForm,
                            "amount",
                            _vm._n($$v)
                          )
                        },
                        expression: "businessmanComplainForm.amount"
                      }
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "el-form-item",
                  {
                    attrs: {
                      label: "投诉原因",
                      rules: [{ required: true, message: "投诉原因不能为空" }],
                      prop: "reason"
                    }
                  },
                  [
                    _c("el-input", {
                      attrs: { type: "textarea", rows: 8 },
                      model: {
                        value: _vm.businessmanComplainForm.reason,
                        callback: function($$v) {
                          _vm.$set(_vm.businessmanComplainForm, "reason", $$v)
                        },
                        expression: "businessmanComplainForm.reason"
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
                            _vm.handleSubmitBusinessmanComplainForm(
                              "businessmanComplainForm"
                            )
                          }
                        }
                      },
                      [_vm._v("提交\n                    ")]
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
        : _vm._e(),
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
        : _vm._e(),
      _vm._v(" "),
      _c(
        "el-dialog",
        {
          attrs: {
            top: "35vh",
            "custom-class": "add-time-dialog",
            title: "增加代练时间",
            visible: _vm.addTimeDialogVisible
          },
          on: {
            "update:visible": function($event) {
              _vm.addTimeDialogVisible = $event
            }
          }
        },
        [
          _c(
            "el-form",
            {
              ref: "addTimeForm",
              attrs: {
                rules: _vm.addTimeFormRules,
                model: _vm.addTimeForm,
                "label-width": "80px"
              }
            },
            [
              _c(
                "el-form-item",
                { attrs: { prop: "day", label: "天" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.addTimeForm.day,
                      callback: function($$v) {
                        _vm.$set(_vm.addTimeForm, "day", _vm._n($$v))
                      },
                      expression: "addTimeForm.day"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { prop: "hour", label: "小时" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.addTimeForm.hour,
                      callback: function($$v) {
                        _vm.$set(_vm.addTimeForm, "hour", _vm._n($$v))
                      },
                      expression: "addTimeForm.hour"
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
                      on: {
                        click: function($event) {
                          _vm.addTimeDialogVisible = false
                        }
                      }
                    },
                    [_vm._v("取消")]
                  ),
                  _vm._v(" "),
                  _c(
                    "el-button",
                    {
                      attrs: { type: "primary" },
                      on: { click: _vm.handleAddDayHour }
                    },
                    [_vm._v("确认")]
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
    2
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-daca9160", module.exports)
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

/***/ "./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-daca9160\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/less-loader/dist/cjs.js!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/Show.vue":
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__("./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-daca9160\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/less-loader/dist/cjs.js!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/Show.vue");
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__("./node_modules/vue-style-loader/lib/addStylesClient.js")("44dc7bbc", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-daca9160\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Show.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-daca9160\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Show.vue");
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

/***/ "./resources/assets/frontend/js/components/order/game-leveling/Show.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__("./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-daca9160\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/less-loader/dist/cjs.js!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/Show.vue")
}
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/Show.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-daca9160\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/Show.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/order/game-leveling/Show.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-daca9160", Component.options)
  } else {
    hotAPI.reload("data-v-daca9160", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});