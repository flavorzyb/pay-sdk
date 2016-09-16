<?php
namespace Pay\AliPay\Modules;

class AliPayTradeStatusTest extends \PHPUnit_Framework_TestCase
{
    public function testTradeStatus()
    {
        $result = new AliPayTradeStatus(AliPayTradeStatus::WAIT_BUYER_PAY);
        self::assertEquals(AliPayTradeStatus::WAIT_BUYER_PAY, $result->getValue());
        self::assertTrue($result->isWaitBuyerPay());
        self::assertFalse($result->isOthers());
        self::assertFalse($result->isTradeClosed());
        self::assertFalse($result->isTradeFinished());
        self::assertFalse($result->isTradeSuccess());

        $result = new AliPayTradeStatus(AliPayTradeStatus::TRADE_FINISHED);
        self::assertEquals(AliPayTradeStatus::TRADE_FINISHED, $result->getValue());
        self::assertFalse($result->isWaitBuyerPay());
        self::assertFalse($result->isOthers());
        self::assertFalse($result->isTradeClosed());
        self::assertTrue($result->isTradeFinished());
        self::assertFalse($result->isTradeSuccess());

        $result = new AliPayTradeStatus(AliPayTradeStatus::TRADE_CLOSED);
        self::assertEquals(AliPayTradeStatus::TRADE_CLOSED, $result->getValue());
        self::assertFalse($result->isWaitBuyerPay());
        self::assertFalse($result->isOthers());
        self::assertTrue($result->isTradeClosed());
        self::assertFalse($result->isTradeFinished());
        self::assertFalse($result->isTradeSuccess());

        $result = new AliPayTradeStatus(AliPayTradeStatus::TRADE_SUCCESS);
        self::assertEquals(AliPayTradeStatus::TRADE_SUCCESS, $result->getValue());
        self::assertFalse($result->isWaitBuyerPay());
        self::assertFalse($result->isOthers());
        self::assertFalse($result->isTradeClosed());
        self::assertFalse($result->isTradeFinished());
        self::assertTrue($result->isTradeSuccess());

        $result = new AliPayTradeStatus(AliPayTradeStatus::OTHERS);
        self::assertEquals(AliPayTradeStatus::OTHERS, $result->getValue());
        self::assertFalse($result->isWaitBuyerPay());
        self::assertTrue($result->isOthers());
        self::assertFalse($result->isTradeClosed());
        self::assertFalse($result->isTradeFinished());
        self::assertFalse($result->isTradeSuccess());

        $result = new AliPayTradeStatus('error');
        self::assertEquals(AliPayTradeStatus::OTHERS, $result->getValue());
        self::assertFalse($result->isWaitBuyerPay());
        self::assertTrue($result->isOthers());
        self::assertFalse($result->isTradeClosed());
        self::assertFalse($result->isTradeFinished());
        self::assertFalse($result->isTradeSuccess());
    }
}
