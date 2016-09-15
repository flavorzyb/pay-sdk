<?php
namespace Pay\AliPay;

use Mockery as m;
use Simple\Http\Client;
use ConfigFactory;

class AliPaySubmitMock extends AliPaySubmit
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

    public function _rsaDecrypt($content)
    {
        return parent::rsaDecrypt($content, $this->config->getPrivateKeyPath());
    }
}

class AliPaySubmitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AliPaySubmitMock
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
        $this->pay = new AliPaySubmitMock($config, $writer);
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn('partner=2088101000137799&req_id=1283133204160&res_data=<?xml version="1.0" encoding="utf-8"?><direct_trade_create_res><request_token>20100830e8085e3e0868a466b822350ede5886e8</request_token></direct_trade_create_res>&sec_id=MD5&service=alipay.wap.trade.create.direct&v=2.0&sign=72a64fb63f0b54f96b10cefb69319e8a');
        $this->pay->setClient($client);
    }

    public function testOptions()
    {
        $config = ConfigFactory::createAliPayConfig();
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $writer->shouldReceive('info')->andReturn(true);
        $this->pay = new AliPaySubmit($config, $writer);
        self::assertTrue($this->pay->getClient() instanceof Client);
    }

    private function createParamToken()
    {
        $reqData        = '<direct_trade_create_req>'.
            '<notify_url>/notifyUrl</notify_url>' .
            '<call_back_url>/backUrl</call_back_url>'.
            '<seller_account_name>test@163.com</seller_account_name>'.
            '<out_trade_no>19766912998122</out_trade_no>'.
            '<subject>test</subject>'.
            '<total_fee>10</total_fee>'.
            '<merchant_url>/merchant_url</merchant_url>'.
            '</direct_trade_create_req>';

        $result = [
            "service"           => "alipay.wap.trade.create.direct",
            "partner"           => 'partner',
            "sec_id"            => '0001',
            "format"            => 'xml',
            "v"                 => '2.0',
            "req_id"            => date('Ymdhis'),
            "req_data"          => $reqData,
            "_input_charset"    => trim(strtolower('utf-8'))
        ];

        return $result;
    }

    private function createParamToRequestToken($token, $reqId)
    {
        /**************************根据授权码token调用交易接口alipay.wap.auth.authAndExecute**************************/
        //业务详细
        $reqData = '<auth_and_execute_req><request_token>' . $token . '</request_token></auth_and_execute_req>';
        //必填
        //构造要请求的参数数组，无需改动
        return [
            "service"           => "alipay.wap.auth.authAndExecute",
            "partner"           => trim('partner'),
            "sec_id"            => trim('0001'),
            "format"            => 'xml',
            "v"                 => '2.0',
            "req_id"            => $reqId,
            "req_data"          => $reqData,
            "_input_charset"    => trim(strtolower('utf-8'))
        ];
    }

    public function testBuildRequestHttpReturnEmpty()
    {
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(false);
        $this->pay->setClient($client);
        self::assertFalse($this->pay->buildRequestHttp($this->createParamToken()));
        self::assertTrue(strlen($this->pay->_rsaDecrypt("test string")) == 0);
    }

    public function testBuildRequestHttp()
    {
        $result         = $this->pay->buildRequestHttp($this->createParamToken());
        //URLDECODE返回的信息
        $result         = urldecode($result);
        //解析远程模拟提交后返回的信息
        $tokenArray     = $this->pay->parseResponse($result);
        //获取request_token
        $requestToken   = $tokenArray['request_token'];
        //建立请求
        $params         = $this->createParamToRequestToken($requestToken, date('Ymdhis'));
        $url = $this->pay->buildRequestHttpURL($params);
        self::assertTrue(strlen($url) > 10);
    }

    public function testBuildRequestHttpErrorService()
    {
        $params = $this->createParamToken();
        $params['service'] .='test_string';
        $result         = $this->pay->buildRequestHttp($params);
        //URLDECODE返回的信息
        $result         = urldecode($result);
        //解析远程模拟提交后返回的信息
        $tokenArray     = $this->pay->parseResponse($result);
        //获取request_token
        $requestToken   = $tokenArray['request_token'];
        //建立请求
        $params         = $this->createParamToRequestToken($requestToken, date('Ymdhis'));
        $url = $this->pay->buildRequestHttpURL($params);
        self::assertTrue(strlen($url) > 10);
    }

    public function testBuildRequestHttpRSA()
    {
        $config = ConfigFactory::createAliPayConfig();
        $config->setSignType('RSA');
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $writer->shouldReceive('info')->andReturn(true);
        $this->pay = new AliPaySubmitMock($config, $writer);
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn('partner=2088101000137799&req_id=1283133204160&res_data=<?xml version="1.0" encoding="utf-8"?><direct_trade_create_res><request_token>20100830e8085e3e0868a466b822350ede5886e8</request_token></direct_trade_create_res>&sec_id=MD5&service=alipay.wap.trade.create.direct&v=2.0&sign=72a64fb63f0b54f96b10cefb69319e8a');
        $this->pay->setClient($client);
        $result         = $this->pay->buildRequestHttp($this->createParamToken());
        //URLDECODE返回的信息
        $result         = urldecode($result);
        //解析远程模拟提交后返回的信息
        $tokenArray     = $this->pay->parseResponse($result);
        //获取request_token
        $requestToken   = $tokenArray['request_token'];
        //建立请求
        $params         = $this->createParamToRequestToken($requestToken, date('Ymdhis'));
        $url = $this->pay->buildRequestHttpURL($params);
        self::assertTrue(strlen($url) > 10);
    }

    public function testBuildRequestHttpErrorType()
    {
        $config = ConfigFactory::createAliPayConfig();
        $config->setSignType('');
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $writer->shouldReceive('info')->andReturn(true);
        $this->pay = new AliPaySubmitMock($config, $writer);
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn('partner=2088101000137799&req_id=1283133204160&res_data=<?xml version="1.0" encoding="utf-8"?><direct_trade_create_res><request_token>20100830e8085e3e0868a466b822350ede5886e8</request_token></direct_trade_create_res>&sec_id=MD5&service=alipay.wap.trade.create.direct&v=2.0&sign=72a64fb63f0b54f96b10cefb69319e8a');
        $this->pay->setClient($client);
        $result         = $this->pay->buildRequestHttp($this->createParamToken());
        //URLDECODE返回的信息
        $result         = urldecode($result);
        //解析远程模拟提交后返回的信息
        $tokenArray     = $this->pay->parseResponse($result);
        //获取request_token
        $requestToken   = $tokenArray['request_token'];
        //建立请求
        $params         = $this->createParamToRequestToken($requestToken, date('Ymdhis'));
        $url = $this->pay->buildRequestHttpURL($params);
        self::assertTrue(strlen($url) > 10);
    }

    public function testBuildRequestHttpMD5()
    {
        $config = ConfigFactory::createAliPayConfig();
        $config->setSignType('MD5');
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $writer->shouldReceive('info')->andReturn(true);
        $this->pay = new AliPaySubmitMock($config, $writer);
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn('partner=2088101000137799&req_id=1283133204160&res_data=<?xml version="1.0" encoding="utf-8"?><direct_trade_create_res><request_token>20100830e8085e3e0868a466b822350ede5886e8</request_token></direct_trade_create_res>&sec_id=MD5&service=alipay.wap.trade.create.direct&v=2.0&sign=72a64fb63f0b54f96b10cefb69319e8a');
        $this->pay->setClient($client);
        $result         = $this->pay->buildRequestHttp($this->createParamToken());
        //URLDECODE返回的信息
        $result         = urldecode($result);
        //解析远程模拟提交后返回的信息
        $tokenArray     = $this->pay->parseResponse($result);
        //获取request_token
        $requestToken   = $tokenArray['request_token'];
        //建立请求
        $params         = $this->createParamToRequestToken($requestToken, date('Ymdhis'));
        $url = $this->pay->buildRequestHttpURL($params);
        self::assertTrue(strlen($url) > 10);
    }
}
