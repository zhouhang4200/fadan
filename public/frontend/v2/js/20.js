webpackJsonp([20],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/finance/AmountFlow.vue":
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

/* harmony default export */ __webpack_exports__["default"] = ({
    methods: {
        // 加载数据
        handleTableData: function handleTableData() {
            var _this = this;

            this.$api.FinanceAmountFlowDataList(this.searchParams).then(function (res) {
                _this.tableData = res.data;
                _this.TotalPage = res.total;
            }).catch(function (err) {
                _this.$alert('获取数据失败, 请重试!', '提示', {
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

        // 重置表单
        handleResetForm: function handleResetForm() {
            this.$refs.form.resetFields;
            this.handleTableData();
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

    watch: {
        t: function t(val) {
            this.weekse = val;
            this.getL();
        }
    },
    data: function data() {
        return {
            tableHeight: 0,
            TradeSubTypeArr: {
                11: '自动加款',
                12: '手动加款',
                13: '奖励加款',
                21: '手动提现',
                22: '手动减款',
                23: '自动提现',
                31: '提现冻结',
                32: '抢单冻结',
                33: '减款冻结',
                41: '提现解冻',
                42: '抢单解冻',
                51: '手续费支出',
                52: '违规扣款',
                53: '奖励撤销扣款',
                54: '短信费',
                55: 'steam手续费扣款',
                61: '手续费退款',
                62: '违规退款',
                63: 'steam手续费退款',
                71: '订单集市支出',
                72: '订单售后扣款',
                73: '代练手续费支出',
                74: '安全保证金支出',
                75: '效率保证金支出',
                76: '代练下单支出',
                77: '游戏代练加款',
                78: '支付订单集市押金',
                79: '订单投诉支出',
                81: '订单集市收入',
                82: '发货失败退款',
                83: '售后退款',
                84: '取消订单退款',
                85: '订单售后退款',
                86: '代练手续费收入',
                87: '退还代练费',
                88: '退还安全保证金',
                89: '退还效率保证金',
                810: '安全保证金收入',
                811: '效率保证金收入',
                812: '代练收入',
                813: '代练撤消退款',
                814: '代练改价退款',
                815: '保证金退款',
                816: '退还订单集市押金',
                817: '订单投诉收入'
            },
            TradeTypeArr: {
                1: '加款',
                2: '减款',
                3: '冻结',
                4: '解冻',
                7: '支出',
                8: '收入'
            },
            searchParams: {
                trade_type: '',
                trade_no: '',
                channel_order_trade_no: '',
                date: '',
                page: 1
            },
            TotalPage: 0,
            tableData: []
        };
    }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-71771912\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/finance/AmountFlow.vue":
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
          ref: "form",
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
                    { attrs: { label: "单号", prop: "name" } },
                    [
                      _c("el-input", {
                        model: {
                          value: _vm.searchParams.trade_no,
                          callback: function($$v) {
                            _vm.$set(_vm.searchParams, "trade_no", $$v)
                          },
                          expression: "searchParams.trade_no"
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
                    { attrs: { label: "类型", prop: "trade_type" } },
                    [
                      _c(
                        "el-select",
                        {
                          attrs: { placeholder: "请选择" },
                          model: {
                            value: _vm.searchParams.trade_type,
                            callback: function($$v) {
                              _vm.$set(_vm.searchParams, "trade_type", $$v)
                            },
                            expression: "searchParams.trade_type"
                          }
                        },
                        [
                          _c("el-option", {
                            key: "0",
                            attrs: { label: "所有类型", value: "0" }
                          }),
                          _vm._v(" "),
                          _vm._l(_vm.TradeTypeArr, function(value, key) {
                            return _c("el-option", {
                              key: key,
                              attrs: { value: key, label: value }
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
                    { attrs: { label: "天猫单号" } },
                    [
                      _c("el-input", {
                        model: {
                          value: _vm.searchParams.channel_order_trade_no,
                          callback: function($$v) {
                            _vm.$set(
                              _vm.searchParams,
                              "channel_order_trade_no",
                              $$v
                            )
                          },
                          expression: "searchParams.channel_order_trade_no"
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
                          value: _vm.searchParams.created_at,
                          callback: function($$v) {
                            _vm.$set(_vm.searchParams, "created_at", $$v)
                          },
                          expression: "searchParams.created_at"
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
                    { staticStyle: { padding: "0 8px" } },
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
                          on: { click: _vm.handleResetForm }
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
            attrs: { prop: "id", label: "流水号", width: "80" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "trade_type", label: "类型", width: "150" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(_vm.TradeTypeArr[scope.row.trade_type]) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "trade_subtype", label: "子类型", width: "150" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(_vm.TradeSubTypeArr[scope.row.trade_subtype]) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "trade_no", label: "订单号" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "order", label: "天猫单号" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(
                          scope.row.order
                            ? scope.row.order.foreign_order_no
                            : ""
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
            attrs: { prop: "fee", label: "变动金额", width: "150" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                    " +
                        _vm._s(Number(scope.row.fee)) +
                        "\n                "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "balance", label: "账户余额", width: "150" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(Number(scope.row.balance)) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "created_at", label: "时间", width: "150" }
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
    require("vue-hot-reload-api")      .rerender("data-v-71771912", module.exports)
  }
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/finance/AmountFlow.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/finance/AmountFlow.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-71771912\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/finance/AmountFlow.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/finance/AmountFlow.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-71771912", Component.options)
  } else {
    hotAPI.reload("data-v-71771912", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});