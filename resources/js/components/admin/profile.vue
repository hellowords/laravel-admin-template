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
                <input type="text" class="form-control" v-model="formAccount.account" v-validate="'required'" data-vv-as="账户" name="account" readonly>
                <span class="help-block"
                      v-show="errors.has('form-account.account')">{{ errors.first('form-account.account')
                  }}</span>
              </div>
            </div>
            <div class="form-group" :class="{'has-error' : errors.has('form-account.email')}">
              <label for="email" class="col-sm-2 control-label">E-mail</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" v-validate="'required'" data-vv-as="E-mail" name="email" v-model="formAccount.email" readonly>
                <span class="help-block"
                      v-show="errors.has('form-account.email')">{{ errors.first('form-account.email')
                  }}</span></div>
            </div>
            <div class="form-group" :class="{'has-error' : errors.has('form-account.name')}">
              <label for="name" class="col-sm-2 control-label">姓名</label>

              <div class="col-sm-10">
                <input type="text" class="form-control" v-validate="'required'" data-vv-as="姓名" name="name" v-model="formAccount.name">
                <span class="help-block"
                      v-show="errors.has('form-account.name')">{{ errors.first('form-account.name')
                  }}</span></div>
            </div>
            <div class="form-group" :class="{'has-error' : errors.has('form-account.phone')}">
              <label for="phone" class="col-sm-2 control-label">手机</label>

              <div class="col-sm-10">
                <input type="text" class="form-control" v-validate="'required'" data-vv-as="手机" name="phone"
                       v-model="formAccount.phone">
                <span class="help-block"
                      v-show="errors.has('form-account.phone')">{{ errors.first('form-account.phone')
                  }}</span></div>
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
        props:{
          user: Object
        },
        data() {
            return {
                formPassword: {
                    old_pass: null,
                    password: null,
                    password_confirmation: null,
                },
                formAccount: {
                    account: this.user.account,
                    email: this.user.email,
                    name: this.user.name,
                    phone: this.user.phone
                }
            };
        },
        created() {
            this.$validator.extend('old_pass', {
                getMessage: field => field + '不正确!',
                validate: (value, args) => {
                    return this.checkOldPassword(value);
                }
            }, {
                immediate: false
            });
        },
        mounted() {
        },
        methods: {
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
                    resolve('Reset Form!')
                    that.clear()
                }).then(() => {
                    that.$validator.reset()
                });
            },
            clear() {
                this.formPassword.old_pass = null;
                this.formPassword.password = null;
                this.formPassword.password_confirmation = null;
            }
        }
    };
</script>
