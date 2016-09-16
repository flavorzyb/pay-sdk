<?php
namespace Pay\AliPay\Modules;

class AliPayTradeWapPayResultTest extends AliPayBaseTest
{

    /**
     * @var AliPayTradeWapPayResult
     */
    protected $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new AliPayTradeWapPayResult();
    }

    /**
     * @return AliPayTradeWapPayResult
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionsIsMutable()
    {
        parent::testOptionsIsMutable();
        self::assertEquals(AliPayTradeWapPayResult::METHOD, $this->getModel()->getMethod());

        $this->getModel()->setOutTradeNo('70501111111S001111119');
        $this->getModel()->setTradeNo('2016081121001004630200142207');
        $this->getModel()->setTotalAmount(9.00);
        $this->getModel()->setSellerId('2088111111116894');

        self::assertEquals('70501111111S001111119', $this->getModel()->getOutTradeNo());
        self::assertEquals('2016081121001004630200142207', $this->getModel()->getTradeNo());
        self::assertEquals(9.00, $this->getModel()->getTotalAmount());
        self::assertEquals('2088111111116894', $this->getModel()->getSellerId());
    }
}
