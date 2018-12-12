webpackJsonp([24],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/account/Employee.vue":
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

/* harmony default export */ __webpack_exports__["default"] = ({
    methods: {
        // 新增按钮
        employeeAdd: function employeeAdd() {
            this.dialogFormVisible = true;
            this.isAdd = true;
            this.isUpdate = false;
            this.isDisabled = false;
            this.title = "新增";
            this.form.username = '';
            this.form.name = '';
            this.form.hasStation = [];
            this.form.phone = '';
            this.form.leveling_type = '';
            this.form.password = '';
            this.form.station = '';
            this.form.qq = '';
            this.form.wechat = '';
            this.form.remark = '';
        },

        // 编辑按钮
        employeeUpdate: function employeeUpdate(row) {
            this.dialogFormVisible = true;
            this.title = "修改";
            this.form = JSON.parse(JSON.stringify(row));
            this.isAdd = false;
            this.isUpdate = true;
            this.isDisabled = true;
        },

        // 多选框改变事件
        switchChange: function switchChange($stationIds) {
            this.form.station = $stationIds;
        },

        // 修改
        submitFormUpdate: function submitFormUpdate(formName) {
            var _this = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this.$api.AccountEmployeeUpdate(_this.form).then(function (res) {
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
            });
        },

        // 新增
        submitFormAdd: function submitFormAdd(formName) {
            var _this2 = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this2.$api.AccountEmployeeAdd(_this2.form).then(function (res) {
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
                _this2.$refs[formName].clearValidate();
            });
        },

        // 取消按钮
        employeeCancel: function employeeCancel(formName) {
            this.dialogFormVisible = false;
            this.$refs[formName].clearValidate();
        },

        // 加载数据
        handleTableData: function handleTableData() {
            var _this3 = this;

            this.$api.AccountEmployeeDataList(this.searchParams).then(function (res) {
                _this3.tableData = res.data;
                _this3.TotalPage = res.total;
            }).catch(function (err) {
                _this3.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },

        // 所有子账号
        handleUser: function handleUser() {
            var _this4 = this;

            this.$api.AccountEmployeeUser(this.searchParams).then(function (res) {
                _this4.AccountEmployeeUser = res;
            }).catch(function (err) {
                _this4.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },

        // 所有岗位
        handleStation: function handleStation() {
            var _this5 = this;

            this.$api.AccountEmployeeStation(this.searchParams).then(function (res) {
                _this5.AccountEmployeeStation = res;
                _this5.form.allStation = res;
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

        // 子账号禁用
        handleSwitch: function handleSwitch(value, row) {
            var _this6 = this;

            this.$api.AccountEmployeeSwitch({ status: value, user_id: row.id }).then(function (res) {
                _this6.$message({
                    showClose: true,
                    type: res.status == 1 ? 'success' : 'error',
                    message: res.message
                });
            }).catch(function (err) {
                _this6.$message({
                    type: 'error',
                    message: '操作失败'
                });
            });
        },

        // 删除
        employeeDelete: function employeeDelete(id) {
            var _this7 = this;

            this.$confirm('您确定要删除吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this7.$api.AccountEmployeeDelete({ user_id: id }).then(function (res) {
                    _this7.$message({
                        showClose: true,
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });
                    _this7.handleTableData();
                }).catch(function (err) {
                    _this7.$message({
                        type: 'error',
                        message: '操作失败'
                    });
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
        this.handleUser();
        this.handleStation();
        this.handleTableHeight();
        window.addEventListener('resize', this.handleTableHeight);
    },
    destroyed: function destroyed() {
        window.removeEventListener('resize', this.handleTableHeight);
    },
    data: function data() {
        var checkPhone = function checkPhone(rule, value, callback) {
            if (!value) {
                return callback(new Error('必填项不能为空!'));
            }

            if (!Number.isInteger(parseInt(value))) {
                callback(new Error('请输入数字值！'));
            } else {
                var reg = /^1[3|4|5|7|8][0-9]\d{8}$/;
                if (reg.test(value)) {
                    callback();
                } else {
                    callback(new Error('请输入正确的手机号！'));
                }
                callback();
            }
            callback();
        };
        var validatePass = function validatePass(rule, value, callback) {
            if (value && (value.length < 6 || value.length > 22)) {
                callback(new Error('请填写6-22位长度的密码！'));
            }
            callback();
        };
        return {
            tableHeight: 0,
            title: '新增',
            allStation: [],
            isDisabled: false,
            isAdd: true,
            isUpdate: false,
            dialogFormVisible: false,
            AccountEmployeeUser: {},
            AccountEmployeeStation: [],
            searchParams: {
                username: '',
                name: '',
                station: '',
                page: 1
            },
            TotalPage: 0,
            tableData: [],
            rules: {
                password: [{ validator: validatePass, trigger: 'blur' }],
                phone: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }, { validator: checkPhone, trigger: 'blur' }],
                username: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                name: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                leveling_type: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }]
            },
            form: {
                username: '',
                name: '',
                hasStation: [],
                allStation: [],
                phone: '',
                password: '',
                leveling_type: '',
                station: [],
                qq: '',
                wechat: '',
                remark: ''
            }
        };
    }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-d2674968\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/account/Employee.vue":
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
        "el-form",
        {
          staticClass: "search-form-inline",
          attrs: { inline: true, model: _vm.searchParams, size: "small" }
        },
        [
          _c(
            "el-row",
            { attrs: { gutter: 16 } },
            [
              _c(
                "el-col",
                { attrs: { span: 4 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "账号" } },
                    [
                      _c("el-input", {
                        model: {
                          value: _vm.searchParams.name,
                          callback: function($$v) {
                            _vm.$set(_vm.searchParams, "name", $$v)
                          },
                          expression: "searchParams.name"
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
                { attrs: { span: 4 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "昵称" } },
                    [
                      _c(
                        "el-select",
                        {
                          attrs: { placeholder: "请选择" },
                          model: {
                            value: _vm.searchParams.username,
                            callback: function($$v) {
                              _vm.$set(_vm.searchParams, "username", $$v)
                            },
                            expression: "searchParams.username"
                          }
                        },
                        _vm._l(_vm.AccountEmployeeUser, function(value, key) {
                          return _c("el-option", {
                            key: key,
                            attrs: { value: key, label: value }
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
                { attrs: { span: 4 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "岗位" } },
                    [
                      _c(
                        "el-select",
                        {
                          attrs: { placeholder: "请选择" },
                          model: {
                            value: _vm.searchParams.station,
                            callback: function($$v) {
                              _vm.$set(_vm.searchParams, "station", $$v)
                            },
                            expression: "searchParams.station"
                          }
                        },
                        _vm._l(_vm.AccountEmployeeStation, function(item) {
                          return _c(
                            "el-option",
                            {
                              key: item.id,
                              attrs: { value: item.id, label: item.name }
                            },
                            [_vm._v(_vm._s(item.name))]
                          )
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
                { attrs: { span: 4 } },
                [
                  _c(
                    "el-form-item",
                    [
                      _c(
                        "el-button",
                        {
                          attrs: { type: "primary" },
                          on: { click: _vm.handleSearch }
                        },
                        [_vm._v("查询")]
                      ),
                      _vm._v(" "),
                      _c(
                        "el-button",
                        {
                          attrs: { type: "primary", size: "small" },
                          on: {
                            click: function($event) {
                              _vm.employeeAdd()
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
          attrs: { data: _vm.tableData, height: _vm.tableHeight, border: "" }
        },
        [
          _c("el-table-column", {
            attrs: { prop: "id", label: "编号", width: "100" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "username", label: "员工昵称", width: "150" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "name", label: "账号", width: "150" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "station", label: "岗位", width: "150" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return _vm._l(scope.row.new_roles, function(item) {
                    return _c("div", [
                      _vm._v(_vm._s(item.name ? item.name : ""))
                    ])
                  })
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "leveling_type", label: "代练类型", width: "" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(scope.row.leveling_type == 1 ? "接单" : "发单") +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "qq", label: "QQ", width: "" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "wechat", label: "微信", width: "" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "phone", label: "电话", width: "" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "remark", label: "备注", width: "200" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "status", label: "状态", width: "200" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _c("el-switch", {
                      attrs: {
                        "active-text": "启用",
                        "inactive-text": "禁用",
                        "active-value": 0,
                        "inactive-value": 1
                      },
                      on: {
                        change: function($event) {
                          _vm.handleSwitch($event, scope.row)
                        }
                      },
                      model: {
                        value: scope.row.status,
                        callback: function($$v) {
                          _vm.$set(scope.row, "status", $$v)
                        },
                        expression: "scope.row.status"
                      }
                    })
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "updated_at", label: "最后操作时间", width: "200" }
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
                            _vm.employeeUpdate(scope.row)
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
                            _vm.employeeDelete(scope.row.id)
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
                "label-width": "80px"
              }
            },
            [
              _c(
                "el-form-item",
                { attrs: { label: "账号", prop: "name" } },
                [
                  _c("el-input", {
                    attrs: {
                      name: "name",
                      autocomplete: "off",
                      disabled: _vm.isDisabled
                    },
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
                { attrs: { label: "昵称", prop: "username" } },
                [
                  _c("el-input", {
                    attrs: { name: "username", autocomplete: "off" },
                    model: {
                      value: _vm.form.username,
                      callback: function($$v) {
                        _vm.$set(_vm.form, "username", $$v)
                      },
                      expression: "form.username"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "密码", prop: "password" } },
                [
                  _c("el-input", {
                    attrs: {
                      autocomplete: "off",
                      placeholder: "不填写则为原密码"
                    },
                    model: {
                      value: _vm.form.password,
                      callback: function($$v) {
                        _vm.$set(_vm.form, "password", $$v)
                      },
                      expression: "form.password"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "类型", prop: "leveling_type" } },
                [
                  _c(
                    "el-radio",
                    {
                      attrs: { label: 1, autocomplete: "off" },
                      model: {
                        value: _vm.form.leveling_type,
                        callback: function($$v) {
                          _vm.$set(_vm.form, "leveling_type", $$v)
                        },
                        expression: "form.leveling_type"
                      }
                    },
                    [_vm._v("接单")]
                  ),
                  _vm._v(" "),
                  _c(
                    "el-radio",
                    {
                      attrs: { label: 2, autocomplete: "off" },
                      model: {
                        value: _vm.form.leveling_type,
                        callback: function($$v) {
                          _vm.$set(_vm.form, "leveling_type", $$v)
                        },
                        expression: "form.leveling_type"
                      }
                    },
                    [_vm._v("发单")]
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "岗位", prop: "station" } },
                [
                  _c(
                    "el-checkbox-group",
                    {
                      on: {
                        change: function($event) {
                          _vm.switchChange(_vm.form.hasStation)
                        }
                      },
                      model: {
                        value: _vm.form.hasStation,
                        callback: function($$v) {
                          _vm.$set(_vm.form, "hasStation", $$v)
                        },
                        expression: "form.hasStation"
                      }
                    },
                    _vm._l(_vm.form.allStation, function(item) {
                      return _c(
                        "el-checkbox",
                        {
                          key: item.id,
                          attrs: { value: item.id, label: item.id }
                        },
                        [_vm._v(_vm._s(item.name))]
                      )
                    })
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "电话", prop: "phone" } },
                [
                  _c("el-input", {
                    attrs: { autocomplete: "off" },
                    model: {
                      value: _vm.form.phone,
                      callback: function($$v) {
                        _vm.$set(_vm.form, "phone", _vm._n($$v))
                      },
                      expression: "form.phone"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "QQ", prop: "qq" } },
                [
                  _c("el-input", {
                    attrs: { name: "qq", autocomplete: "off" },
                    model: {
                      value: _vm.form.qq,
                      callback: function($$v) {
                        _vm.$set(_vm.form, "qq", $$v)
                      },
                      expression: "form.qq"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "微信", prop: "wechat" } },
                [
                  _c("el-input", {
                    attrs: { name: "wechat", autocomplete: "off" },
                    model: {
                      value: _vm.form.wechat,
                      callback: function($$v) {
                        _vm.$set(_vm.form, "wechat", $$v)
                      },
                      expression: "form.wechat"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "备注", prop: "remark" } },
                [
                  _c("el-input", {
                    attrs: {
                      type: "textarea",
                      name: "remark",
                      autocomplete: "off"
                    },
                    model: {
                      value: _vm.form.remark,
                      callback: function($$v) {
                        _vm.$set(_vm.form, "remark", $$v)
                      },
                      expression: "form.remark"
                    }
                  })
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
                          _vm.employeeCancel("form")
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
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-d2674968", module.exports)
  }
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/account/Employee.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/account/Employee.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-d2674968\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/account/Employee.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/account/Employee.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-d2674968", Component.options)
  } else {
    hotAPI.reload("data-v-d2674968", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});