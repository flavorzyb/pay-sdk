<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/7/27
 * Time: 下午2:44
 */

namespace Apps\Pay\WxPay;
use Apps\Common\Log;


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
            Log::pay("WxPayResults Error:签名错误！");
            return false;
        }

        $sign = $this->createSign($key);
        if ($this->getSign() === $sign) {
            return true;
        }

        Log::pay("WxPayResults Error:签名不一致！");
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
     * @return array | false
     */
    public static function getValuesFromXmlString($xml, $key)
    {
        $result = new self();
        $result->initValuesFromXml($xml);

        if(isset($result->values['return_code']) && ('SUCCESS' != $result->values['return_code'])) {
            return $result->getValues();
        }

        if ($result->checkSign($key)) {
            return $result->getValues();
        }

        return false;
    }
}
