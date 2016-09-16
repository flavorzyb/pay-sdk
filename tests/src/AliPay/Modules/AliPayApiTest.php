<?php
namespace Pay\AliPay\Modules;

use Pay\AliPay\AliPayApi;
use ConfigFactory;
use Mockery as m;

class AliPayApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AliPayApi
     */
    private $pay = null;

    protected function setUp()
    {
        parent::setUp();
        $config = ConfigFactory::createAliPayConfig();

        $this->pay = new AliPayApi($config, $this->createWriter());
    }

    private function createWriter()
    {
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('info')->andReturn(true);
        $writer->shouldReceive('debug')->andReturn(true);
        $writer->shouldReceive('error')->andReturn(true);

        return $writer;
    }

    private function createWapPayRequest()
    {
        $result = new AliPayTradeWapPayRequest();

        $result->setBody('对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body');
        $result->setSubject('大乐透');
        $result->setOutTradeNo(date('YmdHis').uniqid());
        $result->setTimeoutExpress('90m');
        $result->setTotalAmount(9.00);
        $result->setAuthToken('');
        $result->setProductCode('QUICK_WAP_PAY');

        return $result;
    }


    /**
     * @expectedException \Pay\AliPay\AliPayException
     */
    public function testPayErrorPrivateFilePath()
    {
        $config = ConfigFactory::createAliPayConfig();
        $config->setPrivateKeyPath('');
        $this->pay = new AliPayApi($config, $this->createWriter());
        $this->pay->pay($this->createWapPayRequest());
    }

    /**
     * @expectedException \Pay\AliPay\AliPayException
     */
    public function testPayEmptyPrivateFile()
    {
        $config = ConfigFactory::createAliPayConfig();
        $config->setPrivateKeyPath($config->getPublicKeyPath());
        $this->pay = new AliPayApi($config, $this->createWriter());
        $this->pay->pay($this->createWapPayRequest());
    }

    public function testPayUnsetSubject()
    {
        $request = $this->createWapPayRequest();
        $request->setSubject('');
        $result = $this->pay->pay($request);
        self::assertFalse($result);
    }

    public function testPay()
    {
        $result = $this->pay->pay($this->createWapPayRequest());
        self::assertTrue(strlen($result) > 500);
    }
}
