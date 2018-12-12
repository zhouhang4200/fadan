webpackJsonp([10],{

/***/ "./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/account/Authentication.vue":
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

/* harmony default export */ __webpack_exports__["default"] = ({
    methods: {
        // 获取编辑页面的数据
        authenticationForm: function authenticationForm() {
            var _this = this;

            this.$api.AccountAuthenticationForm().then(function (res) {
                if (res.type && res.type === 1) {
                    _this.form = res;
                    _this.isCompanyDisabled = true;
                    _this.imageUrl1 = _this.form.front_card_picture;
                    _this.imageUrl2 = _this.form.back_card_picture;
                    _this.imageUrl3 = _this.form.hold_card_picture;

                    if (res.status === 1) {
                        _this.isPersonalShowAdd = false;
                        _this.isPersonalShowEdit = false;
                    } else {
                        _this.isPersonalShowAdd = false;
                        _this.isPersonalShowEdit = true;
                    }
                } else if (res.data && res.type === 2) {
                    _this.companyForm = res;
                    _this.isPersonalDisabled = true;
                    _this.imageUrl4 = _this.form.license_picture;
                    _this.imageUrl5 = _this.form.bank_open_account_picture;
                    _this.imageUrl6 = _this.form.agency_agreement_picture;

                    if (res.status === 1) {
                        _this.isCompanyShowAdd = false;
                        _this.isCompanyShowEdit = false;
                    } else {
                        _this.isCompanyShowAdd = false;
                        _this.isCompanyShowEdit = true;
                    }
                }
            }).catch(function (err) {
                _this.$alert('获取数据失败, 请重试!', '提示', {
                    confirmButtonText: '确定',
                    callback: function callback(action) {}
                });
            });
        },
        handleClick: function handleClick(tab, event) {},

        // 图片上传成功将地址回传给表单
        handleAvatarSuccess: function handleAvatarSuccess(res, file) {
            if (res.status > 0) {
                if (res.name === 'front_card_picture') {
                    this.imageUrl1 = URL.createObjectURL(file.raw);
                    this.form.front_card_picture = res.path;
                } else if (res.name === 'back_card_picture') {
                    this.imageUrl2 = URL.createObjectURL(file.raw);
                    this.form.back_card_picture = res.path;
                } else if (res.name === 'hold_card_picture') {
                    this.imageUrl3 = URL.createObjectURL(file.raw);
                    this.form.hold_card_picture = res.path;
                } else if (res.name === 'license_picture') {
                    this.imageUrl4 = URL.createObjectURL(file.raw);
                    this.companyForm.license_picture = res.path;
                } else if (res.name === 'bank_open_account_picture') {
                    this.imageUrl5 = URL.createObjectURL(file.raw);
                    this.companyForm.bank_open_account_picture = res.path;
                } else if (res.name === 'agency_agreement_picture') {
                    this.imageUrl6 = URL.createObjectURL(file.raw);
                    this.companyForm.agency_agreement_picture = res.path;
                }
            }
        },

        // 图片上传
        beforeAvatarUpload: function beforeAvatarUpload(file) {
            var isJPEG = file.type === 'image/jpeg';
            // const isPng = file.type === 'image/png';
            // const isJPG = file.type === 'image/jpg';
            var isLt2M = file.size / 1024 / 1024 < 2;

            if (!isJPEG) {
                this.$message.error('上传头像图片只能是 JPG JPEG PNG格式!');
            }
            if (!isLt2M) {
                this.$message.error('上传头像图片大小不能超过 2MB!');
            }
            return isJPEG && isLt2M;
        },

        // 修改
        submitFormUpdate: function submitFormUpdate(formName) {
            var _this2 = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    var data = '';
                    if (_this2.form.name) {
                        data = _this2.form;
                    } else if (_this2.companyForm.name) {
                        data = _this2.companyForm;
                    }
                    _this2.$api.AccountAuthenticationUpdate(data).then(function (res) {
                        _this2.$message({
                            showClose: true,
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });
                    }).catch(function (err) {
                        _this2.$alert('获取数据失败, 请重试!', '提示', {
                            confirmButtonText: '确定',
                            callback: function callback(action) {}
                        });
                    });
                } else {
                    return false;
                }
            });
        },

        // 新增
        submitForm: function submitForm(formName) {
            var _this3 = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    var data = '';
                    if (_this3.form.name) {
                        data = _this3.form;
                    } else if (_this3.companyForm.name) {
                        data = _this3.companyForm;
                    }
                    _this3.$api.AccountAuthenticationAdd(data).then(function (res) {
                        _this3.$message({
                            showClose: true,
                            type: res.status == 1 ? 'success' : 'error',
                            message: res.message
                        });
                        _this3.authenticationForm();
                    }).catch(function (err) {
                        _this3.$alert('获取数据失败, 请重试!', '提示', {
                            confirmButtonText: '确定',
                            callback: function callback(action) {}
                        });
                    });
                } else {
                    return false;
                }
            });
        },
        updateForm: function updateForm(row) {
            this.form = row;
        },
        submitUpdateForm: function submitUpdateForm(formName) {
            var _this4 = this;

            this.$refs[formName].validate(function (valid) {
                if (valid) {
                    _this4.$api.AccountAuthenticationUpdate(_this4.form).then(function (res) {
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
                } else {
                    return false;
                }
            });
        }
    },
    created: function created() {
        this.authenticationForm();
    },
    data: function data() {
        var checkPhone = function checkPhone(rule, value, callback) {
            if (!value) {
                return callback(new Error('必填项不能为空!'));
            }
            if (!Number.isInteger(parseInt(value))) {
                callback(new Error('请输入数字值！'));
            } else {
                var reg = /^1[3|4|5|7|8][0-9]\d{8}$/;
                if (reg.test(value)) {
                    callback();
                } else {
                    callback(new Error('请输入正确的手机号！'));
                }
                callback();
            }
            callback();
        };
        var checkQq = function checkQq(rule, value, callback) {
            if (!value) {
                return callback(new Error('必填项不能为空!'));
            }

            if (!Number.isInteger(parseInt(value))) {
                callback(new Error('请输入数字值！'));
            }
            callback();
        };
        return {
            isPersonalShowAdd: true,
            isPersonalShowEdit: false,
            isCompanyShowAdd: true,
            isCompanyShowEdit: false,
            isPersonalDisabled: false,
            isCompanyDisabled: false,
            imageUrl1: '',
            imageUrl2: '',
            imageUrl3: '',
            imageUrl4: '',
            imageUrl5: '',
            imageUrl6: '',
            UploadUrl: '',
            activeName: 'personal',
            rules: {
                identity_card: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }, { validator: checkQq, trigger: 'blur' }],
                bank_number: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }, { validator: checkQq, trigger: 'blur' }],
                phone_number: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }, { validator: checkPhone, trigger: 'blur' }],
                bank_name: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                name: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                front_card_picture: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                back_card_picture: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                hold_card_picture: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }]
            },
            form: {
                type: '',
                name: '',
                phone_number: '',
                bank_name: '',
                bank_number: '',
                identity_card: '',
                front_card_picture: '',
                back_card_picture: '',
                hold_card_picture: ''
            },
            companyForm: {
                type: '',
                name: '',
                phone_number: '',
                bank_name: '',
                bank_number: '',
                license_name: '',
                license_number: '',
                corporation: '',
                license_picture: '',
                bank_open_account_picture: '',
                agency_agreement_picture: ''
            },
            companyFormRules: {
                bank_number: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }, { validator: checkQq, trigger: 'blur' }],
                phone_number: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }, { validator: checkPhone, trigger: 'blur' }],
                bank_name: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                name: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                license_name: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                license_number: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                corporation: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                bank_open_account_picture: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                agency_agreement_picture: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }],
                license_picture: [{ required: true, message: '必填项不可为空!', trigger: 'blur' }]
            }
        };
    }
});

