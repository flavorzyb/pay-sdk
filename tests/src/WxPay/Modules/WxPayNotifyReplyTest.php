<?php
namespace Pay\WxPay\Modules;

class WxPayNotifyReplyTest extends WxPayDataBaseTest
{
    /**
     * @var WxPayNotifyReply
     */
    protected $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new WxPayNotifyReply();
    }

    /**
     * @return WxPayNotifyReply
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionsIsMutable()
    {
        parent::testOptionsIsMutable();

        $this->getModel()->setReturnCode('FAIL');
        $this->getModel()->setReturnMsg('签名失败');
        $this->getModel()->setData('msg', 'aaaaaa');

        self::assertEquals('FAIL', $this->getModel()->getReturnCode());
        self::assertEquals('签名失败', $this->getModel()->getReturnMsg());
    }
}
