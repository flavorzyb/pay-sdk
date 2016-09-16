<?php
namespace Pay\AliPay\Modules;

abstract class AliPayBase
{
    const FORMAT = 'JSON';

    const RSA = 'RSA';

    const VERSION = '1.0';

    /**
     * 支付宝分配给开发者的应用ID
     * @var string
     */
    private $appId = '';

    /**
     * @var AliPayCharset
     */
    private $charset = null;

    /**
     * 商户请求参数的签名串
     * @var string
     */
    private $sign = '';

    /**
     * 发送请求的时间，格式"yyyy-MM-dd HH:mm:ss"
     * @var string
     */
    private $timeStamp = '';

    /**
     * AliPayRequest constructor.
     */
    public function __construct()
    {
        $this->charset = AliPayCharset::createUTF8Charset();
        $this->timeStamp = date('Y-m-d H:i:s');
    }

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
     * 接口名称
     * @return string
     */
    abstract public function getMethod();

    /**
     * 仅支持JSON
     * @return string
     */
    final public function getFormat()
    {
        return self::FORMAT;
    }

    /**
     * @return AliPayCharset
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param AliPayCharset $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * 商户生成签名字符串所使用的签名算法类型，目前支持RSA
     * @return string
     */
    final public function getSignType()
    {
        return self::RSA;
    }

    /**
     * @return string
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * @param string $sign
     */
    public function setSign($sign)
    {
        $this->sign = $sign;
    }

    /**
     * @return string
     */
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }

    /**
     * @param string $timeStamp
     */
    public function setTimeStamp($timeStamp)
    {
        $this->timeStamp = $timeStamp;
    }

    /**
     * 调用的接口版本，固定为：1.0
     * @return string
     */
    final public function getVersion()
    {
        return self::VERSION;
    }
}
