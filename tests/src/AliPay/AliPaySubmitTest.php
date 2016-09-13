<?php
namespace Pay\AliPay;
use Mockery as m;
use Simple\Http\Client;

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
}

class AliPaySubmitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AliPaySubmit
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
        parent::setUp();
        $config = include __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $writer->shouldReceive('info')->andReturn(true);
        $this->pay = new AliPaySubmitMock($config, $writer);
        $client = $this->initClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn('');
        $this->pay->setClient($client);
    }

    public function test()
    {
        $result         = $this->pay->buildRequestHttp(['orderid'=>'1111111']);
        //URLDECODE返回的信息
        $result         = urldecode($result);
        //解析远程模拟提交后返回的信息
        $tokenArray     = $this->pay->parseResponse($result);
    }
}
