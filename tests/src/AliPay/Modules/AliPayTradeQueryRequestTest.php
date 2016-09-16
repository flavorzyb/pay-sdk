<?php
namespace Pay\AliPay\Modules;

class AliPayTradeQueryRequestTest extends AliPayRequestTest
{
    /**
     * @var AliPayTradeQueryRequest
     */
    private $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new AliPayTradeQueryRequest();
    }

    /**
     * @return AliPayTradeQueryRequest
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionsIsMutable()
    {
        parent::testOptionsIsMutable();
        self::assertEquals(AliPayTradeQueryRequest::METHOD, $this->getModel()->getMethod());

        $this->getModel()->setOutTradeNo('70501111111S001111119');
        $this->getModel()->setTradeNo('2016081121001004630200142207');

        self::assertEquals('70501111111S001111119', $this->getModel()->getOutTradeNo());
        self::assertEquals('2016081121001004630200142207', $this->getModel()->getTradeNo());
    }


    public function testBizContent()
    {
        parent::testOptionsIsMutable();
        self::assertEquals(AliPayTradeQueryRequest::METHOD, $this->getModel()->getMethod());

        $this->getModel()->setOutTradeNo('70501111111S001111119');
        $this->getModel()->setTradeNo('2016081121001004630200142207');

        self::assertEquals('{"out_trade_no":"70501111111S001111119","trade_no":"2016081121001004630200142207"}', $this->getModel()->getBizContent());
    }

}
