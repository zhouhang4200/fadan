webpackJsonp([12],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/setting/Goods.vue":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
        goodsAdd: function goodsAdd() {
            this.dialogFormVisible = true;
            this.isAdd = true;
            this.isUpdate = false;
            this.title = "新增";
            this.form = {
                id: '',
                game_name: '',
                seller_nick: '',
                foreign_goods_id: '',
                game_id: '',
                remark: '',
                delivery: '',
                created_at: '',
                updated_at: ''
            };
        },

        // 编辑按钮
        goodsEdit: function goodsEdit(row) {
            this.dialogFormVisible = true;
            this.form = JSON.parse(JSON.stringify(row));
            this.isAdd = false;
            this.title = "修改";
            this.isUpdate = true;
        },

        // 取消按钮
        goodsCancel: function goodsCancel(formName) {
            this.dialogFormVisible = false;
            this.$refs[formName].clearValidate();
        },

        // 添加
        submitFormAdd: function submitFormAdd(formName) {
            var _this = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this.$api.SettingGoodsAdd(_this.form).then(function (res) {
                        _this.$message({
                            showClose: true,
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });
                        // location.reload();
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

        // 修改
        submitFormUpdate: function submitFormUpdate(formName) {
            var _this2 = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this2.$api.SettingGoodsUpdate(_this2.form).then(function (res) {
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

        // 删除
        goodsDelete: function goodsDelete(id) {
            var _this3 = this;

            this.$confirm('您确定要删除吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this3.$api.SettingGoodsDelete({ id: id }).then(function (res) {
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

        // 加载数据
        handleTableData: function handleTableData() {
            var _this4 = this;

            this.$api.SettingGoodsDataList(this.searchParams).then(function (res) {
                _this4.tableData = res.data;
                _this4.TotalPage = res.total;
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

        // 开关状态
        handleSwitch: function handleSwitch(value, row) {
            var _this5 = this;

            this.$api.SettingGoodsDelivery({ delivery: value, id: row.id }).then(function (res) {
                _this5.$message({
                    showClose: true,
                    type: res.status == 1 ? 'success' : 'error',
                    message: res.message
                });
            }).catch(function (err) {
                _this5.$message({
                    type: 'error',
                    message: '操作失败'
                });
            });
        },

        // 游戏
        game: function game() {
            var _this6 = this;

            this.$api.SettingGoodsGame().then(function (res) {
                _this6.games = res;
            }).catch(function (err) {
                _this6.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },

        // 商铺
        sellerNick: function sellerNick() {
            var _this7 = this;

            this.$api.SettingGoodsSellerNick().then(function (res) {
                _this7.sellerNicks = res;
            }).catch(function (err) {
                _this7.$alert('获取数据失败, 请重试!', '提示', {
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
        this.sellerNick();
        this.game();
        this.handleTableHeight();
        window.addEventListener('resize', this.handleTableHeight);
    },
    destroyed: function destroyed() {
        window.removeEventListener('resize', this.handleTableHeight);
    },
    data: function data() {
        return _defineProperty({
            tableHeight: 0,
            form: 'form',
            games: [],
            sellerNicks: [],
            title: '新增',
            isAdd: true,
            isUpdate: false,
            dialogFormVisible: false,
            rules: {
                seller_nick: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                foreign_goods_id: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                game_id: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                remark: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }]
            },
            tableData: [],
            searchParams: {
                page: 1,
                foreign_goods_id: ''
            },
            TotalPage: 0
        }, 'form', {
            id: '',
            game_name: '',
            seller_nick: '',
            foreign_goods_id: '',
            game_id: '',
            remark: '',
            delivery: '',
            created_at: '',
            updated_at: ''
        });
    }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-adb42fea\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/setting/Goods.vue":
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
              "操作提示: 添加了某一淘宝/天猫商品，则会自动获取该商品对应的订单，未添加商品则无法获取商品对应订单。请确保添加商品之前，已进行店铺授权。",
            type: "success",
            closable: false
          }
        }),
        _vm._v(" "),
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
                  { attrs: { span: 7 } },
                  [
                    _c(
                      "el-form-item",
                      { attrs: { label: "淘宝商品ID" } },
                      [
                        _c("el-input", {
                          model: {
                            value: _vm.searchParams.foreign_goods_id,
                            callback: function($$v) {
                              _vm.$set(
                                _vm.searchParams,
                                "foreign_goods_id",
                                $$v
                              )
                            },
                            expression: "searchParams.foreign_goods_id"
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
                                _vm.goodsAdd()
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
              attrs: { prop: "seller_nick", label: "店铺", width: "150" }
            }),
            _vm._v(" "),
            _c("el-table-column", {
              attrs: {
                prop: "foreign_goods_id",
                label: "淘宝商品ID",
                width: "150"
              }
            }),
            _vm._v(" "),
            _c("el-table-column", {
              attrs: { prop: "game_name", label: "绑定游戏", width: "150" }
            }),
            _vm._v(" "),
            _c("el-table-column", {
              attrs: { prop: "remark", label: "备注", width: "" }
            }),
            _vm._v(" "),
            _c("el-table-column", {
              attrs: { prop: "delivery", label: "提验自动发货", width: "180" },
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
                          "inactive-value": 2
                        },
                        on: {
                          change: function($event) {
                            _vm.handleSwitch($event, scope.row)
                          }
                        },
                        model: {
                          value: scope.row.delivery,
                          callback: function($$v) {
                            _vm.$set(scope.row, "delivery", $$v)
                          },
                          expression: "scope.row.delivery"
                        }
                      })
                    ]
                  }
                }
              ])
            }),
            _vm._v(" "),
            _c("el-table-column", {
              attrs: { prop: "created_at", label: "添加时间", width: "180" }
            }),
            _vm._v(" "),
            _c("el-table-column", {
              attrs: { prop: "updated_at", label: "更新时间", width: "180" }
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
                              _vm.goodsEdit(scope.row)
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
                              _vm.goodsDelete(scope.row.id)
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
                  "label-width": "120px"
                }
              },
              [
                _c(
                  "el-form-item",
                  { attrs: { label: "店铺", prop: "seller_nick" } },
                  [
                    _c(
                      "el-select",
                      {
                        attrs: { placeholder: "请选择" },
                        model: {
                          value: _vm.form.seller_nick,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "seller_nick", $$v)
                          },
                          expression: "form.seller_nick"
                        }
                      },
                      _vm._l(_vm.sellerNicks, function(value) {
                        return _c(
                          "el-option",
                          { key: value, attrs: { value: value, label: value } },
                          [_vm._v(_vm._s(value))]
                        )
                      })
                    )
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "el-form-item",
                  { attrs: { label: "绑定游戏", prop: "game_id" } },
                  [
                    _c(
                      "el-select",
                      {
                        attrs: { placeholder: "请选择" },
                        model: {
                          value: _vm.form.game_id,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "game_id", $$v)
                          },
                          expression: "form.game_id"
                        }
                      },
                      _vm._l(_vm.games, function(item) {
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
                ),
                _vm._v(" "),
                _c(
                  "el-form-item",
                  { attrs: { label: "淘宝链接", prop: "foreign_goods_id" } },
                  [
                    _c("el-input", {
                      attrs: { name: "foreign_goods_id", autocomplete: "off" },
                      model: {
                        value: _vm.form.foreign_goods_id,
                        callback: function($$v) {
                          _vm.$set(_vm.form, "foreign_goods_id", $$v)
                        },
                        expression: "form.foreign_goods_id"
                      }
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "el-form-item",
                  { attrs: { label: "备注信息", prop: "remark" } },
                  [
                    _c("el-input", {
                      attrs: { type: "textarea" },
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
                            _vm.goodsCancel("form")
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
    require("vue-hot-reload-api")      .rerender("data-v-adb42fea", module.exports)
  }
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/setting/Goods.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/setting/Goods.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-adb42fea\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/setting/Goods.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/setting/Goods.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-adb42fea", Component.options)
  } else {
    hotAPI.reload("data-v-adb42fea", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});