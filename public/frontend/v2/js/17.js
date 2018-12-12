webpackJsonp([17],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/finance/Order.vue":
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

/* harmony default export */ __webpack_exports__["default"] = ({
    methods: {
        // 加载数据
        handleTableData: function handleTableData() {
            var _this = this;

            this.$api.FinanceOrderDataList(this.searchParams).then(function (res) {
                _this.tableData = res.data;
                _this.TotalPage = res.total;
            }).catch(function (err) {
                _this.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },
        handleGame: function handleGame() {
            var _this2 = this;

            this.$api.FinanceGame(this.searchParams).then(function (res) {
                _this2.GameArr = res;
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

        // 表格高度计算
        handleTableHeight: function handleTableHeight() {
            this.tableHeight = window.innerHeight - 318;
        }
    },
    created: function created() {
        this.handleTableData();
        this.handleGame();
        this.handleTableHeight();
        window.addEventListener('resize', this.handleTableHeight);
    },
    destroyed: function destroyed() {
        window.removeEventListener('resize', this.handleTableHeight);
    },
    data: function data() {
        return {
            tableHeight: 0,
            GameArr: {},
            PlatformArr: {
                1: 'show91',
                3: '蚂蚁',
                4: 'dd373',
                5: '丸子'
            },
            StatusArr: {
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
            searchParams: {
                status: '',
                game_id: '',
                trade_no: '',
                platform_id: '',
                seller_nick: '',
                date: '',
                page: 1
            },
            TotalPage: 0,
            tableData: []
        };
    }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-10410de6\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/finance/Order.vue":
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
                { attrs: { span: 5 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "单号" } },
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
                { attrs: { span: 5 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "游戏" } },
                    [
                      _c(
                        "el-select",
                        {
                          attrs: { placeholder: "请选择" },
                          model: {
                            value: _vm.searchParams.game_id,
                            callback: function($$v) {
                              _vm.$set(_vm.searchParams, "game_id", $$v)
                            },
                            expression: "searchParams.game_id"
                          }
                        },
                        _vm._l(_vm.GameArr, function(value, key) {
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
                { attrs: { span: 6 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "店铺名称" } },
                    [
                      _c("el-input", {
                        model: {
                          value: _vm.searchParams.seller_nick,
                          callback: function($$v) {
                            _vm.$set(_vm.searchParams, "seller_nick", $$v)
                          },
                          expression: "searchParams.seller_nick"
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
                    { attrs: { label: "接单平台" } },
                    [
                      _c("el-input", {
                        model: {
                          value: _vm.searchParams.platform_id,
                          callback: function($$v) {
                            _vm.$set(_vm.searchParams, "platform_id", $$v)
                          },
                          expression: "searchParams.platform_id"
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
                { attrs: { span: 5 } },
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
                { attrs: { span: 2 } },
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
            attrs: { prop: "trade_no", label: "内部单号", width: "180" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: {
              prop: "channel_order_trade_no",
              label: "淘宝单号",
              width: "180"
            },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(
                          scope.row.channel_order_trade_no
                            ? scope.row.channel_order_trade_no
                            : "--"
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
            attrs: { prop: "source_order_no", label: "补款单号", width: "180" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(
                          scope.row.source_order_no[1]
                            ? scope.row.source_order_no[1]
                            : "--"
                        ) +
                        "\n                " +
                        _vm._s(
                          scope.row.source_order_no[2]
                            ? scope.row.source_order_no[2]
                            : "--"
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
            attrs: { prop: "game_name", label: "游戏", width: "80" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "status", label: "订单状态", width: "" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(_vm.StatusArr[scope.row.status]) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "seller_nick", label: "店铺名称", width: "" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(
                          scope.row.seller_nick ? scope.row.seller_nick : "--"
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
            attrs: { prop: "platform_id", label: "接单平台", width: "" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(
                          scope.row.platform_id
                            ? _vm.PlatformArr[scope.row.platform_id]
                            : "--"
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
            attrs: { prop: "taobao_amount", label: "淘宝金额", width: "" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "taobao_refund", label: "淘宝退款", width: "" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "pay_amount", label: "支付代练费用", width: "" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "get_amount", label: "获得赔偿金额", width: "" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: {
              prop: "get_complain_amount",
              label: "获得投诉金额",
              width: ""
            }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "poundage", label: "手续费", width: "" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "profit", label: "最终支付金额", width: "" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: {
              prop: "customer_service_name",
              label: "发单客服",
              width: ""
            }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: {
              prop: "taobao_created_at",
              label: "淘宝下单时间",
              width: "180"
            },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(
                          scope.row.taobao_created_at
                            ? scope.row.taobao_created_at
                            : "--"
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
            attrs: { prop: "complete_at", label: "代练结算时间", width: "180" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(
                          scope.row.complete_at ? scope.row.complete_at : "--"
                        ) +
                        "\n            "
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
    require("vue-hot-reload-api")      .rerender("data-v-10410de6", module.exports)
  }
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/finance/Order.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/finance/Order.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-10410de6\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/finance/Order.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/finance/Order.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-10410de6", Component.options)
  } else {
    hotAPI.reload("data-v-10410de6", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});