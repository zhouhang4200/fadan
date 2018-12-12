webpackJsonp([8],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/Taobao.vue":
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
//
//
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
    props: [],
    computed: {
        tableDataEmpty: function tableDataEmpty() {
            return [this.tableData.length === 0 ? ' el-table_empty' : ''];
        }
    },
    data: function data() {
        return {
            statusQuantity: [],
            gameLevelingTypeOptions: [],
            gameOptions: [],
            search: {
                status: '99',
                order_no: '',
                buyer_nick: '',
                game_id: '',
                game_leveling_type_id: '',
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
        // 表格高度计算
        handleTableHeight: function handleTableHeight() {
            this.tableHeight = window.innerHeight - 366;
        },

        // 获取订单状态数量
        handleStatusQuantity: function handleStatusQuantity() {
            var _this = this;

            this.$api.taobaoOrderStatusQuantity(this.search).then(function (res) {
                _this.statusQuantity = res;
            }).catch(function (err) {});
        },

        // 加载订单数据
        handleTableData: function handleTableData() {
            var _this2 = this;

            this.tableLoading = true;
            this.$api.taobaoOrder(this.search).then(function (res) {
                _this2.tableData = res.data;
                _this2.tableDataTotal = res.total;
                _this2.tableLoading = false;
            }).catch(function (err) {
                _this2.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
                _this2.tableLoading = false;
            });
            this.handleStatusQuantity();
        },

        // 加载游戏选项
        handleGameOptions: function handleGameOptions() {
            var _this3 = this;

            this.$api.games().then(function (res) {
                _this3.gameOptions = res.data;
            }).catch(function (err) {});
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
                    _this4.gameLevelingTypeOptions = res;
                }).catch(function (err) {});
            } else {
                this.gameLevelingTypeOptions = [];
            }
        },

        // 发布操作
        handleCreate: function handleCreate(row) {
            // location.href= this.orderRepeatApi + '/' + row.trade_no;
        },

        // 隐藏操作
        handleHide: function handleHide(row) {
            // location.href= this.orderRepeatApi + '/' + row.trade_no;
        },

        // 重置表单
        handleResetForm: function handleResetForm() {
            this.search = {
                status: '',
                order_no: '',
                buyer_nick: '',
                game_id: '',
                game_leveling_type_id: '',
                start_created_at: '',
                created_at: '',
                page: 1
            };
            this.handleTableData();
        }
    },
    created: function created() {
        this.handleTableHeight();
        this.handleTableData();
        this.handleGameOptions();
        window.addEventListener('resize', this.handleTableHeight);
    },
    destroyed: function destroyed() {
        window.removeEventListener('resize', this.handleTableHeight);
    }
});

/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3cd63a41\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/Taobao.vue":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.game-leveling-order-tab .el-tabs__item {\n    font-weight: normal;\n}\n.game-leveling-order-table .el-button {\n    width: 80px;\n}\n.el-table_empty .el-table__empty-block {\n    width: auto !important;\n}\n.search-form-inline .el-select,\n.search-form-inline .el-date-editor--daterange.el-input__inner,\n.search-form-inline .el-form-item {\n    width:100%;\n}\n.search-form-inline .el-range-separator {\n    width:10%;\n}\n.search-form-inline .el-form-item__content {\n    width:80%;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-3cd63a41\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/Taobao.vue":
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
                            key: item.id,
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
          _c("el-tab-pane", { attrs: { name: "99" } }, [
            _c("span", { attrs: { slot: "label" }, slot: "label" }, [
              _vm._v("\n                全部\n            ")
            ])
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "0" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("\n                待处理\n                "),
                this.statusQuantity[0] != undefined
                  ? _c("el-badge", { attrs: { value: this.statusQuantity[0] } })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "1" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("\n                已发布\n                "),
                this.statusQuantity[1] != undefined
                  ? _c("el-badge", { attrs: { value: this.statusQuantity[1] } })
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
                _vm._v("\n                已隐藏\n                "),
                this.statusQuantity[2] != undefined
                  ? _c("el-badge", { attrs: { value: this.statusQuantity[2] } })
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
              value: _vm.tableLoading,
              expression: "tableLoading"
            }
          ],
          staticClass: "game-leveling-order-table",
          staticStyle: { width: "100%", height: "800px" },
          attrs: { height: _vm.tableHeight, data: _vm.tableData, border: "" }
        },
        [
          _c("el-table-column", {
            attrs: {
              fixed: "",
              prop: "seller_nick",
              label: "店铺",
              width: "150"
            }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "tid", label: "订单号", width: "150" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "province", label: "淘宝订单状态", width: "120" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "city", label: "平台订单状态", width: "120" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "game_name", label: "绑定游戏", width: "80" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "zip", label: "买家旺旺", width: "120" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _c(
                      "a",
                      {
                        attrs: {
                          href:
                            "http://www.taobao.com/webww/ww.php?ver=3&touid=" +
                            scope.row.buyer_nick +
                            "&siteid=cntaobao&status=1&charset=utf-8",
                          target: "_blank"
                        }
                      },
                      [
                        _c("div", { staticStyle: { "margin-left": "10px" } }, [
                          _vm._v(_vm._s(scope.row.buyer_nick))
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
            attrs: { prop: "price", label: "购买单价" }
          }),
          _vm._v(" "),
          _c("el-table-column", { attrs: { prop: "num", label: "购买数量" } }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "payment", label: "实付金额" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "created", label: "下单时间" }
          }),
          _vm._v(" "),
          _c("el-table-column", { attrs: { prop: "remark", label: "备注" } }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { fixed: "right", label: "操作", width: "200" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _c(
                      "el-button",
                      {
                        attrs: { size: "small" },
                        on: {
                          click: function($event) {
                            _vm.handleCreate(scope.row)
                          }
                        }
                      },
                      [_vm._v("发布")]
                    ),
                    _vm._v(" "),
                    _c(
                      "el-button",
                      {
                        attrs: { size: "small", type: "primary" },
                        on: {
                          click: function($event) {
                            _vm.handleHide(scope.row)
                          }
                        }
                      },
                      [_vm._v("隐藏")]
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
    require("vue-hot-reload-api")      .rerender("data-v-3cd63a41", module.exports)
  }
}

/***/ }),

/***/ "./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3cd63a41\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/Taobao.vue":
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__("./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3cd63a41\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/Taobao.vue");
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__("./node_modules/vue-style-loader/lib/addStylesClient.js")("9f1eee5e", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3cd63a41\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Taobao.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3cd63a41\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Taobao.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/order/game-leveling/Taobao.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__("./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3cd63a41\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/Taobao.vue")
}
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/Taobao.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-3cd63a41\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/Taobao.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/order/game-leveling/Taobao.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-3cd63a41", Component.options)
  } else {
    hotAPI.reload("data-v-3cd63a41", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});