webpackJsonp([13],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/setting/Auxiliary.vue":
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
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    methods: {
        // 新增按钮
        markupAdd: function markupAdd() {
            this.dialogFormVisible = true;
            this.isAdd = true;
            this.isUpdate = false;
            this.title = "新增";
            this.form = {
                id: '',
                markup_amount: '',
                markup_time: '',
                markup_type: 0,
                markup_money: '',
                markup_frequency: '',
                markup_number: ''
            };
        },

        // 编辑按钮
        markupUpdate: function markupUpdate(row) {
            this.dialogFormVisible = true;
            this.form = JSON.parse(JSON.stringify(row));
            this.isAdd = false;
            this.title = "修改";
            this.isUpdate = true;
        },

        // 取消按钮
        markupCancel: function markupCancel(formName) {
            this.dialogFormVisible = false;
            this.$refs[formName].clearValidate();
        },
        handleClick: function handleClick(tab, event) {
            this.handleTableDataChannel();
        },

        // 加价添加
        submitFormAdd: function submitFormAdd(formName) {
            var _this = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this.$api.SettingMarkupAdd(_this.form).then(function (res) {
                        _this.$message({
                            showClose: true,
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });
                        _this.handleTableData();
                    }).catch(function (err) {
                        _this.$message({
                            type: 'error',
                            message: '操作失败'
                        });
                    });
                } else {
                    return false;
                }
                _this.$refs[formName].clearValidate();
            });
        },

        // 加价修改
        submitFormUpdate: function submitFormUpdate(formName) {
            var _this2 = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this2.$api.SettingMarkupUpdate(_this2.form).then(function (res) {
                        _this2.$message({
                            showClose: true,
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });
                        _this2.handleTableData();
                    }).catch(function (err) {
                        _this2.$message({
                            type: 'error',
                            message: '操作失败'
                        });
                    });
                } else {
                    return false;
                }
            });
        },

        // 加价删除
        markupDelete: function markupDelete(id) {
            var _this3 = this;

            this.$confirm('您确定要删除吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this3.$api.SettingMarkupDelete({ id: id }).then(function (res) {
                    _this3.$message({
                        showClose: true,
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });
                    _this3.handleTableData();
                }).catch(function (err) {
                    _this3.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 加载加价数据
        handleTableData: function handleTableData() {
            var _this4 = this;

            this.$api.SettingMarkupDataList(this.searchParams).then(function (res) {
                _this4.tableData = res.data;
                _this4.TotalPage = res.total;
            }).catch(function (err) {
                _this4.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },

        // 渠道数据
        handleTableDataChannel: function handleTableDataChannel() {
            var _this5 = this;

            this.$api.SettingChannelDataList().then(function (res) {
                _this5.tableDataChannel = res;
            }).catch(function (err) {
                _this5.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },
        handleSearch: function handleSearch() {
            this.handleTableData();
        },
        handleCurrentChange: function handleCurrentChange(page) {
            this.searchParams.page = page;
            this.handleTableData();
        },

        // 渠道开关选择
        switchChange: function switchChange(gameId, gameName, platformIds) {
            var _this6 = this;

            this.$api.SettingChannelSwitch({ game_id: gameId, game_name: gameName, thirds: platformIds }).then(function (res) {
                _this6.$message({
                    showClose: true,
                    type: res.status == 1 ? 'success' : 'error',
                    message: res.message
                });
                _this6.handleTableData();
            }).catch(function (err) {
                _this6.$message({
                    type: 'error',
                    message: '操作失败'
                });
            });
        },

        // 表格高度计算
        handleTableHeight: function handleTableHeight() {
            this.tableHeight = window.innerHeight - 318;
        }
    },
    created: function created() {
        this.handleTableData();
        this.handleTableHeight();
        window.addEventListener('resize', this.handleTableHeight);
    },
    destroyed: function destroyed() {
        window.removeEventListener('resize', this.handleTableHeight);
    },
    data: function data() {
        var greaterZero = function greaterZero(rule, value, callback) {
            if (parseInt(value) < 0) {
                callback(new Error('不可填写负数!'));
            }
            callback();
        };
        return {
            tableHeight: 0,
            channelGroup: [],
            title: '新增',
            activeName: 'markup',
            isAdd: true,
            isUpdate: false,
            dialogFormVisible: false,
            rules: {
                markup_amount: [{ validator: greaterZero, trigger: 'blur' }, { required: true, message: '必填项不可为空', trigger: 'blur' }],
                hour: [{ validator: greaterZero, trigger: 'blur' }, { required: true, message: '必填项不可为空', trigger: 'blur' }, { type: 'number', message: '必须为数字', trigger: 'blur' }],
                minute: [{ validator: greaterZero, trigger: 'blur' }, { required: true, message: '必填项不可为空', trigger: 'blur' }, { type: 'number', message: '必须为数字', trigger: 'blur' }],
                markup_type: [{ validator: greaterZero, trigger: 'blur' }, { required: true, message: '必填项不可为空', trigger: 'blur' }, { type: 'number', message: '必须为数字', trigger: 'blur' }],
                markup_time: [{ validator: greaterZero, trigger: 'blur' }, { required: true, message: '必填项不可为空', trigger: 'blur' }, { type: 'number', message: '必须为数字', trigger: 'blur' }],
                markup_money: [{ validator: greaterZero, trigger: 'blur' }, { required: true, message: '必填项不可为空', trigger: 'blur' }],
                markup_frequency: [{ validator: greaterZero, trigger: 'blur' }, { required: true, message: '必填项不可为空', trigger: 'blur' }, { type: 'number', message: '必须为数字', trigger: 'blur' }],
                markup_number: [{ validator: greaterZero, trigger: 'blur' }, { required: true, message: '必填项不可为空', trigger: 'blur' }, { type: 'number', message: '必须为数字', trigger: 'blur' }]
            },
            tableData: [],
            tableDataChannel: [],
            searchParams: {
                page: 1
            },
            TotalPage: 0,
            type: {
                0: '绝对值',
                1: '百分比'
            },
            form: {
                id: '',
                markup_amount: '',
                markup_time: '',
                markup_type: 0,
                markup_money: '',
                markup_frequency: '',
                markup_number: ''
            },
            channelForm: {
                channel: ''
            }
        };
    }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-755392fb\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/setting/Auxiliary.vue":
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "main content amount-flow" },
    [
      _c(
        "el-tabs",
        {
          on: { "tab-click": _vm.handleClick },
          model: {
            value: _vm.activeName,
            callback: function($$v) {
              _vm.activeName = $$v
            },
            expression: "activeName"
          }
        },
        [
          _c(
            "el-tab-pane",
            { attrs: { label: "自动加价设置", name: "markup" } },
            [
              _c("el-alert", {
                staticStyle: { "margin-bottom": "15px" },
                attrs: {
                  title:
                    "操作提示: “自动加价”功能可以自动给“未接单”状态的订单增加代练费。",
                  type: "success",
                  closable: false
                }
              }),
              _vm._v(" "),
              _c(
                "el-form",
                {
                  staticClass: "search-form-inline",
                  attrs: {
                    inline: true,
                    model: _vm.searchParams,
                    size: "small"
                  }
                },
                [
                  _c(
                    "el-row",
                    { attrs: { gutter: 16 } },
                    [
                      _c(
                        "el-col",
                        { attrs: { span: 5 } },
                        [
                          _c(
                            "el-form-item",
                            [
                              _c(
                                "el-button",
                                {
                                  attrs: { type: "primary", size: "small" },
                                  on: {
                                    click: function($event) {
                                      _vm.markupAdd()
                                    }
                                  }
                                },
                                [_vm._v("新增")]
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
                "el-table",
                {
                  staticStyle: { width: "100%", "margin-top": "1px" },
                  attrs: {
                    data: _vm.tableData,
                    height: _vm.tableHeight,
                    border: ""
                  }
                },
                [
                  _c("el-table-column", {
                    attrs: {
                      prop: "markup_amount",
                      label: "价格区间",
                      width: "200"
                    },
                    scopedSlots: _vm._u([
                      {
                        key: "default",
                        fn: function(scope) {
                          return [
                            _vm._v(
                              "\n                        0 < 发单价 <= " +
                                _vm._s(scope.row.markup_amount) +
                                "\n                    "
                            )
                          ]
                        }
                      }
                    ])
                  }),
                  _vm._v(" "),
                  _c("el-table-column", {
                    attrs: {
                      prop: "markup_time",
                      label: "加价开始时间",
                      width: ""
                    }
                  }),
                  _vm._v(" "),
                  _c("el-table-column", {
                    attrs: {
                      prop: "markup_type",
                      label: "加价类型",
                      width: ""
                    },
                    scopedSlots: _vm._u([
                      {
                        key: "default",
                        fn: function(scope) {
                          return [
                            _vm._v(
                              "\n                        " +
                                _vm._s(_vm.type[scope.row.markup_type]) +
                                "\n                    "
                            )
                          ]
                        }
                      }
                    ])
                  }),
                  _vm._v(" "),
                  _c("el-table-column", {
                    attrs: {
                      prop: "markup_money",
                      label: "增加金额",
                      width: ""
                    }
                  }),
                  _vm._v(" "),
                  _c("el-table-column", {
                    attrs: {
                      prop: "markup_frequency",
                      label: "加价频率",
                      width: ""
                    }
                  }),
                  _vm._v(" "),
                  _c("el-table-column", {
                    attrs: {
                      prop: "markup_number",
                      label: "加价次数限制",
                      width: ""
                    }
                  }),
                  _vm._v(" "),
                  _c("el-table-column", {
                    attrs: { label: "操作", width: "200" },
                    scopedSlots: _vm._u([
                      {
                        key: "default",
                        fn: function(scope) {
                          return [
                            _c(
                              "el-button",
                              {
                                attrs: { type: "primary", size: "small" },
                                on: {
                                  click: function($event) {
                                    _vm.markupUpdate(scope.row)
                                  }
                                }
                              },
                              [_vm._v("编辑")]
                            ),
                            _vm._v(" "),
                            _c(
                              "el-button",
                              {
                                attrs: { type: "primary", size: "small" },
                                on: {
                                  click: function($event) {
                                    _vm.markupDelete(scope.row.id)
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
              ),
              _vm._v(" "),
              _c("el-pagination", {
                staticStyle: { "margin-top": "25px" },
                attrs: {
                  background: "",
                  "current-page": _vm.searchParams.page,
                  "page-size": 15,
                  layout: "total, prev, pager, next, jumper",
                  total: _vm.TotalPage
                },
                on: {
                  "current-change": _vm.handleCurrentChange,
                  "update:currentPage": function($event) {
                    _vm.$set(_vm.searchParams, "page", $event)
                  }
                }
              }),
              _vm._v(" "),
              _c(
                "el-dialog",
                {
                  attrs: { title: _vm.title, visible: _vm.dialogFormVisible },
                  on: {
                    "update:visible": function($event) {
                      _vm.dialogFormVisible = $event
                    }
                  }
                },
                [
                  _c(
                    "el-form",
                    {
                      ref: "form",
                      attrs: {
                        model: _vm.form,
                        rules: _vm.rules,
                        "label-width": "135px"
                      }
                    },
                    [
                      _c(
                        "el-form-item",
                        { attrs: { label: "发单价", prop: "markup_amount" } },
                        [
                          _c(
                            "el-row",
                            { attrs: { gutter: 10 } },
                            [
                              _c(
                                "el-col",
                                { attrs: { span: 20 } },
                                [
                                  _c("el-input", {
                                    attrs: { autocomplete: "off" },
                                    model: {
                                      value: _vm.form.markup_amount,
                                      callback: function($$v) {
                                        _vm.$set(
                                          _vm.form,
                                          "markup_amount",
                                          _vm._n($$v)
                                        )
                                      },
                                      expression: "form.markup_amount"
                                    }
                                  })
                                ],
                                1
                              ),
                              _vm._v(" "),
                              _c(
                                "el-col",
                                { attrs: { span: 2 } },
                                [
                                  _c(
                                    "el-tooltip",
                                    {
                                      staticClass: "item",
                                      attrs: {
                                        effect: "dark",
                                        content: "请填写正整数值",
                                        placement: "right-start"
                                      }
                                    },
                                    [
                                      _c("i", {
                                        staticClass: "el-icon-question"
                                      })
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
                        "el-form-item",
                        {
                          attrs: {
                            label: "加价开始时间(m)",
                            prop: "markup_time"
                          }
                        },
                        [
                          _c(
                            "el-row",
                            { attrs: { gutter: 10 } },
                            [
                              _c(
                                "el-col",
                                { attrs: { span: 20 } },
                                [
                                  _c("el-input", {
                                    attrs: { autocomplete: "off" },
                                    model: {
                                      value: _vm.form.markup_time,
                                      callback: function($$v) {
                                        _vm.$set(
                                          _vm.form,
                                          "markup_time",
                                          _vm._n($$v)
                                        )
                                      },
                                      expression: "form.markup_time"
                                    }
                                  })
                                ],
                                1
                              ),
                              _vm._v(" "),
                              _c(
                                "el-col",
                                { attrs: { span: 2 } },
                                [
                                  _c(
                                    "el-tooltip",
                                    {
                                      staticClass: "item",
                                      attrs: {
                                        effect: "dark",
                                        content:
                                          "订单上架后第1次加价的时间，请填写正整数值，可以为0",
                                        placement: "right-start"
                                      }
                                    },
                                    [
                                      _c("i", {
                                        staticClass: "el-icon-question"
                                      })
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
                        "el-form-item",
                        { attrs: { label: "加价类型", prop: "markup_type" } },
                        [
                          _c(
                            "el-row",
                            { attrs: { gutter: 10 } },
                            [
                              _c(
                                "el-col",
                                { attrs: { span: 20 } },
                                [
                                  _c(
                                    "el-radio",
                                    {
                                      attrs: { label: 0, autocomplete: "off" },
                                      model: {
                                        value: _vm.form.markup_type,
                                        callback: function($$v) {
                                          _vm.$set(_vm.form, "markup_type", $$v)
                                        },
                                        expression: "form.markup_type"
                                      }
                                    },
                                    [_vm._v("绝对值")]
                                  ),
                                  _vm._v(" "),
                                  _c(
                                    "el-radio",
                                    {
                                      attrs: { label: 1, autocomplete: "off" },
                                      model: {
                                        value: _vm.form.markup_type,
                                        callback: function($$v) {
                                          _vm.$set(_vm.form, "markup_type", $$v)
                                        },
                                        expression: "form.markup_type"
                                      }
                                    },
                                    [_vm._v("百分比")]
                                  )
                                ],
                                1
                              ),
                              _vm._v(" "),
                              _c(
                                "el-col",
                                { attrs: { span: 2 } },
                                [
                                  _c(
                                    "el-tooltip",
                                    {
                                      staticClass: "item",
                                      attrs: {
                                        effect: "dark",
                                        placement: "right-start"
                                      }
                                    },
                                    [
                                      _c(
                                        "div",
                                        {
                                          attrs: { slot: "content" },
                                          slot: "content"
                                        },
                                        [
                                          _vm._v(
                                            "选择“绝对值”，则“增加值”中填写的值为增加的金额；选择“百分比”,\n                                        "
                                          ),
                                          _c("br"),
                                          _vm._v(
                                            "则“增加值”中填写的值（百分数）乘以订单代练价格为增加的金额，所填写的值均为正整数或带2位小数"
                                          )
                                        ]
                                      ),
                                      _vm._v(" "),
                                      _c("i", {
                                        staticClass: "el-icon-question"
                                      })
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
                        "el-form-item",
                        { attrs: { label: "增加金额", prop: "markup_money" } },
                        [
                          _c(
                            "el-row",
                            { attrs: { gutter: 10 } },
                            [
                              _c(
                                "el-col",
                                { attrs: { span: 20 } },
                                [
                                  _c("el-input", {
                                    attrs: { autocomplete: "off" },
                                    model: {
                                      value: _vm.form.markup_money,
                                      callback: function($$v) {
                                        _vm.$set(
                                          _vm.form,
                                          "markup_money",
                                          _vm._n($$v)
                                        )
                                      },
                                      expression: "form.markup_money"
                                    }
                                  })
                                ],
                                1
                              ),
                              _vm._v(" "),
                              _c(
                                "el-col",
                                { attrs: { span: 2 } },
                                [
                                  _c(
                                    "el-tooltip",
                                    {
                                      staticClass: "item",
                                      attrs: {
                                        effect: "dark",
                                        content: "请填写正整数值",
                                        placement: "right-start"
                                      }
                                    },
                                    [
                                      _c("i", {
                                        staticClass: "el-icon-question"
                                      })
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
                        "el-form-item",
                        {
                          attrs: { label: "加价频率", prop: "markup_frequency" }
                        },
                        [
                          _c(
                            "el-row",
                            { attrs: { gutter: 10 } },
                            [
                              _c(
                                "el-col",
                                { attrs: { span: 20 } },
                                [
                                  _c("el-input", {
                                    attrs: { autocomplete: "off" },
                                    model: {
                                      value: _vm.form.markup_frequency,
                                      callback: function($$v) {
                                        _vm.$set(
                                          _vm.form,
                                          "markup_frequency",
                                          _vm._n($$v)
                                        )
                                      },
                                      expression: "form.markup_frequency"
                                    }
                                  })
                                ],
                                1
                              ),
                              _vm._v(" "),
                              _c(
                                "el-col",
                                { attrs: { span: 2 } },
                                [
                                  _c(
                                    "el-tooltip",
                                    {
                                      staticClass: "item",
                                      attrs: {
                                        effect: "dark",
                                        content: "请填写正整数值",
                                        placement: "right-start"
                                      }
                                    },
                                    [
                                      _c("i", {
                                        staticClass: "el-icon-question"
                                      })
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
                        "el-form-item",
                        {
                          attrs: {
                            label: "加价次数限制",
                            prop: "markup_number"
                          }
                        },
                        [
                          _c(
                            "el-row",
                            { attrs: { gutter: 10 } },
                            [
                              _c(
                                "el-col",
                                { attrs: { span: 20 } },
                                [
                                  _c("el-input", {
                                    attrs: { autocomplete: "off" },
                                    model: {
                                      value: _vm.form.markup_number,
                                      callback: function($$v) {
                                        _vm.$set(
                                          _vm.form,
                                          "markup_number",
                                          _vm._n($$v)
                                        )
                                      },
                                      expression: "form.markup_number"
                                    }
                                  })
                                ],
                                1
                              ),
                              _vm._v(" "),
                              _c(
                                "el-col",
                                { attrs: { span: 2 } },
                                [
                                  _c(
                                    "el-tooltip",
                                    {
                                      staticClass: "item",
                                      attrs: {
                                        effect: "dark",
                                        content: "填写0为无次数限制",
                                        placement: "right-start"
                                      }
                                    },
                                    [
                                      _c("i", {
                                        staticClass: "el-icon-question"
                                      })
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
                        "el-form-item",
                        [
                          _vm.isAdd
                            ? _c(
                                "el-button",
                                {
                                  attrs: { type: "primary" },
                                  on: {
                                    click: function($event) {
                                      _vm.submitFormAdd("form")
                                    }
                                  }
                                },
                                [_vm._v("确认添加")]
                              )
                            : _vm._e(),
                          _vm._v(" "),
                          _vm.isUpdate
                            ? _c(
                                "el-button",
                                {
                                  attrs: { type: "primary" },
                                  on: {
                                    click: function($event) {
                                      _vm.submitFormUpdate("form")
                                    }
                                  }
                                },
                                [_vm._v("确认修改")]
                              )
                            : _vm._e(),
                          _vm._v(" "),
                          _c(
                            "el-button",
                            {
                              on: {
                                click: function($event) {
                                  _vm.markupCancel("form")
                                }
                              }
                            },
                            [_vm._v("取消")]
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
            "el-tab-pane",
            { attrs: { label: "发布渠道设置", name: "channel" } },
            [
              _c("el-alert", {
                staticStyle: { "margin-bottom": "15px" },
                attrs: {
                  title:
                    "操作提示：发布渠道设置可以控制发布的订单所能转单的平台，每种游戏至少选择一家平台。",
                  type: "success",
                  closable: false
                }
              }),
              _vm._v(" "),
              [
                _c(
                  "el-table",
                  {
                    staticStyle: { width: "100%" },
                    attrs: { data: _vm.tableDataChannel, border: "" }
                  },
                  [
                    _c("el-table-column", {
                      attrs: { prop: "game", label: "游戏", width: "200" },
                      scopedSlots: _vm._u([
                        {
                          key: "default",
                          fn: function(scope) {
                            return [
                              _vm._v(
                                "\n                                " +
                                  _vm._s(scope.row.name) +
                                  "\n                            "
                              )
                            ]
                          }
                        }
                      ])
                    }),
                    _vm._v(" "),
                    _c("el-table-column", {
                      attrs: { prop: "channel", label: "发布渠道", width: "" },
                      scopedSlots: _vm._u([
                        {
                          key: "default",
                          fn: function(scope) {
                            return [
                              _c(
                                "el-checkbox-group",
                                {
                                  on: {
                                    change: function($event) {
                                      _vm.switchChange(
                                        scope.row.id,
                                        scope.row.name,
                                        scope.row.hasModel
                                      )
                                    }
                                  },
                                  model: {
                                    value: scope.row.hasModel,
                                    callback: function($$v) {
                                      _vm.$set(scope.row, "hasModel", $$v)
                                    },
                                    expression: "scope.row.hasModel"
                                  }
                                },
                                _vm._l(scope.row.allChannel, function(item) {
                                  return _c(
                                    "el-checkbox",
                                    { key: item.id, attrs: { label: item.id } },
                                    [_vm._v(_vm._s(item.name))]
                                  )
                                })
                              )
                            ]
                          }
                        }
                      ])
                    })
                  ],
                  1
                )
              ]
            ],
            2
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
    require("vue-hot-reload-api")      .rerender("data-v-755392fb", module.exports)
  }
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/setting/Auxiliary.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/setting/Auxiliary.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-755392fb\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/setting/Auxiliary.vue")
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
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
Component.options.__file = "resources/assets/frontend/js/components/setting/Auxiliary.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-755392fb", Component.options)
  } else {
    hotAPI.reload("data-v-755392fb", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});