webpackJsonp([25],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/account/BlackList.vue":
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

/* harmony default export */ __webpack_exports__["default"] = ({
    methods: {
        //新增按钮
        blackListAdd: function blackListAdd() {
            this.isAdd = true;
            this.isUpdate = false;
            this.title = '打手黑名单新增';
            this.dialogFormVisible = true;
            this.form = {
                hatchet_man_name: '',
                hatchet_man_phone: '',
                hatchet_man_qq: '',
                content: ''
            };
        },

        // 编辑按钮
        blackListUpdate: function blackListUpdate(row) {
            this.isAdd = false;
            this.isUpdate = true;
            this.title = '打手黑名单修改';
            this.dialogFormVisible = true;
            this.form = JSON.parse(JSON.stringify(row));
        },

        // 取消按钮
        blackListCancel: function blackListCancel(formName) {
            this.dialogFormVisible = false;
            this.$refs[formName].clearValidate();
        },

        // 添加
        submitFormAdd: function submitFormAdd(formName) {
            var _this = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this.$api.AccountBlackListAdd(_this.form).then(function (res) {
                        _this.$message({
                            showClose: true,
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });
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
            this.handleTableData();
        },

        // 修改
        submitFormUpdate: function submitFormUpdate(formName) {
            var _this2 = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this2.$api.AccountBlackListUpdate(_this2.form).then(function (res) {
                        _this2.$message({
                            showClose: true,
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });
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
            this.handleTableData();
        },

        // 加载数据
        handleTableData: function handleTableData() {
            var _this3 = this;

            this.$api.AccountBlackListDataList(this.searchParams).then(function (res) {
                _this3.tableData = res.data;
                _this3.TotalPage = res.total;
            }).catch(function (err) {
                _this3.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },
        handleName: function handleName() {
            var _this4 = this;

            this.$api.AccountBlackListName(this.searchParams).then(function (res) {
                _this4.AccountBlackListName = res;
            }).catch(function (err) {
                _this4.$alert('获取数据失败, 请重试!', '提示', {
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

        // 删除
        blackListDelete: function blackListDelete(id) {
            var _this5 = this;

            this.$confirm('您确定要删除吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this5.$api.AccountBlackListDelete({ id: id }).then(function (res) {
                    _this5.$message({
                        showClose: true,
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });
                    _this5.handleTableData();
                }).catch(function (err) {
                    _this5.$message({
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
        this.handleName();
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
        var checkQq = function checkQq(rule, value, callback) {
            if (!value) {
                return callback(new Error('必填项不能为空!'));
            }

            if (!Number.isInteger(parseInt(value))) {
                callback(new Error('请输入数字值！'));
            }
            callback();
        };
        return {
            tableHeight: 0,
            isAdd: true,
            isUpdate: false,
            title: '新增',
            url: '',
            dialogFormVisible: false,
            AccountBlackListName: {},
            searchParams: {
                hatchet_man_name: '',
                hatchet_man_phone: '',
                hatchet_man_qq: '',
                page: 1
            },
            TotalPage: 0,
            tableData: [],
            rules: {
                hatchet_man_qq: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }, { validator: checkQq, trigger: 'blur' }],
                hatchet_man_name: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                hatchet_man_phone: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }, { validator: checkPhone, trigger: 'blur' }]
            },
            form: {
                hatchet_man_name: '',
                hatchet_man_phone: '',
                hatchet_man_qq: '',
                content: ''
            }
        };
    }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-7b18c10f\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/account/BlackList.vue":
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
                    { attrs: { label: "昵称" } },
                    [
                      _c(
                        "el-select",
                        {
                          attrs: { placeholder: "请选择" },
                          model: {
                            value: _vm.searchParams.hatchet_man_name,
                            callback: function($$v) {
                              _vm.$set(
                                _vm.searchParams,
                                "hatchet_man_name",
                                $$v
                              )
                            },
                            expression: "searchParams.hatchet_man_name"
                          }
                        },
                        _vm._l(_vm.AccountBlackListName, function(value, key) {
                          return _c(
                            "el-option",
                            { key: key, attrs: { value: value, label: value } },
                            [_vm._v(_vm._s(value))]
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
                    { attrs: { label: "电话" } },
                    [
                      _c("el-input", {
                        model: {
                          value: _vm.searchParams.hatchet_man_phone,
                          callback: function($$v) {
                            _vm.$set(_vm.searchParams, "hatchet_man_phone", $$v)
                          },
                          expression: "searchParams.hatchet_man_phone"
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
                    { attrs: { label: "QQ" } },
                    [
                      _c("el-input", {
                        model: {
                          value: _vm.searchParams.hatchet_man_qq,
                          callback: function($$v) {
                            _vm.$set(_vm.searchParams, "hatchet_man_qq", $$v)
                          },
                          expression: "searchParams.hatchet_man_qq"
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
                              _vm.blackListAdd()
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
            attrs: { prop: "hatchet_man_name", label: "打手昵称", width: "200" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "hatchet_man_phone", label: "电话", width: "200" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "hatchet_man_qq", label: "QQ", width: "200" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "content", label: "备注", width: "" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { label: "操作", width: "250" },
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
                            _vm.blackListUpdate(scope.row)
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
                            _vm.blackListDelete(scope.row.id)
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
                { attrs: { label: "打手昵称", prop: "hatchet_man_name" } },
                [
                  _c("el-input", {
                    attrs: { autocomplete: "off" },
                    model: {
                      value: _vm.form.hatchet_man_name,
                      callback: function($$v) {
                        _vm.$set(_vm.form, "hatchet_man_name", $$v)
                      },
                      expression: "form.hatchet_man_name"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "打手电话", prop: "hatchet_man_phone" } },
                [
                  _c("el-input", {
                    attrs: { autocomplete: "off" },
                    model: {
                      value: _vm.form.hatchet_man_phone,
                      callback: function($$v) {
                        _vm.$set(_vm.form, "hatchet_man_phone", _vm._n($$v))
                      },
                      expression: "form.hatchet_man_phone"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "打手QQ", prop: "hatchet_man_qq" } },
                [
                  _c("el-input", {
                    attrs: { autocomplete: "off" },
                    model: {
                      value: _vm.form.hatchet_man_qq,
                      callback: function($$v) {
                        _vm.$set(_vm.form, "hatchet_man_qq", _vm._n($$v))
                      },
                      expression: "form.hatchet_man_qq"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "备注", prop: "content" } },
                [
                  _c("el-input", {
                    attrs: { type: "textarea", autocomplete: "off" },
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
                        [_vm._v("确认")]
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
                          _vm.blackListCancel("form")
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
    require("vue-hot-reload-api")      .rerender("data-v-7b18c10f", module.exports)
  }
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/account/BlackList.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/account/BlackList.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-7b18c10f\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/account/BlackList.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/account/BlackList.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7b18c10f", Component.options)
  } else {
    hotAPI.reload("data-v-7b18c10f", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});