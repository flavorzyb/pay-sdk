<?php
namespace Pay;

use Mockery as m;
use ConfigFactory;
use Pay\Modules\LimitPay;
use Pay\Modules\PayNotify;
use Pay\Modules\PayOrder;
use Pay\WxPay\Modules\WxPayNotifyReply;
use Pay\WxPay\WxJsApiPay;
use Pay\WxPay\WxNativePay;
use Pay\WxPay\WxPayApi;
use Pay\Modules\PayOrderQueryResult;

class WxPayMock extends WxPay
{
    public function _pay(PayOrder $payOrder, $tradeType, $ip)
    {
        return parent::_pay($payOrder, $tradeType, $ip);
    }
}

class WxPayTest extends PayAbstractTest
{
    /**
     * @var WxPayMock
     */
    protected $pay = null;

    protected function setUp()
    {
        parent::setUp();
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $writer->shouldReceive('info')->andReturn(true);
        $writer->shouldReceive('debug')->andReturn(true);
        $this->pay = new WxPayMock(ConfigFactory::createWxConfig(), $writer);
    }

    protected function getPay()
    {
        return $this->pay;
    }


    public function testOptions()
    {
        self::assertTrue($this->pay->getWxJsApiPay() instanceof WxJsApiPay);
        self::assertTrue($this->pay->getWxJsApiPay() instanceof WxJsApiPay);
        self::assertTrue($this->pay->getWxNativePay() instanceof WxNativePay);
        self::assertTrue($this->pay->getWxNativePay() instanceof WxNativePay);
        self::assertTrue($this->pay->getWxPayApi() instanceof WxPayApi);
        self::assertTrue($this->pay->getWxPayApi() instanceof WxPayApi);
        $openId = 'wxd678efh567hg6787';
        $this->pay->setOpenId($openId);
        self::assertEquals($openId, $this->pay->getOpenId());
    }

    private function createPayOrder()
    {
        $orderId = date('Ymdhis').mt_rand();
        $result = new PayOrder();
        $result->setOrderId($orderId);
        $result->setGoodsName('goodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoods');
        $result->setPayAmount(10.10);
        $result->setExtra('extra');
        $result->setLimitPay(new LimitPay(LimitPay::NO_CREDIT));
        $result->setNotifyUrl('http://www.notify.com');
        $result->setCallBackUrl('http://www.callback.com');
        $result->setMerchantUrl('http://www.merchant.com');
        $result->setIp('127.0.0.1');

        return $result;
    }

    private function createWxPayApi()
    {
        $result = m::mock('Pay\WxPay\WxPayApi');
        $result->shouldReceive('getMillisecond')->andReturn(time());
        $result->shouldReceive('replyNotify')->andReturn('');
        return $result;
    }

    private function createWxJsApiPay()
    {
        $result = m::mock('Pay\WxPay\WxJsApiPay');
        $result->shouldReceive('createJsApiParameters')->andReturn('test');
        return $result;
    }

    private function createWxNativePay()
    {
        $result = m::mock('Pay\WxPay\WxNativePay');
        $result->shouldReceive('createNativeUrl')->andReturn('test');
        $result->shouldReceive('createAppPayParams')->andReturn(['test']);
        return $result;
    }

    public function testPayCheckReturnFalse()
    {
        $order = $this->createPayOrder();
        $order->setIp('');
        self::assertFalse($this->pay->pay($order, '127.0.0.1'));

        $order = $this->createPayOrder();
        $order->setPayAmount(0);
        self::assertFalse($this->pay->pay($order, '127.0.0.1'));
    }

    public function testPayReturnError()
    {
        $wxPayApi =$this->createWxPayApi();
        $wxPayApi->shouldReceive('unifiedOrder')->andReturn(false);
        $this->pay->setWxPayApi($wxPayApi);
        $result = $this->pay->pay($this->createPayOrder(), '127.0.0.1');
        self::assertFalse($result);

        $result = $this->pay->nativePay($this->createPayOrder(), '127.0.0.1');
        self::assertFalse($result);

        $result = $this->pay->appPay($this->createPayOrder(), '127.0.0.1');
        self::assertFalse($result);
    }

