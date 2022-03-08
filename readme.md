<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>


## 搭建

基于基础框架的搭建

**1、下载laravel**

**2、执行composer require kgophp/basic**
    

**3、执行数据迁移，数据填充**

1）、配置.env数据库

2）、auth.php 中增加

        guard:

        'admin' => [
            'driver' => 'passport',
            'provider' => 'admin'
        ],

        providers:
        
        'admin' => [
            'driver' => 'eloquent',
            'model' => YK\Basic\Models\AdminUser::class,
        ],

3）、迁移
    
    php artisan vendor:publish --provider="YK\Basic\Providers\BasicServiceProvider"

4）、安装数据及文件

修改app\Providers\AppServiceProvider.php

    use Illuminate\Support\Facades\Schema;

boot函数中增加：
    
    Schema::defaultStringLength(191);

执行：

    php artisan basic:install

    如报错重新执行，需要将数据库清空，并删除database\migrations目录下的原生成文件




**4、Passport 安装和配置**

    php artisan passport:install

    执行成功后获取到相应的密码授予客户端的 ID 和 secret 并且配置到相对应的 config/index.js :
    
    export default {
      admin: {
        authorize: {
          clientId: ID,
          clientSecret: secret
        }
    }




**5、修改app/Http/Kernel.php**

app/Http/Kernel.php 中 $routeMiddleware 属性添加路由中间 oauth.providers 和 basic.permission，
并将auth中间件替换为如下：

    protected $routeMiddleware = [
        //'auth' => \App\Http\Middleware\Authenticate::class,
        'auth' => \SMartins\PassportMultiauth\Http\Middleware\MultiAuthenticate::class,
        'oauth.providers' => \SMartins\PassportMultiauth\Http\Middleware\AddCustomProvider::class,
        'admin.permission' => \YK\Basic\Middleware\Authenticate::class,




**6、安装vue等前端框架**

    npm install

    npm install -D vuex@~3.0.1 vue-router@~3.0.1 vue-i18n@~8.1.0 localforage@~1.7.2 element-ui@~2.8.2

    npm install echarts font-awesome vue-count-to

webpack.mix.js文件，增加：

    mix.js('resources/js/app.js', 'public/js')
        .sass('resources/sass/app.scss', 'public/css')
        .js('resources/js/admin.js', 'public/js');




**7、运行 Mix**

    npm run watch

    npm run production



**8、运行**

修改routes\web.php


    Route::get('/', function () {
        return view('dashboard');
    });

执行：
    
    php artisan serve


## 登录

url: http://localhost:8000/#/admin/login

username: admin

password: secret


- **[mojito](https://moell-peng.github.io/mojito-doc/)**
- **[Vue](https://cn.vuejs.org/v2/guide/)**
- **[Vue Router](https://router.vuejs.org/zh/)**
- **[Vuex](https://vuex.vuejs.org/zh/guide/)**
- **[Element](https://element.eleme.cn/#/zh-CN/component/installation)**
- **[laravel/passport]()**
- **[smartins/passport-multiauth]()**
- **[spatie/laravel-permission]()**


