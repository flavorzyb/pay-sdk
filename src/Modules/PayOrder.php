<?php
namespace Pay\Modules;

class PayOrder extends  AbstractPay
{
    /**
     * 服务器异步通知页面路径
     * @var string
     */
    private $_notifyUrl     = '';
    /**
     * 页面跳转同步通知页面路径
     * @var string
     */
    private $_callBackUrl   = '';
    /**
     * 操作中断返回地址
     * @var string
     */
    private $_merchantUrl   = '';

    /**
     * 支付客户端IP
     * @var string
     */
    private $_ip            = '';

    /**
     * 服务器异步通知页面路径
     * @return string
     */
    public function getNotifyUrl()
    {
        return $this->_notifyUrl;
    }

    /**
     * 服务器异步通知页面路径
     * @param string $notifyUrl
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->_notifyUrl = trim($notifyUrl);
    }

    /**
     * 页面跳转同步通知页面路径
     * @return string
     */
    public function getCallBackUrl()
    {
        return $this->_callBackUrl;
    }

    /**
     * 页面跳转同步通知页面路径
     * @param string $callBackUrl
     */
    public function setCallBackUrl($callBackUrl)
    {
        $this->_callBackUrl = trim($callBackUrl);
    }

    /**
     * 操作中断返回地址
     * @return string
     */
    public function getMerchantUrl()
    {
        return $this->_merchantUrl;
    }

    /**
     * 操作中断返回地址
     * @param string $merchantUrl
     */
    public function setMerchantUrl($merchantUrl)
    {
        $this->_merchantUrl = trim($merchantUrl);
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->_ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->_ip = trim($ip);
    }
}
