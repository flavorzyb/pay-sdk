<?php
namespace Pay\AliPay\Modules;

class AliPayTradeWapPayRequestTest extends AliPayRequestTest
{
    /**
     * @var AliPayTradeWapPayRequest
     */
    private $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new AliPayTradeWapPayRequest();
    }

    /**
     * @return AliPayTradeWapPayRequest
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionsIsMutable()
    {
        parent::testOptionsIsMutable();
        $this->getModel()->setNotifyUrl('http://domain.com/CallBack/notify_url.jsp');
        $this->getModel()->setReturnUrl('http://domain.com/CallBack/return_url.jsp');
        $this->getModel()->setBody('对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body');
        $this->getModel()->setSubject('大乐透');
        $this->getModel()->setOutTradeNo('70501111111S001111119');
        $this->getModel()->setTimeoutExpress('90m');
        $this->getModel()->setTotalAmount(9.00);
        $this->getModel()->setSellerId('2088123456789012');
        $this->getModel()->setAuthToken('appopenBb64d181d0146481ab6a762c00714cC27');
        $this->getModel()->setProductCode('QUICK_WAP_PAY');

        self::assertEquals('http://domain.com/CallBack/notify_url.jsp', $this->getModel()->getNotifyUrl());
        self::assertEquals('http://domain.com/CallBack/return_url.jsp', $this->getModel()->getReturnUrl());
        self::assertEquals('对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body', $this->getModel()->getBody());
        self::assertEquals('大乐透', $this->getModel()->getSubject());
        self::assertEquals('70501111111S001111119', $this->getModel()->getOutTradeNo());
        self::assertEquals('90m', $this->getModel()->getTimeoutExpress());
        self::assertEquals(9.00, $this->getModel()->getTotalAmount());
        self::assertEquals('2088123456789012', $this->getModel()->getSellerId());
        self::assertEquals('appopenBb64d181d0146481ab6a762c00714cC27', $this->getModel()->getAuthToken());
        self::assertEquals('QUICK_WAP_PAY', $this->getModel()->getProductCode());

        self::assertEquals(AliPayTradeWapPayRequest::METHOD, $this->getModel()->getMethod());
        $str = '{"subject":"\u5927\u4e50\u900f","out_trade_no":"70501111111S001111119","total_amount":9,"product_code":"QUICK_WAP_PAY","body":"\u5bf9\u4e00\u7b14\u4ea4\u6613\u7684\u5177\u4f53\u63cf\u8ff0\u4fe1\u606f\u3002\u5982\u679c\u662f\u591a\u79cd\u5546\u54c1\uff0c\u8bf7\u5c06\u5546\u54c1\u63cf\u8ff0\u5b57\u7b26\u4e32\u7d2f\u52a0\u4f20\u7ed9body","timeout_express":"90m","seller_id":"2088123456789012","auth_token":"appopenBb64d181d0146481ab6a762c00714cC27"}';
        self::assertEquals($str, $this->getModel()->getBizContent());
    }

    public function testBizContent()
    {
        parent::testOptionsIsMutable();
        $this->getModel()->setNotifyUrl('http://domain.com/CallBack/notify_url.jsp');
        $this->getModel()->setReturnUrl('http://domain.com/CallBack/return_url.jsp');
        $this->getModel()->setBody('对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body');
        $this->getModel()->setSubject('大乐透');
        $this->getModel()->setOutTradeNo('70501111111S001111119');
        $this->getModel()->setTimeoutExpress('90m');
        $this->getModel()->setTotalAmount(9.00);
        $this->getModel()->setSellerId('2088123456789012');
        $this->getModel()->setAuthToken('appopenBb64d181d0146481ab6a762c00714cC27');
        $this->getModel()->setProductCode('QUICK_WAP_PAY');

        $str = '{"subject":"\u5927\u4e50\u900f","out_trade_no":"70501111111S001111119","total_amount":9,"product_code":"QUICK_WAP_PAY","body":"\u5bf9\u4e00\u7b14\u4ea4\u6613\u7684\u5177\u4f53\u63cf\u8ff0\u4fe1\u606f\u3002\u5982\u679c\u662f\u591a\u79cd\u5546\u54c1\uff0c\u8bf7\u5c06\u5546\u54c1\u63cf\u8ff0\u5b57\u7b26\u4e32\u7d2f\u52a0\u4f20\u7ed9body","timeout_express":"90m","seller_id":"2088123456789012","auth_token":"appopenBb64d181d0146481ab6a762c00714cC27"}';
        self::assertEquals($str, $this->getModel()->getBizContent());
    }
}
