<?php
namespace Pay\AliPay\Modules;

abstract class AliPayRequest extends AliPayBase
{
    /**
     * 请求参数的集合，最大长度不限，除公共参数外所有请求参数都必须放在这个参数中传递，具体参照各产品快速接入文档
     * @return string
     */
    abstract public function getBizContent();
}
