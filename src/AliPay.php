<?php
namespace Pay;

use Pay\AliPay\AliPayApi;
use Pay\AliPay\Modules\AliPayConfig;
use Pay\AliPay\Modules\AliPayTradeQueryRequest;
use Pay\AliPay\Modules\AliPayTradeStatus;
use Pay\AliPay\Modules\AliPayTradeWapPayRequest;
use Pay\Modules\PayOrderQueryResult;
use Pay\Modules\PayTradeStatus;
use Simple\Log\Writer;
use Pay\Modules\PayOrder;
use Pay\Modules\PayOrderQuery;

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
     * @return  string | false
     * @override
     */
    public function pay(PayOrder $payOrder, $ip)
    {
        if (!parent::pay($payOrder, $ip)) {
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

    private function buildTradeStatus(AliPayTradeStatus $status)
    {
        if ($status->isClosed()) {
            return PayTradeStatus::createClosedStatus();
        } elseif ($status->isFinished()) {
            return PayTradeStatus::createClosedStatus();
        } elseif ($status->isSuccess()) {
            return PayTradeStatus::createSuccessStatus();
        } elseif ($status->isWaitBuyerPay()) {
            return PayTradeStatus::createNotPayStatus();
        }

        return PayTradeStatus::createOthersStatus();
    }

    /**
     * 订单查询
     * @param PayOrderQuery $query
     * @param string $ip
     * @return false|PayOrderQueryResult
     */
    public function orderQuery(PayOrderQuery $query, $ip)
    {
        if (!parent::orderQuery($query, $ip)) {
            return false;
        }

        $data = new AliPayTradeQueryRequest();
        $data->setTradeNo($query->getTradeNo());
        $data->setOutTradeNo($query->getOrderId());

        $query = $this->getAliPayApi()->orderQuery($data);

        if (false == $query) {
            return false;
        }

        $result = new PayOrderQueryResult();
        $result->setOrderId($query->getOutTradeNo());
        $result->setTradeNo($query->getTradeNo());
        $result->setTotalAmount($query->getTotalAmount());
        $result->setReceiptAmount($query->getReceiptAmount());
        $result->setTradeStatus($this->buildTradeStatus($query->getTradeStatus()));

        return $result;
    }
}
