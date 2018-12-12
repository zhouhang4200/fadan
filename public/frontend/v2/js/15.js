webpackJsonp([15],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/Channel.vue":
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

/* harmony default export */ __webpack_exports__["default"] = ({
    methods: {
        //显示同意退款弹窗
        showRefund: function showRefund(tradeNo) {
            var _this = this;

            this.dialogAgreeRefundFormVisible = true;
            this.$api.GameLevelingChannelOrderRefund({ trade_no: tradeNo }).then(function (res) {
                _this.refund_amount = res.refund_amount;
                _this.refund_reason = res.refund_reason;
                _this.trade_no = res.game_leveling_channel_order_trade_no;
            }).catch(function (err) {
                _this.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },

        //显示拒绝退款弹窗
        showRefuseRefund: function showRefuseRefund(tradeNo) {
            var _this2 = this;

            this.dialogRefuseRefundFormVisible = true;
            this.form.trade_no = tradeNo;
            this.$api.GameLevelingChannelOrderRefund({ trade_no: tradeNo }).then(function (res) {
                _this2.trade_no = res.game_leveling_channel_order_trade_no;
            }).catch(function (err) {
                _this2.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },

        // 同意退款
        agreeRefund: function agreeRefund(tradeNo) {
            var _this3 = this;

            this.$api.GameLevelingChannelOrderAgreeRefund({ trade_no: tradeNo }).then(function (res) {
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

            this.handleTableData();
        },

        // 拒绝退款
        refuseRefund: function refuseRefund(formName) {
            var _this4 = this;

            this.$api.GameLevelingChannelOrderRefuseRefund(this.form).then(function (res) {
                _this4.$message({
                    showClose: true,
                    type: res.status == 1 ? 'success' : 'error',
                    message: res.message
                });
            }).catch(function (err) {
                _this4.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
            this.handleTableData();
        },

        // 加载数据
        handleTableData: function handleTableData() {
            var _this5 = this;

            this.$api.GameLevelingChannelOrder(this.searchParams).then(function (res) {
                _this5.tableData = res.data;
                _this5.TotalPage = res.total;
                _this5.loading = false;
            }).catch(function (err) {
                _this5.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
            this.handStatusCount();
        },

        // 获取渠道游戏
        handleGameData: function handleGameData() {
            var _this6 = this;

            this.$api.GameLevelingChannelGame().then(function (res) {
                _this6.games = res;
            }).catch(function (err) {
                _this6.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },

        // 获取状态数量
        handStatusCount: function handStatusCount() {
            var _this7 = this;

            this.$api.GameLevelingChannelStatus(this.searchParams).then(function (res) {
                _this7.statusCount = res;
            }).catch(function (err) {
                _this7.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },

        // 标签
        handleClick: function handleClick() {
            this.handleTableData();
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
        this.handleGameData();
        this.handleTableHeight();
        window.addEventListener('resize', this.handleTableHeight);
    },
    destroyed: function destroyed() {
        window.removeEventListener('resize', this.handleTableHeight);
    },
    data: function data() {
        return {
            loading: true,
            status_leveling: {
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
            status: {
                1: '待付款',
                2: '进行中',
                3: '待收货',
                4: '完成',
                5: '退款中',
                6: '已退款'
            },
            trade_no: '',
            refund_amount: '',
            refund_reason: '',
            statusCount: [],
            activeChannel: '',
            games: [],
            agreeRefundForm: {},
            refuseRefundForm: {},
            sendOrder: false,
            agreeRefundButton: false,
            refuseRefundButton: false,
            dialogAgreeRefundFormVisible: false,
            dialogRefuseRefundFormVisible: false,
            tableHeight: 0,
            dialogFormVisible: false,
            searchParams: {
                trade_no: '',
                game_name: '',
                status: '',
                date: '',
                page: 1
            },
            form: {
                trade_no: '',
                refuse_refund_reason: ''
            },
            TotalPage: 0,
            tableData: []
        };
    }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-e5065000\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/Channel.vue":
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
                { attrs: { span: 6 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "订单编号" } },
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
                { attrs: { span: 6 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "绑定游戏" } },
                    [
                      _c(
                        "el-select",
                        {
                          attrs: { placeholder: "请选择" },
                          model: {
                            value: _vm.searchParams.game_name,
                            callback: function($$v) {
                              _vm.$set(_vm.searchParams, "game_name", $$v)
                            },
                            expression: "searchParams.game_name"
                          }
                        },
                        _vm._l(_vm.games, function(value, key) {
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
                { attrs: { span: 6 } },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "发布时间" } },
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
        "el-tabs",
        {
          on: { "tab-click": _vm.handleClick },
          model: {
            value: _vm.searchParams.status,
            callback: function($$v) {
              _vm.$set(_vm.searchParams, "status", $$v)
            },
            expression: "searchParams.status"
          }
        },
        [
          _c("el-tab-pane", { attrs: { name: "0" } }, [
            _c("span", { attrs: { slot: "label" }, slot: "label" }, [
              _vm._v("全部")
            ])
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "1" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("待付款"),
                this.statusCount[1] != undefined
                  ? _c("el-badge", { attrs: { value: this.statusCount[1] } })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "2" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("进行中"),
                this.statusCount[2] != undefined
                  ? _c("el-badge", { attrs: { value: this.statusCount[2] } })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "3" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("待收货"),
                this.statusCount[3] != undefined
                  ? _c("el-badge", { attrs: { value: this.statusCount[3] } })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "5" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("退款中"),
                this.statusCount[6] != undefined
                  ? _c("el-badge", { attrs: { value: this.statusCount[6] } })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "4" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("已完成"),
                this.statusCount[4] != undefined
                  ? _c("el-badge", { attrs: { value: this.statusCount[4] } })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "6" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("已退款"),
                this.statusCount[7] != undefined
                  ? _c("el-badge", { attrs: { value: this.statusCount[7] } })
                  : _vm._e()
              ],
              1
            )
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
              value: _vm.loading,
              expression: "loading"
            }
          ],
          staticStyle: { width: "100%", "margin-top": "1px" },
          attrs: { data: _vm.tableData, height: _vm.tableHeight, border: "" }
        },
        [
          _c("el-table-column", {
            attrs: { prop: "trade_no", label: "订单号", width: "200" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "status", label: "订单状态", width: "100" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [_vm._v(_vm._s(_vm.status[scope.row.status]))]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: {
              prop: "game_leveling_order_status",
              label: "平台订单状态",
              width: "100"
            },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return _vm._l(scope.row.game_leveling_orders, function(item) {
                    return scope.row.game_leveling_orders
                      ? _c("span", [
                          _vm._v(_vm._s(_vm.status_leveling[item.status]))
                        ])
                      : _vm._e()
                  })
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "game_name", label: "绑定游戏", width: "100" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "player_info", label: "卖家QQ/电话", width: "200" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "QQ：" +
                        _vm._s(scope.row.player_qq ? scope.row.player_qq : "")
                    ),
                    _c("br"),
                    _vm._v("电话：" + _vm._s(scope.row.player_phone))
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "amount", label: "购买单价", width: "100" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [_vm._v(_vm._s(Number(scope.row.amount)))]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "payment_amount", label: "实付金额", width: "100" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [_vm._v(_vm._s(Number(scope.row.payment_amount)))]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "created_at", label: "下单时间", width: "200" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "remark", label: "备注", width: "" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { label: "操作", width: "250" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    scope.row.status === 5
                      ? _c(
                          "el-button",
                          {
                            attrs: { type: "primary", size: "small" },
                            on: {
                              click: function($event) {
                                _vm.showRefund(scope.row.trade_no)
                              }
                            }
                          },
                          [_vm._v("同意退款")]
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    scope.row.status === 5
                      ? _c(
                          "el-button",
                          {
                            attrs: { type: "primary", size: "small" },
                            on: {
                              click: function($event) {
                                _vm.showRefuseRefund(scope.row.trade_no)
                              }
                            }
                          },
                          [_vm._v("拒绝退款")]
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
          attrs: {
            title: "同意退款",
            modal: true,
            "modal-append-to-body": true,
            visible: _vm.dialogAgreeRefundFormVisible
          },
          on: {
            "update:visible": function($event) {
              _vm.dialogAgreeRefundFormVisible = $event
            }
          }
        },
        [
          _c("div", { staticStyle: { "font-size": "18px" } }, [
            _vm._v("\n            你确定同意用户全额退款（部分退款）申请吗？"),
            _c("br"),
            _vm._v("\n            退款金额：" + _vm._s(_vm.refund_amount)),
            _c("br"),
            _vm._v(
              "\n            退款原因：" +
                _vm._s(_vm.refund_reason) +
                "\n        "
            )
          ]),
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
                  on: {
                    click: function($event) {
                      _vm.dialogAgreeRefundFormVisible = false
                    }
                  }
                },
                [_vm._v("取 消")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                {
                  attrs: { type: "primary" },
                  on: {
                    click: function($event) {
                      _vm.agreeRefund(_vm.trade_no)
                    }
                  }
                },
                [_vm._v("确 定")]
              )
            ],
            1
          )
        ]
      ),
      _vm._v(" "),
      _c(
        "el-dialog",
        {
          attrs: {
            title: "拒绝退款",
            modal: true,
            "modal-append-to-body": true,
            visible: _vm.dialogRefuseRefundFormVisible
          },
          on: {
            "update:visible": function($event) {
              _vm.dialogRefuseRefundFormVisible = $event
            }
          }
        },
        [
          _c(
            "el-form",
            { attrs: { model: _vm.form } },
            [
              _c(
                "div",
                {
                  staticStyle: { "font-size": "18px", "margin-bottom": "10px" }
                },
                [_vm._v("你确定拒绝用户全额退款（部分退款）申请吗？")]
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                [
                  _c("el-input", {
                    attrs: { type: "textarea", placeholder: "请输入拒绝原因" },
                    model: {
                      value: _vm.form.refuse_refund_reason,
                      callback: function($$v) {
                        _vm.$set(_vm.form, "refuse_refund_reason", $$v)
                      },
                      expression: "form.refuse_refund_reason"
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
                  on: {
                    click: function($event) {
                      _vm.dialogRefuseRefundFormVisible = false
                    }
                  }
                },
                [_vm._v("取 消")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                {
                  attrs: { type: "primary" },
                  on: {
                    click: function($event) {
                      _vm.refuseRefund("agreeRefundForm")
                    }
                  }
                },
                [_vm._v("确 定")]
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
    require("vue-hot-reload-api")      .rerender("data-v-e5065000", module.exports)
  }
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/order/game-leveling/Channel.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/Channel.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-e5065000\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/Channel.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/order/game-leveling/Channel.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-e5065000", Component.options)
  } else {
    hotAPI.reload("data-v-e5065000", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});