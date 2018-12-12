webpackJsonp([22],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/account/Mine.vue":
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

/* harmony default export */ __webpack_exports__["default"] = ({
    methods: {
        handleUpdate: function handleUpdate() {
            var _this = this;

            this.$api.AccountMineUpdate(this.editForm).then(function (res) {
                _this.$message({
                    showClose: true,
                    type: res.status == 1 ? 'success' : 'error',
                    message: res.message
                });
            }).catch(function (err) {
                _this.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },
        handleForm: function handleForm() {
            var _this2 = this;

            this.$api.AccountMineForm().then(function (res) {
                _this2.editForm = res;
            }).catch(function (err) {
                _this2.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        }
    },
    created: function created() {
        this.handleForm();
    },
    data: function data() {
        return {
            dialogFormVisible: false,
            editForm: {
                'name': '',
                'email': '',
                'type': '',
                'password': ''
            }
        };
    }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-1c34b171\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/account/Mine.vue":
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
          ref: "editForm",
          attrs: { model: _vm.editForm, "label-width": "80px" }
        },
        [
          _c(
            "el-form-item",
            { attrs: { label: "账号" } },
            [
              _c("el-input", {
                attrs: { disabled: true },
                model: {
                  value: _vm.editForm.name,
                  callback: function($$v) {
                    _vm.$set(_vm.editForm, "name", $$v)
                  },
                  expression: "editForm.name"
                }
              })
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "el-form-item",
            { attrs: { label: "邮箱" } },
            [
              _c("el-input", {
                attrs: { disabled: true, placeholder: "" },
                model: {
                  value: _vm.editForm.email,
                  callback: function($$v) {
                    _vm.$set(_vm.editForm, "email", $$v)
                  },
                  expression: "editForm.email"
                }
              })
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "el-form-item",
            { attrs: { label: "密码" } },
            [
              _c("el-input", {
                attrs: { placeholder: "不填写则为原密码" },
                model: {
                  value: _vm.editForm.password,
                  callback: function($$v) {
                    _vm.$set(_vm.editForm, "password", $$v)
                  },
                  expression: "editForm.password"
                }
              })
            ],
            1
          ),
          _vm._v(" "),
          _c("el-form-item", {
            attrs: { label: "代练" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return [
                    _c(
                      "el-radio",
                      {
                        attrs: { label: 1 },
                        model: {
                          value: _vm.editForm.type,
                          callback: function($$v) {
                            _vm.$set(_vm.editForm, "type", $$v)
                          },
                          expression: "editForm.type"
                        }
                      },
                      [_vm._v("接单")]
                    ),
                    _vm._v(" "),
                    _c(
                      "el-radio",
                      {
                        attrs: { label: 2 },
                        model: {
                          value: _vm.editForm.type,
                          callback: function($$v) {
                            _vm.$set(_vm.editForm, "type", $$v)
                          },
                          expression: "editForm.type"
                        }
                      },
                      [_vm._v("发单")]
                    )
                  ]
                }
              }
            ])
          }),
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
                      _vm.handleUpdate()
                    }
                  }
                },
                [_vm._v("修改")]
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
    require("vue-hot-reload-api")      .rerender("data-v-1c34b171", module.exports)
  }
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/account/Mine.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/account/Mine.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-1c34b171\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/account/Mine.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/account/Mine.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-1c34b171", Component.options)
  } else {
    hotAPI.reload("data-v-1c34b171", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});