<?php
namespace Pay;

use ConfigFactory;
use Mockery as m;
use Pay\AliPay\AliPayApi;
use Pay\AliPay\Modules\AliPayTradeQueryResult;
use Pay\AliPay\Modules\AliPayTradeStatus;
use Pay\Modules\PayOrder;
use Pay\Modules\LimitPay;
use Pay\Modules\PayOrderQuery;
use Pay\Modules\PayOrderQueryResult;

class AliPayTest extends PayAbstractTest
{
    /**
     * @var AliPay
     */
    protected $pay = null;

    protected function setUp()
    {
        parent::setUp();
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $writer->shouldReceive('info')->andReturn(true);
        $writer->shouldReceive('debug')->andReturn(true);
        $this->pay = new AliPay(ConfigFactory::createAliPayConfig(), $writer);
    }

    public function testOptions()
    {
        self::assertTrue($this->pay->getAliPayApi() instanceof AliPayApi);
        self::assertTrue($this->pay->getAliPayApi() instanceof AliPayApi);
    }

    private function createPayOrder()
    {
        $orderId = date('Ymdhis').mt_rand();
        $result = new PayOrder();
        $result->setOrderId($orderId);
        $result->setGoodsName('goodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoodsgoods');
        $result->setPayAmount(10.10);
        $result->setExtra('extra');
        $result->setLimitPay(new LimitPay(LimitPay::NO_CREDIT));
        $result->setNotifyUrl('http://www.notify.com');
        $result->setCallBackUrl('http://www.callback.com');
        $result->setMerchantUrl('http://www.merchant.com');
        $result->setIp('127.0.0.1');

        return $result;
    }

    private function getPayReturnString()
    {
        return "<form id='alipaysubmit' name='alipaysubmit' action='https://openapi.alipaydev.com/gateway.do?charset=utf-8' method='POST'><input type='hidden' name='app_id' value='2016091600523436'/><input type='hidden' name='method' value='alipay.trade.wap.pay'/><input type='hidden' name='format' value='JSON'/><input type='hidden' name='charset' value='utf-8'/><input type='hidden' name='sign_type' value='RSA'/><input type='hidden' name='sign' value='m1gKXIK+tBKc17TUDSXcF1YWdhzOANpYe7jTcAn4p7t/OdQhxLW9yTAFwA/wBbGM2MTRfl9PVZuDbFhxVvOiZmxDNBYRTRiv/XVxwUUEsuVg87tHt8R8yJYCr/fenhAkUCs/4jv0L4FRnjShxDOp0GrCjbGtO6WdigyuWp75lFo='/><input type='hidden' name='timestamp' value='2016-09-17 21:24:09'/><input type='hidden' name='version' value='1.0'/><input type='hidden' name='biz_content' value='{\"subject\":\"\u5927\u4e50\u900f\",\"out_trade_no\":\"2016091721240957dd43f95de45\",\"total_amount\":9,\"product_code\":\"QUICK_WAP_PAY\",\"body\":\"\u5bf9\u4e00\u7b14\u4ea4\u6613\u7684\u5177\u4f53\u63cf\u8ff0\u4fe1\u606f\u3002\u5982\u679c\u662f\u591a\u79cd\u5546\u54c1\uff0c\u8bf7\u5c06\u5546\u54c1\u63cf\u8ff0\u5b57\u7b26\u4e32\u7d2f\u52a0\u4f20\u7ed9body\",\"timeout_express\":\"90m\",\"seller_id\":\"2088102175865018\"}'/><input type='hidden' name='notify_url' value='http://upgrade.zhuyanbin.cn/notify.php'/><input type='hidden' name='return_url' value='http://upgrade.zhuyanbin.cn/notify.php'/><input type='submit' value='ok' style='display:none;''></form><script>document.forms['alipaysubmit'].submit();</script>";
    }
    private function createAliPayApi()
    {
        $result = m::mock('Pay\AliPay\AliPayApi');
        $result->shouldReceive('pay')->andReturn($this->getPayReturnString());
        return $result;
    }

    protected function getPay()
    {
        return $this->pay;
    }

    public function testPay()
    {
        $this->pay->setAliPayApi($this->createAliPayApi());
        self::assertEquals($this->getPayReturnString(), $this->pay->pay($this->createPayOrder(), '127.0.0.1'));
    }

    private function createAliPayOrderQueryResult()
    {
        $result = new AliPayTradeQueryResult();
        $result->setTradeNo('2013112011001004330000121536');
        $result->setOutTradeNo('6823789339978248');
        $result->setTotalAmount(88.82);
        $result->setReceiptAmount(15.23);
        $result->setTradeStatus(new AliPayTradeStatus('TRADE_CLOSED'));

        return $result;
    }

    public function testOrderQuery()
    {
        $queryResult = $this->createAliPayOrderQueryResult();
        $api = $this->createAliPayApi();
        $queryResult->setTradeStatus(new AliPayTradeStatus(AliPayTradeStatus::TRADE_SUCCESS));
        $api->shouldReceive('orderQuery')->andReturn($queryResult);
        $this->pay->setAliPayApi($api);
        $result = $this->pay->orderQuery($this->createOrderQuery(), '127.0.0.1');
        self::assertTrue($result instanceof PayOrderQueryResult);

        $api = $this->createAliPayApi();
        $queryResult->setTradeStatus(new AliPayTradeStatus(AliPayTradeStatus::OTHERS));
        $api->shouldReceive('orderQuery')->andReturn($queryResult);
        $this->pay->setAliPayApi($api);
        $result = $this->pay->orderQuery($this->createOrderQuery(), '127.0.0.1');
        self::assertTrue($result instanceof PayOrderQueryResult);

        $api = $this->createAliPayApi();
        $queryResult->setTradeStatus(new AliPayTradeStatus(AliPayTradeStatus::TRADE_CLOSED));
        $api->shouldReceive('orderQuery')->andReturn($queryResult);
        $this->pay->setAliPayApi($api);
        $result = $this->pay->orderQuery($this->createOrderQuery(), '127.0.0.1');
        self::assertTrue($result instanceof PayOrderQueryResult);

        $api = $this->createAliPayApi();
        $queryResult->setTradeStatus(new AliPayTradeStatus(AliPayTradeStatus::TRADE_FINISHED));
        $api->shouldReceive('orderQuery')->andReturn($queryResult);
        $this->pay->setAliPayApi($api);
        $result = $this->pay->orderQuery($this->createOrderQuery(), '127.0.0.1');
        self::assertTrue($result instanceof PayOrderQueryResult);

        $api = $this->createAliPayApi();
        $queryResult->setTradeStatus(new AliPayTradeStatus(AliPayTradeStatus::WAIT_BUYER_PAY));
        $api->shouldReceive('orderQuery')->andReturn($queryResult);
        $this->pay->setAliPayApi($api);
        $result = $this->pay->orderQuery($this->createOrderQuery(), '127.0.0.1');
        self::assertTrue($result instanceof PayOrderQueryResult);
    }

    public function testOrderQueryReturnFalse()
    {
        $api = $this->createAliPayApi();
        $api->shouldReceive('orderQuery')->andReturn(false);
        $this->pay->setAliPayApi($api);
        $result = $this->pay->orderQuery($this->createOrderQuery(), '127.0.0.1');
        self::assertFalse($result);
    }
}
