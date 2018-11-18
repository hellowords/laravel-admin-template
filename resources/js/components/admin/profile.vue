<template>
  <div>
    <div class="col-md-3">
      <div class="box box-danger">
        <div class="box-header with-border">
          <h3 class="box-title">重置密码</h3>
        </div>
        <form role="form" v-model="formPassword" data-vv-scope="form-password">
          <div class="box-body">
            <div class="form-group" :class="{'has-error' : errors.has('form-password.old_pass')}">
              <label for="old_pass">原密码</label>
              <input type="password" v-validate="'required|min:6|old_pass'" data-vv-as="原密码" name="old_pass"
                     class="form-control" v-model="formPassword.old_pass" data-vv-validate-on="blur">
              <span class="help-block"
                    v-show="errors.has('form-password.old_pass')">{{ errors.first('form-password.old_pass') }}</span>
            </div>
            <div class="form-group" :class="{'has-error' : errors.has('form-password.password')}">
              <label for="password">新密码</label>
              <input type="password" v-validate="'required|min:6'" ref="pw_confirm" data-vv-as="新密码" name="password"
                     class="form-control" v-model="formPassword.password">
              <span class="help-block"
                    v-show="errors.has('form-password.password')">{{ errors.first('form-password.password') }}</span>
            </div>
            <div class="form-group" :class="{'has-error' : errors.has('form-password.password_confirmation')}">
              <label for="password_confirmation">确认密码</label>
              <input type="password" v-validate="'required|min:6|confirmed:pw_confirm'" data-vv-as="确认密码"
                     name="password_confirmation"
                     class="form-control" v-model="formPassword.password_confirmation">
              <span class="help-block"
                    v-show="errors.has('form-password.password_confirmation')">{{ errors.first('form-password.password_confirmation')
                }}</span>
            </div>
          </div>
          <div class="box-footer">
            <button type="button" class="btn btn-warning btn-block" @click="handleRestPassword()">重置</button>
          </div>
        </form>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">个人信息</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <form class="form-horizontal" v-model="formAccount" data-vv-scope="form-account">
          <div class="box-body">
            <div class="form-group" :class="{'has-error' : errors.has('form-account.account')}">
              <label for="account" class="col-sm-2 control-label">账户</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" v-model="formAccount.account" v-validate="'required'"
                       data-vv-as="账户" name="account" readonly>
                <span class="help-block"
                      v-show="errors.has('form-account.account')">{{ errors.first('form-account.account')
                  }}</span>
              </div>
            </div>
            <div class="form-group" :class="{'has-error' : errors.has('form-account.email')}">
              <label for="email" class="col-sm-2 control-label">E-mail</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" v-validate="'required'" data-vv-as="E-mail" name="email"
                       v-model="formAccount.email" readonly>
                <span class="help-block"
                      v-show="errors.has('form-account.email')">{{ errors.first('form-account.email')
                  }}</span></div>
            </div>
            <div class="form-group" :class="{'has-error' : errors.has('form-account.name')}">
              <label for="name" class="col-sm-2 control-label">姓名</label>

              <div class="col-sm-10">
                <input type="text" class="form-control" v-validate="'required'" data-vv-as="姓名" name="name"
                       v-model="formAccount.name">
                <span class="help-block"
                      v-show="errors.has('form-account.name')">{{ errors.first('form-account.name')
                  }}</span></div>
            </div>
            <div class="form-group" :class="{'has-error' : errors.has('form-account.phone')}">
              <label for="phone" class="col-sm-2 control-label">手机</label>

              <div class="col-sm-6">
                <input type="text" class="form-control" v-validate="'required'" data-vv-as="手机" name="phone"
                       v-model="formAccount.phone" readonly>
                <span class="help-block"
                      v-show="errors.has('form-account.phone')">{{ errors.first('form-account.phone')
                  }}</span>
              </div>
              <div class="col-sm-4">
                <Button @click="phoneModal = true">更换手机</Button>
                <Modal v-model="phoneModal"
                       title="更换手机"
                       :loading="loading"
                       @on-ok="handleModifyPhone"
                       @on-cancel="handleResetForm">
                  <form class="form-horizontal" v-model="formPhone" data-vv-scope="form-phone">
                    <div class="form-group" :class="{'has-error' : errors.has('form-phone.phone')}">
                      <label for="phone" class="col-sm-2 control-label">手机</label>
                      <div class="col-sm-6">
                        <input type="text" class="form-control" v-validate data-vv-rules="required|phone|unique_phone"
                               data-vv-as="手机" name="phone"
                               v-model="formPhone.phone">
                        <span class="help-block"
                              v-show="errors.has('form-phone.phone')">{{ errors.first('form-phone.phone')
                  }}</span>
                      </div>
                    </div>
                    <div class="form-group" :class="{'has-error' : errors.has('form-phone.code')}">
                      <label for="code" class="col-sm-2 control-label">验证码</label>
                      <div class="col-sm-6">
                        <input type="text" class="form-control" v-validate="'required'" data-vv-as="验证码" name="code"
                               v-model="formPhone.code">
                        <span class="help-block"
                              v-show="errors.has('form-phone.code')">{{ errors.first('form-phone.code')
                  }}</span>
                      </div>
                      <div class="col-sm-4">
                        <Button @click="handleClickCodeButton" :disabled="disabled">
                          <span v-if="!sendMsgDisabled && !reGet">发送验证码</span>
                          <span v-if="!sendMsgDisabled && reGet">重新获取</span>
                          <span v-if="sendMsgDisabled">{{rTime+'秒后重新获取'}}</span>
                        </Button>
                      </div>
                    </div>
                  </form>
                </Modal>
              </div>
            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <button type="button" class="btn btn-info pull-right" @click="handleUpdate()">更新</button>
          </div>
          <!-- /.box-footer -->
        </form>
      </div>
    </div>
    <div class="col-md-3">
    </div>
  </div>
