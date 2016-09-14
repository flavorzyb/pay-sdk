<?php
namespace Pay\WxPay\Modules;


class WxPayReport extends WxPayDataBase
{
    /**
     * 设置微信分配的公众账号ID
     * @param string $value
     **/
    public function setAppId($value)
    {
        $this->values['appid'] = $value;
    }
    /**
     * 获取微信分配的公众账号ID的值
     * @return string
     **/
    public function getAppId()
    {
        return $this->get('appid');
    }

    /**
     * 设置微信支付分配的商户号
     * @param string $value
     **/
    public function setMchId($value)
    {
        $this->values['mch_id'] = $value;
    }
    /**
     * 获取微信支付分配的商户号的值
     * @return string
     **/
    public function getMchId()
    {
        return $this->get('mch_id');
    }

    /**
     * 设置微信支付分配的终端设备号，商户自定义
     * @param string $value
     **/
    public function setDeviceInfo($value)
    {
        $this->values['device_info'] = $value;
    }
    /**
     * 获取微信支付分配的终端设备号，商户自定义的值
     * @return string
     **/
    public function getDeviceInfo()
    {
        return $this->get('device_info');
    }

    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
     * @param string $value
     **/
    public function setNonceStr($value)
    {
        $this->values['nonce_str'] = $value;
    }
    /**
     * 获取随机字符串，不长于32位。推荐随机数生成算法的值
     * @return string
     **/
    public function getNonceStr()
    {
        return $this->get('nonce_str');
    }

    /**
     * 设置上报对应的接口的完整URL，类似：https://api.mch.weixin.qq.com/pay/unifiedorder对于被扫支付，为更好的和商户共同分析一次业务行为的整体耗时情况，对于两种接入模式，请都在门店侧对一次被扫行为进行一次单独的整体上报，上报URL指定为：https://api.mch.weixin.qq.com/pay/micropay/total关于两种接入模式具体可参考本文档章节：被扫支付商户接入模式其它接口调用仍然按照调用一次，上报一次来进行。
     * @param string $value
     **/
    public function setInterfaceUrl($value)
    {
        $this->values['interface_url'] = $value;
    }
    /**
     * 获取上报对应的接口的完整URL，类似：https://api.mch.weixin.qq.com/pay/unifiedorder对于被扫支付，为更好的和商户共同分析一次业务行为的整体耗时情况，对于两种接入模式，请都在门店侧对一次被扫行为进行一次单独的整体上报，上报URL指定为：https://api.mch.weixin.qq.com/pay/micropay/total关于两种接入模式具体可参考本文档章节：被扫支付商户接入模式其它接口调用仍然按照调用一次，上报一次来进行。的值
     * @return string
     **/
    public function getInterfaceUrl()
    {
        return $this->get('interface_url');
    }

    /**
     * 设置接口耗时情况，单位为毫秒
     * @param string $value
     **/
    public function setExecuteTime($value)
    {
        $this->values['execute_time_'] = $value;
    }
    /**
     * 获取接口耗时情况，单位为毫秒的值
     * @return string
     **/
    public function getExecuteTime()
    {
        return $this->get('execute_time_');
    }

    /**
     * 设置SUCCESS/FAIL此字段是通信标识，非交易标识，交易是否成功需要查看trade_state来判断
     * @param string $value
     **/
    public function setReturnCode($value)
    {
        $this->values['return_code'] = $value;
    }
    /**
     * 获取SUCCESS/FAIL此字段是通信标识，非交易标识，交易是否成功需要查看trade_state来判断的值
     * @return string
     **/
    public function getReturnCode()
    {
        return $this->get('return_code');
    }

    /**
     * 设置返回信息，如非空，为错误原因签名失败参数格式校验错误
     * @param string $value
     **/
    public function setReturnMsg($value)
    {
        $this->values['return_msg'] = $value;
    }
    /**
     * 获取返回信息，如非空，为错误原因签名失败参数格式校验错误的值
     * @return string
     **/
    public function getReturnMsg()
    {
        return $this->get('return_msg');
    }

    /**
     * 设置SUCCESS/FAIL
     * @param string $value
     **/
    public function setResultCode($value)
    {
        $this->values['result_code'] = $value;
    }
    /**
     * 获取SUCCESS/FAIL的值
     * @return string
     **/
    public function getResultCode()
    {
        return $this->get('result_code');
    }

    /**
     * 设置ORDERNOTEXIST—订单不存在SYSTEMERROR—系统错误
     * @param string $value
     **/
    public function setErrCode($value)
    {
        $this->values['err_code'] = $value;
    }
    /**
     * 获取ORDERNOTEXIST—订单不存在SYSTEMERROR—系统错误的值
     * @return string
     **/
    public function getErrCode()
    {
        return $this->get('err_code');
    }

    /**
     * 设置结果信息描述
     * @param string $value
     **/
    public function setErrCodeDes($value)
    {
        $this->values['err_code_des'] = $value;
    }
    /**
     * 获取结果信息描述的值
     * @return string
     **/
    public function getErrCodeDes()
    {
        return $this->get('err_code_des');
    }

    /**
     * 设置商户系统内部的订单号,商户可以在上报时提供相关商户订单号方便微信支付更好的提高服务质量。
     * @param string $value
     **/
    public function setOutTradeNo($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    /**
     * 获取商户系统内部的订单号,商户可以在上报时提供相关商户订单号方便微信支付更好的提高服务质量。 的值
     * @return string
     **/
    public function getOutTradeNo()
    {
        return $this->get('out_trade_no');
    }

    /**
     * 设置发起接口调用时的机器IP
     * @param string $value
     **/
    public function setUserIp($value)
    {
        $this->values['user_ip'] = $value;
    }
    /**
     * 获取发起接口调用时的机器IP 的值
     * @return string
     **/
    public function getUserIp()
    {
        return $this->get('user_ip');
    }

    /**
     * 设置系统时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则
     * @param string $value
     **/
    public function setTime($value)
    {
        $this->values['time'] = $value;
    }
    /**
     * 获取系统时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则的值
     * @return string
     **/
    public function getTime()
    {
        return $this->get('time');
    }
}
