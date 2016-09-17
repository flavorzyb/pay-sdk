<?php
namespace Pay\Modules;

abstract class AbstractPayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return AbstractPay
     */
    abstract protected function getModel();

    public function testOptionsIsMutable()
    {
        $orderId = date('Ymdhis').mt_rand();
        $this->getModel()->setOrderId($orderId);
        $this->getModel()->setGoodsName('goods');
        $this->getModel()->setPayAmount(10.10);
        $this->getModel()->setExtra('extra');
        $this->getModel()->setLimitPay(new LimitPay(LimitPay::NO_CREDIT));
        $this->getModel()->setTimeoutExpress(3600);

        self::assertEquals($orderId, $this->getModel()->getOrderId());
        self::assertEquals('goods', $this->getModel()->getGoodsName());
        self::assertEquals(10.10, $this->getModel()->getPayAmount());
        self::assertEquals('extra', $this->getModel()->getExtra());
        self::assertEquals(LimitPay::NO_CREDIT, $this->getModel()->getLimitPay()->getValue());
        self::assertEquals(3600, $this->getModel()->getTimeoutExpress());
    }
}