    public function testPayReturnCodeFail()
    {
        $wxPayApi =$this->createWxPayApi();
        $data = ['result_code' => 'FAIL', 'return_code' => 'FAIL', 'return_msg' => 'OK'];
        $wxPayApi->shouldReceive('unifiedOrder')->andReturn($data);
        $this->pay->setWxPayApi($wxPayApi);
        $result = $this->pay->pay($this->createPayOrder(), '127.0.0.1');
        self::assertFalse($result);

        $result = $this->pay->nativePay($this->createPayOrder(), '127.0.0.1');
        self::assertFalse($result);

        $result = $this->pay->appPay($this->createPayOrder(), '127.0.0.1');
        self::assertFalse($result);
    }

    public function testPay()
    {
        $wxPayApi =$this->createWxPayApi();
        $data = ['appid' => 'wx2421b1c4370ec43b', 'mch_id' => '10000100', 'nonce_str' => 'IITRi8Iabbblz1Jc', 'prepay_id' => 'wx201411101639507cbf6ffd8b0779950874', 'result_code' => 'SUCCESS', 'return_code' => 'SUCCESS', 'return_msg' => 'OK', 'sign' => 'E34C76BF2C66B91F96F189F56E2E9BC2', 'trade_type' => 'JSAPI'];
        $wxPayApi->shouldReceive('unifiedOrder')->andReturn($data);

        $this->pay->setWxPayApi($wxPayApi);
        $result = $this->pay->pay($this->createPayOrder(), '127.0.0.1');
        self::assertEquals($data, $result);

        $result = $this->pay->nativePay($this->createPayOrder(), '127.0.0.1');
        self::assertEquals($data, $result);

        $result = $this->pay->appPay($this->createPayOrder(), '127.0.0.1');
        self::assertEquals($data, $result);
    }

    public function testPayErrorTradeType()
    {
        $wxPayApi =$this->createWxPayApi();
        $this->pay->setWxPayApi($wxPayApi);
        $result = $this->pay->_pay($this->createPayOrder(), 'ErrorType', '127.0.0.1');
        self::assertFalse($result);
    }

    public function testCreateParams()
    {
        $this->pay->setWxJsApiPay($this->createWxJsApiPay());
        $this->pay->setWxNativePay($this->createWxNativePay());

        self::assertEquals('test', $this->pay->createJsApiParameters([]));
        self::assertEquals('test', $this->pay->createNativeUrl([]));
        self::assertEquals(['test'], $this->pay->createAppPayParams([]));
    }

    public function testParseNotify()
    {
        self::assertFalse($this->pay->parseNotify('', '127.0.0.1'));

        ///////////////验证签名错误//////////////////////////////
        $str = '<xml>
  <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
  <attach><![CDATA[支付测试]]></attach>
  <bank_type><![CDATA[CFT]]></bank_type>
  <fee_type><![CDATA[CNY]]></fee_type>
  <is_subscribe><![CDATA[Y]]></is_subscribe>
  <mch_id><![CDATA[10000100]]></mch_id>
  <nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
  <openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
  <out_trade_no><![CDATA[1409811653]]></out_trade_no>
  <result_code><![CDATA[SUCCESS]]></result_code>
  <return_code><![CDATA[SUCCESS]]></return_code>
  <sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
  <sub_mch_id><![CDATA[10000100]]></sub_mch_id>
  <time_end><![CDATA[20140903131540]]></time_end>
  <total_fee>1</total_fee>
  <trade_type><![CDATA[JSAPI]]></trade_type>
  <transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
</xml>';
        $wxPayApi =$this->createWxPayApi();
        $wxPayApi->shouldReceive('notify')->andReturn(false);
        $this->pay->setWxPayApi($wxPayApi);
        self::assertFalse($this->pay->parseNotify($str, '127.0.0.1'));

        ///////////////校验订单失败//////////////////////////////
        $data = ['transaction_id'=>'1004400740201409030005092168', 'out_trade_no'=>'1409811653', 'attach'=>'test|group', 'total_fee'=>100, 'result_code'=>'SUCCESS'];
        $wxPayApi =$this->createWxPayApi();
        $wxPayApi->shouldReceive('notify')->andReturn($data);
        $wxPayApi->shouldReceive('orderQuery')->andReturn(false);
        $this->pay->setWxPayApi($wxPayApi);
        self::assertFalse($this->pay->parseNotify($str, '127.0.0.1'));

        ///////////////校验订单成功//////////////////////////////
        $wxPayApi =$this->createWxPayApi();
        $wxPayApi->shouldReceive('notify')->andReturn($data);
        $data = ['return_code'=>'SUCCESS', 'result_code'=>'SUCCESS', 'trade_state'=>'SUCCESS', 'transaction_id'=>'1004400740201409030005092168', 'out_trade_no'=>'1409811653'];
        $data['total_fee'] = 12;
        $data['cash_fee'] = 12;
        $wxPayApi->shouldReceive('orderQuery')->andReturn($data);
        $this->pay->setWxPayApi($wxPayApi);
        self::assertTrue($this->pay->parseNotify($str, '127.0.0.1') instanceof PayNotify);
    }

