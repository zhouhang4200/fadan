webpackJsonp([11],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/setting/Message.vue":
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
    methods: {
        // 编辑按钮
        ShowEditForm: function ShowEditForm(row) {
            this.dialogFormVisible = true;
            this.form = JSON.parse(JSON.stringify(row));
        },

        // 修改
        submitForm: function submitForm(formName) {
            var _this = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this.$api.SettingMessageUpdate(_this.form).then(function (res) {
                        _this.$message({
                            showClose: true,
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });
                        _this.handleTableData();
                    }).catch(function (err) {
                        _this.$alert('获取数据失败, 请重试!', '提示', {
                            confirmButtonText: '确定',
                            callback: function callback(action) {}
                        });
                    });
                } else {
                    return false;
                }
            });
        },

        // 加载数据
        handleTableData: function handleTableData() {
            var _this2 = this;

            this.$api.SettingMessageDataList(this.searchParams).then(function (res) {
                _this2.tableData = res.data;
                _this2.TotalPage = res.total;
            }).catch(function (err) {
                _this2.$alert('获取数据失败, 请重试!', '提示', {
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

        // 开关状态
        handleSwitch: function handleSwitch(value, row) {
            var _this3 = this;

            this.$api.SettingMessageStatus({ status: value, id: row.id }).then(function (res) {
                _this3.$message({
                    showClose: true,
                    type: res.status == 1 ? 'success' : 'error',
                    message: res.message
                });
            }).catch(function (err) {
                _this3.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
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
        var checkHas = function checkHas(rule, value, callback) {
            if (value === '') {
                callback(new Error('必填项不能为空!'));
            }
            callback();
        };
        return {
            tableHeight: 0,
            dialogFormVisible: false,
            rules: {
                name: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                contents: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }]
            },
            tableData: [],
            purpose: {
                1: '被接单提示',
                2: '已完成提示',
                3: '待验收提示',
                4: '撤销中提示',
                5: '仲裁中提示'
            },
            searchParams: {
                page: 1
            },
            TotalPage: 0,
            form: {
                id: '',
                name: '',
                contents: ''
            }
        };
    }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-0ddc8608\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/setting/Message.vue":
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "main content amount-flow" },
    [
      [
        _c("el-alert", {
          staticStyle: { "margin-bottom": "15px" },
          attrs: {
            title:
              "操作提示: 编辑自动发送短信模板，可在打手接单/完成订单/提交验收/提交撤销/提交仲裁时自动发送短信提醒用户。",
            type: "success",
            closable: false
          }
        }),
        _vm._v(" "),
        _c(
          "el-table",
          {
            staticStyle: { width: "100%", "margin-top": "1px" },
            attrs: { data: _vm.tableData, height: _vm.tableHeight, border: "" }
          },
          [
            _c("el-table-column", {
              attrs: { prop: "name", label: "短信名称", width: "150" }
            }),
            _vm._v(" "),
            _c("el-table-column", {
              attrs: { prop: "contents", label: "短信内容", width: "" }
            }),
            _vm._v(" "),
            _c("el-table-column", {
              attrs: { prop: "purpose", label: "发送场景", width: "150" },
              scopedSlots: _vm._u([
                {
                  key: "default",
                  fn: function(scope) {
                    return [
                      _vm._v(
                        "\n                    " +
                          _vm._s(_vm.purpose[scope.row.purpose]) +
                          "\n                "
                      )
                    ]
                  }
                }
              ])
            }),
            _vm._v(" "),
            _c("el-table-column", {
              attrs: { prop: "status", label: "状态", width: "150" },
              scopedSlots: _vm._u([
                {
                  key: "default",
                  fn: function(scope) {
                    return [
                      _c("el-switch", {
                        attrs: {
                          "active-text": "启用",
                          "inactive-text": "禁用",
                          "active-value": 1,
                          "inactive-value": 0
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
              attrs: { label: "操作", width: "150" },
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
                              _vm.ShowEditForm(scope.row)
                            }
                          }
                        },
                        [_vm._v("编辑")]
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
            attrs: { title: "短信模板编辑", visible: _vm.dialogFormVisible },
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
                  { attrs: { label: "短信名称", prop: "name" } },
                  [
                    _c("el-input", {
                      attrs: { name: "name", autocomplete: "off" },
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
                  { attrs: { label: "短信内容", prop: "contents" } },
                  [
                    _c("el-input", {
                      attrs: { name: "contents", autocomplete: "off" },
                      model: {
                        value: _vm.form.contents,
                        callback: function($$v) {
                          _vm.$set(_vm.form, "contents", $$v)
                        },
                        expression: "form.contents"
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
                            _vm.submitForm("form")
                          }
                        }
                      },
                      [_vm._v("确认修改")]
                    ),
                    _vm._v(" "),
                    _c(
                      "el-button",
                      {
                        on: {
                          click: function($event) {
                            _vm.dialogFormVisible = false
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
      ]
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
    require("vue-hot-reload-api")      .rerender("data-v-0ddc8608", module.exports)
  }
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/setting/Message.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/setting/Message.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-0ddc8608\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/setting/Message.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/setting/Message.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-0ddc8608", Component.options)
  } else {
    hotAPI.reload("data-v-0ddc8608", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});