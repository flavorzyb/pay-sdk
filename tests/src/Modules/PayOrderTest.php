<?php
namespace Pay\Modules;

class PayOrderTest extends AbstractPayTest
{
    /**
     * @var PayOrder
     */
    private $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new PayOrder();
    }

    /**
     * @return PayOrder
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionsIsMutable()
    {
        parent::testOptionsIsMutable();
        $this->getModel()->setNotifyUrl('http://www.notify.com');
        $this->getModel()->setCallBackUrl('http://www.callback.com');
        $this->getModel()->setMerchantUrl('http://www.merchant.com');
        $this->getModel()->setIp('127.0.0.1');

        self::assertEquals('http://www.notify.com', $this->getModel()->getNotifyUrl());
        self::assertEquals('http://www.callback.com', $this->getModel()->getCallBackUrl());
        self::assertEquals('http://www.merchant.com', $this->getModel()->getMerchantUrl());
        self::assertEquals('127.0.0.1', $this->getModel()->getIp());
    }
}
