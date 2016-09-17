<?php
namespace Pay;


use Pay\Modules\PayOrder;

abstract class PayAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return PayAbstract
     */
    abstract protected function getPay();
    public function testPayVerify()
    {
        $order = new PayOrder();
        self::assertFalse($this->getPay()->pay($order, '127.0.0.1'));

        $order->setOrderId('1415659990');
        self::assertFalse($this->getPay()->pay($order, '127.0.0.1'));

        $order->setGoodsName('苹果手机"');
        self::assertFalse($this->getPay()->pay($order, '127.0.0.1'));

        $order->setPayAmount(11);
        self::assertFalse($this->getPay()->pay($order, '127.0.0.1'));
    }
}
