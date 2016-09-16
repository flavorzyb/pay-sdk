<?php
namespace Pay\AliPay\Modules;

class AliPayTradeQueryResultTest extends AliPayResultTest
{
    /**
     * @var AliPayTradeQueryResult
     */
    private $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new AliPayTradeQueryResult();
    }

    /**
     * @return AliPayTradeQueryResult
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
        $this->getModel()->setTradeStatus(new AliPayTradeStatus('TRADE_CLOSED'));
        $this->getModel()->setTotalAmount(10.82);

        $this->getModel()->setReceiptAmount(12.81);
        $this->getModel()->setBuyerPayAmount(13.81);
        $this->getModel()->setPointAmount(13.01);
        $this->getModel()->setInvoiceAmount(12.11);
        $this->getModel()->setSendPayDate('2014-11-27 15:45:57');

        $this->getModel()->setAlipayStoreId('2015040900077001000100001232');
        $this->getModel()->setStoreId('NJ_S_001');
        $this->getModel()->setTerminalId('NJ_T_001');
        $this->getModel()->setStoreName('证大五道口店');
        $this->getModel()->setBuyerUserId('2088101117955611');

        $this->getModel()->setDiscountGoodsDetail('[{"goods_id":"STANDARD1026181538","goods_name":"雪碧","discount_amount":"100.00","voucher_id":"2015102600073002039000002D5O"}]');
        $this->getModel()->setIndustrySepcDetail('{"registration_order_pay":{"brlx":"1","cblx":"1"}}');
        $this->getModel()->setFundBillList([new AliPayTradeFundBill()]);

        self::assertEquals('2013112011001004330000121536', $this->getModel()->getTradeNo());
        self::assertEquals('6823789339978248', $this->getModel()->getOutTradeNo());
        self::assertEquals('159****5620', $this->getModel()->getBuyerLogonId());
        self::assertEquals('TRADE_CLOSED', $this->getModel()->getTradeStatus()->getValue());
        self::assertEquals(10.82, $this->getModel()->getTotalAmount());

        self::assertEquals(12.81, $this->getModel()->getReceiptAmount());
        self::assertEquals(13.81, $this->getModel()->getBuyerPayAmount());
        self::assertEquals(13.01, $this->getModel()->getPointAmount());
        self::assertEquals(12.11, $this->getModel()->getInvoiceAmount());
        self::assertEquals('2014-11-27 15:45:57', $this->getModel()->getSendPayDate());

        self::assertEquals('2015040900077001000100001232', $this->getModel()->getAlipayStoreId());
        self::assertEquals('NJ_S_001', $this->getModel()->getStoreId());
        self::assertEquals('NJ_T_001', $this->getModel()->getTerminalId());
        self::assertEquals('证大五道口店', $this->getModel()->getStoreName());
        self::assertEquals('2088101117955611', $this->getModel()->getBuyerUserId());

        self::assertEquals('[{"goods_id":"STANDARD1026181538","goods_name":"雪碧","discount_amount":"100.00","voucher_id":"2015102600073002039000002D5O"}]', $this->getModel()->getDiscountGoodsDetail());
        self::assertEquals('{"registration_order_pay":{"brlx":"1","cblx":"1"}}', $this->getModel()->getIndustrySepcDetail());
        self::assertEquals([new AliPayTradeFundBill()], $this->getModel()->getFundBillList());
    }
}
