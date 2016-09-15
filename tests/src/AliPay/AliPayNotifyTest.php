<?php
namespace Pay\AliPay;

use Mockery as m;
use Simple\Http\Client;
use ConfigFactory;

class AliPayNotifyMock extends AliPayNotify
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

    public function rsaDecrypt($content, $privateKeyPath)
    {
        return $content;
    }

    public function _getSignVerify($data, $sign, $isSort)
    {
        return $this->getSignVerify($data, $sign, $isSort);
    }
}

class AliPayNotifyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AliPayNotifyMock
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
        parent::setUp();
        $config = ConfigFactory::createAliPayConfig();
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $writer->shouldReceive('info')->andReturn(true);
        $this->pay = new AliPayNotifyMock($config, $writer);
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn('partner=2088101000137799&req_id=1283133204160&res_data=<?xml version="1.0" encoding="utf-8"?><direct_trade_create_res><request_token>20100830e8085e3e0868a466b822350ede5886e8</request_token></direct_trade_create_res>&sec_id=MD5&service=alipay.wap.trade.create.direct&v=2.0&sign=72a64fb63f0b54f96b10cefb69319e8a');
        $this->pay->setClient($client);
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

    public function testVerifyNotifyWithHttps()
    {
        $config = ConfigFactory::createAliPayConfig();
        $config->setTransport('https');
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $writer->shouldReceive('info')->andReturn(true);
        $this->pay = new AliPayNotifyMock($config, $writer);
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn('partner=2088101000137799&req_id=1283133204160&res_data=<?xml version="1.0" encoding="utf-8"?><direct_trade_create_res><request_token>20100830e8085e3e0868a466b822350ede5886e8</request_token></direct_trade_create_res>&sec_id=MD5&service=alipay.wap.trade.create.direct&v=2.0&sign=72a64fb63f0b54f96b10cefb69319e8a');
        $this->pay->setClient($client);
        self::assertFalse($this->pay->verifyNotify($this->getNotifyData()));
    }

    public function testVerifyNotifyWithReturnEmptyString()
    {
        $config = ConfigFactory::createAliPayConfig();
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $writer->shouldReceive('info')->andReturn(true);
        $this->pay = new AliPayNotifyMock($config, $writer);
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(false);
        $client->shouldReceive('getResponse')->andReturn('');
        $this->pay->setClient($client);
        self::assertFalse($this->pay->verifyNotify($this->getNotifyData()));
    }

    public function testDecryptAndGetSignVerify()
    {
        self::assertEquals('test string', $this->pay->decrypt('test string'));
        $data = $this->getNotifyData();
        self::assertFalse($this->pay->_getSignVerify($data, $data['sign'], true));
    }

    public function testVerifyNotifyWithMd5()
    {
        $config = ConfigFactory::createAliPayConfig();
        $config->setSignType('MD5');
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $writer->shouldReceive('info')->andReturn(true);
        $this->pay = new AliPayNotifyMock($config, $writer);
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn('partner=2088101000137799&req_id=1283133204160&res_data=<?xml version="1.0" encoding="utf-8"?><direct_trade_create_res><request_token>20100830e8085e3e0868a466b822350ede5886e8</request_token></direct_trade_create_res>&sec_id=MD5&service=alipay.wap.trade.create.direct&v=2.0&sign=72a64fb63f0b54f96b10cefb69319e8a');
        $this->pay->setClient($client);
        self::assertFalse($this->pay->verifyNotify($this->getNotifyData()));
    }

    public function testVerifyNotify()
    {
        self::assertFalse($this->pay->verifyNotify([]));
        $data = $this->getNotifyData();
        unset($data['notify_data']);
        self::assertFalse($this->pay->verifyNotify($data));
        $data = $this->getNotifyData();
        unset($data['sign']);
        self::assertFalse($this->pay->verifyNotify($data));
        self::assertFalse($this->pay->verifyNotify($this->getNotifyData()));
    }
}
