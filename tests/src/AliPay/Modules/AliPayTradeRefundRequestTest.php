<?php
namespace Pay\AliPay\Modules;

class AliPayTradeRefundRequestTest extends AliPayRequestTest
{
    /**
     * @var AliPayTradeRefundRequest
     */
    private $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new AliPayTradeRefundRequest();
    }

    /**
     * @return AliPayTradeRefundRequest
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionsIsMutable()
    {
        parent::testOptionsIsMutable();
        self::assertEquals(AliPayTradeRefundRequest::METHOD, $this->getModel()->getMethod());

        $this->getModel()->setOutTradeNo('70501111111S001111119');
        $this->getModel()->setTradeNo('2016081121001004630200142207');
        $this->getModel()->setAppAuthToken('appopenBb64d181d0146481ab6a762c00714cC27');
        $this->getModel()->setOperatorId('OP001');
        $this->getModel()->setRefundAmount(9.72);
        $this->getModel()->setRefundReason('正常退款');
        $this->getModel()->setOutRequestNo('HZ01RF001');
        $this->getModel()->setStoreId('NJ_S_001');
        $this->getModel()->setTerminalId('NJ_T_001');

        self::assertEquals('70501111111S001111119', $this->getModel()->getOutTradeNo());
        self::assertEquals('2016081121001004630200142207', $this->getModel()->getTradeNo());
        self::assertEquals('appopenBb64d181d0146481ab6a762c00714cC27', $this->getModel()->getAppAuthToken());
        self::assertEquals('OP001', $this->getModel()->getOperatorId());
        self::assertEquals(9.72, $this->getModel()->getRefundAmount());
        self::assertEquals('正常退款', $this->getModel()->getRefundReason());
        self::assertEquals('HZ01RF001', $this->getModel()->getOutRequestNo());
        self::assertEquals('NJ_S_001', $this->getModel()->getStoreId());
        self::assertEquals('NJ_T_001', $this->getModel()->getTerminalId());
    }


    public function testBizContent()
    {
        parent::testOptionsIsMutable();

        $this->getModel()->setOutTradeNo('70501111111S001111119');
        $this->getModel()->setTradeNo('2016081121001004630200142207');
        $this->getModel()->setAppAuthToken('appopenBb64d181d0146481ab6a762c00714cC27');
        $this->getModel()->setOperatorId('OP001');
        $this->getModel()->setRefundAmount(9.72);
        $this->getModel()->setRefundReason('正常退款');
        $this->getModel()->setOutRequestNo('HZ01RF001');
        $this->getModel()->setStoreId('NJ_S_001');
        $this->getModel()->setTerminalId('NJ_T_001');

        $str = '{"refund_amount":9.72,"trade_no":"2016081121001004630200142207","out_trade_no":"70501111111S001111119","refund_reason":"\u6b63\u5e38\u9000\u6b3e","out_request_no":"HZ01RF001","store_id":"NJ_S_001","terminal_id":"NJ_T_001","operator_id":"OP001"}';
        self::assertEquals($str, $this->getModel()->getBizContent());
    }
}
