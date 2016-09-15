<?php
namespace Pay\WxPay\Modules;


class WxPayTradeStateTest extends \PHPUnit_Framework_TestCase
{
    public function testTradeState()
    {
        $result = new WxPayTradeState(WxPayTradeState::SUCCESS);
        self::assertEquals(WxPayTradeState::SUCCESS, $result->getValue());
        self::assertTrue($result->isSuccess());
        self::assertFalse($result->isClosed());
        self::assertFalse($result->isNotPay());
        self::assertFalse($result->isOthers());
        self::assertFalse($result->isPayError());
        self::assertFalse($result->isRefund());
        self::assertFalse($result->isRevoked());
        self::assertFalse($result->isUserPaying());

        $result = new WxPayTradeState(WxPayTradeState::CLOSED);
        self::assertEquals(WxPayTradeState::CLOSED, $result->getValue());
        self::assertFalse($result->isSuccess());
        self::assertTrue($result->isClosed());
        self::assertFalse($result->isNotPay());
        self::assertFalse($result->isOthers());
        self::assertFalse($result->isPayError());
        self::assertFalse($result->isRefund());
        self::assertFalse($result->isRevoked());
        self::assertFalse($result->isUserPaying());

        $result = new WxPayTradeState(WxPayTradeState::NOTPAY);
        self::assertEquals(WxPayTradeState::NOTPAY, $result->getValue());
        self::assertFalse($result->isSuccess());
        self::assertFalse($result->isClosed());
        self::assertTrue($result->isNotPay());
        self::assertFalse($result->isOthers());
        self::assertFalse($result->isPayError());
        self::assertFalse($result->isRefund());
        self::assertFalse($result->isRevoked());
        self::assertFalse($result->isUserPaying());

        $result = new WxPayTradeState(WxPayTradeState::OTHERS);
        self::assertEquals(WxPayTradeState::OTHERS, $result->getValue());
        self::assertFalse($result->isSuccess());
        self::assertFalse($result->isClosed());
        self::assertFalse($result->isNotPay());
        self::assertTrue($result->isOthers());
        self::assertFalse($result->isPayError());
        self::assertFalse($result->isRefund());
        self::assertFalse($result->isRevoked());
        self::assertFalse($result->isUserPaying());

        $result = new WxPayTradeState(WxPayTradeState::PAYERROR);
        self::assertEquals(WxPayTradeState::PAYERROR, $result->getValue());
        self::assertFalse($result->isSuccess());
        self::assertFalse($result->isClosed());
        self::assertFalse($result->isNotPay());
        self::assertFalse($result->isOthers());
        self::assertTrue($result->isPayError());
        self::assertFalse($result->isRefund());
        self::assertFalse($result->isRevoked());
        self::assertFalse($result->isUserPaying());

        $result = new WxPayTradeState(WxPayTradeState::REFUND);
        self::assertEquals(WxPayTradeState::REFUND, $result->getValue());
        self::assertFalse($result->isSuccess());
        self::assertFalse($result->isClosed());
        self::assertFalse($result->isNotPay());
        self::assertFalse($result->isOthers());
        self::assertFalse($result->isPayError());
        self::assertTrue($result->isRefund());
        self::assertFalse($result->isRevoked());
        self::assertFalse($result->isUserPaying());

        $result = new WxPayTradeState(WxPayTradeState::REVOKED);
        self::assertEquals(WxPayTradeState::REVOKED, $result->getValue());
        self::assertFalse($result->isSuccess());
        self::assertFalse($result->isClosed());
        self::assertFalse($result->isNotPay());
        self::assertFalse($result->isOthers());
        self::assertFalse($result->isPayError());
        self::assertFalse($result->isRefund());
        self::assertTrue($result->isRevoked());
        self::assertFalse($result->isUserPaying());

        $result = new WxPayTradeState(WxPayTradeState::USERPAYING);
        self::assertEquals(WxPayTradeState::USERPAYING, $result->getValue());
        self::assertFalse($result->isSuccess());
        self::assertFalse($result->isClosed());
        self::assertFalse($result->isNotPay());
        self::assertFalse($result->isOthers());
        self::assertFalse($result->isPayError());
        self::assertFalse($result->isRefund());
        self::assertFalse($result->isRevoked());
        self::assertTrue($result->isUserPaying());
    }
}
