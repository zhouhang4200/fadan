webpackJsonp([14],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/setting/Authorize.vue":
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

/* harmony default export */ __webpack_exports__["default"] = ({
    methods: {
        // 删除
        authorizeDelete: function authorizeDelete(id) {
            var _this = this;

            this.$confirm('您确定要删除吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this.$api.SettingAuthorizeDelete({ id: id }).then(function (res) {
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
            });
        },

        // 授权
        authorize: function authorize() {
            var _this2 = this;

            this.$api.SettingAuthorizeUrl().then(function (res) {
                _this2.url = res;
                window.location.href = _this2.url;
            }).catch(function (err) {
                _this2.$message({
                    type: 'error',
                    message: '操作失败'
                });
            });
        },

        // 加载数据
        handleTableData: function handleTableData() {
            var _this3 = this;

            this.$api.SettingAuthorizeDataList(this.searchParams).then(function (res) {
                _this3.tableData = res.data.data;
                _this3.TotalPage = res.data.total;

                if (res.bind === 1) {
                    _this3.loading = true;
                }
            }).catch(function (err) {
                _this3.$alert('获取数据失败, 请重试!', '提示', {
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
            loading: false,
            url: '',
            tableData: [],
            searchParams: {
                page: 1
            },
            TotalPage: 0
        };
    }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-44ee009e\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/setting/Authorize.vue":
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
              "操作提示: 该授权用于抓取您淘宝店铺的订单。授权成功后您店铺订单会自动同步到平台中。",
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
                  { attrs: { span: 5 } },
                  [
                    _c(
                      "el-form-item",
                      [
                        _c(
                          "el-button",
                          {
                            attrs: { type: "primary", size: "small" },
                            on: {
                              click: function($event) {
                                _vm.authorize()
                              }
                            }
                          },
                          [_vm._v("授权")]
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
              attrs: { prop: "wang_wang", label: "店铺旺旺", width: "300" }
            }),
            _vm._v(" "),
            _c("el-table-column", {
              attrs: { prop: "created_at", label: "添加时间", width: "" }
            }),
            _vm._v(" "),
            _c("el-table-column", {
              attrs: { label: "操作", width: "300" },
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
                              _vm.authorizeDelete(scope.row.id)
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
        })
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
    require("vue-hot-reload-api")      .rerender("data-v-44ee009e", module.exports)
  }
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/setting/Authorize.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/setting/Authorize.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-44ee009e\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/setting/Authorize.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/setting/Authorize.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-44ee009e", Component.options)
  } else {
    hotAPI.reload("data-v-44ee009e", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});