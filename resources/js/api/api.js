import req from "./request";

let bashUrl = 'admin/';

export default {
    //获取用户分页数据
    getUsers(params) {
        return req.get(bashUrl + 'users', params);
    },
    //添加用户
    postUser(params) {
        return req.post(bashUrl + 'user', params);
    },
    //删除用户
    delUser(params) {
        return req.del(bashUrl + 'user/' + params);
    },
    //验证原密码
    checkOldPass(params) {
        return req.post(bashUrl + 'user/check_pass', params);
    },
    updatePassword(params) {
        return req.post(bashUrl + 'user/update_pass', params);
    },
    //更新用户信息
    updateUser(params) {
        return req.put(bashUrl + 'user', params);
    },
    //添加角色
    addRole(params) {
        return req.post(bashUrl + 'role', params);
    },
    //添加权限
    addPermission(params) {
        return req.post(bashUrl + 'permission', params);
    },
    //获取所有角色
    getRoles(params) {
        return req.get(bashUrl + 'roles', params);
    },
    //获取所有权限
    getPermissions(params) {
        return req.get(bashUrl + 'permissions', params);
    },
    //更新用户角色
    postSyncRole(id, params) {
        return req.post(bashUrl + 'sync_role/' + id, params);
    },
    //更新用户权限
    postSyncPermission(id, params) {
        return req.post(bashUrl + 'sync_permission/' + id, params)
    },
    //同步角色的权限
    putSyncPermissionByRole(id, params) {
        return req.put(bashUrl + 'sync_permission_by_role/' + id, params);
    }
}