<?php
namespace Pay\AliPay;

use Pay\AliPay\Modules\AliPayTradeQueryRequest;
use Pay\AliPay\Modules\AliPayTradeQueryResult;
use Pay\AliPay\Modules\AliPayTradeWapPayRequest;
use Pay\AliPay\Modules\AliPayTradeWapPayResult;
use ConfigFactory;
use Mockery as m;
use Simple\Http\Client;

class AliPayApiMock extends AliPayApi
{
    /**
     * @var Client
     */
    private $client = null;

    public $isMockRsaVerify = false;
    /**
     * @return Client
     */
    public function getClient()
    {
        if (null == $this->client) {
            return parent::getClient();
        }
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    protected function verify($data, $sign)
    {
        if ($this->isMockRsaVerify) {
            return true;
        }
        return parent::verify($data, $sign);
    }
}
class AliPayApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AliPayApiMock
     */
    private $pay = null;

    protected function setUp()
    {
        parent::setUp();
        $config = ConfigFactory::createAliPayConfig();

        $this->pay = new AliPayApiMock($config, $this->createWriter());
    }

    private function createWriter()
    {
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('info')->andReturn(true);
        $writer->shouldReceive('debug')->andReturn(true);
        $writer->shouldReceive('error')->andReturn(true);

        return $writer;
    }

    private function createClient()
    {
        $result = m::mock('Simple\Http\Client');
        $result->shouldReceive('setUrl')->andReturn(true);

        return $result;
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

    private function createOrderQueryRequest()
    {
        $result = new AliPayTradeQueryRequest();
        $result->setTradeNo('2016091721001004360200059782');
        $result->setOutTradeNo('2016091703060157dc4299104e3');

        return $result;
    }

    public function testOrderQuery()
    {
        self::assertTrue($this->pay->getClient() instanceof Client);

        $data ='{"alipay_trade_query_response":{"code":"10000","msg":"Success","buyer_logon_id":"kxk***@sandbox.com","buyer_pay_amount":"0.00","buyer_user_id":"2088102169132360","invoice_amount":"0.00","open_id":"20880016753930000893495010410136","out_trade_no":"2016091703060157dc4299104e3","point_amount":"0.00","receipt_amount":"0.00","send_pay_date":"2016-09-17 03:06:14","total_amount":"9.00","trade_no":"2016091721001004360200059782","trade_status":"TRADE_SUCCESS"},"sign":"uYs6IErMvNG7nowLUesFjv+xD2EzYnFEAtSQ1ndYOFsG61IKWmlndZnW01OWsNNawJE6eMW/DA9w+7+injrXw7s8Fh1ROcDUT2hxOndYnCYz8X3+4dPzVZT5q2SCauF0PR36C7b/be3R1vn/N5YkSasH89EtKoL85u2jrTTyqdE="}';
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $result = $this->pay->orderQuery($this->createOrderQueryRequest());
        self::assertTrue($result instanceof AliPayTradeQueryResult);
    }

    public function testOrderQueryExecReturnFalse()
    {
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(false);
        $this->pay->setClient($client);
        $result = $this->pay->orderQuery($this->createOrderQueryRequest());
        self::assertFalse($result);
    }

    public function testOrderQueryTradeNoIsEmpty()
    {
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(false);
        $this->pay->setClient($client);
        $result = $this->pay->orderQuery(new AliPayTradeQueryRequest());
        self::assertFalse($result);
    }

    public function testOrderQueryExecReturnError()
    {
        $data ='test';
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $result = $this->pay->orderQuery($this->createOrderQueryRequest());
        self::assertFalse($result);
    }

    public function testOrderQueryVerifyReturnFalse()
    {
        $data ='{"alipay_trade_query_response":{"code":"10000","msg":"Success","buyer_logon_id":"kxk***@sandbox.com","buyer_pay_amount":"0.00","buyer_user_id":"2088102169132360","invoice_amount":"0.00","open_id":"20880016753930000893495010410136","out_trade_no":"2016091703060157dc4299104e3","point_amount":"0.00","receipt_amount":"0.00","send_pay_date":"2016-09-17 03:06:14","total_amount":"9.00","trade_no":"2016091721001004360200059782","trade_status":"TRADE_SUCCESS"},"sign":"uYs6IErMvNG7nowLUesFjv+xD2EzYnFEAtSQ1ndYOFsG61IKWmlndZnW01OWsNNawJE6eMW/DA9w+7+injrXw7s8Fh1ROcDUT2hxOnCYz8X3+4dPzVZT5q2SCauF0PR36C7b/be3R1vn/N5YkSasH89EtKoL85u2jrTTyqdE="}';
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $result = $this->pay->orderQuery($this->createOrderQueryRequest());
        self::assertFalse($result);
    }

    public function testOrderQueryMockVerifyWithErrorCode()
    {
        $data ='{"alipay_trade_query_response":{"code":"20000","msg":"Service Currently Unavailable","sub_code":"isp.unknow-error","sub_msg":"系统繁忙"}}';
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $this->pay->isMockRsaVerify = true;
        $result = $this->pay->orderQuery($this->createOrderQueryRequest());
        self::assertFalse($result);
    }

    public function testOrderQueryMockVerify()
    {
        $data ='{
    "alipay_trade_query_response":{
        "alipay_store_id":"2015040900077001000100001232",
        "buyer_logon_id":"159****5620",
        "buyer_pay_amount":8.88,
        "buyer_user_id":"2088101117955611",
        "code":"10000",
        "discount_goods_detail":"[{\"goods_id\":\"STANDARD1026181538\",\"goods_name\":\"雪碧\",\"discount_amount\":\"100.00\",\"voucher_id\":\"2015102600073002039000002D5O\"}]",
        "fund_bill_list":[{
            "amount":10,
            "fund_channel":"ALIPAYACCOUNT",
            "real_amount":11.21
        }],
        "industry_sepc_detail":"{\"registration_order_pay\":{\"brlx\":\"1\",\"cblx\":\"1\"}}",
        "invoice_amount":12.11,
        "msg":"Success",
        "open_id":"2088102122524333",
        "out_trade_no":"6823789339978248",
        "point_amount":10,
        "receipt_amount":"15.25",
        "send_pay_date":"2014-11-27 15:45:57",
        "store_id":"NJ_S_001",
        "store_name":"证大五道口店",
        "terminal_id":"NJ_T_001",
        "total_amount":88.88,
        "trade_no":"2013112011001004330000121536",
        "trade_status":"TRADE_CLOSED"
    },
    "sign":"uYs6IErMvNG7nowLUesFjv+xD2EzYnFEAtSQ1ndYOFsG61IKWmlndZnW01OWsNNawJE6eMW/DA9w+7+injrXw7s8Fh1ROcDUT2hxOnCYz8X3+4dPzVZT5q2SCauF0PR36C7b/be3R1vn/N5YkSasH89EtKoL85u2jrTTyqdE="
}';
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $this->pay->isMockRsaVerify = true;
        $order = $this->createOrderQueryRequest();
        $order->setAppAuthToken('POiPhfDxOYBfUNn1lkeT');
        $result = $this->pay->orderQuery($order);
        self::assertTrue($result instanceof AliPayTradeQueryResult);
    }
}
