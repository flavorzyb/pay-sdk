<?php
namespace Pay\Modules;

/**
 * Class AbstractPay
 * 支付抽象类
 * @package Pay\Modules
 */
abstract class AbstractPay
{
    /**
     * 订单号
     * @var string
     */
    private $orderId       = '';
    /**
     * 商品名称
     * @var string
     */
    private $goodsName     = '';
    /**
     * 支付款
     * @var float
     */
    private $payAmount     = 0;
    /**
     * 扩展字段
     * @var string
     */
    private $extra         = '';
    /**
     *  指定支付方式  no_credit--指定不能使用信用卡支付
     * @var LimitPay
     */
    private $limitPay      = null;

    public function __construct()
    {
        $this->limitPay = new LimitPay(LimitPay::NORMAL);
    }

    /**
     * 订单号
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * 订单号
     * @param string $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = trim($orderId);
    }

    /**
     * 商品名称
     * @return string
     */
    public function getGoodsName()
    {
        return $this->goodsName;
    }

    /**
     * 商品名称
     * @param string $goodsName
     */
    public function setGoodsName($goodsName)
    {
        $this->goodsName = trim($goodsName);
    }

    /**
     * 支付款
     * @return int
     */
    public function getPayAmount()
    {
        return $this->payAmount;
    }

    /**
     * 支付款
     * @param float $payAmount
     */
    public function setPayAmount($payAmount)
    {
        $this->payAmount = round(floatval($payAmount), 2);
    }

    /**
     * 扩展字段
     * @return string
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * 扩展字段
     * @param string $extra
     */
    public function setExtra($extra)
    {
        $this->extra = trim($extra);
    }

    /**
     * 指定支付方式
     * @return LimitPay
     */
    public function getLimitPay()
    {
        return $this->limitPay;
    }

    /**
     * 指定支付方式
     * @param LimitPay $limitPay
     */
    public function setLimitPay(LimitPay $limitPay)
    {
        $this->limitPay = $limitPay;
    }
}
