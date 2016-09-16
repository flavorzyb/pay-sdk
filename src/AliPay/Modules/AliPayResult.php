<?php
namespace Pay\AliPay\Modules;

abstract class AliPayResult
{
    /**
     * 网关返回码
     * @var string
     */
    private $code = '';
    /**
     * 网关返回码描述
     * @var string
     */
    private $msg = '';
    /**
     * 业务返回码
     * @var string
     */
    private $subCode = '';
    /**
     * 业务返回码描述
     * @var string
     */
    private $subMsg = '';
    /**
     * 签名
     * @var string
     */
    private $sign = '';

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
    }

    /**
     * @return string
     */
    public function getSubCode()
    {
        return $this->subCode;
    }

    /**
     * @param string $subCode
     */
    public function setSubCode($subCode)
    {
        $this->subCode = $subCode;
    }

    /**
     * @return string
     */
    public function getSubMsg()
    {
        return $this->subMsg;
    }

    /**
     * @param string $subMsg
     */
    public function setSubMsg($subMsg)
    {
        $this->subMsg = $subMsg;
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
}

