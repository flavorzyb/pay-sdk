<?php
namespace Pay\WxPay\Modules;

/**
 *
 * 企业付款输入对象
 */
class WxPayTransfer extends WxPayDataBase
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
     * 设置APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。
     * @param string $value
     **/
    public function setSpbillCreateIp($value)
    {
        $this->values['spbill_create_ip'] = $value;
    }

    /**
     * 获取APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。的值
     * @return string
     **/
    public function getSpbillCreateIp()
    {
        return $this->get('spbill_create_ip');
    }

    /**
     * 商户订单号
     * @param $value
     */
    public function setPartnerTradeNo($value)
    {
        $this->values['partner_trade_no'] = $value;
    }

    /**
     * 商户订单号
     * @return string
     */
    public function getPartnerTradeNo()
    {
        return $this->get('partner_trade_no');
    }

    /**
     * 设置trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。
     * 下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。
     * @param string $value
     **/
    public function setOpenId($value)
    {
        $this->values['openid'] = $value;
    }

    /**
     * 获取trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。
     * 下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。 的值
     * @return string
     **/
    public function getOpenId()
    {
        return $this->get('openid');
    }

    /**
     * 校验用户姓名选项
     * NO_CHECK：不校验真实姓名
     * FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账）
     * OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
     *
     * @param WxPayCheckName $checkName
     */
    public function setCheckName(WxPayCheckName $checkName)
    {
        $this->values['check_name'] = $checkName->getValue();
    }

    /**
     * 校验用户姓名选项
     * NO_CHECK：不校验真实姓名
     * FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账）
     * OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
     * @return mixed|string
     */
    public function getCheckName()
    {
        return $this->get('check_name', WxPayCheckName::OPTION_CHECK);
    }

    /**
     * 收款用户姓名
     * 收款用户真实姓名。
     * 如果check_name设置为FORCE_CHECK或OPTION_CHECK，则必填用户真实姓名
     * @param string $value
     */
    public function setReUserName($value)
    {
        $this->values['re_user_name'] = $value;
    }

    /**
     * 收款用户姓名
     * 收款用户真实姓名。
     * 如果check_name设置为FORCE_CHECK或OPTION_CHECK，则必填用户真实姓名
     * @return string
     */
    public function getReUserName()
    {
        return $this->get('re_user_name');
    }

    /**
     * 企业付款金额，单位为分
     * @param int $value
     */
    public function setAmount($value)
    {
        $this->values['amount'] = intval($value);
    }

    /**
     * 企业付款金额，单位为分
     * @return int
     */
    public function getAmount()
    {
        return intval($this->get('amount', 0));
    }

    /**
     * 企业付款操作说明信息。必填
     * @param string $value
     */
    public function setDescription($value)
    {
        $this->values['desc'] = $value;
    }

    /**
     * 企业付款操作说明信息。必填
     * @return string
     */
    public function getDescription()
    {
        return $this->get('desc');
    }
}
