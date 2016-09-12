<?php

/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/7/24
 * Time: 下午2:20
 */
namespace Apps\Pay\WxPay;

/**
 *
 * 数据对象基础类，该类中定义数据类最基本的行为，包括：
 * 计算/设置/获取签名、输出xml格式的参数、从xml读取数据对象等
 *
 */
abstract class WxPayDataBase
{
    protected $values = array();

    /**
     * 设置签名，详见签名生成算法
     * @param   string $sign
     **/
    public function setSign($sign)
    {
        $this->values['sign'] = $sign;
    }

    /**
     * 获取签名，详见签名生成算法的值
     * @return string
     **/
    public function getSign()
    {
        return $this->values['sign'];
    }

    /**
     * 判断签名，详见签名生成算法是否存在
     * @return bool
     **/
    public function isSetSign()
    {
        return array_key_exists('sign', $this->values);
    }

    /**
     * 输出xml字符
     * @return string
     * @throws WxPayException
     **/
    public function toXml()
    {
        if(!is_array($this->values)
            || count($this->values) <= 0)
        {
            throw new WxPayException("数组数据异常！");
        }

        $xml = "<xml>";
        foreach ($this->values as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
    public function initValuesFromXml($xml)
    {
        if (!$xml) {
            throw new WxPayException("xml数据异常！");
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 格式化参数格式化成url参数
     * @return string
     */
    public function toUrlParams()
    {
        $result = "";
        foreach ($this->values as $k => $v) {
            if (("sign" != $k) && ("" != $v) && (!is_array($v))) {
                $result .= $k . "=" . $v . "&";
            }
        }

        return substr($result, 0 , -1);
    }

    /**
     * 生成签名
     * @param   string $key
     * @return  string 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function createSign($key)
    {
        //签名步骤一：按字典序排序参数
        ksort($this->values);
        $string = $this->toUrlParams();
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 获取设置的值
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
}
