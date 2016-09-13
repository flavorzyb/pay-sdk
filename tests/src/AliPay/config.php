<?php
return [
    //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    //合作身份者id，以2088开头的16位纯数字
    'partner'               => 'partner',
    //安全检验码，以数字和字母组成的32位字符
    //如果签名方式设置为“MD5”时，请设置该参数
    'key'                   => 'key',
    //商户的私钥（后缀是.pen）文件相对路径
    //如果签名方式设置为“0001”时，请设置该参数
    'private_key_path'      => __DIR__ .'/rsa_private_key.pem',
    //支付宝公钥（后缀是.pen）文件相对路径
    //如果签名方式设置为“0001”时，请设置该参数
    'ali_public_key_path'   => __DIR__ .'/alipay_public_key.pem',
    //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    //签名方式 不需修改
    'sign_type'             => '0001',
    //字符编码格式 目前支持 gbk 或 utf-8
    'input_charset'         => 'utf-8',
    //ca证书路径地址，用于curl中ssl校验
    //请保证cert.pem文件在当前文件夹目录中
    'cert'                  => __DIR__ .'/cacert.pem',
    //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
    'transport'             => 'http',
    // 支付宝账号 万曲
    'alipay_account'        => 'alipay_account',
    //服务器异步通知页面路径
    "notify_url"            => '/Mall/PayResponse/aliPay',
    //页面跳转同步通知页面路径
    "call_back_url"         => '/Mall/PayResponse/index',
    //操作中断返回地址
    "merchant_url"          => '/Mall/PayResponse/interrupt',
];