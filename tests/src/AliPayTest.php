<?php
namespace Pay;

use ConfigFactory;
use Mockery as m;
use Pay\AliPay\AliPayNotify;
use Pay\AliPay\AliPaySubmit;
use Pay\Modules\PayNotify;
use Pay\Modules\PayOrder;
use Pay\Modules\LimitPay;

class AliPayMock extends AliPay
{
    /**
     * @var AliPaySubmit
     */
    protected $aliPaySubmit = null;

    /**
     * @var AliPayNotify
     */
    protected $aliPayNotify = null;
    public function createAliPayNotify()
    {
        return $this->aliPayNotify;
    }

    public function setAliPayNotify($aliPayNotify)
    {
        $this->aliPayNotify = $aliPayNotify;
    }

    public function _createAliPayNotify()
    {
        return parent::createAliPayNotify();
    }

    public function createAliPaySubmit()
    {
        return $this->aliPaySubmit;
    }

    public function setAliPaySubmit($aliPaySubmit)
    {
        $this->aliPaySubmit = $aliPaySubmit;
    }

    public function _createAliPaySubmit()
    {
        return parent::createAliPaySubmit();
    }
}

class AliPayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AliPayMock
     */
    protected $pay = null;

    protected function setUp()
    {
        parent::setUp();
        $config = ConfigFactory::createAliPayConfig();
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $writer->shouldReceive('info')->andReturn(true);

        $this->pay = new AliPayMock($config, $writer);
    }

    protected function createAliPayNotify()
    {
        $result = m::mock('Pay\AliPay\AliPayNotify');
        return $result;
    }

    protected function createAliPaySubmit()
    {
        $result = m::mock('Pay\AliPay\AliPaySubmit');
        return $result;
    }

    protected function getNotifyData()
    {
        $result = [];
        $result['service'] = 'alipay.wap.trade.create.direct';
        $result['sign'] = 'Rw/y4ROnNicXhaj287Fiw5pvP6viSyg53H3iNiJ61D3YVi7zGniG2680pZv6rakMCeXX++q9XRLw8Rj6I1//qHrwMAHS1hViNW6hQYsh2TqemuL/xjXRCY3vjm1HCoZOUa5zF2jU09yG23MsMIUx2FAWCL/rgbcQcOjLe5FugTc=';
        $result['v'] = '1.0';
        $result['sec_id'] = '0001';
        $result['notify_data'] = '<notify><payment_type>1</payment_type><subject>收银台【1283134629741】</subject><trade_no>2010083000136835</trade_no><buyer_email>dinglang@a.com</buyer_email><gmt_create>2010-08-30 10:17:24</gmt_create><notify_type>trade_status_sync</notify_type><quantity>1</quantity><out_trade_no>1283134629741</out_trade_no><notify_time>2010-08-30 10:18:15</notify_time><seller_id>2088101000137799</seller_id><trade_status>TRADE_FINISHED</trade_status><is_total_fee_adjust>N</is_total_fee_adjust><total_fee>1.00</total_fee><gmt_payment>2010-08-30 10:18:26</gmt_payment><seller_email>chenf003@yahoo.cn</seller_email><gmt_close>2010-08-30 10:18:26</gmt_close><price>1.00</price><buyer_id>2088102001172352</buyer_id><notify_id>509ad84678759176212c247c46bec05303</notify_id><use_coupon>N</use_coupon></notify>';

        return $result;
    }

    public function testGetAliPayNotifyAndGetAliPaySubmit()
    {
        self::assertTrue($this->pay->_createAliPayNotify() instanceof AliPayNotify);
        self::assertTrue($this->pay->_createAliPaySubmit() instanceof AliPaySubmit);
    }

    public function testParseNotify()
    {
        $data = $this->getNotifyData();
        unset($data['notify_data']);
        self::assertNull($this->pay->parseNotify($data));
        /////////////////////////////////////////////////////////////////////////////
        $data = $this->getNotifyData();
        $aliPayNotify = $this->createAliPayNotify();
        $aliPayNotify->shouldReceive('verifyNotify')->andReturn(false);
        $this->pay->setAliPayNotify($aliPayNotify);
        self::assertNull($this->pay->parseNotify($data));
        /////////////////////////////////////////////////////////////////////////////
        $data = $this->getNotifyData();
        $aliPayNotify = $this->createAliPayNotify();
        $aliPayNotify->shouldReceive('verifyNotify')->andReturn(true);
        $aliPayNotify->shouldReceive('decrypt')->andReturn($data['notify_data']);
        $this->pay->setAliPayNotify($aliPayNotify);
        self::assertTrue($this->pay->parseNotify($data) instanceof PayNotify);
        /////////////////////////////////////////////////////////////////////////////
        $data = $this->getNotifyData();
        $aliPayNotify = $this->createAliPayNotify();
        $aliPayNotify->shouldReceive('verifyNotify')->andReturn(true);
        $aliPayNotify->shouldReceive('decrypt')->andReturn($data['notify_data']);
        $this->pay->setAliPayNotify($aliPayNotify);
        self::assertTrue($this->pay->parseNotify($data) instanceof PayNotify);
        /////////////////////////////////////////////////////////////////////////////
        $data = $this->getNotifyData();
        $aliPayNotify = $this->createAliPayNotify();
        $aliPayNotify->shouldReceive('verifyNotify')->andReturn(true);
        $aliPayNotify->shouldReceive('decrypt')->andReturn('<notify><payment_type>1</payment_type><subject>收银台【1283134629741】</subject><trade_no>2010083000136835</trade_no><buyer_email>dinglang@a.com</buyer_email><gmt_create>2010-08-30 10:17:24</gmt_create><notify_type>trade_status_sync</notify_type><quantity>1</quantity><out_trade_no>1283134629741|group</out_trade_no><notify_time>2010-08-30 10:18:15</notify_time><seller_id>2088101000137799</seller_id><trade_status>TRADE_FINISHED</trade_status><is_total_fee_adjust>N</is_total_fee_adjust><total_fee>1.00</total_fee><gmt_payment>2010-08-30 10:18:26</gmt_payment><seller_email>chenf003@yahoo.cn</seller_email><gmt_close>2010-08-30 10:18:26</gmt_close><price>1.00</price><buyer_id>2088102001172352</buyer_id><notify_id>509ad84678759176212c247c46bec05303</notify_id><use_coupon>N</use_coupon></notify>');
        $this->pay->setAliPayNotify($aliPayNotify);
        self::assertTrue($this->pay->parseNotify($data) instanceof PayNotify);
        /////////////////////////////////////////////////////////////////////////////
        $data = $this->getNotifyData();
        $aliPayNotify = $this->createAliPayNotify();
        $aliPayNotify->shouldReceive('verifyNotify')->andReturn(true);
        $aliPayNotify->shouldReceive('decrypt')->andReturn('<payment_type>1</payment_type>');
        $this->pay->setAliPayNotify($aliPayNotify);
        self::assertNull($this->pay->parseNotify($data));
    }

    public function testParseNotifyWithMd5()
    {
        $config = ConfigFactory::createAliPayConfig();
        $config->setSignType('MD5');
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $writer->shouldReceive('info')->andReturn(true);

        $this->pay = new AliPayMock($config, $writer);
        $data = $this->getNotifyData();
        $aliPayNotify = $this->createAliPayNotify();
        $aliPayNotify->shouldReceive('verifyNotify')->andReturn(true);
        $aliPayNotify->shouldReceive('decrypt')->andReturn($data['notify_data']);
        $this->pay->setAliPayNotify($aliPayNotify);
        self::assertTrue($this->pay->parseNotify($data) instanceof PayNotify);
    }

    private function createPayOrder()
    {
        $orderId = date('Ymdhis').mt_rand();
        $result = new PayOrder();
        $result->setOrderId($orderId);
        $result->setGoodsName('goods');
        $result->setPayAmount(10.10);
        $result->setExtra('extra');
        $result->setLimitPay(new LimitPay(LimitPay::NO_CREDIT));
        $result->setNotifyUrl('http://www.notify.com');
        $result->setCallBackUrl('http://www.callback.com');
        $result->setMerchantUrl('http://www.merchant.com');
        $result->setIp('127.0.0.1');

        return $result;
    }
    /**
     * @runInSeparateProcess
     */
    public function testPayUrlErrorOrder()
    {
        $order = $this->createPayOrder();
        $order->setOrderId('');
        $result = $this->pay->payUrl($order);
        self::assertEmpty($result);
        ////////////////////////////////////////////////////////
        $order = $this->createPayOrder();
        $order->setGoodsName('');
        $result = $this->pay->payUrl($order);
        self::assertEmpty($result);
        ////////////////////////////////////////////////////////
        $order = $this->createPayOrder();
        $order->setGoodsName('');
        $result = $this->pay->payUrl($order);
        self::assertEmpty($result);
        ////////////////////////////////////////////////////////
        $order = $this->createPayOrder();
        $order->setPayAmount(0.0000001);
        $result = $this->pay->payUrl($order);
        self::assertEmpty($result);
        ////////////////////////////////////////////////////////
        $order = $this->createPayOrder();
        $order->setExtra('test|group');
        $result = $this->pay->payUrl($order);
        self::assertEmpty($result);
    }

    public function testPayUrl()
    {
        $order = $this->createPayOrder();
        $aliPaySubmit = $this->createAliPaySubmit();
        $aliPaySubmit->shouldReceive('buildRequestHttp')->andReturn('');
        $aliPaySubmit->shouldReceive('parseResponse')->andReturn(['request_token'=>'201110259f7686ab763c20e630db9902166f0bfa']);
        $aliPaySubmit->shouldReceive('buildRequestHttpURL')->andReturn('http://www.alipay.com/pay');
        $this->pay->setAliPaySubmit($aliPaySubmit);
        $result = $this->pay->payUrl($order);
        self::assertEquals('http://www.alipay.com/pay', $result);
    }

    /**
     * @runInSeparateProcess
     */
    public function testPay()
    {
        $order = $this->createPayOrder();
        $aliPaySubmit = $this->createAliPaySubmit();
        $aliPaySubmit->shouldReceive('buildRequestHttp')->andReturn('');
        $aliPaySubmit->shouldReceive('parseResponse')->andReturn(['request_token'=>'201110259f7686ab763c20e630db9902166f0bfa']);
        $aliPaySubmit->shouldReceive('buildRequestHttpURL')->andReturn('http://www.alipay.com/pay');
        $this->pay->setAliPaySubmit($aliPaySubmit);
        ob_start();
        $this->pay->pay($order);
        $result = get_http_header();
        header_remove();
        ob_end_clean();
        self::assertEquals(['Location:http://www.alipay.com/pay'], $result);
    }
}
