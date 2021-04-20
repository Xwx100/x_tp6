## 技术栈总览
[有道云可视化链接](http://note.youdao.com/noteshare?id=98c5845f68cd8622346d275a2b93a221&sub=853987E023F8403EBE14600F3157CFA6)

### docker容器化
1. 操作系统 `centos-8.*`
2. 网络代理 `nginx-1.19.*`
3. 磁盘数据库 `mysql-8.*`
4. 编程语言 `php-7.4`
5. 内存数据库 `redis-5.*`
6. 代码部署管理 `jenkins`
7. 代码版本管理 `github`
8. 消息队列 `rabbit-mq`
9. 微服务 `think-swoole 3.0`
10. 前端 `vue3.*`

### 项目
|en|zh|desc|
|---|---|---|
|dnmp|docker-compose 容器管理||
|x_cms|后端代码||
|x_cms_front|前端代码|jenkins构建后自动部署到后端public目录|
|x_cms_service|微服务代码||


#### 生命周期

```
sequenceDiagram

participant user as 用户
participant browser as 浏览器
participant server_nginx as 服务端-nginx
participant server_x_cms_front as 服务端-x_cms_front
participant server_fpm as 服务端-fpm
participant server_x_cms as 服务端-x_cms
participant server_redis as 服务端-redis
participant server_mysql as 服务端-mysql
participant server_cms_service as 服务端-x_cms_service（后端解耦）


user->>browser: 
browser->>server_nginx: 发起请求
server_nginx->>server_x_cms_front: 转发请求
server_x_cms_front->>server_nginx: index.html
server_nginx->>browser: 前端首页
browser->>user: 展示内容
user->>browser: 根据展示内容 点击
browser->>server_x_cms_front: 调用js发起axios请求
server_x_cms_front->>server_x_cms: 获取数据
server_x_cms->>server_redis: 获取内存数据
server_redis->>server_mysql: 未命中 走磁盘数据
server_mysql->>server_x_cms: 给予 磁盘数据
server_x_cms->>server_cms_service: 获取数据
server_x_cms->>server_x_cms_front: 给予 后端数据（json请求格式）
server_x_cms_front->>server_x_cms_front: 根据 后端数据 渲染成html
server_x_cms_front->>browser: 展示
browser->>user: 展示
```


### 代码规范

#### 若是改原框架内容
请使用 容器提供provider

#### 若要在原框架内容基础上增加
请使用 服务注册service

并挂载到 专属对象xuApp

### 容器 注册对象 xuApp

#### 日志服务（兼容http和微服务）

#### 前端参数转模型参数服务

#### 布隆过滤器

#### 并发锁

#### 逻辑层

#### 验证层


## 安装http

> [文档](https://guzzle-cn.readthedocs.io/zh_CN/latest/overview.html)

```bash
composer config -g --unset repos.packagist
composer require guzzlehttp/guzzle
```