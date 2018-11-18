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
            <Table :columns="roleColumns" :data="roleData" border :loading="loadingRole"></Table>
          </div>
          <div class="box-footer">
            <Page class="pull-right" :total="rTotal" size="small" show-total :current="rCurrentPage"
                  @on-change="changeRolePage"></Page>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">权限</h3>
          </div>
          <div class="box-body">
            <Table :columns="permissionColumns" :data="permissionData" border :loading="loadingPermission"></Table>
          </div>
          <div class="box-footer">
            <!--Bug 角色Modal弹层只能渲染一页权限数据-->
            <!--<Page class="pull-right" :total="pTotal" size="small" show-total :current="pCurrentPage"-->
            <!--@on-change="changePermissionPage"></Page>-->
          </div>
        </div>
      </div>
    </div>
    <Modal v-model="modalPermission" title="权限" @on-ok="permissionOK" @on-cancel="handleCancel">
      <CheckboxGroup v-model="permissionsCheck">
        <Checkbox v-for="(item,index) in permissionData" :label="item.name" :value="item.id" :key="index"></Checkbox>
      </CheckboxGroup>
    </Modal>
  </div>
</template>

<script>
    import poptipPermission from './poptipPermission.vue';

    export default {
        components: {
            poptipPermission
        },
        props: {},
        data() {
            return {
                modalPermission: false,
                permissionsCheck: [],
                loadingPermission: false,
                loadingRole: false,
                formPermission: {
                    name: null,
                },
                formRole: {
                    name: null
                },
                roleColumns: [
                    {
                        title: '角色',
                        key: 'name'
                    },
                    {
                        title: '人数',
                        key: 'counts',
                        render: (h, params) => {
                            let counts = params.row.users_count;
                            return h('span', {}, counts);
                        }
                    },
                    {
                        title: '权限',
                        tooltip: 'true',
                        key: 'roles',
                        render: (h, params) => {
                            let permissions = params.row.permissions.map(res => {
                                return res['name'];
                            });
                            return h(poptipPermission, {
                                props: {
                                    permissions: permissions,
                                }
                            });
                        }
                    },
                    {
                        title: '#',
                        key: 'created_at',
                        render: (h, params) => {
                            return h('Button', {
                                props: {
                                    size: 'small',
                                    type: 'primary'
                                },
                                on: {
                                    click: () => {
                                        this.handleSyncPermissionByRole(params.row);
                                    }
                                }
                            }, '添加权限')
                        }
                    }
                ],
                permissionColumns: [
                    {
                        title: '权限',
                        key: 'name'
                    },
                    {
                        title: '人数',
                        render: (h, params) => {
                            let counts = params.row.users_count;
                            return h('span', {}, counts);
                        }
                    },
                    {
                        title: '创建时间',
                        key: 'created_at'
                    }
                ],
                permissionData: [],
                roleData: [],
                // pTotal: 0,
                // pCurrentPage: 1,
                rTotal: 0,
                roleID: 0,
                rCurrentPage: 1
            };
        },
        mounted() {
            this.getRoles();
            this.getPermissions();
        },
        methods: {
            getRoles() {
                let that = this;
                that.loadingRole = true;
                that.$api.getRoles({page: that.rCurrentPage}).then(res => {
                    if (res.code == 200) {
                        that.loadingRole = false;
                        that.rTotal = res.data.total;
                        that.rCurrentPage = res.data.current_page;
                        that.roleData = res.data.data;
                    }
                });
            },
            getPermissions() {
                let that = this;
                that.loadingPermission = true;
                that.$api.getPermissions({page: that.pCurrentPage}).then(res => {
                    if (res.code == 200) {
                        that.loadingPermission = false;
                        // that.pCurrentPage = res.data.current_page;
                        // that.pTotal = res.data.total;
                        that.permissionData = res.data;
                    }
                });
            },
            handleSyncPermissionByRole(val) {
                this.permissionsCheck = val.permissions.map(res=>{
                    return res.name;
                });
                this.roleID = val.id;
                this.modalPermission = true;
            },
            handleAddPermission() {
                let that = this;
                that.$validator.validateAll('form-permission').then(res => {
                    if (res) {
                        that.addPermission();
                    } else {
                        this.$Message.error('数据有误！请重新检查!');
                    }
                })
            },
            handleAddRole() {
                let that = this;
                that.$validator.validateAll('form-role').then(res => {
                    if (res) {
                        that.addRole();
                    } else {
                        this.$Message.error('数据有误！请重新检查!');
                    }
                })
            },
            changeRolePage(page) {
                this.rCurrentPage = page;
                this.getRoles();
            },
            // changePermissionPage(page) {
            //     this.pCurrentPage = page;
            //     this.getPermissions();
            // },
            addRole() {
                let that = this;
                that.$api.addRole(that.formRole).then(res => {
                    if (res.code == 200) {
                        that.$Message.success('添加成功！');
                        that.getRoles();
                        that.handleResetForm();
                    }
                });
            },
            addPermission() {
                let that = this;
                that.$api.addPermission(that.formPermission).then(res => {
                    if (res.code == 200) {
                        that.$Message.success('添加成功！');
                        that.getPermissions();
                        that.handleResetForm();
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
            permissionOK() {
                let that = this;
                this.$api.putSyncPermissionByRole(this.roleID,{'permissions': this.permissionsCheck}).then(res => {
                    if (res.code === 200) {
                        that.getRoles();
                        that.handleCancel();
                        that.$Message.success(res.data);
                    }
                });
            },
            handleCancel() {
                this.roleID = 0;
                this.permissionsCheck = [];
            },
            clear() {
                this.formPermission.name = null;
                this.formRole.name = null;
            }
        }
    };
</script>
