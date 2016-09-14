<?php
namespace Pay\WxPay;

use Mockery as m;
use Pay\WxPay\Modules\WxPayOrderQuery;
use Simple\Http\Client;
use ConfigFactory;

class WxPayApiMock extends WxPayApi
{
    protected $client = null;

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
    }
}

class WxPayApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WxPayApi
     */
    protected $pay = null;

    protected function initClient()
    {
        $result = m::mock('Simple\Http\Client');
        $result->shouldReceive('setUrl')->andReturnNull();
        $result->shouldReceive('setMethod')->andReturnNull();
        $result->shouldReceive('setPostFields')->andReturnNull();
        $result->shouldReceive('setProxyHost')->andReturnNull();
        $result->shouldReceive('setProxyPort')->andReturnNull();
        $result->shouldReceive('setSslVerifyPeer')->andReturnNull();
        $result->shouldReceive('setSslVerifyHost')->andReturnNull();
        $result->shouldReceive('setHeader')->andReturnNull();
        $result->shouldReceive('setCaInfo')->andReturnNull();
        $result->shouldReceive('useCert')->andReturnNull();

        return $result;
    }

    protected function setUp()
    {
        $config = ConfigFactory::createWxConfig();

        parent::setUp();
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $this->pay = new WxPayApiMock($config, $writer);
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn('');
        $this->pay->setClient($client);
    }

    public function testClient()
    {
        $config = ConfigFactory::createWxConfig();
        $this->pay = new WxPayApi($config, $this->pay->getLog());
        self::assertTrue($this->pay->getClient() instanceof Client);
    }

    public function testReport()
    {
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn('ok');
        $this->pay->setClient($client);

        $data = ['return_code'=>'fail','return_msg'=>'msg', 'result_code'=>'fail',
                'err_code'=>-20, 'err_code_des'=>'error', 'out_trade_no'=> 'out_trade_no',
                'device_info' =>'device_info'];
        self::assertEquals('ok', $this->pay->reportCostTime(WxPayApi::UNIFIED_ORDER_URL, time(), $data, '127.0.0.1'));

        //////////////[reportLevel==0]//////////////////////////////////////////////////////////////////////////////////
        $config = $this->pay->getConfig();
        $config->setReportLevel(0);
        $this->pay = new WxPayApiMock($config, $this->pay->getLog());
        self::assertEquals(true, $this->pay->reportCostTime(WxPayApi::UNIFIED_ORDER_URL, time(), $data, '127.0.0.1'));

        /////////////[reportLevel=1 return_code=SUCCESS result_code=SUCCESS]////////////////////////////////////////////
        $config->setReportLevel(1);
        $this->pay = new WxPayApiMock($config, $this->pay->getLog());
        $data['return_code'] = 'SUCCESS';
        $data['result_code'] = 'SUCCESS';
        self::assertEquals(true, $this->pay->reportCostTime(WxPayApi::UNIFIED_ORDER_URL, time(), $data, '127.0.0.1'));

    }

    public function testMissReturnCode()
    {
        $data = ['return_msg'=>'msg', 'result_code'=>'fail',
            'err_code'=>-20, 'err_code_des'=>'error', 'out_trade_no'=> 'out_trade_no',
            'device_info' =>'device_info'];
        self::assertEquals(false, $this->pay->reportCostTime(WxPayApi::UNIFIED_ORDER_URL, time(), $data, '127.0.0.1'));
    }

    public function testMissResultCode()
    {
        $data = ['return_code'=>'fail', 'return_msg'=>'msg',
            'err_code'=>-20, 'err_code_des'=>'error', 'out_trade_no'=> 'out_trade_no',
            'device_info' =>'device_info'];
        self::assertEquals(false, $this->pay->reportCostTime(WxPayApi::UNIFIED_ORDER_URL, time(), $data, '127.0.0.1'));
    }

    public function testPostXmlCurl()
    {
        $config = ConfigFactory::createWxConfig();
        $config->setCurlProxyHost('127.0.0.1');
        $config->setCurlProxyPort(8118);
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(false);
        $client->shouldReceive('getResponse')->andReturn('ok');

        $this->pay = new WxPayApiMock($config, $this->pay->getLog());
        $this->pay->setClient($client);

        self::assertEquals(false, $this->pay->postXmlCurl('', '', true));
    }

    public function testNotify()
    {
        $xml = '<xml>
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
  <sign><![CDATA[77A8A29625B4FB89DCE4CA9921D810F3]]></sign>
  <sub_mch_id><![CDATA[10000100]]></sub_mch_id>
  <time_end><![CDATA[20140903131540]]></time_end>
  <total_fee>1</total_fee>
  <trade_type><![CDATA[JSAPI]]></trade_type>
  <transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
</xml>';
        $result = $this->pay->notify($xml);
        self::assertNotEmpty($result);

        ob_start();
        $this->pay->replyNotify($xml);
        $result = ob_get_contents();
        ob_end_clean();
        self::assertEquals($xml, $result);
    }

    public function testOrderQuery()
    {
        $order = new WxPayOrderQuery();
        $ip = '127.0.0.1';
        $result = $this->pay->orderQuery($order, $ip);
        self::assertFalse($result);

        $order->setOutTradeNo('sdsdf');
        $xml = '<xml>
   <return_code><![CDATA[SUCCESS]]></return_code>
   <return_msg><![CDATA[OK]]></return_msg>
   <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
   <mch_id><![CDATA[10000100]]></mch_id>
   <device_info><![CDATA[1000]]></device_info>
   <nonce_str><![CDATA[TN55wO9Pba5yENl8]]></nonce_str>
   <sign><![CDATA[BDF0099C15FF7BC6B1585FBB110AB635]]></sign>
   <result_code><![CDATA[SUCCESS]]></result_code>
   <openid><![CDATA[oUpF8uN95-Ptaags6E_roPHg7AG0]]></openid>
   <is_subscribe><![CDATA[Y]]></is_subscribe>
   <trade_type><![CDATA[MICROPAY]]></trade_type>
   <bank_type><![CDATA[CCB_DEBIT]]></bank_type>
   <total_fee>1</total_fee>
   <fee_type><![CDATA[CNY]]></fee_type>
   <transaction_id><![CDATA[1008450740201411110005820873]]></transaction_id>
   <out_trade_no><![CDATA[1415757673]]></out_trade_no>
   <attach><![CDATA[订单额外描述]]></attach>
   <time_end><![CDATA[20141111170043]]></time_end>
   <trade_state><![CDATA[SUCCESS]]></trade_state>
</xml>';
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($xml);
        $this->pay->setClient($client);
        $result = $this->pay->orderQuery($order, $ip);
        self::assertFalse($result);
    }
}