    public function testReplyNotify()
    {
        $result = new WxPayNotifyReply();
        $result->setReturnCode('SUCCESS');
        $result->setReturnMsg('签名失败');
        $result->setData('msg', 'aaaaaa');
        ob_start();
        $this->pay->replyNotify($result, true);
        $result = ob_get_contents();
        ob_end_clean();
        $str = '<xml><msg><![CDATA[aaaaaa]]></msg><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[签名失败]]></return_msg><sign><![CDATA[3D191CB8978B03EBF5B1E02929B897D9]]></sign></xml>';
        self::assertEquals($str, $result);
    }

    public function testOrderQuery()
    {
        $status = ['SUCCESS', 'REFUND', 'NOTPAY', 'CLOSED', 'REVOKED', 'USERPAYING', 'PAYERROR'];
        foreach ($status as $v) {
            $queryResult = ['transaction_id'=> '', 'out_trade_no'=> '', 'total_fee'=>100,'cash_fee'=> 66, 'trade_state'=> $v];
            $api = $this->createWxPayApi();
            $api->shouldReceive('orderQuery')->andReturn($queryResult);
            $this->pay->setWxPayApi($api);
            $result = $this->pay->orderQuery($this->createOrderQuery(), '127.0.0.1');
            self::assertTrue($result instanceof PayOrderQueryResult);
        }

        $api = $this->createWxPayApi();
        $api->shouldReceive('orderQuery')->andReturn(false);
        $this->pay->setWxPayApi($api);
        $result = $this->pay->orderQuery($this->createOrderQuery(), '127.0.0.1');
        self::assertFalse($result);
    }

    public function testOrderClose()
    {
        $queryResult = ['result_code'=> 'SUCCESS', 'return_code'=> 'SUCCESS'];
        $api = $this->createWxPayApi();
        $api->shouldReceive('closeOrder')->andReturn($queryResult);
        $this->pay->setWxPayApi($api);
        $result = $this->pay->closeOrder($this->createCloseQuery(), '127.0.0.1');
        self::assertTrue($result);

        $api = $this->createWxPayApi();
        $api->shouldReceive('closeOrder')->andReturn(false);
        $this->pay->setWxPayApi($api);
        $result = $this->pay->closeOrder($this->createCloseQuery(), '127.0.0.1');
        self::assertFalse($result);
    }

    public function testNotifyReply()
    {
        ob_start();
        $this->pay->notifyReplySuccess(new PayNotify());
        $result = ob_get_contents();
        ob_end_clean();
        self::assertEquals("<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>", $result);

        ob_start();
        $this->pay->notifyReplyFail(new PayNotify());
        $result = ob_get_contents();
        ob_end_clean();
        self::assertEquals("<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[校验订单失败]]></return_msg></xml>", $result);
    }
}
