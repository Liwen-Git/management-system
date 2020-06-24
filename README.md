# management-system

### 部署

#### 安装配置环境
* php>=7.2.5、php-fpm
```php
# php.ini配置
upload_max_filesize = 64M
post_max_size = 64M
memory_limit = 256M
```
* php扩展：redis、xlswriter 
```php
# php 扩展
pecl install redis
pecl install xlswriter
```
* nginx
```php
# nginx 分配给请求数据的buffer大小
client_body_buffer_size 5m;
# nginx中 客户端请求服务器最大允许大小
client_max_body_size 20m;
```
* mysql
* redis
* composer 以及相关镜像
* node、npm 以及相关镜像

#### 后台

##### 复制.envexample为.env
```php
cp .envexample .env
```

##### 生成Application key
```php
php artisan key:generate
```

##### 后台依赖安装
```php
composer install
```

##### JWT Generate secret key
```php
php artisan jwt:secret
```

##### storage软链接， 并注意`storage`目录权限问题
```php
php artisan storage:link
```

##### 配置.env中的相关配置，如数据库、redis、session等


#### 前台

##### 前台依赖安装
`npm run build` 后再提交，只需要nginx路径指到`dist/index.html`下就可以了，就不需要执行如下命令。
```php
npm install
```

