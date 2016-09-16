<?php
namespace Pay\AliPay;

class AliConfig
{
    //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    //AppId
    private $appId = '';
    //商户的私钥（后缀是.pem）文件相对路径
    //如果签名方式设置为“0001”时，请设置该参数
    private $privateKeyPath = '';
    //支付宝公钥（后缀是.pem）文件相对路径
    //如果签名方式设置为“0001”时，请设置该参数
    private $publicKeyPath = '';
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
    public function getPrivateKeyPath()
    {
        return $this->privateKeyPath;
    }

    /**
     * @param string $privateKeyPath
     */
    public function setPrivateKeyPath($privateKeyPath)
    {
        $this->privateKeyPath = $privateKeyPath;
    }

    /**
     * @return string
     */
    public function getPublicKeyPath()
    {
        return $this->publicKeyPath;
    }

    /**
     * @param string $publicKeyPath
     */
    public function setPublicKeyPath($publicKeyPath)
    {
        $this->publicKeyPath = $publicKeyPath;
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
