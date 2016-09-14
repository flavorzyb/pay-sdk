<?php
namespace Pay\WxPay\Modules;


class WxPayNotifyReply extends WxPayDataBase
{
    /**
     *
     * 设置错误码 FAIL 或者 SUCCESS
     * @param string
     */
    public function setReturnCode($returnCode)
    {
        $this->values['return_code'] = $returnCode;
    }

    /**
     *
     * 获取错误码 FAIL 或者 SUCCESS
     * @return string $return_code
     */
    public function getReturnCode()
    {
        return $this->get('return_code');
    }

    /**
     *
     * 设置错误信息
     * @param string $returnMsg
     */
    public function setReturnMsg($returnMsg)
    {
        $this->values['return_msg'] = $returnMsg;
    }

    /**
     *
     * 获取错误信息
     * @return string
     */
    public function getReturnMsg()
    {
        return $this->get('return_msg');
    }

    /**
     *
     * 设置返回参数
     * @param string $key
     * @param string $value
     */
    public function setData($key, $value)
    {
        $this->values[$key] = $value;
    }
}
