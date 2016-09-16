<?php
namespace Pay\AliPay\Modules;


class AliPayTradeCloseRequestTest extends AliPayRequestTest
{
    /**
     * @var AliPayTradeCloseRequest
     */
    private $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new AliPayTradeCloseRequest();
    }

    /**
     * @return AliPayTradeCloseRequest
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionsIsMutable()
    {
        parent::testOptionsIsMutable();
        $this->getModel()->setNotifyUrl('http://domain.com/CallBack/notify_url.jsp');
        $this->getModel()->setAppAuthToken('appopenBb64d181d0146481ab6a762c00714cC27');

        $this->getModel()->setOutTradeNo('70501111111S001111119');
        $this->getModel()->setTradeNo('2016081121001004630200142207');
        $this->getModel()->setOperatorId('YX01');

        self::assertEquals(AliPayTradeCloseRequest::METHOD, $this->getModel()->getMethod());
        self::assertEquals('http://domain.com/CallBack/notify_url.jsp', $this->getModel()->getNotifyUrl());
        self::assertEquals('appopenBb64d181d0146481ab6a762c00714cC27', $this->getModel()->getAppAuthToken());
        self::assertEquals('70501111111S001111119', $this->getModel()->getOutTradeNo());
        self::assertEquals('2016081121001004630200142207', $this->getModel()->getTradeNo());
        self::assertEquals('YX01', $this->getModel()->getOperatorId());
    }

    public function testBizContent()
    {
        parent::testOptionsIsMutable();
        $this->getModel()->setNotifyUrl('http://domain.com/CallBack/notify_url.jsp');
        $this->getModel()->setAppAuthToken('appopenBb64d181d0146481ab6a762c00714cC27');

        $this->getModel()->setOutTradeNo('70501111111S001111119');
        $this->getModel()->setTradeNo('2016081121001004630200142207');
        $this->getModel()->setOperatorId('YX01');

        $str = '{"trade_no":"2016081121001004630200142207","out_trade_no":"70501111111S001111119","operator_id":"YX01"}';
        self::assertEquals($str, $this->getModel()->getBizContent());
    }
}
