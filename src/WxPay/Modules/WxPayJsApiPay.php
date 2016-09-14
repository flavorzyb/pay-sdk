<?php
namespace Pay\WxPay\Modules;


class WxPayJsApiPay extends WxPayDataBase
{
    /**
     * 设置微信分配的公众账号ID
     * @param string $value
     **/
    public function setAppId($value)
    {
        $this->values['appId'] = $value;
    }
    /**
     * 获取微信分配的公众账号ID的值
     * @return string
     **/
    public function getAppId()
    {
        return $this->get('appId');
    }

    /**
     * 设置支付时间戳
     * @param string $value
     **/
    public function setTimeStamp($value)
    {
        $this->values['timeStamp'] = trim($value);
    }
    /**
     * 获取支付时间戳的值
     * @return string
     **/
    public function getTimeStamp()
    {
        return $this->get('timeStamp');
    }

    /**
     * 随机字符串
     * @param string $value
     **/
    public function setNonceStr($value)
    {
        $this->values['nonceStr'] = $value;
    }
    /**
     * 获取notify随机字符串值
     * @return string
     **/
    public function getNonceStr()
    {
        return $this->get('nonceStr');
    }

    /**
     * 设置订单详情扩展字符串
     * @param string $value
     **/
    public function setPackage($value)
    {
        $this->values['package'] = $value;
    }
    /**
     * 获取订单详情扩展字符串的值
     * @return string
     **/
    public function getPackage()
    {
        return $this->get('package');
    }

    /**
     * 设置签名方式
     * @param string $value
     **/
    public function setSignType($value)
    {
        $this->values['signType'] = $value;
    }
    /**
     * 获取签名方式
     * @return string
     **/
    public function getSignType()
    {
        return $this->get('signType');
    }

    /**
     * 设置签名方式
     * @param string $value
     **/
    public function setPaySign($value)
    {
        $this->values['paySign'] = $value;
    }
    /**
     * 获取签名方式
     * @return string
     **/
    public function getPaySign()
    {
        return $this->get('paySign');
    }
}