/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-8eb5f4d4\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/account/Authentication.vue":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.avatar-uploader .el-upload {\n    border: 1px dashed #d9d9d9;\n    border-radius: 6px;\n    cursor: pointer;\n    position: relative;\n    overflow: hidden;\n}\n.avatar-uploader .el-upload:hover {\n    border-color: #409EFF;\n}\n.avatar-uploader-icon {\n    font-size: 28px;\n    color: #8c939d;\n    width: 500px;\n    height: 400px;\n    line-height: 400px;\n    text-align: center;\n}\n.avatar {\n    width: 500px;\n    height: 400px;\n    display: block;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-8eb5f4d4\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/account/Authentication.vue":
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
        "el-tabs",
        {
          on: { "tab-click": _vm.handleClick },
          model: {
            value: _vm.activeName,
            callback: function($$v) {
              _vm.activeName = $$v
            },
            expression: "activeName"
          }
        },
        [
          _c(
            "el-tab-pane",
            {
              attrs: {
                label: "个人认证",
                name: "personal",
                disabled: _vm.isPersonalDisabled
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
                    { attrs: { label: "真实姓名", prop: "name" } },
                    [
                      _c("el-input", {
                        attrs: { autocomplete: "off" },
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
                    { attrs: { label: "手机号", prop: "phone_number" } },
                    [
                      _c("el-input", {
                        attrs: { autocomplete: "off" },
                        model: {
                          value: _vm.form.phone_number,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "phone_number", _vm._n($$v))
                          },
                          expression: "form.phone_number"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    { attrs: { label: "开户银行卡号", prop: "bank_number" } },
                    [
                      _c("el-input", {
                        attrs: { autocomplete: "off" },
                        model: {
                          value: _vm.form.bank_number,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "bank_number", $$v)
                          },
                          expression: "form.bank_number"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    { attrs: { label: "开户银行名称", prop: "bank_name" } },
                    [
                      _c("el-input", {
                        attrs: { autocomplete: "off" },
                        model: {
                          value: _vm.form.bank_name,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "bank_name", $$v)
                          },
                          expression: "form.bank_name"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    { attrs: { label: "身份证号", prop: "identity_card" } },
                    [
                      _c("el-input", {
                        attrs: { autocomplete: "off" },
                        model: {
                          value: _vm.form.identity_card,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "identity_card", $$v)
                          },
                          expression: "form.identity_card"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    {
                      attrs: {
                        label: "身份证正面照",
                        prop: "front_card_picture"
                      }
                    },
                    [
                      _c(
                        "el-upload",
                        {
                          staticClass: "avatar-uploader",
                          attrs: {
                            action:
                              "/v2/account/authentication-upload?name=front_card_picture",
                            "show-file-list": false,
                            accept: "image/jpeg,image/jpg,image/png",
                            "on-success": _vm.handleAvatarSuccess,
                            "before-upload": _vm.beforeAvatarUpload
                          }
                        },
                        [
                          _vm.imageUrl1
                            ? _c("img", {
                                staticClass: "avatar",
                                attrs: { src: _vm.imageUrl1 }
                              })
                            : _c("i", {
                                staticClass: "el-icon-plus avatar-uploader-icon"
                              })
                        ]
                      ),
                      _vm._v(" "),
                      _c("el-input", {
                        attrs: { autocomplete: "off", type: "hidden" },
                        model: {
                          value: _vm.form.front_card_picture,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "front_card_picture", $$v)
                          },
                          expression: "form.front_card_picture"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    {
                      attrs: {
                        label: "身份证背面照",
                        prop: "back_card_picture"
                      }
                    },
                    [
                      _c(
                        "el-upload",
                        {
                          staticClass: "avatar-uploader",
                          attrs: {
                            action:
                              "/v2/account/authentication-upload?name=back_card_picture",
                            "show-file-list": false,
                            "on-success": _vm.handleAvatarSuccess,
                            "before-upload": _vm.beforeAvatarUpload
                          }
                        },
                        [
                          _vm.imageUrl2
                            ? _c("img", {
                                staticClass: "avatar",
                                attrs: { src: _vm.imageUrl2 }
                              })
                            : _c("i", {
                                staticClass: "el-icon-plus avatar-uploader-icon"
                              })
                        ]
                      ),
                      _vm._v(" "),
                      _c("el-input", {
                        attrs: { autocomplete: "off", type: "hidden" },
                        model: {
                          value: _vm.form.back_card_picture,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "back_card_picture", $$v)
                          },
                          expression: "form.back_card_picture"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    {
                      attrs: {
                        label: "手持身份证正面照",
                        prop: "hold_card_picture"
                      }
                    },
                    [
                      _c(
                        "el-upload",
                        {
                          staticClass: "avatar-uploader",
                          attrs: {
                            action:
                              "/v2/account/authentication-upload?name=hold_card_picture",
                            "show-file-list": false,
                            "on-success": _vm.handleAvatarSuccess,
                            "before-upload": _vm.beforeAvatarUpload
                          }
                        },
                        [
                          _vm.imageUrl3
                            ? _c("img", {
                                staticClass: "avatar",
                                attrs: { src: _vm.imageUrl3 }
                              })
                            : _c("i", {
                                staticClass: "el-icon-plus avatar-uploader-icon"
                              })
                        ]
                      ),
                      _vm._v(" "),
                      _c("el-input", {
                        attrs: { autocomplete: "off", type: "hidden" },
                        model: {
                          value: _vm.form.hold_card_picture,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "hold_card_picture", $$v)
                          },
                          expression: "form.hold_card_picture"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    [
                      this.isPersonalShowAdd
                        ? _c(
                            "el-button",
                            {
                              attrs: { type: "primary" },
                              on: {
                                click: function($event) {
                                  _vm.submitForm("form")
                                }
                              }
                            },
                            [_vm._v("确认添加")]
                          )
                        : _vm._e(),
                      _vm._v(" "),
                      this.isPersonalShowEdit
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
                        : _vm._e()
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
            "el-tab-pane",
            {
              attrs: {
                label: "企业认证",
                name: "company",
                disabled: _vm.isCompanyDisabled
              }
            },
            [
              _c(
                "el-form",
                {
                  ref: "companyForm",
                  attrs: {
                    model: _vm.companyForm,
                    rules: _vm.companyFormRules,
                    "label-width": "120px"
                  }
                },
                [
                  _c(
                    "el-form-item",
                    { attrs: { label: "真实姓名", prop: "name" } },
                    [
                      _c("el-input", {
                        attrs: { autocomplete: "off" },
                        model: {
                          value: _vm.companyForm.name,
                          callback: function($$v) {
                            _vm.$set(_vm.companyForm, "name", $$v)
                          },
                          expression: "companyForm.name"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    { attrs: { label: "手机号", prop: "phone_number" } },
                    [
                      _c("el-input", {
                        attrs: { autocomplete: "off" },
                        model: {
                          value: _vm.companyForm.phone_number,
                          callback: function($$v) {
                            _vm.$set(
                              _vm.companyForm,
                              "phone_number",
                              _vm._n($$v)
                            )
                          },
                          expression: "companyForm.phone_number"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    { attrs: { label: "开户银行卡号", prop: "bank_number" } },
                    [
                      _c("el-input", {
                        attrs: { autocomplete: "off" },
                        model: {
                          value: _vm.companyForm.bank_number,
                          callback: function($$v) {
                            _vm.$set(_vm.companyForm, "bank_number", $$v)
                          },
                          expression: "companyForm.bank_number"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    { attrs: { label: "开户银行名称", prop: "bank_name" } },
                    [
                      _c("el-input", {
                        attrs: { autocomplete: "off" },
                        model: {
                          value: _vm.companyForm.bank_name,
                          callback: function($$v) {
                            _vm.$set(_vm.companyForm, "bank_name", $$v)
                          },
                          expression: "companyForm.bank_name"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    { attrs: { label: "营业执照名称", prop: "license_name" } },
                    [
                      _c("el-input", {
                        attrs: { autocomplete: "off" },
                        model: {
                          value: _vm.companyForm.license_name,
                          callback: function($$v) {
                            _vm.$set(_vm.companyForm, "license_name", $$v)
                          },
                          expression: "companyForm.license_name"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    {
                      attrs: { label: "营业执照号码", prop: "license_number" }
                    },
                    [
                      _c("el-input", {
                        attrs: { autocomplete: "off" },
                        model: {
                          value: _vm.companyForm.license_number,
                          callback: function($$v) {
                            _vm.$set(_vm.companyForm, "license_number", $$v)
                          },
                          expression: "companyForm.license_number"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    { attrs: { label: "法人姓名", prop: "corporation" } },
                    [
                      _c("el-input", {
                        attrs: { autocomplete: "off" },
                        model: {
                          value: _vm.companyForm.corporation,
                          callback: function($$v) {
                            _vm.$set(_vm.companyForm, "corporation", $$v)
                          },
                          expression: "companyForm.corporation"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    {
                      attrs: {
                        label: "营业执照正面照",
                        prop: "license_picture"
                      }
                    },
                    [
                      _c(
                        "el-upload",
                        {
                          staticClass: "avatar-uploader",
                          attrs: {
                            action:
                              "/v2/account/authentication-upload?name=license_picture",
                            "show-file-list": false,
                            accept: "image/jpeg,image/jpg,image/png",
                            "on-success": _vm.handleAvatarSuccess,
                            "before-upload": _vm.beforeAvatarUpload
                          }
                        },
                        [
                          _vm.imageUrl4
                            ? _c("img", {
                                staticClass: "avatar",
                                attrs: { src: _vm.imageUrl4 }
                              })
                            : _c("i", {
                                staticClass: "el-icon-plus avatar-uploader-icon"
                              })
                        ]
                      ),
                      _vm._v(" "),
                      _c("el-input", {
                        attrs: { autocomplete: "off", type: "hidden" },
                        model: {
                          value: _vm.companyForm.license_picture,
                          callback: function($$v) {
                            _vm.$set(_vm.companyForm, "license_picture", $$v)
                          },
                          expression: "companyForm.license_picture"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    {
                      attrs: {
                        label: "银行开户许可证照片",
                        prop: "bank_open_account_picture"
                      }
                    },
                    [
                      _c(
                        "el-upload",
                        {
                          staticClass: "avatar-uploader",
                          attrs: {
                            action:
                              "/v2/account/authentication-upload?name=bank_open_account_picture",
                            "show-file-list": false,
                            "on-success": _vm.handleAvatarSuccess,
                            "before-upload": _vm.beforeAvatarUpload
                          }
                        },
                        [
                          _vm.imageUrl5
                            ? _c("img", {
                                staticClass: "avatar",
                                attrs: { src: _vm.imageUrl5 }
                              })
                            : _c("i", {
                                staticClass: "el-icon-plus avatar-uploader-icon"
                              })
                        ]
                      ),
                      _vm._v(" "),
                      _c("el-input", {
                        attrs: { autocomplete: "off", type: "hidden" },
                        model: {
                          value: _vm.companyForm.bank_open_account_picture,
                          callback: function($$v) {
                            _vm.$set(
                              _vm.companyForm,
                              "bank_open_account_picture",
                              $$v
                            )
                          },
                          expression: "companyForm.bank_open_account_picture"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    {
                      attrs: {
                        label: "代办协议照片",
                        prop: "agency_agreement_picture"
                      }
                    },
                    [
                      _c(
                        "el-upload",
                        {
                          staticClass: "avatar-uploader",
                          attrs: {
                            action:
                              "/v2/account/authentication-upload?name=agency_agreement_picture",
                            "show-file-list": false,
                            "on-success": _vm.handleAvatarSuccess,
                            "before-upload": _vm.beforeAvatarUpload
                          }
                        },
                        [
                          _vm.imageUrl6
                            ? _c("img", {
                                staticClass: "avatar",
                                attrs: { src: _vm.imageUrl6 }
                              })
                            : _c("i", {
                                staticClass: "el-icon-plus avatar-uploader-icon"
                              })
                        ]
                      ),
                      _vm._v(" "),
                      _c("el-input", {
                        attrs: { autocomplete: "off", type: "hidden" },
                        model: {
                          value: _vm.form.agency_agreement_picture,
                          callback: function($$v) {
                            _vm.$set(_vm.form, "agency_agreement_picture", $$v)
                          },
                          expression: "form.agency_agreement_picture"
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "el-form-item",
                    [
                      this.isCompanyShowAdd
                        ? _c(
                            "el-button",
                            {
                              attrs: { type: "primary" },
                              on: {
                                click: function($event) {
                                  _vm.submitForm("companyForm")
                                }
                              }
                            },
                            [_vm._v("确认添加")]
                          )
                        : _vm._e(),
                      _vm._v(" "),
                      this.isCompanyShowEdit
                        ? _c(
                            "el-button",
                            {
                              attrs: { type: "primary" },
                              on: {
                                click: function($event) {
                                  _vm.submitFormUpdate("companyForm")
                                }
                              }
                            },
                            [_vm._v("确认修改")]
                          )
                        : _vm._e()
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
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-8eb5f4d4", module.exports)
  }
}

/***/ }),

/***/ "./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-8eb5f4d4\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/account/Authentication.vue":
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__("./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-8eb5f4d4\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/account/Authentication.vue");
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__("./node_modules/vue-style-loader/lib/addStylesClient.js")("4fd04cec", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-8eb5f4d4\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Authentication.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-8eb5f4d4\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Authentication.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ "./resources/assets/frontend/js/components/account/Authentication.vue":
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__("./node_modules/vue-style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-8eb5f4d4\",\"scoped\":false,\"hasInlineConfig\":true}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./resources/assets/frontend/js/components/account/Authentication.vue")
}
var normalizeComponent = __webpack_require__("./node_modules/vue-loader/lib/component-normalizer.js")
/* script */
var __vue_script__ = __webpack_require__("./node_modules/babel-loader/lib/index.js?{\"cacheDirectory\":true,\"presets\":[[\"env\",{\"modules\":false,\"targets\":{\"browsers\":[\"> 2%\"],\"uglify\":true}}]],\"plugins\":[\"transform-object-rest-spread\",[\"transform-runtime\",{\"polyfill\":false,\"helpers\":false}],\"syntax-dynamic-import\"]}!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./resources/assets/frontend/js/components/account/Authentication.vue")
/* template */
var __vue_template__ = __webpack_require__("./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-8eb5f4d4\",\"hasScoped\":false,\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./resources/assets/frontend/js/components/account/Authentication.vue")
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
Component.options.__file = "resources/assets/frontend/js/components/account/Authentication.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-8eb5f4d4", Component.options)
  } else {
    hotAPI.reload("data-v-8eb5f4d4", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ })

});