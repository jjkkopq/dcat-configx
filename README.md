## Dcat Admin 网站设置 - 系统变量配置管理

用于配置系统中各种的动态变量 设置值列为可编辑列 变量值为 config("变量名") 的值

### 安装

### composer 安装
```
composer require jjkkopq/dcat-configx
```


### 启用插件
```
开发工具 -> 扩展 -> jjkkopq.dcat-configx -> 升级 -> 启用
```

# 使用
界面在 `/admin/configx`

# 附加
打开文件 `app/Admin/bootstrap.php`
加载设置界面
```php

Admin::navbar(function (Navbar $navbar) {
    //..
        \Jjkkopq\DcatConfigx\UserAdmin::loadNavbar($navbar);
    //..
});

```
# 获取变量值

```
dd(config("admin.name"));
```
or

```
Configx::val("admin.name")
```

