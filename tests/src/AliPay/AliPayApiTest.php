<?php
namespace Pay\AliPay;

use Pay\AliPay\Modules\AliPayNotify;
use Pay\AliPay\Modules\AliPayTradeCloseRequest;
use Pay\AliPay\Modules\AliPayTradeCloseResult;
use Pay\AliPay\Modules\AliPayTradeQueryRequest;
use Pay\AliPay\Modules\AliPayTradeQueryResult;
use Pay\AliPay\Modules\AliPayTradeRefundQueryRequest;
use Pay\AliPay\Modules\AliPayTradeRefundQueryResult;
use Pay\AliPay\Modules\AliPayTradeRefundRequest;
use Pay\AliPay\Modules\AliPayTradeRefundResult;
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

    protected function rsaVerify(array $data)
    {
        if ($this->isMockRsaVerify) {
            return true;
        }
        return parent::rsaVerify($data);
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
        self::assertTrue(strlen($result) > 500);
    }

    public function testParsePayReturnResult()
    {
        $uri = 'total_amount=9.00&timestamp=2016-09-17+03%3A06%3A17&sign=SU2ZqczH5R2cbWp3Ipa3xJJMqAlpiBRZKx1pwQwubV3g5k3qCSdpVoMVhbW7lWYMuNWl3kgxJjBdVt5dmmUgx52SHjT9TA4oeM5dZ6wG6gB%2F3PE9GBH0BLEKxuAG4VvZprsksHrgzq4Yo1aoYPffdkpEEJn7O69CKUpmZ8ePl90%3D&trade_no=2016091721001004360200059782&sign_type=RSA&charset=utf-8&seller_id=2088102175865018&method=alipay.trade.wap.pay.return&app_id=2016091600523436&out_trade_no=2016091703060157dc4299104e3&version=1.0';
        $data = [];
        parse_str($uri, $data);

        self::assertTrue($this->pay->parsePayReturnResult($data) instanceof AliPayTradeWapPayResult);
    }

    public function testParsePayReturnResultErrorAppId()
    {
        $uri = 'total_amount=9.00&timestamp=2016-09-17+03%3A06%3A17&sign=SU2ZqczH5R2cbWp3Ipa3xJJMqAlpiBRZKx1pwQwubV3g5k3qCSdpVoMVhbW7lWYMuNWl3kgxJjBdVt5dmmUgx52SHjT9TA4oeM5dZ6wG6gB%2F3PE9GBH0BLEKxuAG4VvZprsksHrgzq4Yo1aoYPffdkpEEJn7O69CKUpmZ8ePl90%3D&trade_no=2016091721001004360200059782&sign_type=RSA&charset=utf-8&seller_id=2088102175865018&method=alipay.trade.wap.pay.return&app_id=20160910523436&out_trade_no=2016091703060157dc4299104e3&version=1.0';
        $data = [];
        parse_str($uri, $data);
        $this->pay->isMockRsaVerify = true;
        self::assertFalse($this->pay->parsePayReturnResult($data));
    }

    public function testParsePayReturnVerifyFalse()
    {
        $uri = 'total_amount=9.00&timestamp=2016-09-17+03%3A06%3A17&sign=SU2ZqczH5R2cbWp3Ipa3xJJMqAlpiBRZKx1pwQwubV3g5k3qCSdpVoMVhbW7lWYMuNWl3kgxJjBdVt5dmmUgx52SHjT9TA4oeM5dZ6wG6gB%2F3PE9GBH0BLEKxuAG4VvZprsksHrgzq4Yo1aoYPffdkpEEJn7O69CKUpmZ8ePl90%3D&trade_no=2016091721001004360200059782&sign_type=RSA&charset=utf-8&seller_id=2088102175865018&method=alipay.trade.wap.pay.return&app_id=2016091600523436&out_trade_no=2016091703060157dc4299104e3&version=1.0';
        $data = [];
        parse_str($uri, $data);
        unset($data['total_amount']);
        self::assertFalse($this->pay->parsePayReturnResult($data));
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
        self::assertFalse($this->pay->parsePayReturnResult($data));
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
        self::assertFalse($this->pay->parsePayReturnResult($data));
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

    private function createOrderCloseRequest()
    {
        $result = new AliPayTradeCloseRequest();
        $result->setNotifyUrl('http://www.zhuyanbin.com/CallBack/notify_url.jsp');

        $result->setOutTradeNo('2016091703060157dc4299104e3');
        $result->setOperatorId('YX01');
        $result->setAppAuthToken('POiPhfDxOYBfUNn1lkeT');

        return $result;
    }
    public function testOrderClose()
    {
        $data ='{
    "alipay_trade_close_response":{
        "code":"10000",
        "msg":"Success",
        "out_trade_no":"YX_001",
        "trade_no":"2013112111001004500000675971"
    },
    "sign":"uYs6IErMvNG7nowLUesFjv+xD2EzYnFEAtSQ1ndYOFsG61IKWmlndZnW01OWsNNawJE6eMW/DA9w+7+injrXw7s8Fh1ROcDUT2hxOnCYz8X3+4dPzVZT5q2SCauF0PR36C7b/be3R1vn/N5YkSasH89EtKoL85u2jrTTyqdE="
}';

        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $this->pay->isMockRsaVerify = true;
        $result = $this->pay->closeOrder($this->createOrderCloseRequest());
        self::assertTrue($result instanceof AliPayTradeCloseResult);
    }

    public function testOrderCloseTradeNoIsEmpty()
    {
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(false);
        $this->pay->setClient($client);
        $this->pay->isMockRsaVerify = true;
        $result = $this->pay->closeOrder(new AliPayTradeCloseRequest());
        self::assertFalse($result);
    }

    public function testOrderCloseExecReturnFalse()
    {
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(false);
        $this->pay->setClient($client);
        $this->pay->isMockRsaVerify = true;
        $result = $this->pay->closeOrder($this->createOrderCloseRequest());
        self::assertFalse($result);
    }

    public function testOrderCloseErrorReturnString()
    {
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn('test');
        $this->pay->setClient($client);
        $this->pay->isMockRsaVerify = true;
        $result = $this->pay->closeOrder($this->createOrderCloseRequest());
        self::assertFalse($result);
    }

    public function testOrderCloseErrorCode()
    {
        $data = '{
    "alipay_trade_close_response":{
        "code":"20000",
        "msg":"Service Currently Unavailable",
        "sub_code":"isp.unknow-error",
        "sub_msg":"系统繁忙"
    }
}';
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $result = $this->pay->closeOrder($this->createOrderCloseRequest());
        self::assertFalse($result);
    }

    public function testOrderCloseVerifyFalse()
    {
        $data ='{
    "alipay_trade_close_response":{
        "code":"10000",
        "msg":"Success",
        "out_trade_no":"YX_001",
        "trade_no":"2013112111001004500000675971"
    },
    "sign":"uYs6IErMvNG7nowLUesFjv+xD2EzYnFEAtSQ1ndYOFsG61IKWmlndZnW01OWsNNawJE6eMW/DA9w+7+injrXw7s8Fh1ROcDUT2hxOnCYz8X3+4dPzVZT5q2SCauF0PR36C7b/be3R1vn/N5YkSasH89EtKoL85u2jrTTyqdE="
}';
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $result = $this->pay->closeOrder($this->createOrderCloseRequest());
        self::assertFalse($result);
    }

    private function createOrderRefundRequest()
    {
        $result = new AliPayTradeRefundRequest();
        $result->setTradeNo('2016091721001004360200059782');
        $result->setOutTradeNo('2016091703060157dc4299104e3');
        $result->setRefundAmount(0.20);
        $result->setRefundReason('正常退款');
        $result->setOperatorId('YX01');
        $no = date('YmdHis').uniqid();
        $result->setOutRequestNo($no);
        return $result;
    }

    public function testOrderRefund()
    {
        $data = '{"alipay_trade_refund_response":{"code":"10000","msg":"Success","buyer_logon_id":"kxk***@sandbox.com","buyer_user_id":"2088102169132360","fund_change":"Y","gmt_refund_pay":"2016-09-17 13:41:16","open_id":"20880016753930000893495010410136","out_trade_no":"2016091703060157dc4299104e3","refund_fee":"0.10","send_back_fee":"0.00","trade_no":"2016091721001004360200059782"},"sign":"Dm8bgz00F+wEs9jN5NDdlEnjDrxtKW6wImO+hUw2cXKDNIeeT0fqxuDbqQbwTKhcTuIWodTcsp2YSVdpsdwjpv49MAVi0zhbCdoGlmhgYyxs4C9R0/1MenAiB6ydwdI9sQmCLORnMmR6YCY7YqMlabb1q0rV1BBi5oVyyQjuHP4="}';
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $result = $this->pay->refund($this->createOrderRefundRequest());
        self::assertTrue($result instanceof AliPayTradeRefundResult);
    }

    public function testOrderRefundTradeNoIsEmpty()
    {
        $result = $this->pay->refund(new AliPayTradeRefundRequest());
        self::assertFalse($result);
    }

    public function testOrderRefundAmountIsZero()
    {
        $request = $this->createOrderRefundRequest();
        $request->setRefundAmount(0);
        $result = $this->pay->refund($request);
        self::assertFalse($result);
    }

    public function testOrderRefundExeReturnFalse()
    {
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(false);
        $this->pay->setClient($client);
        $result = $this->pay->refund($this->createOrderRefundRequest());
        self::assertFalse($result);
    }

    public function testOrderRefundReturnErrorString()
    {
        $data = 'test';
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $request = $this->createOrderRefundRequest();
        $request->setAppAuthToken('POiPhfDxOYBfUNn1lkeT');
        $result = $this->pay->refund($request);
        self::assertFalse($result);
    }

    public function testOrderRefundReturnErrorCode()
    {
        $data = '{
    "alipay_trade_refund_response":{
        "code":"20000",
        "msg":"Service Currently Unavailable",
        "sub_code":"isp.unknow-error",
        "sub_msg":"系统繁忙"
    },
    "sign":"ERITJKEIJKJHKKKKKKKHJEREEEEEEEEEEE"
}';
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $result = $this->pay->refund($this->createOrderRefundRequest());
        self::assertFalse($result);
    }

    public function testOrderRefundVerifyFalse()
    {
        $data = '{"alipay_trade_refund_response":{"code":"10000","msg":"Success","buyer_logon_id":"kxk***@sandbox.com","buyer_user_id":"2088102169132360","fund_change":"Y","gmt_refund_pay":"2016-09-17 13:41:16","open_id":"20880016753930000893495010410136","out_trade_no":"2016091703060157dc4299104e3","refund_fee":"0.10","send_back_fee":"0.00","trade_no":"2016091721001004360200059782"},"sign":"Dm8bgz00F+wEs9jN5NDdlEnjDrxtKW6wImO+hUw2cXKDNIeeT0fqxuDbqQbwTKhcTuIWodTcsp2YSVdpsdwjpv49MAVi0zhbCdoGlmhgY4C9R0/1MenAiB6ydwdI9sQmCLORnMmR6YCY7YqMlabb1q0rV1BBi5oVyyQjuHP4="}';
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $result = $this->pay->refund($this->createOrderRefundRequest());
        self::assertFalse($result);
    }

    public function testOrderRefundWithMockVerify()
    {
        $data = '{
    "alipay_trade_refund_response":{
        "buyer_logon_id":"159****5620",
        "buyer_user_id":"2088101117955611",
        "code":"10000",
        "fund_change":"Y",
        "gmt_refund_pay":"2014-11-27 15:45:57",
        "msg":"Success",
        "open_id":"2088102122524333",
        "out_trade_no":"6823789339978248",
        "refund_detail_item_list":[{
            "amount":10,
            "fund_channel":"ALIPAYACCOUNT",
            "real_amount":11.21
        }],
        "refund_fee":88.88,
        "send_back_fee":"1.8",
        "store_name":"望湘园联洋店",
        "trade_no":"支付宝交易号"
    },
    "sign":"ERITJKEIJKJHKKKKKKKHJEREEEEEEEEEEE"
}';
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $this->pay->isMockRsaVerify = true;
        $result = $this->pay->refund($this->createOrderRefundRequest());
        self::assertTrue($result instanceof AliPayTradeRefundResult);
    }

    private function createOrderRefundQueryRequest()
    {
        $result = new AliPayTradeRefundQueryRequest();
        $result->setTradeNo('2016091721001004360200059782');
        $result->setOutTradeNo('2016091703060157dc4299104e3');
        $result->setOutRequestNo('2016091714150157dcdf6550595');
        $result->setAppAuthToken('POiPhfDxOYBfUNn1lkeT');
        return $result;

    }
    public function testRefundQuery()
    {
        $data = '{"alipay_trade_fastpay_refund_query_response":{"code":"10000","msg":"Success","out_request_no":"2016091714150157dcdf6550595","out_trade_no":"2016091703060157dc4299104e3","refund_amount":"0.20","total_amount":"9.00","trade_no":"2016091721001004360200059782"},"sign":"AFn0kiUIj94lo6WuFEAT788rwzHVKffSwPzj2mZ+uFSE6ZCS04fanLNhKerinTtU6+7MmAxf3xgduWFHbTniU75xrsiOrbU6sDG5b/wNzuIw7/hSr2AzTeTtIO+nX825hTOdOaN9oy9wWFFGFX/6UGdz7G/PjefuMQ+BqfDolGI="}';
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $result = $this->pay->refundQuery($this->createOrderRefundQueryRequest());
        self::assertTrue($result instanceof AliPayTradeRefundQueryResult);
    }

    public function testRefundQueryEmptyTradeNo()
    {
        $request = $this->createOrderRefundQueryRequest();
        $request->setTradeNo('');
        $request->setOutTradeNo('');
        $result = $this->pay->refundQuery($request);
        self::assertFalse($result);
    }

    public function testRefundQueryEmptyRequestNo()
    {
        $request = $this->createOrderRefundQueryRequest();
        $request->setOutRequestNo('');
        $result = $this->pay->refundQuery($request);
        self::assertFalse($result);
    }

    public function testRefundQueryExecReturnFalse()
    {
        $request = $this->createOrderRefundQueryRequest();
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(false);
        $this->pay->setClient($client);
        $result = $this->pay->refundQuery($request);
        self::assertFalse($result);
    }

    public function testRefundQueryReturnErrorString()
    {
        $request = $this->createOrderRefundQueryRequest();
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn('test');
        $this->pay->setClient($client);
        $result = $this->pay->refundQuery($request);
        self::assertFalse($result);
    }

    public function testRefundQueryReturnErrorCode()
    {
        $data = '{
    "alipay_trade_fastpay_refund_query_response":{
        "code":"20000",
        "msg":"Service Currently Unavailable",
        "sub_code":"isp.unknow-error",
        "sub_msg":"系统繁忙"
    },
    "sign":"ERITJKEIJKJHKKKKKKKHJEREEEEEEEEEEE"
}
';
        $request = $this->createOrderRefundQueryRequest();
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $result = $this->pay->refundQuery($request);
        self::assertFalse($result);
    }

    public function testRefundQueryVerifyFalse()
    {
        $data = '{"alipay_trade_fastpay_refund_query_response":{"code":"10000","msg":"Success","out_request_no":"2016091714150157dcdf6550595","out_trade_no":"2016091703060157dc4299104e3","refund_amount":"0.20","total_amount":"9.00","trade_no":"2016091721001004360200059782"},"sign":"AFn0kiUIj94lo6WuFEAT788rwzHVKffSwPzj2mZ+uZCS04fanLNhKerinTtU6+7MmAxf3xgduWFHbTniU75xrsiOrbU6sDG5b/wNzuIw7/hSr2AzTeTtIO+nX825hTOdOaN9oy9wWFFGFX/6UGdz7G/PjefuMQ+BqfDolGI="}';

        $request = $this->createOrderRefundQueryRequest();
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $result = $this->pay->refundQuery($request);
        self::assertFalse($result);
    }

    public function testRefundQueryMockVerifyTrue()
    {
        $data = '{
    "alipay_trade_fastpay_refund_query_response":{
        "code":"10000",
        "msg":"Success",
        "out_request_no":"20150320010101001",
        "out_trade_no":"20150320010101001",
        "refund_amount":12.33,
        "refund_reason":"用户退款请求",
        "total_amount":100.20,
        "trade_no":"2014112611001004680073956707"
    },
    "sign":"ERITJKEIJKJHKKKKKKKHJEREEEEEEEEEEE"
}';

        $request = $this->createOrderRefundQueryRequest();
        $client = $this->createClient();
        $client->shouldReceive('exec')->andReturn(true);
        $client->shouldReceive('getResponse')->andReturn($data);
        $this->pay->setClient($client);
        $this->pay->isMockRsaVerify =true;
        $result = $this->pay->refundQuery($request);
        self::assertTrue($result instanceof AliPayTradeRefundQueryResult);
    }

    public function testParseNotify()
    {
        $str = 'a:16:{s:9:"notify_id";s:34:"05389b31a838d4698d2f59b9f808f19is2";s:11:"gmt_payment";s:19:"2016-09-17 12:50:51";s:11:"notify_type";s:17:"trade_status_sync";s:4:"sign";s:172:"SFED+VWKFPewcuAe7BOjywgLRAmh9JWHJAiDOLrtRqTZs0CrlUCg3ahKby73Rfy1nlqNuhtno/GoBianQQtYTx8pDasoj5MHhhAIuK+Z4UitRJC3uzgdELfQgh/xOdl0RGhEgNtXWCTRZzgcpeWQ7J0cdaIQJ+MYR3nhlqlSn/Y=";s:8:"trade_no";s:28:"2016091721001004360200059786";s:8:"buyer_id";s:16:"2088102169132360";s:4:"body";s:106:"对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body";s:6:"app_id";s:16:"2016091600523436";s:10:"gmt_create";s:19:"2016-09-17 12:50:50";s:12:"out_trade_no";s:27:"2016091712452457dcca649f405";s:9:"seller_id";s:16:"2088102175865018";s:11:"notify_time";s:19:"2016-09-17 12:50:51";s:7:"subject";s:9:"大乐透";s:12:"trade_status";s:13:"TRADE_SUCCESS";s:12:"total_amount";s:4:"9.00";s:9:"sign_type";s:3:"RSA";}';
        $result = $this->pay->parseNotify(unserialize($str));
        self::assertTrue($result instanceof AliPayNotify);
    }

    public function testParseNotifyVerifyFalse()
    {
        $str = 'a:16:{s:9:"notify_id";s:34:"05389b31a838d4698d2f59b9f808f19is2";s:11:"gmt_payment";s:19:"2016-09-17 12:50:51";s:11:"notify_type";s:17:"trade_status_sync";s:4:"sign";s:172:"SFED+VWKFPewcuAe7BOjywgLRAmh9JWHJAiDOLrtRqTZs0CrlUCg3ahKby73Rfy1nlqNuhtno/GoBianQQtYTx8pDasoj5MHhhAIuK+Z4UitRJC3uzgdELfQgh/xOdl0RGhEgNtXWCTRZzgcpeWQ7J0cdaIQJ+MYR3nhlqlSn/Y=";s:8:"trade_no";s:28:"2016091721001004360200059786";s:8:"buyer_id";s:16:"2088102169132360";s:4:"body";s:106:"对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body";s:6:"app_id";s:16:"2016091600523436";s:10:"gmt_create";s:19:"2016-09-17 12:50:50";s:12:"out_trade_no";s:27:"2016091712452457dcca649f405";s:9:"seller_id";s:16:"2088102175865018";s:11:"notify_time";s:19:"2016-09-17 12:50:51";s:7:"subject";s:9:"大乐透";s:12:"trade_status";s:13:"TRADE_SUCCESS";s:12:"total_amount";s:4:"9.00";s:9:"sign_type";s:3:"RSA";}';
        $data = unserialize($str);
        $data['notify_id'] = '05389b31a838d4698d2f';
        $result = $this->pay->parseNotify($data);
        self::assertFalse($result);
    }

    public function testParseNotifyErrorAppId()
    {
        $str = 'a:16:{s:9:"notify_id";s:34:"05389b31a838d4698d2f59b9f808f19is2";s:11:"gmt_payment";s:19:"2016-09-17 12:50:51";s:11:"notify_type";s:17:"trade_status_sync";s:4:"sign";s:172:"SFED+VWKFPewcuAe7BOjywgLRAmh9JWHJAiDOLrtRqTZs0CrlUCg3ahKby73Rfy1nlqNuhtno/GoBianQQtYTx8pDasoj5MHhhAIuK+Z4UitRJC3uzgdELfQgh/xOdl0RGhEgNtXWCTRZzgcpeWQ7J0cdaIQJ+MYR3nhlqlSn/Y=";s:8:"trade_no";s:28:"2016091721001004360200059786";s:8:"buyer_id";s:16:"2088102169132360";s:4:"body";s:106:"对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body";s:6:"app_id";s:16:"2016091600523436";s:10:"gmt_create";s:19:"2016-09-17 12:50:50";s:12:"out_trade_no";s:27:"2016091712452457dcca649f405";s:9:"seller_id";s:16:"2088102175865018";s:11:"notify_time";s:19:"2016-09-17 12:50:51";s:7:"subject";s:9:"大乐透";s:12:"trade_status";s:13:"TRADE_SUCCESS";s:12:"total_amount";s:4:"9.00";s:9:"sign_type";s:3:"RSA";}';
        $data = unserialize($str);
        $data['app_id'] = '20160916005236';
        $this->pay->isMockRsaVerify = true;
        $result = $this->pay->parseNotify($data);
        self::assertFalse($result);
    }

    public function testParseNotifyFullSettings()
    {
        $str = 'a:16:{s:9:"notify_id";s:34:"05389b31a838d4698d2f59b9f808f19is2";s:11:"gmt_payment";s:19:"2016-09-17 12:50:51";s:11:"notify_type";s:17:"trade_status_sync";s:4:"sign";s:172:"SFED+VWKFPewcuAe7BOjywgLRAmh9JWHJAiDOLrtRqTZs0CrlUCg3ahKby73Rfy1nlqNuhtno/GoBianQQtYTx8pDasoj5MHhhAIuK+Z4UitRJC3uzgdELfQgh/xOdl0RGhEgNtXWCTRZzgcpeWQ7J0cdaIQJ+MYR3nhlqlSn/Y=";s:8:"trade_no";s:28:"2016091721001004360200059786";s:8:"buyer_id";s:16:"2088102169132360";s:4:"body";s:106:"对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body";s:6:"app_id";s:16:"2016091600523436";s:10:"gmt_create";s:19:"2016-09-17 12:50:50";s:12:"out_trade_no";s:27:"2016091712452457dcca649f405";s:9:"seller_id";s:16:"2088102175865018";s:11:"notify_time";s:19:"2016-09-17 12:50:51";s:7:"subject";s:9:"大乐透";s:12:"trade_status";s:13:"TRADE_SUCCESS";s:12:"total_amount";s:4:"9.00";s:9:"sign_type";s:3:"RSA";}';
        $this->pay->isMockRsaVerify = true;
        $data = unserialize($str);
        $data['out_biz_no'] = 'HZRF001';
        $data['buyer_logon_id'] = '15901825620';
        $data['seller_email'] = 'zhuzhanghu@alitest.com';
        $data['receipt_amount'] = 10.22;
        $data['invoice_amount'] = 20.22;
        $data['buyer_pay_amount'] = 2.22;
        $data['point_amount'] = 12.22;
        $data['refund_fee'] = 11.22;
        $data['gmt_refund'] = '2015-04-28 15:45:57.320';
        $data['gmt_close'] = '2015-04-28 15:45:57';
        $data['fund_bill_list'] = [["amount"=>"15.00","fundChannel"=>"ALIPAYACCOUNT"]];
        $result = $this->pay->parseNotify($data);
        self::assertTrue($result instanceof AliPayNotify);
    }
}
