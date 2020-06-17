# management-system

### 部署

#### 安装配置环境
* php、php-fpm
* nginx
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

##### 配置.env中的相关配置，如数据库、redis、session等


#### 前台

##### 前台依赖安装
`npm run build` 后再提交，只需要nginx路径指到`public/index.html`下就可以了，就不需要执行如下命令。
```php
npm install
```

