<?php
namespace Pay\AliPay\Modules;


class AliPayTradeRefundQueryResultTest extends AliPayResultTest
{
    /**
     * @var AliPayTradeRefundQueryResult
     */
    private $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new AliPayTradeRefundQueryResult();
    }

    /**
     * @return AliPayTradeRefundQueryResult
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionIsMutable()
    {
        parent::testOptionIsMutable();
        $this->getModel()->setTradeNo('2013112011001004330000121536');
        $this->getModel()->setOutTradeNo('6823789339978248');
        $this->getModel()->setOutRequestNo('20150320010101001');
        $this->getModel()->setRefundReason('用户退款请求');
        $this->getModel()->setTotalAmount(100.20);
        $this->getModel()->setRefundAmount(12.33);

        self::assertEquals('2013112011001004330000121536', $this->getModel()->getTradeNo());
        self::assertEquals('6823789339978248', $this->getModel()->getOutTradeNo());
        self::assertEquals('20150320010101001', $this->getModel()->getOutRequestNo());
        self::assertEquals('用户退款请求', $this->getModel()->getRefundReason());
        self::assertEquals(100.20, $this->getModel()->getTotalAmount());
        self::assertEquals(12.33, $this->getModel()->getRefundAmount());

    }
}
