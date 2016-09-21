<?php
namespace Pay;

use Pay\Modules\PayNotify;
use Pay\Modules\PayOrder;
use Pay\Modules\PayOrderClose;
use Pay\Modules\PayOrderQuery;
use Simple\Log\Writer;

abstract class PayAbstract
{
    /**
     * @var Writer
     */
    protected $logWriter = null;

    /**
     * PayAbstract constructor.
     * @param Writer $logWriter
     */
    public function __construct(Writer $logWriter)
    {
        $this->logWriter = $logWriter;
    }

    /**
     * @return Writer
     */
    public function getLogWriter()
    {
        return $this->logWriter;
    }

    /**
     * 支付
     * @param PayOrder $payOrder
     * @param string $ip
     * @return bool|string
     */
    public function pay(PayOrder $payOrder, $ip)
    {
        if ('' == $payOrder->getOrderId()) {
            $this->getLogWriter()->error("订单ID不能为空");
            return false;
        }

        if ('' == $payOrder->getGoodsName()) {
            $this->getLogWriter()->error("商品名不能为空");
            return false;
        }

        if (0.01 > $payOrder->getPayAmount()) {
            $this->getLogWriter()->error("支付款不能为0");
            return false;
        }

        return true;
    }

    /**
     * @param PayOrderQuery $query
     * @param $ip
     * @return bool
     */
    public function orderQuery(PayOrderQuery $query, $ip)
    {
        if (('' == $query->getTradeNo()) && ('' == $query->getOrderId())) {
            return false;
        }

        return true;
    }

    /**
     * 关闭订单
     * @param PayOrderClose $query
     * @param string $ip
     * @return bool
     */
    public function closeOrder(PayOrderClose $query, $ip)
    {
        if (('' == $query->getTradeNo()) && ('' == $query->getOrderId())) {
            return false;
        }

        return true;
    }

    /**
     * 解析支付结果异步通知
     *
     * @param string $string json string
     * @param string $ip
     * @return false | PayNotify
     */
    abstract public function parseNotify($string, $ip);

    /**
     * 解析支付同步返回通知
     * @param string $string json string
     * @param string $ip
     */
    abstract public function parsePayReturnResult($string, $ip);


    /**
     * 支付结果异步通知 回复成功
     * @param PayNotify $notify
     */
    abstract public function notifyReplySuccess(PayNotify $notify);
    /**
     * 支付结果异步通知 回复失败
     * @param PayNotify $notify
     */
    abstract public function notifyReplyFail(PayNotify $notify);
}
