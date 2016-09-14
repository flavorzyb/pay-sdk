<?php
namespace Pay\WxPay\Modules;

/**
 *
 * 提交退款输入对象
 *
 */
class WxPayRefund extends WxPayDataBase
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
     * 设置微信支付分配的终端设备号，与下单一致
     * @param string $value
     **/
    public function setDeviceInfo($value)
    {
        $this->values['device_info'] = $value;
    }
    /**
     * 获取微信支付分配的终端设备号，与下单一致的值
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
     * 设置商户系统内部的订单号,transaction_id、out_trade_no二选一，如果同时存在优先级：transaction_id> out_trade_no
     * @param string $value
     **/
    public function setOutTradeNo($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    /**
     * 获取商户系统内部的订单号,transaction_id、out_trade_no二选一，如果同时存在优先级：transaction_id> out_trade_no的值
     * @return string
     **/
    public function getOutTradeNo()
    {
        return $this->get('out_trade_no');
    }

    /**
     * 设置商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔
     * @param string $value
     **/
    public function setOutRefundNo($value)
    {
        $this->values['out_refund_no'] = $value;
    }
    /**
     * 获取商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔的值
     * @return string
     **/
    public function getOutRefundNo()
    {
        return $this->get('out_refund_no');
    }

    /**
     * 设置订单总金额，单位为分，只能为整数，详见支付金额
     * @param int $value
     **/
    public function setTotalFee($value)
    {
        $this->values['total_fee'] = $value;
    }
    /**
     * 获取订单总金额，单位为分，只能为整数，详见支付金额的值
     * @return int
     **/
    public function getTotalFee()
    {
        return $this->get('total_fee', 0);
    }

    /**
     * 设置退款总金额，订单总金额，单位为分，只能为整数，详见支付金额
     * @param int $value
     **/
    public function setRefundFee($value)
    {
        $this->values['refund_fee'] = $value;
    }
    /**
     * 获取退款总金额，订单总金额，单位为分，只能为整数，详见支付金额的值
     * @return int
     **/
    public function getRefundFee()
    {
        return $this->get('refund_fee', 0);
    }

    /**
     * 设置货币类型，符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型
     * @param string $value
     **/
    public function setRefundFeeType($value)
    {
        $this->values['refund_fee_type'] = $value;
    }
    /**
     * 获取货币类型，符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型的值
     * @return string
     **/
    public function getRefundFeeType()
    {
        return $this->get('refund_fee_type');
    }

    /**
     * 设置操作员帐号, 默认为商户号
     * @param string $value
     **/
    public function setOpUserId($value)
    {
        $this->values['op_user_id'] = $value;
    }
    /**
     * 获取操作员帐号, 默认为商户号的值
     * @return string
     **/
    public function getOpUserId()
    {
        return $this->get('op_user_id');
    }
}