<?php
namespace Pay\AliPay\Modules;

class AliPayTradeRefundResultTest extends AliPayResultTest
{
    /**
     * @var AliPayTradeRefundResult
     */
    private $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new AliPayTradeRefundResult();
    }

    /**
     * @return AliPayTradeRefundResult
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
        $this->getModel()->setBuyerLogonId('159****5620');
        $this->getModel()->setStoreName('证大五道口店');
        $this->getModel()->setBuyerUserId('2088101117955611');

        $this->getModel()->setFundChange('Y');
        $this->getModel()->setRefundFee(9.22);
        $this->getModel()->setGmtRefundPay('2014-11-27 15:45:57');
        $this->getModel()->setSendBackFee(10.22);
        $this->getModel()->setRefundDetailItemList([new AliPayTradeFundBill()]);

        self::assertEquals('2013112011001004330000121536', $this->getModel()->getTradeNo());
        self::assertEquals('6823789339978248', $this->getModel()->getOutTradeNo());
        self::assertEquals('159****5620', $this->getModel()->getBuyerLogonId());
        self::assertEquals('证大五道口店', $this->getModel()->getStoreName());
        self::assertEquals('2088101117955611', $this->getModel()->getBuyerUserId());

        self::assertEquals('Y', $this->getModel()->getFundChange());
        self::assertEquals(9.22, $this->getModel()->getRefundFee());
        self::assertEquals('2014-11-27 15:45:57', $this->getModel()->getGmtRefundPay());
        self::assertEquals(10.22, $this->getModel()->getSendBackFee());
        self::assertEquals([new AliPayTradeFundBill()], $this->getModel()->getRefundDetailItemList());
    }
}
