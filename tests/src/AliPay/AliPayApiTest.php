<?php
namespace Pay\AliPay;

use Pay\AliPay\Modules\AliPayTradeWapPayRequest;
use Pay\AliPay\Modules\AliPayTradeWapPayResult;
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
        file_put_contents('/Users/flavor/wwwroot/test/alipay.php', $result);
        self::assertTrue(strlen($result) > 500);
    }

    public function testParsePayReturnResult()
    {
        $uri = 'total_amount=9.00&timestamp=2016-09-17+03%3A06%3A17&sign=SU2ZqczH5R2cbWp3Ipa3xJJMqAlpiBRZKx1pwQwubV3g5k3qCSdpVoMVhbW7lWYMuNWl3kgxJjBdVt5dmmUgx52SHjT9TA4oeM5dZ6wG6gB%2F3PE9GBH0BLEKxuAG4VvZprsksHrgzq4Yo1aoYPffdkpEEJn7O69CKUpmZ8ePl90%3D&trade_no=2016091721001004360200059782&sign_type=RSA&charset=utf-8&seller_id=2088102175865018&method=alipay.trade.wap.pay.return&app_id=2016091600523436&out_trade_no=2016091703060157dc4299104e3&version=1.0';
        $data = [];
        parse_str($uri, $data);

        self::assertTrue($this->pay->parsePayReturnResult($data) instanceof AliPayTradeWapPayResult);
    }

    public function testParsePayReturnVerifyFalse()
    {
        $uri = 'total_amount=9.00&timestamp=2016-09-17+03%3A06%3A17&sign=SU2ZqczH5R2cbWp3Ipa3xJJMqAlpiBRZKx1pwQwubV3g5k3qCSdpVoMVhbW7lWYMuNWl3kgxJjBdVt5dmmUgx52SHjT9TA4oeM5dZ6wG6gB%2F3PE9GBH0BLEKxuAG4VvZprsksHrgzq4Yo1aoYPffdkpEEJn7O69CKUpmZ8ePl90%3D&trade_no=2016091721001004360200059782&sign_type=RSA&charset=utf-8&seller_id=2088102175865018&method=alipay.trade.wap.pay.return&app_id=2016091600523436&out_trade_no=2016091703060157dc4299104e3&version=1.0';
        $data = [];
        parse_str($uri, $data);
        unset($data['total_amount']);
        self::assertNull($this->pay->parsePayReturnResult($data));
    }

    /**
     * @expectedException \Pay\AliPay\AliPayException
     */
    public function testPayEmptyPublicFile()
    {
        $config = ConfigFactory::createAliPayConfig();
        $config->setPublicKeyPath($config->getPrivateKeyPath());
        $this->pay = new AliPayApi($config, $this->createWriter());
        $uri = 'total_amount=9.00&timestamp=2016-09-17+03%3A06%3A17&sign=SU2ZqczH5R2cbWp3Ipa3xJJMqAlpiBRZKx1pwQwubV3g5k3qCSdpVoMVhbW7lWYMuNWl3kgxJjBdVt5dmmUgx52SHjT9TA4oeM5dZ6wG6gB%2F3PE9GBH0BLEKxuAG4VvZprsksHrgzq4Yo1aoYPffdkpEEJn7O69CKUpmZ8ePl90%3D&trade_no=2016091721001004360200059782&sign_type=RSA&charset=utf-8&seller_id=2088102175865018&method=alipay.trade.wap.pay.return&app_id=2016091600523436&out_trade_no=2016091703060157dc4299104e3&version=1.0';
        $data = [];
        parse_str($uri, $data);
        unset($data['total_amount']);
        self::assertNull($this->pay->parsePayReturnResult($data));
    }

    /**
     * @expectedException \Pay\AliPay\AliPayException
     */
    public function testPayErrorPublicFilePath()
    {
        $config = ConfigFactory::createAliPayConfig();
        $config->setPublicKeyPath('');
        $this->pay = new AliPayApi($config, $this->createWriter());
        $uri = 'total_amount=9.00&timestamp=2016-09-17+03%3A06%3A17&sign=SU2ZqczH5R2cbWp3Ipa3xJJMqAlpiBRZKx1pwQwubV3g5k3qCSdpVoMVhbW7lWYMuNWl3kgxJjBdVt5dmmUgx52SHjT9TA4oeM5dZ6wG6gB%2F3PE9GBH0BLEKxuAG4VvZprsksHrgzq4Yo1aoYPffdkpEEJn7O69CKUpmZ8ePl90%3D&trade_no=2016091721001004360200059782&sign_type=RSA&charset=utf-8&seller_id=2088102175865018&method=alipay.trade.wap.pay.return&app_id=2016091600523436&out_trade_no=2016091703060157dc4299104e3&version=1.0';
        $data = [];
        parse_str($uri, $data);
        unset($data['total_amount']);
        self::assertNull($this->pay->parsePayReturnResult($data));
    }
}
