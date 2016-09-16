<?php
namespace Pay\WxPay;

use Mockery as m;
use Pay\WxPay\Modules\WxPayCheckName;
use Pay\WxPay\Modules\WxPayCloseOrder;
use Pay\WxPay\Modules\WxPayOrderQuery;
use Pay\WxPay\Modules\WxPayRefund;
use Pay\WxPay\Modules\WxPayRefundQuery;
use Pay\WxPay\Modules\WxPayTransfer;
use Pay\WxPay\Modules\WxPayUnifiedOrder;
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

    public function _postXmlCurl($xml, $url, $useCert = false)
    {
        return parent::postXmlCurl($xml, $url, $useCert);
    }

    public function reportCostTime($url, $startTimeStamp, $data, $clientIp)
    {
        return parent::reportCostTime($url, $startTimeStamp, $data, $clientIp);
    }
}

class WxPayApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WxPayApiMock
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
        $writer->shouldReceive('info')->andReturn(true);
        $writer->shouldReceive('debug')->andReturn(true);
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
        $xml = 'test';
        $url = 'test';
        $config = ConfigFactory::createWxConfig();
        $config->setCurlProxyHost('127.0.0.1');
        $config->setCurlProxyPort(8118);

        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $this->pay = new WxPayApiMock($config, $writer);
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(false);
        $this->pay->setClient($client);
        self::assertFalse($this->pay->_postXmlCurl($xml, $url, true));
    }

    public function testUnifiedOrder()
    {
        $order = new WxPayUnifiedOrder();
        $ip = '127.0.0.1';
        $result = $this->pay->unifiedOrder($order, $ip);
        self::assertFalse($result);

        $order->setOutTradeNo('1409811653');
        $ip = '127.0.0.1';
        $result = $this->pay->unifiedOrder($order, $ip);
        self::assertFalse($result);

        $order->setBody('goods');
        $ip = '127.0.0.1';
        $result = $this->pay->unifiedOrder($order, $ip);
        self::assertFalse($result);

        $order->setTotalFee(100);
        $ip = '127.0.0.1';
        $result = $this->pay->unifiedOrder($order, $ip);
        self::assertFalse($result);

        $order->setTradeType('JSAPI');
        $ip = '127.0.0.1';
        $result = $this->pay->unifiedOrder($order, $ip);
        self::assertFalse($result);

        $order->setTradeType('NATIVE');
        $ip = '127.0.0.1';
        $result = $this->pay->unifiedOrder($order, $ip);
        self::assertFalse($result);


        $order->setTradeType('JSAPI');
        $order->setOpenId('oUpF8uMEb4qRXf22hE3X68TekukE');
        $ip = '127.0.0.1';
        $xml = '<xml>
   <return_code><![CDATA[SUCCESS]]></return_code>
   <return_msg><![CDATA[OK]]></return_msg>
   <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
   <mch_id><![CDATA[10000100]]></mch_id>
   <device_info><![CDATA[1000]]></device_info>
   <nonce_str><![CDATA[TN55wO9Pba5yENl8]]></nonce_str>
   <sign><![CDATA[72C8C1396782FD6A450E13350D729C9B]]></sign>
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
        $client->shouldReceive('exec')->andReturn(false);
        $client->shouldReceive('getResponse')->andReturn($xml);
        $this->pay->setClient($client);
        $result = $this->pay->unifiedOrder($order, $ip);
        self::assertTrue(false === $result);

        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($xml);
        $this->pay->setClient($client);
        $result = $this->pay->unifiedOrder($order, $ip);
        self::assertTrue(false !== $result);
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
   <sign><![CDATA[72C8C1396782FD6A450E13350D729C9B]]></sign>
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
        $client->shouldReceive('exec')->andReturn(false);
        $client->shouldReceive('getResponse')->andReturn($xml);
        $this->pay->setClient($client);
        $result = $this->pay->orderQuery($order, $ip);
        self::assertTrue(false === $result);


        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($xml);
        $this->pay->setClient($client);
        $result = $this->pay->orderQuery($order, $ip);
        self::assertTrue(false !== $result);
    }

    public function testCloseOrder()
    {
        $order = new WxPayCloseOrder();
        $ip = '127.0.0.1';
        $result = $this->pay->closeOrder($order, $ip);
        self::assertFalse($result);

        $order->setOutTradeNo('1415983244');
        $xml = '<xml>
   <return_code><![CDATA[SUCCESS]]></return_code>
   <return_msg><![CDATA[OK]]></return_msg>
   <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
   <mch_id><![CDATA[10000100]]></mch_id>
   <nonce_str><![CDATA[BFK89FC6rxKCOjLX]]></nonce_str>
   <sign><![CDATA[0CA42060976D60AC7D454686BD498A5C]]></sign>
   <result_code><![CDATA[SUCCESS]]></result_code>
   <result_msg><![CDATA[OK]]></result_msg>
</xml>';
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(false);
        $client->shouldReceive('getResponse')->andReturn($xml);
        $this->pay->setClient($client);
        $result = $this->pay->closeOrder($order, $ip);
        self::assertTrue(false === $result);


        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($xml);
        $this->pay->setClient($client);
        $result = $this->pay->closeOrder($order, $ip);
        self::assertTrue(false !== $result);
    }

    public function testRefund()
    {
        $order = new WxPayRefund();
        $ip = '127.0.0.1';
        $result = $this->pay->refund($order, $ip);
        self::assertFalse($result);

        $order->setTransactionId('1008450740201411110005820873');
        $result = $this->pay->refund($order, $ip);
        self::assertFalse($result);

        $order->setOutRefundNo('1415701182');
        $result = $this->pay->refund($order, $ip);
        self::assertFalse($result);

        $order->setTotalFee(100);
        $result = $this->pay->refund($order, $ip);
        self::assertFalse($result);

        $order->setRefundFee(10);
        $result = $this->pay->refund($order, $ip);
        self::assertFalse($result);

        $order->setOpUserId('10000100');
        $xml = '<xml>
   <return_code><![CDATA[SUCCESS]]></return_code>
   <return_msg><![CDATA[OK]]></return_msg>
   <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
   <mch_id><![CDATA[10000100]]></mch_id>
   <nonce_str><![CDATA[NfsMFbUFpdbEhPXP]]></nonce_str>
   <sign><![CDATA[6226C665C83CA0B8C9707878924C54C6]]></sign>
   <result_code><![CDATA[SUCCESS]]></result_code>
   <transaction_id><![CDATA[1008450740201411110005820873]]></transaction_id>
   <out_trade_no><![CDATA[1415757673]]></out_trade_no>
   <out_refund_no><![CDATA[1415701182]]></out_refund_no>
   <refund_id><![CDATA[2008450740201411110000174436]]></refund_id>
   <refund_channel><![CDATA[]]></refund_channel>
   <refund_fee>1</refund_fee> 
</xml>';
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(false);
        $client->shouldReceive('getResponse')->andReturn($xml);
        $this->pay->setClient($client);
        $result = $this->pay->refund($order, $ip);
        self::assertTrue(false === $result);


        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($xml);
        $this->pay->setClient($client);
        $result = $this->pay->refund($order, $ip);
        self::assertTrue(false !== $result);
    }

    public function testRefundQuery()
    {
        $order = new WxPayRefundQuery();
        $ip = '127.0.0.1';
        $result = $this->pay->refundQuery($order, $ip);
        self::assertFalse($result);

        $order->setTransactionId('1008450740201411110005820873');
        $xml = '<xml>
   <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
   <mch_id><![CDATA[10000100]]></mch_id>
   <nonce_str><![CDATA[TeqClE3i0mvn3DrK]]></nonce_str>
   <out_refund_no_0><![CDATA[1415701182]]></out_refund_no_0>
   <out_trade_no><![CDATA[1415757673]]></out_trade_no>
   <refund_count>1</refund_count>
   <refund_fee_0>1</refund_fee_0>
   <refund_id_0><![CDATA[2008450740201411110000174436]]></refund_id_0>
   <refund_status_0><![CDATA[PROCESSING]]></refund_status_0>
   <result_code><![CDATA[SUCCESS]]></result_code>
   <return_code><![CDATA[SUCCESS]]></return_code>
   <return_msg><![CDATA[OK]]></return_msg>
   <sign><![CDATA[EB06D6FEC8D9090854FC25A6F9BACE11]]></sign>
   <transaction_id><![CDATA[1008450740201411110005820873]]></transaction_id>
</xml>';
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(false);
        $client->shouldReceive('getResponse')->andReturn($xml);
        $this->pay->setClient($client);
        $result = $this->pay->refundQuery($order, $ip);
        self::assertTrue(false === $result);

        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($xml);
        $this->pay->setClient($client);
        $result = $this->pay->refundQuery($order, $ip);
        self::assertTrue(false !== $result);
    }

    public function testTransfers()
    {
        $order = new WxPayTransfer();
        $ip = '127.0.0.1';
        $result = $this->pay->transfers($order, $ip);
        self::assertFalse($result);

        /**
         * //检测必填参数
        if (('' == $transfer->getPartnerTradeNo()) ||
        ('' == $transfer->getOpenId()) ||
        (0 == $transfer->getAmount()) ||
        ('' == $transfer->getDescription()) ||
        ('' == $transfer->getSpbillCreateIp())) {
        return false;
        }

        //如果check_name设置为FORCE_CHECK或OPTION_CHECK，则必填用户真实姓名
        if (($transfer->getCheckName() != WxPayCheckName::NO_CHECK) && ('' == $transfer->getReUserName())) {
        return false;
        }
         *
         */
        $order->setPartnerTradeNo('10000098201411111234567890');
        $result = $this->pay->transfers($order, $ip);
        self::assertFalse($result);

        $order->setOpenId('oUpF8uN95-Ptaags6E_roPHg7AG0');
        $result = $this->pay->transfers($order, $ip);
        self::assertFalse($result);

        $order->setAmount(11);
        $result = $this->pay->transfers($order, $ip);
        self::assertFalse($result);

        $order->setDescription('理赔');
        $result = $this->pay->transfers($order, $ip);
        self::assertFalse($result);

        $order->setSpbillCreateIp('127.0.0.1');
        $result = $this->pay->transfers($order, $ip);
        self::assertFalse($result);

        $order->setCheckName(new WxPayCheckName(WxPayCheckName::FORCE_CHECK));
        $order->setReUserName('王小虎');
        $xml = '<xml>
<return_code><![CDATA[SUCCESS]]></return_code>
<return_msg><![CDATA[]]></return_msg>
<mch_appid><![CDATA[wxec38b8ff840bd989]]></mch_appid>
<mchid><![CDATA[10013274]]></mchid>
<device_info><![CDATA[]]></device_info>
<nonce_str><![CDATA[lxuDzMnRjpcXzxLx0q]]></nonce_str>
<result_code><![CDATA[SUCCESS]]></result_code>
<partner_trade_no><![CDATA[10013574201505191526582441]]></partner_trade_no>
<payment_no><![CDATA[1000018301201505190181489473]]></payment_no>
<payment_time><![CDATA[2015-05-19 15：26：59]]></payment_time>
</xml>';
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(false);
        $client->shouldReceive('getResponse')->andReturn($xml);
        $this->pay->setClient($client);
        $result = $this->pay->transfers($order, $ip);
        self::assertTrue(false === $result);



        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($xml);
        $this->pay->setClient($client);
        $result = $this->pay->transfers($order, $ip);
        self::assertTrue(false !== $result);
    }
}
