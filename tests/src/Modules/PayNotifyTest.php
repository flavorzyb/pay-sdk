<?php
namespace Pay\Modules;


class PayNotifyTest extends PayOrderQueryResultTest
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
        parent::testOptionIsMutable();
    }
}
