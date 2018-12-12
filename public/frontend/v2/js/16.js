webpackJsonp([16],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/finance/Withdraw.vue":
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

/* harmony default export */ __webpack_exports__["default"] = ({
    // 初始化数据
    created: function created() {
        this.handleTableData();
        this.CanWithdraw();
        this.handleTableHeight();
        window.addEventListener('resize', this.handleTableHeight);
    },
    destroyed: function destroyed() {
        window.removeEventListener('resize', this.handleTableHeight);
    },

    methods: {
        // 表格加载数据
        handleTableData: function handleTableData() {
            var _this = this;

            this.$api.FinanceWithdrawDataList(this.searchParams).then(function (res) {
                _this.tableData = res.data;
                _this.TotalPage = res.total;
            }).catch(function (err) {
                _this.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },
        handleCurrentChange: function handleCurrentChange(page) {
            this.searchParams.page = page;
            this.handleTableData();
        },
        handleSearch: function handleSearch() {
            this.handleTableData();
        },

        // 我的提现里面input框输入的说明文字
        CanWithdraw: function CanWithdraw() {
            var _this2 = this;

            this.$api.FinanceWithdrawCan().then(function (res) {
                _this2.placeString = '可以提现金额: ' + res;
            }).catch(function (err) {
                _this2.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },

        // 表单提交
        submitForm: function submitForm(formName) {
            var _this3 = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this3.$api.FinanceWithdrawAdd(_this3.ruleForm).then(function (res) {
                        if (res.status > 0) {
                            _this3.$message({
                                showClose: true,
                                message: '发送成功!',
                                type: 'success'
                            });
                        } else {
                            _this3.$message({
                                showClose: true,
                                message: '发送失败:' + res.message,
                                type: 'error'
                            });
                        }
                    }).catch(function (err) {
                        _this3.$message({
                            showClose: true,
                            message: '服务器错误,发送失败!',
                            type: 'error'
                        });
                    });
                    _this3.dialogFormVisible = false;
                } else {
                    return false;
                }
            });
        },
        resetForm: function resetForm(formName) {
            this.$refs[formName].resetFields();
        },

        // 表格高度计算
        handleTableHeight: function handleTableHeight() {
            this.tableHeight = window.innerHeight - 318;
        }
    },
    data: function data() {
        // 表单验证
        var checkFee = function checkFee(rule, value, callback) {
            setTimeout(function () {
                if (!Number.isInteger(value)) {
                    callback(new Error('请输入数字值'));
                } else {
                    callback();
                }
            }, 1000);
        };
        return {
            tableHeight: 0,
            // 表单提交规则和数据
            ruleForm: {
                fee: '',
                remark: ''
            },
            rules: {
                fee: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }, { validator: checkFee, trigger: 'blur' }]
            },
            dialogFormVisible: false,
            // 表单查找和表单数据
            tableData: [],
            StatusArr: {
                1: '申请中',
                2: '填单提现完成',
                3: '拒绝',
                4: '待审核',
                5: '待确认',
                6: '办款中',
                7: '提现成功',
                8: '提现失败'
            },
            searchParams: {
                date: '',
                status: '',
                page: 1
            },
            TotalPage: 0,
            // 申请提现输入框提示语句
            placeString: ''
        };
    }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-7cb58d1b\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/finance/Withdraw.vue":
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
                    { attrs: { label: "状态" } },
                    [
                      _c(
                        "el-select",
                        {
                          attrs: { placeholder: "请选择" },
                          model: {
                            value: _vm.searchParams.status,
                            callback: function($$v) {
                              _vm.$set(_vm.searchParams, "status", $$v)
                            },
                            expression: "searchParams.status"
                          }
                        },
                        _vm._l(_vm.StatusArr, function(value, key) {
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
                { attrs: { span: 5 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "日期" } },
                    [
                      _c("el-date-picker", {
                        attrs: {
                          type: "daterange",
                          align: "right",
                          "unlink-panels": "",
                          format: "yyyy-MM-dd",
                          "value-format": "yyyy-MM-dd",
                          "range-separator": "至",
                          "start-placeholder": "开始日期",
                          "end-placeholder": "结束日期"
                        },
                        model: {
                          value: _vm.searchParams.date,
                          callback: function($$v) {
                            _vm.$set(_vm.searchParams, "date", $$v)
                          },
                          expression: "searchParams.date"
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
                          attrs: { type: "primary" },
                          on: {
                            click: function($event) {
                              _vm.dialogFormVisible = true
                            }
                          }
                        },
                        [_vm._v("余额提现")]
                      )
                    ],
                    1
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-dialog",
                {
                  attrs: { title: "余额提现", visible: _vm.dialogFormVisible },
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
                      ref: "ruleForm",
                      staticClass: "demo-ruleForm",
                      attrs: {
                        model: _vm.ruleForm,
                        "status-icon": "",
                        rules: _vm.rules,
                        "label-width": "80px"
                      }
                    },
                    [
                      _c(
                        "el-form-item",
                        { attrs: { label: "提现金额", prop: "fee" } },
                        [
                          _c("el-input", {
                            attrs: {
                              type: "text",
                              placeholder: _vm.placeString
                            },
                            on: { CanWithdraw: _vm.CanWithdraw },
                            model: {
                              value: _vm.ruleForm.fee,
                              callback: function($$v) {
                                _vm.$set(_vm.ruleForm, "fee", _vm._n($$v))
                              },
                              expression: "ruleForm.fee"
                            }
                          })
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "el-form-item",
                        { attrs: { label: "备注说明", prop: "remark" } },
                        [
                          _c("el-input", {
                            attrs: { type: "text", placeholder: "可以不填写" },
                            model: {
                              value: _vm.ruleForm.remark,
                              callback: function($$v) {
                                _vm.$set(_vm.ruleForm, "remark", $$v)
                              },
                              expression: "ruleForm.remark"
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
                                  _vm.submitForm("ruleForm")
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
                                  _vm.resetForm("ruleForm")
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
            attrs: { prop: "no", label: "提现单号", width: "200" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "fee", label: "提现金额", width: "200" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "status", label: "状态", width: "200" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(_vm.StatusArr["" + scope.row.status]) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "remark", label: "备注", width: "" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "created_at", label: "创建时间", width: "200" }
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
      })
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
    require("vue-hot-reload-api")      .rerender("data-v-7cb58d1b", module.exports)
  }
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/finance/Withdraw.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/finance/Withdraw.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-7cb58d1b\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/finance/Withdraw.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/finance/Withdraw.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7cb58d1b", Component.options)
  } else {
    hotAPI.reload("data-v-7cb58d1b", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});