</template>

<script>
    export default {
        props: {
            user: Object
        },
        data() {
            return {
                disabled: true,
                reGet: false, // 重新获取
                rTime: 60, // 发送验证码倒计时
                sendMsgDisabled: false, // 发送验证码按钮状态
                phoneModal: false,
                loading: true,
                formPassword: {
                    old_pass: null,
                    password: null,
                    password_confirmation: null,
                },
                formPhone: {
                    phone: null,
                    code: null
                },
                formAccount: {
                    account: this.user.account,
                    email: this.user.email,
                    name: this.user.name,
                    phone: this.user.phone,
                }
            };
        },
        created() {
            this.$validator.extend('old_pass', {
                getMessage: field => field + '不正确!',
                validate: (value, args) => {
                    console.log(this.checkOldPassword(value));
                    return this.checkOldPassword(value);
                }
            }, {
                immediate: false
            });
            this.$validator.extend('unique_phone', {
                getMessage: field => field + '已存在!',
                validate: (value, args) => {
                    let that = this;
                    that.checkUinquePhone(value).then(res => {
                        that.disabled = !res
                    });
                    return this.checkUinquePhone(value);
                }
            }, {
                immediate: false
            });

            this.$validator.extend('phone', {
                getMessage: field => field + '格式不对!',
                validate: (value, args) => {
                    let reg = /^((1[3-8][0-9])+\d{8})$/;
                    this.disabled = !reg.test(value);
                    return reg.test(value);
                }
            }, {
                immediate: false
            });

        },
        methods: {
            checkUinquePhone(val) {
                return this.$api.checkUniquePhone({'phone': val}).then(res => {
                    return res.code == 200 ? false : true;
                });
            },
            handleClickCodeButton() {
                let that = this;
                that.sendMsgDisabled = true;
                let rTime = that.rTime;
                that.disabled = true;
                let interval = window.setInterval(() => {
                    if (--that.rTime <= 0) {
                        that.rTime = rTime;
                        that.sendMsgDisabled = false;
                        that.reGet = true;
                        that.disabled = false;
                        window.clearInterval(interval);
                    }
                }, 1000);
                that.postPhoneToGetCode();
            },
            postPhoneToGetCode() {
                let that = this;
                this.$api.getCode({'phone': this.formPhone.phone}).then(res => {
                    if (res.code == 200) {
                        that.$Message.success(res.data);
                    }
                })
            },
            handleModifyPhone() {
                let that = this;
                that.$validator.validateAll('form-phone').then((result) => {
                    if (result) {
                        that.loading = true;
                        that.updatePhone();
                    } else {
                        this.$Message.error('数据有误！请重新检查!');
                        this.loading = false;
                    }
                });
            },
            updatePhone() {
                let that = this;
                that.$api.checkCode(this.formPhone).then(res => {
                    if (res.code === 200) {
                        that.loading = false;
                        that.$Message.success(res.data);
                        that.phoneModal = false;
                        that.getUserInfo();
                    } else {
                        that.$Message.error(res.data);
                    }
                });
            },
            getUserInfo() {
                let that = this;
                that.$api.getUserInfo().then(res => {
                    if (res.code === 200) {
                        that.formAccount = {
                            account: res.data.account,
                            email: res.data.email,
                            name: res.data.name,
                            phone: res.data.phone,
                        };
                        that.handleResetForm();
                    }
                })
            },
            checkOldPassword(val) {
                let form = {
                    old_pass: val
                };
                return this.$api.checkOldPass(form).then(res => {
                    return res.code == 200 ? true : false;
                });
            },
            handleRestPassword() {
                let that = this;
                that.$validator.validateAll('form-password').then((result) => {
                    if (result) {
                        that.updatePass();
                    } else {
                        this.$Message.error('数据有误！请重新检查!');
                    }
                });
            },
            updatePass() {
                let that = this;
                that.$api.updatePassword(that.formPassword).then(res => {
                    if (res.code == 200) {
                        that.$Message.success('更新成功');
                        that.handleResetForm();
                    }
                })
            },
            handleUpdate() {
                let that = this;
                that.$validator.validateAll('form-account').then(res => {
                    if (res) {
                        that.updateUser();
                    } else {
                        this.$Message.error('数据有误！请重新检查!');
                    }
                })
            },
            updateUser() {
                let that = this;
                that.$api.updateUser(that.formAccount).then(res => {
                    if (res.code == 200) {
                        that.$Message.success('更新成功！');
                        that.handleResetForm();
                    } else {
                        that.$Message.error('错误！');
                    }
                });
            },
            handleResetForm() {
                let that = this;
                new Promise(function (resolve, reject) {
                    resolve('Reset Form!');
                    that.clear()
                }).then(() => {
                    that.$validator.reset()
                });
            },
            clear() {
                this.formPhone = {
                    phone:null,
                    code: null
                };
                this.formPassword.old_pass = null;
                this.formPassword.password = null;
                this.formPassword.password_confirmation = null;
            }
        }
    };
</script>
