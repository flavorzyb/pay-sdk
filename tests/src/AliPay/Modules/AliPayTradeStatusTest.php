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
        self::assertFalse($result->isClosed());
        self::assertFalse($result->isFinished());
        self::assertFalse($result->isSuccess());

        $result = new AliPayTradeStatus(AliPayTradeStatus::TRADE_FINISHED);
        self::assertEquals(AliPayTradeStatus::TRADE_FINISHED, $result->getValue());
        self::assertFalse($result->isWaitBuyerPay());
        self::assertFalse($result->isOthers());
        self::assertFalse($result->isClosed());
        self::assertTrue($result->isFinished());
        self::assertFalse($result->isSuccess());

        $result = new AliPayTradeStatus(AliPayTradeStatus::TRADE_CLOSED);
        self::assertEquals(AliPayTradeStatus::TRADE_CLOSED, $result->getValue());
        self::assertFalse($result->isWaitBuyerPay());
        self::assertFalse($result->isOthers());
        self::assertTrue($result->isClosed());
        self::assertFalse($result->isFinished());
        self::assertFalse($result->isSuccess());

        $result = new AliPayTradeStatus(AliPayTradeStatus::TRADE_SUCCESS);
        self::assertEquals(AliPayTradeStatus::TRADE_SUCCESS, $result->getValue());
        self::assertFalse($result->isWaitBuyerPay());
        self::assertFalse($result->isOthers());
        self::assertFalse($result->isClosed());
        self::assertFalse($result->isFinished());
        self::assertTrue($result->isSuccess());

        $result = new AliPayTradeStatus(AliPayTradeStatus::OTHERS);
        self::assertEquals(AliPayTradeStatus::OTHERS, $result->getValue());
        self::assertFalse($result->isWaitBuyerPay());
        self::assertTrue($result->isOthers());
        self::assertFalse($result->isClosed());
        self::assertFalse($result->isFinished());
        self::assertFalse($result->isSuccess());

        $result = new AliPayTradeStatus('error');
        self::assertEquals(AliPayTradeStatus::OTHERS, $result->getValue());
        self::assertFalse($result->isWaitBuyerPay());
        self::assertTrue($result->isOthers());
        self::assertFalse($result->isClosed());
        self::assertFalse($result->isFinished());
        self::assertFalse($result->isSuccess());
    }
}
