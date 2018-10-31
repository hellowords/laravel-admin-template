<template>
  <div>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">添加用户</h3>
        </div>
        <form role="form" v-model="formUser">
          <div class="box-body">
            <div class="form-group" :class="{'has-error' : errors.has('account')}">
              <label for="Account">账户</label>
              <input type="text" v-validate="'required'" data-vv-as="账户" name="account"
                     class="form-control" v-model="formUser.account" placeholder="输入账户">
              <span class="help-block" v-show="errors.has('account')">{{ errors.first('account') }}</span>
            </div>
            <div class="form-group" :class="{'has-error' : errors.has('email')}">
              <label for="email">邮箱</label>
              <input type="email" v-validate="'required|email'" data-vv-as="邮箱" name="email"
                     class="form-control" v-model="formUser.email" placeholder="Email">
              <span class="help-block" v-show="errors.has('email')">{{ errors.first('email') }}</span>
            </div>
            <div class="form-group" :class="{'has-error' : errors.has('name')}">
              <label for="name">姓名</label>
              <input type="text" v-validate="'required'" data-vv-as="姓名" name="name"
                     class="form-control" v-model="formUser.name" placeholder="姓名">
              <span class="help-block" v-show="errors.has('name')">{{ errors.first('name') }}</span>
            </div>
          </div>
          <div class="box-footer">
            <button type="button" class="btn btn-primary btn-block" @click="handleAddUser()">添加</button>
          </div>
        </form>
      </div>

    </div>
    <div class="col-md-9">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">用户一览</h3>
          <div class="box-tools">
            <!--<div class="input-group input-group-sm" style="width: 150px;">-->
            <!--<input type="text" name="table_search" class="form-control pull-right" placeholder="Search">-->

            <!--<div class="input-group-btn">-->
            <!--<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>-->
            <!--</div>-->
            <!--</div>-->
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <Table :columns="userColumns" :data="users"></Table>
          <Page class="pull-right" :total="total" show-sizer show-total :current="pageParams.page"
                @on-change="changePage" @on-page-size-change="changePerPage" :page-size="pageParams.per_page"
                :page-size-opts="pageSizeOpts"></Page>
        </div>
        <!-- /.box-body -->
      </div>
    </div>
    <Modal v-model="modalRole" :title="roleTitle" @on-ok="roleOk" @on-cancel="handleCancel">
      <RadioGroup v-model="rolesCheck">
        <Radio v-for="(item,index) in roles" :label="item.label" :value="item.value" :key="index"></Radio>
      </RadioGroup>
    </Modal>
    <Modal v-model="modalPermission" :title="permissionTitle" @on-ok="permissionOK" @on-cancel="handleCancel">
      <CheckboxGroup v-model="permissionsCheck">
        <Checkbox v-for="(item,index) in permissions" :label="item.label" :value="item.value" :key="index"></Checkbox>
      </CheckboxGroup>
    </Modal>
  </div>
</template>

