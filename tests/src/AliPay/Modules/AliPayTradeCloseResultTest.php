<?php
namespace Pay\AliPay\Modules;


class AliPayTradeCloseResultTest extends AliPayResultTest
{
    /**
     * @var AliPayTradeCloseResult
     */
    private $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new AliPayTradeCloseResult();
    }

    /**
     * @return AliPayTradeCloseResult
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionIsMutable()
    {
        parent::testOptionIsMutable();
        $this->getModel()->setTradeNo('2013112611001004680073956707');
        $this->getModel()->setOutTradeNo('HZ0120131127001');

        self::assertEquals('2013112611001004680073956707', $this->getModel()->getTradeNo());
        self::assertEquals('HZ0120131127001', $this->getModel()->getOutTradeNo());
    }
}
