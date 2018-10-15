# Laravel 5.7 后台模板

使用`AdminLTE`后台模板，基础功能使用 `Vue`开发，组件使用`iview`

## 测试地址： [https://demo.happyhack.cn](https://demo.happyhack.cn)

## 功能

- 用户注册、登录
- 邮箱验证
- 后台用户基础管理
- 角色权限管理
- 个人中心

后台预览

![image1](https://cdn.happyhack.cn/github/images/1.png)
![image2](https://cdn.happyhack.cn/github/images/2.png)
![image3](https://cdn.happyhack.cn/github/images/3.png)
![image4](https://cdn.happyhack.cn/github/images/4.png)

添加了一些配置 邮件需要自己配置、具体查看`.env.example`，新增配置文件在`config/website.php`.

## 安装步骤
> composer中添加了常用的包: mongodb、redis 如果不需要则删除这些包

```bash
 git clone https://github.com/hellowords/laravel-admin-template.git
 
 cd laravel-admin-template
 
 composer install

 cp .env.example .env 

 # .env中配置邮件、数据库等

 php artisan key:gen
 
 php artisan migrate
 
 npm install
 
 # 如果修改过后台模板需要运行此命令
 #npm run dev 
 
```
## Todo

- 媒体管理