<script>
    import poptipPermission from './poptipPermission.vue';
    export default {
        components:{
            poptipPermission
        },
        props: {
            roles: Array,
            permissions: Array,
        },
        data() {
            return {
                rolesCheck: '',
                permissionsCheck: [],
                modalRole: false,
                modalPermission: false,
                roleTitle: '',
                permissionTitle: '',
                formUser: {
                    account: null,
                    email: null,
                    name: null,
                },
                userColumns: [
                    {
                        title: '用户名',
                        key: 'account',
                    },
                    {
                        title: '角色',
                        key: 'roles',
                        render: (h, params) => {
                            return params.row.roles.map(res => {
                                return h('Tag', {
                                    props: {
                                        color: 'success',
                                    }
                                }, res);
                            });
                        }
                    },
                    {
                        title: '权限',
                        tooltip: 'true',
                        key: 'roles',
                        render: (h, params) => {
                            return h(poptipPermission, {
                                props: {
                                    permissions: params.row.permissions,
                                }
                            });
                        }
                    },
                    {
                        title: '邮箱',
                        tooltip: 'true',
                        key: 'email',
                    },
                    {
                        title: '上次活动时间',
                        tooltip: 'true',
                        key: 'last_active_at',
                    },
                    {
                        title: '操作',
                        key: '#',
                        render: (h, params) => {
                            let is_disable = params.row.id == 1 ? true : false;
                            return h('div', [
                                h('Poptip', {
                                    props: {
                                        title: '确认执行该操作？',
                                        confirm: true,
                                        transfer: true,
                                    },
                                    on: {
                                        'on-ok': () => {
                                            this.deleteUser(params.row.id)
                                        }
                                    }
                                }, [
                                    h('Button', {
                                        props: {
                                            type: 'warning',
                                            size: 'small',
                                            disabled: is_disable
                                        },
                                        style: {
                                            marginRight: '1px'
                                        }
                                    }, '删除')
                                ]),
                                h('Button', {
                                    props: {
                                        type: 'success',
                                        size: 'small'
                                    },
                                    style: {
                                        marginRight: '1px'
                                    },
                                    on: {
                                        click: () => {
                                            this.handleClickRole(params.row);
                                        }
                                    }
                                }, '角色'),
                                h('Button', {
                                    props: {
                                        type: 'primary',
                                        size: 'small'
                                    },
                                    on: {
                                        click: () => {
                                            this.handleClickPermission(params.row);
                                        }
                                    }
                                }, '权限'),
                            ]);
                        }
                    },
                ],
                users: [],
                total: 0,
                pageParams: {
                    page: 1,
                    per_page: 10,
                },
                pageSizeOpts: [
                    10,
                    20,
                    50
                ],
                checkUserId: 0,
            };
        },
        mounted() {
            this.getUsers();
        },
        methods: {
            roleOk() {
                let that = this;
                that.$api.postSyncRole(that.checkUserId, {'data': that.rolesCheck}).then(res => {
                    if (res.code == 200) {
                        that.$Message.success(res.data);
                        that.checkUserId = 0;
                        that.rolesCheck = '';
                        that.getUsers();
                    } else {
                        that.$Message.error('未知错误！');
                    }
                })
            },
            permissionOK() {
                let that = this;
                that.$api.postSyncPermission(that.checkUserId, {'data': that.permissionsCheck}).then(res => {
                    if (res.code == 200) {
                        that.$Message.success(res.data);
                        that.permissionsCheck = [];
                        that.checkUserId = 0;
                        that.getUsers();
                    } else {
                        that.$Message.error('未知错误！');
                    }
                })
            },
            handleClickPermission(val) {
                this.checkUserId = val.id;
                this.permissionTitle = val.account + ' 权限';
                this.permissionsCheck = val.permissions;
                this.modalPermission = true;
            },
            handleClickRole(val) {
                this.checkUserId = val.id;
                this.roleTitle = val.account + ' 角色';
                this.rolesCheck = val.roles[0];
                this.modalRole = true;
            },
            getUsers() {
                let that = this;
                this.$api.getUsers(that.pageParams).then(res => {
                    if (res.code == 200) {
                        that.users = res.data.data;
                        that.total = res.data.total;
                    } else {
                        that.$Message.error('未知错误！');
                    }
                });
            },
            changePage(page) {
                this.pageParams.page = page;
                this.getUsers();
            },
            changePerPage(val) {
                this.pageParams.per_page = val;
                this.getUsers();
            },
            handleAddUser() {
                let that = this;
                that.$validator.validateAll().then((result) => {
                    if (result) {
                        that.postUser(that.formUser);
                    } else {
                        this.$Message.error('数据有误！请重新检查!');
                    }
                });
            },
            postUser(params) {
                let that = this;
                that.$api.postUser(params).then(res => {
                    if (res.code == 200) {
                        that.$Message.success(res.data);
                        that.handleResetForm();
                        that.getUsers();
                    }
                })
            },
            deleteUser(val) {
                let that = this;
                that.$api.delUser(val).then(res => {
                    if (res.code == 200) {
                        that.$Message.success(res.data);
                        that.getUsers();
                    }
                })
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
                this.formUser.email = null;
                this.formUser.account = null;
                this.formUser.name = null;
            },
            handleCancel() {

            }
        }
    }
</script>
