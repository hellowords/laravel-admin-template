<template>
  <div>
    <div class="col-md-3">
      <div class="box box-success box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">添加角色</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <form role="form" v-model="formRole" data-vv-scope="form-role">
            <div class="form-group" :class="{'has-error' : errors.has('form-role.name')}">
              <label for="role_name">角色</label>
              <input type="text" v-validate="'required|min:2'" data-vv-as="角色名" name="name"
                     class="form-control" v-model="formRole.name">
              <span class="help-block"
                    v-show="errors.has('form-role.name')">{{ errors.first('form-role.name') }}</span>
            </div>
          </form>
        </div>
        <div class="box-footer">
          <button type="button" class="btn btn-success btn-block" @click="handleAddRole()">添加角色</button>
        </div>
      </div>
      <div class="box box-primary box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">添加权限</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <form role="form" v-model="formPermission" data-vv-scope="form-permission">
            <div class="form-group" :class="{'has-error' : errors.has('form-permission.name')}">
              <label for="per_name">权限</label>
              <input type="text" v-validate="'required|min:2'" data-vv-as="权限名" name="name"
                     class="form-control" v-model="formPermission.name">
              <span class="help-block"
                    v-show="errors.has('form-permission.name')">{{ errors.first('form-permission.name') }}</span>
            </div>
          </form>
        </div>
        <div class="box-footer">
          <button type="button" class="btn btn-primary btn-block" @click="handleAddPermission()">添加权限</button>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">角色</h3>
          </div>
          <div class="box-body">
            <Table :columns="roleColumns" :data="roleData"></Table>
          </div>
          <div class="box-footer">
            <Page></Page>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">权限</h3>
          </div>
          <div class="box-body">
            <Table :columns="permissionColumns" :data="permissionData"></Table>
          </div>
          <div class="box-footer">
            <Page></Page>
          </div>
        </div>
      </div>
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
                formPermission: {
                    name: null,
                },
                formRole:{
                    name: null
                },
                roleColumns:[
                    {
                        title: '角色',
                        key: 'role_name'
                    },
                    {
                        title: '人数',
                        key: 'counts'
                    },
                    {
                        title: '创建时间',
                        key: 'created_at'
                    }
                ],
                permissionColumns:[
                    {
                        title: '权限',
                        key: 'role_name'
                    },
                    {
                        title: '人数',
                        key: 'counts'
                    },
                    {
                        title: '创建时间',
                        key: 'created_at'
                    }
                ],
                permissionData:[],
                roleData:[]
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
            handleAddPermission(){},
            handleAddRole(){},
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
