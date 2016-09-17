<?php
namespace Pay\AliPay\Modules;


class AliPayNotifyTest extends \PHPUnit_Framework_TestCase
{
    public function testOptionsIsMutable()
    {
        $result = new AliPayNotify();
        $result->setOutTradeNo('6823789339978248');
        $result->setTradeNo('2013112011001004330000121536');
        $result->setAppId('2014072300007148');
        $result->setBody('当面付交易内容');
        $result->setBuyerId('2088102122524333');

        $result->setBuyerLogonId('15901825620');
        $result->setBuyerPayAmount(13.88);
        $result->setFundBillList([new AliPayTradeFundBill()]);
        $result->setGmtClose('2015-04-29 15:45:57');
        $result->setGmtCreate('2015-04-27 15:45:57');

        $result->setGmtRefund('2015-04-28 15:45:57');
        $result->setInvoiceAmount(10.00);
        $result->setNotifyId('ac05099524730693a8b330c5ecf72da9786');
        $result->setNotifyTime('2015-14-27 15:45:58');
        $result->setNotifyType('trade_status_sync');


        $result->setOutBizNo('HZRF001');
        $result->setSignType('RSA');
        $result->setSign('601510b7970e52cc63db0f44997cf70e');
        $result->setSellerId('2088101106499364');
        $result->setSellerEmail('zhuzhanghu@alitest.com');

        $result->setTradeStatus(new AliPayTradeStatus('TRADE_CLOSED'));
        $result->setTotalAmount(20);
        $result->setReceiptAmount(15);
        $result->setPointAmount(12.00);
        $result->setRefundFee(2.58);

        $result->setSubject('当面付交易');
        $result->setGmtPayment('2015-04-27 15:45:57');

        self::assertEquals('6823789339978248', $result->getOutTradeNo());
        self::assertEquals('2013112011001004330000121536', $result->getTradeNo());
        self::assertEquals('2014072300007148', $result->getAppId());
        self::assertEquals('当面付交易内容', $result->getBody());
        self::assertEquals('2088102122524333', $result->getBuyerId());

        self::assertEquals('15901825620', $result->getBuyerLogonId());
        self::assertEquals(13.88, $result->getBuyerPayAmount());
        self::assertEquals([new AliPayTradeFundBill()], $result->getFundBillList());
        self::assertEquals('2015-04-29 15:45:57', $result->getGmtClose());
        self::assertEquals('2015-04-27 15:45:57', $result->getGmtCreate());

        self::assertEquals('2015-04-28 15:45:57', $result->getGmtRefund());
        self::assertEquals(10.00, $result->getInvoiceAmount());
        self::assertEquals('ac05099524730693a8b330c5ecf72da9786', $result->getNotifyId());
        self::assertEquals('2015-14-27 15:45:58', $result->getNotifyTime());
        self::assertEquals('trade_status_sync', $result->getNotifyType());

        self::assertEquals('HZRF001', $result->getOutBizNo());
        self::assertEquals('RSA', $result->getSignType());
        self::assertEquals('601510b7970e52cc63db0f44997cf70e', $result->getSign());
        self::assertEquals('2088101106499364', $result->getSellerId());
        self::assertEquals('zhuzhanghu@alitest.com', $result->getSellerEmail());

        self::assertEquals('TRADE_CLOSED', $result->getTradeStatus()->getValue());
        self::assertEquals(20, $result->getTotalAmount());
        self::assertEquals(15, $result->getReceiptAmount());
        self::assertEquals(12.00, $result->getPointAmount());
        self::assertEquals(2.58, $result->getRefundFee());

        self::assertEquals('当面付交易', $result->getSubject());
        self::assertEquals('2015-04-27 15:45:57', $result->getGmtPayment());
    }
}
