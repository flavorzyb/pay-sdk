<?php
namespace Pay\WxPay\Modules;

class WxPayUnifiedOrderTest extends WxPayDataBaseTest
{
    /**
     * @var WxPayUnifiedOrder
     */
    protected $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new WxPayUnifiedOrder();
    }

    /**
     * @return WxPayUnifiedOrder
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionsIsMutable()
    {
        $start = time();
        $end = time() + 3600;
        parent::testOptionsIsMutable();
        $detail = '{"goods_detail":[{"goods_id":"iphone6s_16G","wxpay_goods_id":"1001","goods_name":"iPhone6s 16G","goods_num":1,"price":528800,"goods_category":"123456","body":"苹果手机"},{"goods_id":"iphone6s_32G","wxpay_goods_id":"1002","goods_name":"iPhone6s 32G","quantity":1,"price":608800,"goods_category":"123789","body":"苹果手机"}]}';
        $this->getModel()->setAppId('wx71be479776815a2a');
        $this->getModel()->setMchId('10000100');
        $this->getModel()->setNonceStr('Vz6WsT7xm6iwJyls');
        $this->getModel()->setDeviceInfo('013467007045764');
        $this->getModel()->setOutTradeNo('1415659990');
        $this->getModel()->setBody('腾讯充值中心-QQ会员充值');
        $this->getModel()->setDetail($detail);
        $this->getModel()->setFeeType('CNY');
        $this->getModel()->setLimitPay('no_credit');
        $this->getModel()->setOpenId('oUpF8uMuAJO_M2pxb1Q9zNjWeS6o');
        $this->getModel()->setProductId('12235413214070356458058');
        $this->getModel()->setTradeType('NATIVE');
        $this->getModel()->setTotalFee(888);
        $this->getModel()->setTimeStart($start);
        $this->getModel()->setTimeExpire($end);
        $this->getModel()->setSpbillCreateIp('127.0.0.1');
        $this->getModel()->setNotifyUrl('http://www.weixin.qq.com/wxpay/pay.php');
        $this->getModel()->setAttach('深圳分店');
        $this->getModel()->setGoodsTag('WXG');

        self::assertEquals('wx71be479776815a2a', $this->getModel()->getAppId());
        self::assertEquals('10000100', $this->getModel()->getMchId());
        self::assertEquals('Vz6WsT7xm6iwJyls', $this->getModel()->getNonceStr());
        self::assertEquals('013467007045764', $this->getModel()->getDeviceInfo());
        self::assertEquals('1415659990', $this->getModel()->getOutTradeNo());
        self::assertEquals('腾讯充值中心-QQ会员充值', $this->getModel()->getBody());
        self::assertEquals($detail, $this->getModel()->getDetail());
        self::assertEquals('CNY', $this->getModel()->getFeeType());
        self::assertEquals('no_credit', $this->getModel()->getLimitPay());
        self::assertEquals('oUpF8uMuAJO_M2pxb1Q9zNjWeS6o', $this->getModel()->getOpenId());
        self::assertEquals('12235413214070356458058', $this->getModel()->getProductId());
        self::assertEquals('NATIVE', $this->getModel()->getTradeType());
        self::assertEquals(888, $this->getModel()->getTotalFee());
        self::assertEquals($start, $this->getModel()->getTimeStart());
        self::assertEquals($end, $this->getModel()->getTimeExpire());
        self::assertEquals('127.0.0.1', $this->getModel()->getSpbillCreateIp());
        self::assertEquals('http://www.weixin.qq.com/wxpay/pay.php', $this->getModel()->getNotifyUrl());
        self::assertEquals('深圳分店', $this->getModel()->getAttach());
        self::assertEquals('WXG', $this->getModel()->getGoodsTag());
    }
}
