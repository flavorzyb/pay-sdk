<?php
return ['appId'     => 'appId',
    'appSecret' => 'appSecret',
    'mchId'     => 'mchId',
    'key'       => 'key',

    //=======【证书路径设置】=====================================
    /**
     * 设置商户证书路径
     * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     * @var string
     */
    'sslCertPath'   => __DIR__ . '/apiclient_cert.pem',
    'sslKeyPath'    => __DIR__ . '/apiclient_key.pem',

    //=======【curl代理设置】===================================
    /**
     * 这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     * @var string
     */
    'curlProxyHost' => '0.0.0.0',
    'curlProxyPort' => 0,

    //=======【上报信息配置】===================================
    /**
     * 接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
     * 开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     * @var int
     */
    'reportLevel'   => 1,

    //服务器异步通知页面路径
    "notify_url"            =>  '/Mall/PayResponse/wxPay',
    //页面跳转同步通知页面路径
    "call_back_url"         => '/Mall/PayResponse/index',
    //操作中断返回地址
    "merchant_url"          => '/Mall/PayResponse/interrupt',];