<?php
namespace Pay\Modules;

class PayOrderQueryTest extends \PHPUnit_Framework_TestCase
{
    public function testOptionIsMutable()
    {
        $result = new PayOrderQuery();
        $result->setTradeNo('1009660380201506130728806387');
        $result->setOrderId('20150806125346');

        self::assertEquals('20150806125346', $result->getOrderId());
        self::assertEquals('1009660380201506130728806387', $result->getTradeNo());
    }
}
