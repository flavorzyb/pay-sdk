<?php
namespace Pay\AliPay\Modules;


class AliPayTradeRefundQueryRequestTest extends AliPayRequestTest
{
    /**
     * @var AliPayTradeRefundQueryRequest
     */
    private $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new AliPayTradeRefundQueryRequest();
    }

    /**
     * @return AliPayTradeRefundQueryRequest
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionsIsMutable()
    {
        parent::testOptionsIsMutable();
        self::assertEquals(AliPayTradeRefundQueryRequest::METHOD, $this->getModel()->getMethod());

        $this->getModel()->setOutTradeNo('70501111111S001111119');
        $this->getModel()->setTradeNo('2016081121001004630200142207');
        $this->getModel()->setAppAuthToken('appopenBb64d181d0146481ab6a762c00714cC27');
        $this->getModel()->setOutRequestNo('HZ01RF001');

        self::assertEquals('70501111111S001111119', $this->getModel()->getOutTradeNo());
        self::assertEquals('2016081121001004630200142207', $this->getModel()->getTradeNo());
        self::assertEquals('appopenBb64d181d0146481ab6a762c00714cC27', $this->getModel()->getAppAuthToken());
        self::assertEquals('HZ01RF001', $this->getModel()->getOutRequestNo());
    }


    public function testBizContent()
    {
        parent::testOptionsIsMutable();

        $this->getModel()->setOutTradeNo('70501111111S001111119');
        $this->getModel()->setTradeNo('2016081121001004630200142207');
        $this->getModel()->setAppAuthToken('appopenBb64d181d0146481ab6a762c00714cC27');
        $this->getModel()->setOutRequestNo('HZ01RF001');

        self::assertEquals('{"out_request_no":"HZ01RF001","trade_no":"2016081121001004630200142207","out_trade_no":"70501111111S001111119"}', $this->getModel()->getBizContent());
    }
}
