<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/12
 * Time: 上午10:59
 */

namespace Apps\Pay\WxPay;


class WxPayNativePayData extends WxPayDataBase
{
    public function __construct()
    {
        $this->values['package'] = 'NATIVE';
    }

    /**
     * 设置微信分配的公众账号ID
     * @param string $value
     **/
    public function setAppId($value)
    {
        $this->values['appid'] = trim($value);
    }

    /**
     * 获取微信分配的公众账号ID的值
     * @return string
     **/
    public function getAppId()
    {
        return $this->values['appid'];
    }

    /**
     * 判断微信分配的公众账号ID是否存在
     * @return boolean
     **/
    public function isSetAppId()
    {
        return array_key_exists('appid', $this->values);
    }

    /**
     * 设置支付时间戳
     * @param string $value
     **/
    public function setTimeStamp($value)
    {
        $this->values['timestamp'] = trim($value);
    }

    /**
     * 获取支付时间戳的值
     * @return string
     **/
    public function getTimeStamp()
    {
        return $this->values['timestamp'];
    }

    /**
     * 判断支付时间戳是否存在
     * @return boolean
     **/
    public function isSetTimeStamp()
    {
        return array_key_exists('timestamp', $this->values);
    }

    /**
     * 设置随机字符串
     * @param string $value
     **/
    public function setNonceStr($value)
    {
        $this->values['noncestr'] = trim($value);
    }

    /**
     * 获取随机字符串的值
     * @return string
     **/
    public function getNonceStr()
    {
        return $this->values['noncestr'];
    }

    /**
     * 判断随机字符串是否存在
     * @return boolean
     **/
    public function isSetNonceStr()
    {
        return array_key_exists('noncestr', $this->values);
    }

    /**
     * @param string $value
     **/
    public function setPrePayId($value)
    {
        $this->values['prepayid'] = trim($value);
    }

    /**
     * @return string
     **/
    public function getPrePayId()
    {
        return $this->values['prepayid'];
    }

    /**
     * @return boolean
     **/
    public function isSetPrePayId()
    {
        return array_key_exists('prepayid', $this->values);
    }

    public function setPartnerId($partnerId)
    {
        $this->values['partnerid'] = trim($partnerId);
    }

    public function getPartnerId()
    {
        return $this->values['partnerid'];
    }
}
