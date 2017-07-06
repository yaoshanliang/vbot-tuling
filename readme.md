## 微信-图灵机器人

### 背景

* [vbot](https://github.com/HanSon/vbot)
* [图灵API](http://www.tuling123.com/)

### 安装

```
git clone https://github.com/yaoshanliang/vbot-tuling.git
cd vbot-tuling
composer install
```

主动发消息需要安装[swoole](https://github.com/swoole/swoole-src/)

* Install via pecl

    `pecl install swoole`

* Install from source

    ```
    sudo apt-get install php7.0-dev
    git clone https://github.com/swoole/swoole-src.git
    cd swoole-src
    phpize
    ./configure
    make && make install
    ```

### 运行

```
php run.php
```

开始关键字:**聊天**

退出关键字:**不聊了**

<img src="http://osnrkuxuq.bkt.clouddn.com/vbot-log">

<img src="http://osnrkuxuq.bkt.clouddn.com/WechatIMG180.jpeg" width="360px">    <img src="http://osnrkuxuq.bkt.clouddn.com/WechatIMG179.jpeg" style="padding-left: 20px" width="360px">
