<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/7/22
 * Time: 上午10:28
 */

namespace Apps\Pay;


use Apps\Common\Log;
use Apps\Pay\Model\PayOrderModel;

abstract class PayAbstract
{

    /**
     * 生成支付的url
     * @param PayOrderModel $payOrder
     * @return string
     */
    abstract protected function _payUrl(PayOrderModel $payOrder);

    /**
     * 支付
     * @param PayOrderModel $payOrder
     */
    public function pay(PayOrderModel $payOrder)
    {
        $url = $this->payUrl($payOrder);
        Log::pay("pay url:".$url);
        if ('' != $url) {
            header('Location:' . $url);
            exit();
        }
    }

    /**
     * 支付URL
     * @param PayOrderModel $payOrder
     * @return string
     */
    public function payUrl(PayOrderModel $payOrder)
    {
        if (!$this->check($payOrder)) {
            return '';
        }

        return $this->_payUrl($payOrder);
    }

    /**
     * 检查
     * @param PayOrderModel $payOrder
     * @return bool
     */
    protected function check(PayOrderModel $payOrder)
    {
        if ('' == $payOrder->getOrderId()) {
            Log::pay("error: 订单ID不能为空");
            return false;
        }

        if ('' == $payOrder->getGoodsName()) {
            Log::pay("error: 商品名不能为空");
            return false;
        }

        if (0 >= $payOrder->getPayAmount()) {
            Log::pay("error: 支付款不能为0");
            return false;
        }

        if (false !== stripos($payOrder->getExtra(), '|')) {
            Log::pay("error: 扩展字段不能包含|字符");
            return false;
        }

        return true;
    }
}
