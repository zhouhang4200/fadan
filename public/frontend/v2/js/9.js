webpackJsonp([9],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanComplain.vue":
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

/* harmony default export */ __webpack_exports__["default"] = ({
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
            searchParams: {
                status: '99',
                order_no: '',
                buyer_nick: '',
                game_id: '',
                game_leveling_type_id: '',
                start_created_at: '',
                created_at: '',
                page: 1
            },
            platform: {
                5: '丸子代练',
                2: '91代练',
                3: '蚂蚁代练'
            },
            taobaoStatusMap: {
                1: '投诉中',
                2: '已取消',
                3: '投诉成功',
                4: '投诉失败'
            },
            complainStatusMap: {
                1: '投诉中',
                2: '已取消',
                3: '投诉成功',
                4: '投诉失败'
            },
            orderStatusMap: {
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
            this.tableHeight = window.innerHeight - 318;
        },

        // 获取订单状态数量
        handleStatusQuantity: function handleStatusQuantity() {
            var _this = this;

            this.$api.businessmanComplainStatusQuantity().then(function (res) {
                _this.statusQuantity = res;
            });
        },

        // 加载订单数据
        handleTableData: function handleTableData() {
            var _this2 = this;

            this.tableLoading = true;
            this.$api.businessmanComplain(this.searchParams).then(function (res) {
                _this2.tableData = res.data;
                _this2.tableDataTotal = res.total;
                _this2.tableLoading = false;
            });
            this.handleStatusQuantity();
        },

        // 加载游戏选项
        handleGameOptions: function handleGameOptions() {
            var _this3 = this;

            this.$api.games().then(function (res) {
                _this3.gameOptions = res.data;
                var currentThis = _this3;
                res.forEach(function (item) {
                    currentThis.gameMap[item.id] = item.name;
                });
            });
        },

        // 搜索
        handleSearch: function handleSearch() {
            this.handleTableData();
        },

        // 切换页码
        handleParamsPage: function handleParamsPage(page) {
            this.searchParams.page = page;
            this.handleTableData();
        },

        // 切换状态tab
        handleParamsStatus: function handleParamsStatus() {
            this.handleTableData();
        },

        // 选择游戏后加载代练类型
        handleSearchParamsGameId: function handleSearchParamsGameId() {
            var _this4 = this;

            if (this.searchParams.game_id) {
                this.$api.gameLevelingTypes({
                    'game_id': this.searchParams.game_id
                }).then(function (res) {
                    _this4.gameLevelingTypeOptions = res;
                });
            } else {
                this.gameLevelingTypeOptions = [];
            }
        },

        // 查看投诉图片
        handleShowImage: function handleShowImage(row) {
            var _this5 = this;

            // 请求图片
            this.$api.businessmanComplainImage({
                'id': row.id
            }).then(function (res) {
                var h = _this5.$createElement;
                var item = [];
                res.content.forEach(function (val) {
                    item.push(h('el-carousel-item', null, [h('img', {
                        attrs: {
                            src: val,
                            class: 'avatar'
                        }
                    }, '')]));
                });

                _this5.$msgbox({
                    title: '查看仲裁图片',
                    message: h('el-carousel', null, item),
                    showCancelButton: true,
                    confirmButtonText: '确定',
                    cancelButtonText: '取消'
                });
            }).catch(function (err) {
                _this5.$message({
                    type: 'error',
                    message: '操作失败'
                });
            });
        },

        // 取消投诉操作
        handleCancelComplain: function handleCancelComplain(row) {
            var _this6 = this;

            this.$confirm('您确定要"取消投诉"吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                // 取消操作
                _this6.$api.businessmanComplainCancel({
                    'id': row.id
                }).then(function (res) {
                    _this6.$message({
                        type: res.status == 1 ? 'success' : 'error',
                        message: res.message
                    });
                    if (res.status == 1) {
                        _this6.handleTableData();
                    }
                }).catch(function (err) {
                    _this6.$message({
                        type: 'error',
                        message: '操作失败'
                    });
                });
            });
        },

        // 重置表单
        handleResetForm: function handleResetForm() {
            this.searchParams = {
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

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-491af420\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanComplain.vue":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.game-leveling-order-tab .el-tabs__item {\n    font-weight: normal;\n}\n.game-leveling-order-table .el-button {\n    width: 80px;\n}\n.el-table_empty .el-table__empty-block {\n    width: auto !important;\n}\n.search-form-inline .el-select,\n.search-form-inline .el-date-editor--daterange.el-input__inner,\n.search-form-inline .el-form-item {\n    width:100%;\n}\n.search-form-inline .el-range-separator {\n    width:10%;\n}\n.search-form-inline .el-form-item__content {\n    width:80%;\n}\n.avatar {\n    width: 100%;\n    height: 100%;\n    display: block;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-491af420\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanComplain.vue":
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
                    { attrs: { label: "订单单号", prop: "name" } },
                    [
                      _c("el-input", {
                        model: {
                          value: _vm.searchParams.order_no,
                          callback: function($$v) {
                            _vm.$set(_vm.searchParams, "order_no", $$v)
                          },
                          expression: "searchParams.order_no"
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
                          on: { change: _vm.handleSearchParamsGameId },
                          model: {
                            value: _vm.searchParams.game_id,
                            callback: function($$v) {
                              _vm.$set(_vm.searchParams, "game_id", $$v)
                            },
                            expression: "searchParams.game_id"
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
          ),
          _vm._v(" "),
          _c("el-row", { attrs: { gutter: 16 } })
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
            value: _vm.searchParams.status,
            callback: function($$v) {
              _vm.$set(_vm.searchParams, "status", $$v)
            },
            expression: "searchParams.status"
          }
        },
        [
          _c("el-tab-pane", { attrs: { name: "99" } }, [
            _c("span", { attrs: { slot: "label" }, slot: "label" }, [
              _vm._v("\n                全部\n            ")
            ])
          ]),
          _vm._v(" "),
          _c("el-tab-pane", { attrs: { name: "1" } }, [
            _c(
              "span",
              { attrs: { slot: "label" }, slot: "label" },
              [
                _vm._v("\n                投诉中\n                "),
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
                _vm._v("\n                已取消\n                "),
                this.statusQuantity[2] != undefined
                  ? _c("el-badge", { attrs: { value: this.statusQuantity[2] } })
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
                _vm._v("\n                投诉成功\n                "),
                this.statusQuantity[3] != undefined
                  ? _c("el-badge", { attrs: { value: this.statusQuantity[3] } })
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
                _vm._v("\n                投诉失败\n                "),
                this.statusQuantity[4] != undefined
                  ? _c("el-badge", { attrs: { value: this.statusQuantity[4] } })
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
            attrs: { prop: "tid", label: "订单号", width: "250" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _c(
                      "router-link",
                      {
                        attrs: {
                          to: {
                            name: "gameLevelingOrderShow",
                            query: {
                              trade_no: scope.row.game_leveling_order_trade_no
                            }
                          }
                        }
                      },
                      [
                        _c("div", { staticStyle: { "margin-left": "10px" } }, [
                          _vm._v(
                            " 淘宝：" +
                              _vm._s(
                                scope.row.game_leveling_order
                                  .channel_order_trade_no
                              )
                          )
                        ]),
                        _vm._v(" "),
                        _c("div", { staticStyle: { "margin-left": "10px" } }, [
                          _vm._v(
                            " " +
                              _vm._s(
                                _vm.platform[
                                  scope.row.game_leveling_order.platform_id
                                ]
                              ) +
                              "：" +
                              _vm._s(
                                scope.row.game_leveling_order.platform_trade_no
                              )
                          )
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
            attrs: { prop: "province", label: "淘宝订单状态" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(
                          _vm.taobaoStatusMap[
                            scope.row.game_leveling_order.channel_order_status
                          ]
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
            attrs: { prop: "order_status", label: "平台订单状态" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                    " +
                        _vm._s(
                          _vm.orderStatusMap[
                            scope.row.game_leveling_order.status
                          ]
                        ) +
                        "\n                "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "game_name", label: "游戏" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(
                          scope.row.game_leveling_order
                            .game_leveling_order_detail.game_name
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
            attrs: { prop: "amount", label: "要求赔偿金额" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(Number(scope.row.amount)) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "status", label: "投诉状态" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _vm._v(
                      "\n                " +
                        _vm._s(_vm.complainStatusMap[scope.row.status]) +
                        "\n            "
                    )
                  ]
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "created_at", label: "投诉时间" }
          }),
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
                            _vm.handleShowImage(scope.row)
                          }
                        }
                      },
                      [_vm._v("查看截图")]
                    ),
                    _vm._v(" "),
                    scope.row.status == 1
                      ? _c(
                          "el-button",
                          {
                            attrs: { size: "small", type: "primary" },
                            on: {
                              click: function($event) {
                                _vm.handleCancelComplain(scope.row)
                              }
                            }
                          },
                          [_vm._v("取消投诉")]
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
      _c(
        "div",
        { staticClass: "block", staticStyle: { "margin-top": "15px" } },
        [
          _c("el-pagination", {
            attrs: {
              background: "",
              "current-page": _vm.searchParams.page,
              "page-size": 20,
              layout: "total, prev, pager, next, jumper",
              total: _vm.tableDataTotal
            },
            on: {
              "current-change": _vm.handleParamsPage,
              "update:currentPage": function($event) {
                _vm.$set(_vm.searchParams, "page", $event)
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
    require("vue-hot-reload-api")      .rerender("data-v-491af420", module.exports)
  }
}

/***/ }),

/***/ "./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-491af420\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanComplain.vue":
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__("./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-491af420\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanComplain.vue");
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__("./node_modules/vue-style-loader/lib/addStylesClient.js")("41afc80d", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-491af420\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./BusinessmanComplain.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-491af420\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./BusinessmanComplain.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/order/game-leveling/BusinessmanComplain.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__("./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-491af420\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanComplain.vue")
}
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanComplain.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-491af420\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/order/game-leveling/BusinessmanComplain.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/order/game-leveling/BusinessmanComplain.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-491af420", Component.options)
  } else {
    hotAPI.reload("data-v-491af420", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});