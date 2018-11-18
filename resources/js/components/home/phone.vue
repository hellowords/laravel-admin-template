<template>
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title">添加手机</h3>
    </div>
    <div class="box-body">
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
        <div class="box-footer">
          <button type="button" class="btn btn-info pull-right" :loading="loading" @click="handleButton">添加</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
    export default {
        data() {
            return {
                loading: false,
                disabled: true,
                reGet: false, // 重新获取
                rTime: 60, // 发送验证码倒计时
                sendMsgDisabled: false, // 发送验证码按钮状态
                formPhone: {
                    phone: null,
                    code: null
                }
            };
        },
        created() {
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
            handleButton() {
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
                        window.location.href = '/admin/user/profile';
                    }else{
                        that.$Message.error(res.data);
                    }
                });
            },
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
            }
        }
    };
</script>