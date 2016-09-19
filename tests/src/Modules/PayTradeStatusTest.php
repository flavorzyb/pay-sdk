<?php
namespace Pay\Modules;

class PayTradeStatusTest extends \PHPUnit_Framework_TestCase
{
    public function testStatus()
    {
        $result = PayTradeStatus::createSuccessStatus();
        self::assertEquals(PayTradeStatus::SUCCESS, $result->getValue());
        self::assertTrue($result->isSuccess());
        self::assertFalse($result->isClosed());
        self::assertFalse($result->isFinished());
        self::assertFalse($result->isNotPay());
        self::assertFalse($result->isOthers());

        $result = PayTradeStatus::createClosedStatus();
        self::assertEquals(PayTradeStatus::CLOSED, $result->getValue());
        self::assertFalse($result->isSuccess());
        self::assertTrue($result->isClosed());
        self::assertFalse($result->isFinished());
        self::assertFalse($result->isNotPay());
        self::assertFalse($result->isOthers());

        $result = PayTradeStatus::createFinishStatus();
        self::assertEquals(PayTradeStatus::FINISHED, $result->getValue());
        self::assertFalse($result->isSuccess());
        self::assertFalse($result->isClosed());
        self::assertTrue($result->isFinished());
        self::assertFalse($result->isNotPay());
        self::assertFalse($result->isOthers());

        $result = PayTradeStatus::createNotPayStatus();
        self::assertEquals(PayTradeStatus::NOTPAY, $result->getValue());
        self::assertFalse($result->isSuccess());
        self::assertFalse($result->isClosed());
        self::assertFalse($result->isFinished());
        self::assertTrue($result->isNotPay());
        self::assertFalse($result->isOthers());

        $result = PayTradeStatus::createOthersStatus();
        self::assertEquals(PayTradeStatus::OTHERS, $result->getValue());
        self::assertFalse($result->isSuccess());
        self::assertFalse($result->isClosed());
        self::assertFalse($result->isFinished());
        self::assertFalse($result->isNotPay());
        self::assertTrue($result->isOthers());
    }
}
