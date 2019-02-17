# BackRunner's ShortLink

## 概述

简单的、开箱即用的短网址生成系统，用于对长网址进行压缩，方便用在微博、博客、笔记等地方。

支持自定义短链接，支持定时销毁。

## 运行环境

php 7.2(with bcmath module), MySQL 5.7, Apache 2.4.x

打包本项目需要：

NodeJS

bower, gulp, gulp-less, gulp-clean-css, gulp-rename, gulp-uglify, gulp-htmlmin

```bash
npm install -g bower
npm install -g gulp
npm install gulp-less gulp-clean-css gulp-rename gulp-uglift gulp-htmlmin --save-dev
```

## 使用方式

使用Git拉取本项目或直接下载源码，之后在项目根目录执行以下命令补齐依赖：

```bash
bower install
npm install
```

在项目根目录执行下列命令：

```bash
gulp clean-build
```

命令执行完成后，将/public内的所有文件上传到服务器上即可，访问后会自动要求安装。

**请确保站点根目录是可写的、有权限写的，否则安装会出错。**

如果提示安装成功，但是程序仍然跳转到安装页面，请按以下步骤操作：

1、在站点根目录新建一个名为**install.lock**的空文件。

2、复制站点根目录下的config.default.php，更名为config.php。

3、修改config.php内的相关配置。

如果你不想自己手动打包，你也可以选择直接使用已经打包好的发行版本（>=1.0），但这种做法是不推荐的。

## 开发计划

- [x] 安装时新增用户表、管理员用户设置，普通短链接表添加一个hash用于建立索引
- [x] 本地的短链接生成服务
- [ ] php、Apache模块检测
- [ ] 短链接的HTML代码显示
- [x] 添加过期时间（DateTimePicker）
- [x] 短链接跳转
- [ ] 管理面板的基础（标签页、登录登出）
- [ ] 管理员管理
- [ ] 链接管理
- [x] 统计
- [ ] 统计查询
- [ ] Api指南
- [ ] 安装页的表单检查
- [x] 换一个更快的url检查正则
