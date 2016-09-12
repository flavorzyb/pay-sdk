<?php
namespace Pay\WxPay\Modules;


class WxPayNativeAppData extends WxPayNativePayData
{
    public function __construct()
    {
        parent::__construct();
        $this->values['package'] = 'Sign=WXPay';
    }
}
