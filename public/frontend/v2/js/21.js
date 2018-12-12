webpackJsonp([21],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/account/Station.vue":
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

/* harmony default export */ __webpack_exports__["default"] = ({
    methods: {
        // 新增按钮
        ShowAddForm: function ShowAddForm() {
            this.dialogFormVisibleAdd = true;
            this.form = {
                name: '',
                permission: ''
            };
        },

        // 编辑按钮
        ShowEditForm: function ShowEditForm(row) {
            this.handleTableData();
            this.dialogFormVisible = true;
            this.editForm = JSON.parse(JSON.stringify(row));
        },

        // 取消按钮
        cancel: function cancel(formName) {
            this.dialogFormVisible = false;
            this.dialogFormVisibleAdd = false;
            this.$refs[formName].clearValidate();
        },

        // 添加
        submitFormAdd: function submitFormAdd(formName) {
            var _this = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    var permission = '';
                    _this.$refs.tree.getCheckedNodes().forEach(function (v) {
                        permission += v.id + ',';
                    });
                    _this.form.permission = permission;
                    _this.$api.AccountStationAdd(_this.form).then(function (res) {
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
                    _this2.$api.AccountStationUpdate(_this2.editForm).then(function (res) {
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
        stationDelete: function stationDelete(id) {
            var _this3 = this;

            this.$confirm('您确定要删除吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                _this3.$api.AccountStationDelete({ id: id }).then(function (res) {
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

            this.$api.AccountStationDataList(this.searchParams).then(function (res) {
                _this4.tableData = res.data;
                _this4.TotalPage = res.total;
            }).catch(function (err) {
                _this4.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },

        // 获取当前用户所有的权限
        allPermissions: function allPermissions() {
            var _this5 = this;

            this.$api.AccountStationPermission().then(function (res) {
                _this5.permissionTree = res;
                _this5.stations = res;
            }).catch(function (err) {
                _this5.$alert('获取数据失败, 请重试!', '提示', {
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
        handleCheckedStationChange: function handleCheckedStationChange(value) {
            var checkedCount = value.length;
            this.isIndeterminate = true;
        },

        // 表格高度计算
        handleTableHeight: function handleTableHeight() {
            this.tableHeight = window.innerHeight - 318;
        }
    },
    created: function created() {
        this.handleTableData();
        this.allPermissions();
        this.handleTableHeight();
        window.addEventListener('resize', this.handleTableHeight);
    },
    destroyed: function destroyed() {
        window.removeEventListener('resize', this.handleTableHeight);
    },
    data: function data() {
        return {
            tableHeight: 0,
            stations: [],
            isIndeterminate: true,
            expendAll: true,
            permissionTree: [],
            defaultProps: {
                children: 'new_permissions',
                label: 'alias'
            },
            dialogFormVisible: false,
            dialogFormVisibleAdd: false,
            searchParams: {
                page: 1
            },
            TotalPage: 0,
            tableData: [],
            rules: {
                name: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }]
            },
            editFormRules: {
                name: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                checkedPermission: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }]
            },
            form: {
                name: '',
                permission: '',
                checkedPermission: []
            },
            editForm: {
                id: '',
                name: '',
                permission: '',
                checkedPermission: []
            }
        };
    }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-07d4f806\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/account/Station.vue":
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
                    [
                      _c(
                        "el-button",
                        {
                          attrs: { type: "primary", size: "small" },
                          on: { click: _vm.ShowAddForm }
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
            attrs: { prop: "id", label: "序号", width: "100" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "name", label: "岗位名称", width: "100" }
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "station", label: "岗位员工", width: "" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return _vm._l(scope.row.new_users, function(value, key) {
                    return _c(
                      "span",
                      { staticStyle: { "margin-right": "10px" } },
                      [_vm._v(_vm._s(value.username ? value.username : ""))]
                    )
                  })
                }
              }
            ])
          }),
          _vm._v(" "),
          _c("el-table-column", {
            attrs: { prop: "permission", label: "拥有权限", width: "" },
            scopedSlots: _vm._u([
              {
                key: "default",
                fn: function(scope) {
                  return _vm._l(scope.row.new_permissions, function(
                    value,
                    key
                  ) {
                    return _c(
                      "span",
                      { staticStyle: { "margin-right": "10px" } },
                      [_vm._v(_vm._s(value.alias ? value.alias : ""))]
                    )
                  })
                }
              }
            ])
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
                            _vm.ShowEditForm(scope.row)
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
                            _vm.stationDelete(scope.row.id)
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
          attrs: { title: "新增岗位", visible: _vm.dialogFormVisibleAdd },
          on: {
            "update:visible": function($event) {
              _vm.dialogFormVisibleAdd = $event
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
                { attrs: { label: "岗位名称", prop: "name" } },
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
                { attrs: { label: "拥有权限", prop: "permission" } },
                [
                  _c("el-tree", {
                    ref: "tree",
                    attrs: {
                      props: _vm.defaultProps,
                      "default-expand-all": _vm.expendAll,
                      data: _vm.permissionTree,
                      "node-key": "id",
                      "show-checkbox": ""
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
                          _vm.submitFormAdd("form")
                        }
                      }
                    },
                    [_vm._v("确认添加")]
                  ),
                  _vm._v(" "),
                  _c(
                    "el-button",
                    {
                      on: {
                        click: function($event) {
                          _vm.cancel("form")
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
      ),
      _vm._v(" "),
      _c(
        "el-dialog",
        {
          attrs: { title: "编辑岗位", visible: _vm.dialogFormVisible },
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
              ref: "editForm",
              attrs: {
                model: _vm.editForm,
                rules: _vm.editFormRules,
                "label-width": "80px"
              }
            },
            [
              _c(
                "el-form-item",
                { attrs: { label: "岗位名称", prop: "name" } },
                [
                  _c("el-input", {
                    attrs: { autocomplete: "off" },
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
                { attrs: { label: "拥有权限", prop: "checkedPermission" } },
                [
                  _vm._l(_vm.stations, function(item) {
                    return [
                      _c(
                        "el-checkbox",
                        {
                          attrs: { indeterminate: _vm.isIndeterminate },
                          model: {
                            value: item.id,
                            callback: function($$v) {
                              _vm.$set(item, "id", $$v)
                            },
                            expression: "item.id"
                          }
                        },
                        [_vm._v(_vm._s(item.alias))]
                      ),
                      _vm._v(" "),
                      _c(
                        "el-checkbox-group",
                        {
                          staticStyle: { "padding-left": "25px" },
                          on: { change: _vm.handleCheckedStationChange },
                          model: {
                            value: _vm.editForm.checkedPermission,
                            callback: function($$v) {
                              _vm.$set(_vm.editForm, "checkedPermission", $$v)
                            },
                            expression: "editForm.checkedPermission"
                          }
                        },
                        _vm._l(item.new_permissions, function(option) {
                          return _c(
                            "el-checkbox",
                            { key: option.id, attrs: { label: option.id } },
                            [_vm._v(_vm._s(option.alias))]
                          )
                        })
                      )
                    ]
                  })
                ],
                2
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
                          _vm.submitFormUpdate("editForm")
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
                          _vm.cancel("editForm")
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
    require("vue-hot-reload-api")      .rerender("data-v-07d4f806", module.exports)
  }
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/account/Station.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/account/Station.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-07d4f806\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/account/Station.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/account/Station.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-07d4f806", Component.options)
  } else {
    hotAPI.reload("data-v-07d4f806", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});