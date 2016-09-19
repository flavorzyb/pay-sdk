<?php
namespace Pay\Modules;

class PayOrderQueryResultTest extends PayOrderQueryTest
{
    public function testOptionIsMutable()
    {
        parent::testOptionIsMutable();

        $result = new PayOrderQueryResult();
        $result->setTotalAmount(100.21);
        $result->setReceiptAmount(100.10);
        $result->setTradeStatus(PayTradeStatus::createSuccessStatus());

        self::assertEquals(100.21, $result->getTotalAmount());
        self::assertEquals(100.10, $result->getReceiptAmount());
        self::assertEquals(PayTradeStatus::SUCCESS, $result->getTradeStatus()->getValue());
    }
}
