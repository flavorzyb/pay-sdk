<?php
namespace Pay;

use Pay\AliPay\AliPayApi;
use Pay\AliPay\Modules\AliPayConfig;
use Pay\AliPay\Modules\AliPayTradeWapPayRequest;
use Simple\Log\Writer;
use Pay\Modules\PayOrder;

class AliPay extends PayAbstract
{
    /**
     * @var AliPayConfig
     */
    private $config = null;

    /**
     * @var AliPayApi
     */
    private $aliPayApi = null;

    public function __construct(AliPayConfig $config, Writer $logWriter)
    {
        parent::__construct($logWriter);
        $this->config = $config;
    }



    /**
     * @return AliPayApi
     */
    public function getAliPayApi()
    {
        if (null == $this->aliPayApi) {
            $this->setAliPayApi(new AliPayApi($this->config, $this->getLogWriter()));
        }
        return $this->aliPayApi;
    }

    /**
     * @param AliPayApi $aliPayApi
     */
    public function setAliPayApi(AliPayApi $aliPayApi)
    {
        $this->aliPayApi = $aliPayApi;
    }

    /**
     * 支付宝支付
     * 返回提交支付
     * @param   PayOrder $payOrder
     * @param string $ip
     * @return  string | bool
     * @override
     */
    public function pay(PayOrder $payOrder, $ip)
    {
        $result = parent::pay($payOrder, $ip);
        if (!$result) {
            return false;
        }

        $order = new AliPayTradeWapPayRequest();

        if ('' != $this->config->getCallBackUrl()) {
            $order->setReturnUrl($this->config->getCallBackUrl());
        }

        if ('' != $this->config->getNotifyUrl()) {
            $order->setNotifyUrl($this->config->getNotifyUrl());
        }

        if ('' != $payOrder->getExtra()) {
            $order->setBody($payOrder->getExtra());
        }

        $order->setSubject($payOrder->getGoodsName());
        $order->setOutTradeNo($payOrder->getOrderId());

        if ($payOrder->getTimeoutExpress() > 60) {
            $order->setTimeoutExpress(intval($payOrder->getTimeoutExpress() / 60) . 'm');
        }

        $order->setTotalAmount($payOrder->getPayAmount());
        if ('' != $this->config->getSellerId()) {
            $order->setSellerId($this->config->getSellerId());
        }

        return $this->getAliPayApi()->pay($order);
    }
}
