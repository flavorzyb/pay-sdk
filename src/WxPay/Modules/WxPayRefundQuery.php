<?php
namespace Pay\WxPay\Modules;

/**
 *
 * 退款查询输入对象
 */
class WxPayRefundQuery extends WxPayDataBase
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
     * 设置微信支付分配的终端设备号
     * @param string $value
     **/
    public function setDeviceInfo($value)
    {
        $this->values['device_info'] = $value;
    }
    /**
     * 获取微信支付分配的终端设备号的值
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
     * 设置微信订单号
     * @param string $value
     **/
    public function setTransactionId($value)
    {
        $this->values['transaction_id'] = $value;
    }
    /**
     * 获取微信订单号的值
     * @return string
     **/
    public function getTransactionId()
    {
        return $this->get('transaction_id');
    }

    /**
     * 设置商户系统内部的订单号
     * @param string $value
     **/
    public function setOutTradeNo($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    /**
     * 获取商户系统内部的订单号的值
     * @return string
     **/
    public function getOutTradeNo()
    {
        return $this->get('out_trade_no');
    }

    /**
     * 设置商户退款单号
     * @param string $value
     **/
    public function setOutRefundNo($value)
    {
        $this->values['out_refund_no'] = $value;
    }
    /**
     * 获取商户退款单号的值
     * @return string
     **/
    public function getOutRefundNo()
    {
        return $this->get('out_refund_no');
    }

    /**
     * 设置微信退款单号refund_id、out_refund_no、out_trade_no、transaction_id四个参数必填一个，如果同时存在优先级为：refund_id>out_refund_no>transaction_id>out_trade_no
     * @param string $value
     **/
    public function setRefundId($value)
    {
        $this->values['refund_id'] = $value;
    }

    /**
     * 获取微信退款单号refund_id、out_refund_no、out_trade_no、transaction_id四个参数必填一个，如果同时存在优先级为：refund_id>out_refund_no>transaction_id>out_trade_no的值
     * @return string
     **/
    public function getRefundId()
    {
        return $this->get('refund_id');
    }
}
