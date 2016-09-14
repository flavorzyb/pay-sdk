<?php
namespace Pay\WxPay\Modules;

class WxPayConfig
{
    //=======【基本信息设置】=====================================
    //
    /**
     * TODO: 修改这里配置为您自己申请的商户信息
     * 微信公众号信息配置
     *
     * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
     *
     * MCHID：商户号（必须配置，开户邮件中可查看）
     *
     * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
     * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
     *
     * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
     * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
     * @var string
     */
    private $appId = '';
    private $mchId = '';
    private $key = '';
    private $appSecret = '';

    //=======【证书路径设置】=====================================
    /**
     * TODO：设置商户证书路径
     * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     * @var string
     */
    private $sslCertPath = '';
    private $sslKeyPath = '';
    private $rootCaPath = '';
    //=======【curl代理设置】===================================
    /**
     * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     * @var string
     */
    private $curlProxyHost = '0.0.0.0';
    private $curlProxyPort = 0;

    //=======【上报信息配置】===================================
    /**
     * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
     * 开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     * @var int
     */
    private $reportLevel = 0;

    //服务器异步通知页面路径
    private $notifyUrl = '';
    //页面跳转同步通知页面路径
    private $callBackUrl = '';
    //操作中断返回地址
    private $merchantUrl = '';

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return string
     */
    public function getMchId()
    {
        return $this->mchId;
    }

    /**
     * @param string $mchId
     */
    public function setMchId($mchId)
    {
        $this->mchId = $mchId;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * @param string $appSecret
     */
    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
    }

    /**
     * @return string
     */
    public function getSslCertPath()
    {
        return $this->sslCertPath;
    }

    /**
     * @param string $sslCertPath
     */
    public function setSslCertPath($sslCertPath)
    {
        $this->sslCertPath = $sslCertPath;
    }

    /**
     * @return string
     */
    public function getSslKeyPath()
    {
        return $this->sslKeyPath;
    }

    /**
     * @param string $sslKeyPath
     */
    public function setSslKeyPath($sslKeyPath)
    {
        $this->sslKeyPath = $sslKeyPath;
    }

    /**
     * @return string
     */
    public function getRootCaPath()
    {
        return $this->rootCaPath;
    }

    /**
     * @param string $rootCaPath
     */
    public function setRootCaPath($rootCaPath)
    {
        $this->rootCaPath = $rootCaPath;
    }

    /**
     * @return string
     */
    public function getCurlProxyHost()
    {
        return $this->curlProxyHost;
    }

    /**
     * @param string $curlProxyHost
     */
    public function setCurlProxyHost($curlProxyHost)
    {
        $this->curlProxyHost = $curlProxyHost;
    }

    /**
     * @return int
     */
    public function getCurlProxyPort()
    {
        return $this->curlProxyPort;
    }

    /**
     * @param int $curlProxyPort
     */
    public function setCurlProxyPort($curlProxyPort)
    {
        $this->curlProxyPort = $curlProxyPort;
    }

    /**
     * @return int
     */
    public function getReportLevel()
    {
        return $this->reportLevel;
    }

    /**
     * @param int $reportLevel
     */
    public function setReportLevel($reportLevel)
    {
        $this->reportLevel = $reportLevel;
    }

    /**
     * @return string
     */
    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }

    /**
     * @param string $notifyUrl
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
    }

    /**
     * @return string
     */
    public function getCallBackUrl()
    {
        return $this->callBackUrl;
    }

    /**
     * @param string $callBackUrl
     */
    public function setCallBackUrl($callBackUrl)
    {
        $this->callBackUrl = $callBackUrl;
    }

    /**
     * @return string
     */
    public function getMerchantUrl()
    {
        return $this->merchantUrl;
    }

    /**
     * @param string $merchantUrl
     */
    public function setMerchantUrl($merchantUrl)
    {
        $this->merchantUrl = $merchantUrl;
    }
}
