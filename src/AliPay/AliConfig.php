<?php
namespace Pay\AliPay;
/* *
 * 配置文件
 * 版本：3.3
 * 日期：2012-07-19
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 * 提示：如何获取安全校验码和合作身份者id
 * 1.用您的签约支付宝账号登录支付宝网站(www.alipay.com)
 * 2.点击“商家服务”(https://b.alipay.com/order/myorder.htm)
 * 3.点击“查询合作者身份(pid)”、“查询安全校验码(key)”

 * 安全校验码查看时，输入支付密码后，页面呈灰色的现象，怎么办？
 * 解决方法：
 * 1、检查浏览器配置，不让浏览器做弹框屏蔽设置
 * 2、更换浏览器或电脑，重新登录查询。
 */

class AliConfig
{
    //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    //合作身份者id，以2088开头的16位纯数字
    private $partnerId = '';
    //安全检验码，以数字和字母组成的32位字符
    //如果签名方式设置为“MD5”时，请设置该参数
    private $key = '';
    //商户的私钥（后缀是.pen）文件相对路径
    //如果签名方式设置为“0001”时，请设置该参数
    private $privateKeyPath = '';
    //支付宝公钥（后缀是.pen）文件相对路径
    //如果签名方式设置为“0001”时，请设置该参数
    private $publicKeyPath = '';
    //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    //签名方式 不需修改
    private $signType = '0001';
    //字符编码格式 目前支持 gbk 或 utf-8
    private $inputCharset = 'utf-8';
    //ca证书路径地址，用于curl中ssl校验
    //请保证cert.pem文件在当前文件夹目录中
    private $certPath = '';
    //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
    private $transport = 'http';
    // 支付宝账号
    private $account = '';
    //服务器异步通知页面路径
    private $notifyUrl = '';
    //页面跳转同步通知页面路径
    private $callBackUrl = '';
    //操作中断返回地址
    private $merchantUrl = '';

    /**
     * @return string
     */
    public function getPartnerId()
    {
        return $this->partnerId;
    }

    /**
     * @param string $partnerId
     */
    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;
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
    public function getSignType()
    {
        return $this->signType;
    }

    /**
     * @param string $signType
     */
    public function setSignType($signType)
    {
        $this->signType = $signType;
    }

    /**
     * @return string
     */
    public function getInputCharset()
    {
        return $this->inputCharset;
    }

    /**
     * @param string $inputCharset
     */
    public function setInputCharset($inputCharset)
    {
        $this->inputCharset = $inputCharset;
    }

    /**
     * @return string
     */
    public function getCertPath()
    {
        return $this->certPath;
    }

    /**
     * @param string $certPath
     */
    public function setCertPath($certPath)
    {
        $this->certPath = $certPath;
    }

    /**
     * @return string
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param string $transport
     */
    public function setTransport($transport)
    {
        $this->transport = $transport;
    }

    /**
     * @return string
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param string $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
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
