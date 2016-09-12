<?php
namespace Apps\Pay\WxPay;


class WxPayNativeAppData extends WxPayNativePayData
{
    public function __construct()
    {
        $this->values['package'] = 'Sign=WXPay';
    }
}
