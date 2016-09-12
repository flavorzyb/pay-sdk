<?php
namespace Pay\WxPay;

use Mockery as m;
use Pay\WxPay\Modules\WxPayOrderQuery;
use Simple\Http\Client;

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
        $result->shouldReceive('useCert')->andReturnNull();

        return $result;
    }

    protected function setUp()
    {
        $config = ['appId'     => 'appId',
            'appSecret' => 'appSecret',
            'mchId'     => 'mchId',
            'key'       => 'key',

            //=======【证书路径设置】=====================================
            /**
             * 设置商户证书路径
             * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
             * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
             * @var string
             */
            'sslCertPath'   => __DIR__ . '/apiclient_cert.pem',
            'sslKeyPath'    => __DIR__ . '/apiclient_key.pem',

            //=======【curl代理设置】===================================
            /**
             * 这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
             * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
             * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
             * @var string
             */
            'curlProxyHost' => '0.0.0.0',
            'curlProxyPort' => 0,

            //=======【上报信息配置】===================================
            /**
             * 接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
             * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
             * 开启错误上报。
             * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
             * @var int
             */
            'reportLevel'   => 1,

            //服务器异步通知页面路径
            "notify_url"            =>  '/Mall/PayResponse/wxPay',
            //页面跳转同步通知页面路径
            "call_back_url"         => '/Mall/PayResponse/index',
            //操作中断返回地址
            "merchant_url"          => '/Mall/PayResponse/interrupt',];

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
        $this->pay = new WxPayApi($this->pay->getConfig(), $this->pay->getLog());
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
        $config['reportLevel'] = 0;
        $this->pay = new WxPayApiMock($config, $this->pay->getLog());
        self::assertEquals(true, $this->pay->reportCostTime(WxPayApi::UNIFIED_ORDER_URL, time(), $data, '127.0.0.1'));

        /////////////[reportLevel=1 return_code=SUCCESS result_code=SUCCESS]////////////////////////////////////////////
        $config['reportLevel'] = 1;
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
        $config = $this->pay->getConfig();
        $config['curlProxyHost'] = '127.0.0.1';
        $config['curlProxyPort'] = '8118';
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
  <sign><![CDATA[1DAB47362E2C946865AE1CA0BE1360CB]]></sign>
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
