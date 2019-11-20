# bricksasp-spu

## 简介
modularity saas 商品模块

安装
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist bricksasp/spu: "~1.0"
```

or add

```json
"bricksasp/spu": "~1.0"
```

to the require section of your composer.json.


Configuration
-------------

To use this extension, you have to configure the Connection class in your application configuration:

```php
return [
    //....
    'components' => [
        'spu' => [
            'class' => 'bricksasp\spu\Module',
        ],
    ]
];
```