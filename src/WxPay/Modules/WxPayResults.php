<?php
namespace Pay\WxPay\Modules;

/**
 * 接口调用结果类
 * Class WxPayResults
 * @package Apps\Pay\WxPay
 */
class WxPayResults extends  WxPayDataBase
{
    /**
     *
     * 检测签名
     * @param string $key
     * @return bool
     */
    public function checkSign($key)
    {
        if (!$this->isSetSign()) {
            return false;
        }

        $sign = $this->createSign($key);
        if ($this->getSign() === $sign) {
            return true;
        }

        return false;
    }

    /**
     *
     * 设置参数
     * @param string $key
     * @param string $value
     */
    public function setData($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * 将xml转为array
     * 如果签名验证失败，则返回false
     * @param string $xml
     * @param string $key
     * @param bool $isSign
     * @return array | false
     */
    public static function getValuesFromXmlString($xml, $key, $isSign = true)
    {
        $result = new self();
        $result->initValuesFromXml($xml);

        if(isset($result->values['return_code']) && ('SUCCESS' != $result->values['return_code'])) {
            return $result->getValues();
        }

        if (!$isSign) {
            return $result->getValues();
        }

        if ($result->checkSign($key)) {
            return $result->getValues();
        }

        return false;
    }
}
