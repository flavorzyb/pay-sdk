<?php
namespace Pay\Modules;


class PayNotifyTest extends AbstractPayTest
{
    /**
     * @var PayNotify
     */
    private $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new PayNotify();
    }

    /**
     * @return PayNotify
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionsIsMutable()
    {
        parent::testOptionsIsMutable();
        $no = date('YmdHis'). mt_rand();
        $this->getModel()->setTradeNo($no);
        $this->getModel()->setStatus('success');

        self::assertEquals($no, $this->getModel()->getTradeNo());
        self::assertEquals('success', $this->getModel()->getStatus());
    }
}
