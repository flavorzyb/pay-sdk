<?php
namespace Pay\Modules;

class PayOrder extends  AbstractPay
{
    /**
     * 服务器异步通知页面路径
     * @var string
     */
    private $notifyUrl     = '';
    /**
     * 页面跳转同步通知页面路径
     * @var string
     */
    private $callBackUrl   = '';
    /**
     * 操作中断返回地址
     * @var string
     */
    private $merchantUrl   = '';

    /**
     * 支付客户端IP
     * @var string
     */
    private $ip            = '';

    /**
     * 服务器异步通知页面路径
     * @return string
     */
    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }

    /**
     * 服务器异步通知页面路径
     * @param string $notifyUrl
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = trim($notifyUrl);
    }

    /**
     * 页面跳转同步通知页面路径
     * @return string
     */
    public function getCallBackUrl()
    {
        return $this->callBackUrl;
    }

    /**
     * 页面跳转同步通知页面路径
     * @param string $callBackUrl
     */
    public function setCallBackUrl($callBackUrl)
    {
        $this->callBackUrl = trim($callBackUrl);
    }

    /**
     * 操作中断返回地址
     * @return string
     */
    public function getMerchantUrl()
    {
        return $this->merchantUrl;
    }

    /**
     * 操作中断返回地址
     * @param string $merchantUrl
     */
    public function setMerchantUrl($merchantUrl)
    {
        $this->merchantUrl = trim($merchantUrl);
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = trim($ip);
    }
}
