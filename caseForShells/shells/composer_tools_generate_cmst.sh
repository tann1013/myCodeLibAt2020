#! /bin/bash
#controller、model、testcase
#使用说明：在项目根目录下执行如下脚本
#sh generate_cmt.sh User 会生成对应的四个文件（控制器,模型,服务,测试用例）
#app/Http/Controllers/admin/UserController.php
#app/Models/User.php
#app/Services/UserService.php
#/test/admin/UserTest.php

if [ ! -n "$1" ] ;then
    echo "请输出模块名称！如：User"
else
    php artisan make:mchController /admin/$1Controller &&\
    php artisan make:model /Models/$1 &&\
    php artisan make:mchService $1Service &&\
    php artisan make:test /admin/$1Test --unit

    echo "done。"
fi


