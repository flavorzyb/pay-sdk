<?php
namespace Pay;


use Pay\Modules\PayOrder;
use Pay\Modules\PayOrderClose;
use Pay\Modules\PayOrderQuery;

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
    }

    protected function createOrderQuery()
    {
        $result = new PayOrderQuery();
        $result->setOrderId('2016091721001004360200059782');
        $result->setTradeNo('2016091703060157dc4299104e3');

        return $result;
    }

    public function testOrderQueryVerify()
    {
        $query = new PayOrderQuery();
        self::assertFalse($this->getPay()->orderQuery($query, '127.0.0.1'));
    }

    protected function createCloseQuery()
    {
        $result = new PayOrderClose();
        $result->setOrderId('2016091721001004360200059782');
        $result->setTradeNo('2016091703060157dc4299104e3');
        return $result;
    }

    public function testOrderCloseVerify()
    {
        $query = new PayOrderClose();
        self::assertFalse($this->getPay()->closeOrder($query, '127.0.0.1'));
    }
}
