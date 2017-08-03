<?php
namespace Pay\AliPay;

use Pay\AliPay\Modules\AliPayBase;
use Pay\AliPay\Modules\AliPayCharset;
use Pay\AliPay\Modules\AliPayNotify;
use Pay\AliPay\Modules\AliPayRequest;
use Pay\AliPay\Modules\AliPayTradeCloseRequest;
use Pay\AliPay\Modules\AliPayTradeCloseResult;
use Pay\AliPay\Modules\AliPayTradeFundBill;
use Pay\AliPay\Modules\AliPayTradeQueryRequest;
use Pay\AliPay\Modules\AliPayTradeQueryResult;
use Pay\AliPay\Modules\AliPayTradeRefundQueryRequest;
use Pay\AliPay\Modules\AliPayTradeRefundQueryResult;
use Pay\AliPay\Modules\AliPayTradeRefundRequest;
use Pay\AliPay\Modules\AliPayTradeRefundResult;
use Pay\AliPay\Modules\AliPayTradeStatus;
use Pay\AliPay\Modules\AliPayTradeWapPayRequest;
use Pay\AliPay\Modules\AliPayTradeWapPayResult;
use Simple\Log\Writer;
use Simple\Http\Client;
use Pay\AliPay\Modules\AliPayConfig;

class AliPayApi
{
    /**
     * 成功返回的code代码
     */
    const CODE_SUCCESS = '10000';

    /**
     * @var AliPayConfig
     */
    private $config = null;

    /**
     * @var Writer
     */
    private $logWriter = null;

    /**
     * AliPayApi constructor.
     * @param AliPayConfig $config
     * @param Writer $writer
     */
    public function __construct(AliPayConfig $config, Writer $writer)
    {
        $this->config = $config;
        $this->logWriter = $writer;
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        return new Client();
    }

    /**
     * @return Writer
     */
    protected function getLogWriter()
    {
        return $this->logWriter;
    }

    /**
     * @return AliPayConfig
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * 除去数组中的空值和签名参数
     * @param array $data   签名参数组
     * @return array        去掉空值与签名参数后的新签名参数组
     */
    protected function filter(array $data)
    {
        $result = array();

        foreach ($data as $key => $value) {
            if (('sign' != $key) && ('' != $value)) {
                $result[$key]   = $value;
            }
        }

        return $result;
    }

    /**
     * 将数组签名
     * @param array $data
     * @return string
     */
    protected function generateSign(array $data) {
        return $this->sign($this->getSignContent($data));
    }

    /**
     * 将字符串签名
     * @param string $data
     * @return string
     * @throws AliPayException
     */
    protected function sign($data) {
        if (!is_file($this->getConfig()->getPrivateKeyPath())) {
            throw new AliPayException('RSA私钥文件不存在');
        }

        $priKey = trim(file_get_contents($this->getConfig()->getPrivateKeyPath()));
        $res = openssl_get_privatekey($priKey);
        if (!$res) {
            throw new AliPayException('您使用的私钥格式错误，请检查RSA私钥配置');
        }

        openssl_sign($data, $sign, $res);

        openssl_free_key($res);
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * 拼接签名的数据
     * @param array $data
     * @return string
     */
    protected function getSignContent(array $data) {
        $data = $this->filter($data);
        ksort($data);
        reset($data);
        $result = '';

        foreach ($data as $k => $v) {
            $result .= "{$k}={$v}&";
        }

        return substr($result, 0, -1);
    }

    /**
     * 验证数组数据的签名
     * @param array $data
     * @return bool
     */
    protected function rsaVerify(array $data) {
        $sign = $data['sign'];

        if (isset($data['sign_type'])) {
            unset($data['sign_type']);
        }

        return $this->verify($this->getSignContent($data), $sign);
    }

    /**
     * 验证签名
     * @param string $data
     * @param string $sign
     * @return bool
     * @throws AliPayException
     */
    protected function verify($data, $sign) {
        if (!is_file($this->getConfig()->getPublicKeyPath())) {
            throw new AliPayException('RSA公钥文件不存在');
        }

        //读取公钥文件
        $pubKey = trim(file_get_contents($this->getConfig()->getPublicKeyPath()));
        //转换为openssl格式密钥
        $res = openssl_get_publickey($pubKey);

        if (!$res) {
            throw new AliPayException('支付宝RSA公钥错误。请检查公钥文件格式是否正确');
        }

        //调用openssl内置方法验签，返回bool值
        $result = (1 == openssl_verify($data, base64_decode($sign), $res));
        //释放资源
        openssl_free_key($res);

        return $result;
    }

    protected function getBaseParams(AliPayBase $request)
    {
        $result = [];
        $result['app_id'] = $request->getAppId();
        $result['method'] = $request->getMethod();
        $result['format'] = $request->getFormat();
        $result['charset'] = $request->getCharset()->getValue();
        $result['sign_type'] = $request->getSignType();
        $result['sign'] = $request->getSign();
        $result['timestamp'] = $request->getTimeStamp();
        $result['version'] = $request->getVersion();

        return $result;
    }

    protected function getRequestParams(AliPayRequest $request)
    {
        $result = $this->getBaseParams($request);
        $result['biz_content'] = $request->getBizContent();

        return $result;
    }

    protected function getWapPayRequestParams(AliPayTradeWapPayRequest $request)
    {
        $result = $this->getRequestParams($request);
        if ('' != $request->getNotifyUrl()) {
            $result['notify_url'] = $request->getNotifyUrl();
        }

        if ('' != $request->getReturnUrl()) {
            $result['return_url'] = $request->getReturnUrl();
        }

        return $result;
    }

    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param array $data
     * @param AliPayBase $request
     * @return string
     */
    protected function buildRequestForm(array $data, AliPayBase $request) {
        $result = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->getConfig()->getGateWayUrl()."?charset=".trim($request->getCharset()->getValue())."' method='POST'>";

        foreach ($data as $k => $v) {
            if ('' != $v) {
                $v = str_replace("'","&apos;",$v);
                $result.= "<input type='hidden' name='".$k."' value='".$v."'/>";
            }
        }

        //submit按钮控件请不要含有name属性
        $result .= "<input type='submit' value='ok' style='display:none;''></form>";
        $result .= "<script>document.forms['alipaysubmit'].submit();</script>";
        return $result;
    }

    /**
     * init AliPayBase
     * @param AliPayBase $request
     * @return AliPayBase
     */
    protected function initAliPayBase(AliPayBase $request)
    {
        $config = $this->getConfig();
        $request->setAppId($config->getAppId());
        $request->setCharset(AliPayCharset::createUTF8Charset());
        $request->setTimeStamp(date('Y-m-d H:i:s'));
        return $request;
    }

    /**
     * init AliPayTradeWapPayRequest
     * @param AliPayTradeWapPayRequest $request
     * @return AliPayTradeWapPayRequest
     */
    protected function initAliPayTradeWapPayRequest(AliPayTradeWapPayRequest $request)
    {
        $config = $this->getConfig();
        $request->setNotifyUrl($config->getNotifyUrl());
        $request->setReturnUrl($config->getNotifyUrl());
        $request->setSellerId($config->getSellerId());

        $request = $this->initAliPayBase($request);

        return $request;
    }

    /**
     * 支付请求
     * @param AliPayTradeWapPayRequest $request
     * @return bool|string
     */
    public function pay(AliPayTradeWapPayRequest $request)
    {
        if (('' == $request->getSubject()) ||
            ('' == $request->getOutTradeNo()) ||
            (0.01 > $request->getTotalAmount()) ||
            ('' == $request->getProductCode())) {
            return false;
        }

        $request = $this->initAliPayTradeWapPayRequest($request);

        $data = $this->getWapPayRequestParams($request);
        //待签名字符串
        $data['sign'] = $this->generateSign($data);
        return $this->buildRequestForm($data, $request);
    }

    /**
     * 解析支付的同步返回数据
     * @param array $data
     * @return false|AliPayTradeWapPayResult
     */
    public function parsePayReturnResult(array $data)
    {
        if (!$this->rsaVerify($data)) {
            $this->getLogWriter()->error("parsePayReturnResult rsaVerify fail: " . serialize($data));
            return false;
        }

        if ($this->getConfig()->getAppId() != $data['app_id']) {
            return false;
        }

        $result = new AliPayTradeWapPayResult();
        $result->setAppId($data['app_id']);
        $result->setCharset(AliPayCharset::build($data['charset']));
        $result->setTimeStamp($data['timestamp']);
        $result->setTradeNo($data['trade_no']);
        $result->setOutTradeNo($data['out_trade_no']);
        $result->setTotalAmount(floatval($data['total_amount']));
        $result->setSellerId($data['seller_id']);

        return $result;
    }

    /**
     * @param AliPayTradeQueryRequest $request
     * @return array
     */
    protected function getOrderQueryRequestParams(AliPayTradeQueryRequest $request)
    {
        $result = $this->getRequestParams($request);
        if ('' != $request->getAppAuthToken()) {
            $result['app_auth_token'] = $request->getAppAuthToken();
        }

        return $result;
    }

    protected function getOrderCloseRequestParams(AliPayTradeCloseRequest $request)
    {
        $result = $this->getRequestParams($request);
        if ('' != $request->getAppAuthToken()) {
            $result['app_auth_token'] = $request->getAppAuthToken();
        }

        if ('' != $request->getNotifyUrl()) {
            $result['notify_url'] = $request->getNotifyUrl();
        }

        return $result;
    }

    protected function getOrderRefundRequestParams(AliPayTradeRefundRequest $request)
    {
        $result = $this->getRequestParams($request);
        if ('' != $request->getAppAuthToken()) {
            $result['app_auth_token'] = $request->getAppAuthToken();
        }

        return $result;
    }

    protected function getOrderRefundQueryRequestParams(AliPayTradeRefundQueryRequest $request)
    {
        $result = $this->getRequestParams($request);
        if ('' != $request->getAppAuthToken()) {
            $result['app_auth_token'] = $request->getAppAuthToken();
        }

        return $result;
    }

    /**
     * build url
     * @param array $data
     * @return string
     */
    protected function buildUrl(array $data) {
        $result = $this->getConfig()->getGateWayUrl() . '?';
        foreach ($data as $k => $v) {
            $v = urlencode($v);
            $result .= "{$k}={$v}&";
        }

        return substr($result, 0 , -1);
    }

    /**
     * create AliPayTradeFundBill array
     * @param array $data
     * @return array
     */
    protected function createFundBillArray(array $data)
    {
        $result = [];
        foreach ($data as $v) {
            $fb = new AliPayTradeFundBill();
            if (isset($v['fundChannel'])) {
                $fb->setFundChannel($v['fundChannel']);
            }

            if (isset($v['fund_channel'])) {
                $fb->setFundChannel($v['fund_channel']);
            }

            if (isset($v['amount'])) {
                $fb->setAmount(floatval($v['amount']));
            }

            if (isset($v['real_amount'])) {
                $fb->setRealAmount(floatval($v['real_amount']));
            }

            if ('' != $fb->getFundChannel()) {
                $result[] = $fb;
            }
        }

        return $result;
    }

    /**
     * 查询订单
     * @param AliPayTradeQueryRequest $request
     * @return AliPayTradeQueryResult|false
     */
    public function orderQuery(AliPayTradeQueryRequest $request)
    {
        if (('' == $request->getTradeNo()) && ('' == $request->getOutTradeNo())) {
            return false;
        }

        $request = $this->initAliPayBase($request);
        $data = $this->getOrderQueryRequestParams($request);
        $data['sign'] = $this->generateSign($data);

        $client = $this->getClient();
        $client->setUrl($this->buildUrl($data));

        if (!$client->exec()) {
            $this->getLogWriter()->error("order query exec error:" . serialize($data));
            return false;
        }

        $result = $client->getResponse();

        $data = json_decode($result, true);
        if (!isset($data['alipay_trade_query_response'])) {
            $this->getLogWriter()->error("order query json_decode fail: " . $result);
            return false;
        }

        $sign = '';
        if (isset($data['sign'])) {
            $sign = $data['sign'];
        }

        $data = $data['alipay_trade_query_response'];

        if (self::CODE_SUCCESS != $data['code']) {
            $this->getLogWriter()->error("order query exec error:" . serialize($data));
            return false;
        }

        if (!$this->verify(json_encode($data), $sign)) {
            $this->getLogWriter()->error("order query rsaVerify fail: " . $result);
            return false;
        }

        $result = new AliPayTradeQueryResult();
        $result->setCode($data['code']);
        $result->setMsg($data['msg']);
        $result->setBuyerLogonId($data['buyer_logon_id']);
        $result->setBuyerUserId($data['buyer_user_id']);
        $result->setOutTradeNo($data['out_trade_no']);
        $result->setReceiptAmount(floatval($data['receipt_amount']));
        $result->setSendPayDate($data['send_pay_date']);
        $result->setTotalAmount(floatval($data['total_amount']));
        $result->setTradeNo($data['trade_no']);
        $result->setTradeStatus(new AliPayTradeStatus($data['trade_status']));

        if (isset($data['buyer_pay_amount'])) {
            $result->setBuyerPayAmount(floatval($data['buyer_pay_amount']));
        }

        if (isset($data['point_amount'])) {
            $result->setPointAmount(floatval($data['point_amount']));
        }

        if (isset($data['invoice_amount'])) {
            $result->setInvoiceAmount(floatval($data['invoice_amount']));
        }

        if (isset($data['alipay_store_id'])) {
            $result->setAlipayStoreId($data['alipay_store_id']);
        }

        if (isset($data['store_id'])) {
            $result->setStoreId($data['store_id']);
        }

        if (isset($data['terminal_id'])) {
            $result->setTerminalId($data['terminal_id']);
        }

        if (isset($data['store_name'])) {
            $result->setStoreName($data['store_name']);
        }

        if (isset($data['industry_sepc_detail'])) {
            $result->setIndustrySepcDetail($data['industry_sepc_detail']);
        }

        if (isset($data['fund_bill_list'])) {
            if (!is_array($data['fund_bill_list'])) {
                $data['fund_bill_list'] = json_decode($data['fund_bill_list'], true);
            }
            
            $result->setFundBillList($this->createFundBillArray($data['fund_bill_list']));
        }

        return $result;
    }

    /**
     * 关闭交易
     * @param AliPayTradeCloseRequest $request
     * @return bool|AliPayTradeCloseResult
     */
    public function closeOrder(AliPayTradeCloseRequest $request)
    {
        if (('' == $request->getTradeNo()) && ('' == $request->getOutTradeNo())) {
            return false;
        }

        $request = $this->initAliPayBase($request);
        $data = $this->getOrderCloseRequestParams($request);
        $data['sign'] = $this->generateSign($data);

        $client = $this->getClient();
        $client->setUrl($this->buildUrl($data));

        if (!$client->exec()) {
            $this->getLogWriter()->error("order close exec error:" . serialize($data));
            return false;
        }

        $result = $client->getResponse();

        $data = json_decode($result, true);
        if (!isset($data['alipay_trade_close_response'])) {
            $this->getLogWriter()->error("order close json_decode fail: " . $result);
            return false;
        }

        $sign = '';
        if (isset($data['sign'])) {
            $sign = $data['sign'];
        }

        $data = $data['alipay_trade_close_response'];

        if (self::CODE_SUCCESS != $data['code']) {
            $this->getLogWriter()->error("order close exec error:" . serialize($data));
            return false;
        }

        if (!$this->verify(json_encode($data), $sign)) {
            $this->getLogWriter()->error("order close rsaVerify fail: " . $result);
            return false;
        }

        $result = new AliPayTradeCloseResult();
        $result->setCode($data['code']);
        $result->setMsg($data['msg']);

        if (isset($data['trade_no'])) {
            $result->setTradeNo($data['trade_no']);
        }

        if (isset($data['out_trade_no'])) {
            $result->setOutTradeNo($data['out_trade_no']);
        }

        return $result;
    }

    /**
     * @param AliPayTradeRefundRequest $request
     * @return AliPayTradeRefundResult|false
     */
    public function refund(AliPayTradeRefundRequest $request)
    {
        if (('' == $request->getTradeNo()) && ('' == $request->getOutTradeNo())) {
            return false;
        }

        if ($request->getRefundAmount() < 0.01) {
            return false;
        }

        $request = $this->initAliPayBase($request);
        $data = $this->getOrderRefundRequestParams($request);
        $data['sign'] = $this->generateSign($data);

        $client = $this->getClient();
        $client->setUrl($this->buildUrl($data));

        if (!$client->exec()) {
            $this->getLogWriter()->error("order refund exec error:" . serialize($data));
            return false;
        }

        $result = $client->getResponse();

        $data = json_decode($result, true);
        if (!isset($data['alipay_trade_refund_response'])) {
            $this->getLogWriter()->error("order refund json_decode fail: " . $result);
            return false;
        }

        $sign = '';
        if (isset($data['sign'])) {
            $sign = $data['sign'];
        }

        $data = $data['alipay_trade_refund_response'];

        if (self::CODE_SUCCESS != $data['code']) {
            $this->getLogWriter()->error("order refund exec error:" . serialize($data));
            return false;
        }

        if (!$this->verify(json_encode($data), $sign)) {
            $this->getLogWriter()->error("order refund rsaVerify fail: " . $result);
            return false;
        }

        $result = new AliPayTradeRefundResult();
        $result->setCode($data['code']);
        $result->setMsg($data['msg']);

        $result->setOutTradeNo($data['out_trade_no']);
        $result->setTradeNo($data['trade_no']);
        $result->setBuyerLogonId($data['buyer_logon_id']);
        $result->setFundChange($data['fund_change']);
        $result->setRefundFee(floatval($data['refund_fee']));
        $result->setGmtRefundPay($data['gmt_refund_pay']);
        $result->setBuyerUserId($data['buyer_user_id']);

        if (isset($data['refund_detail_item_list'])) {
            $result->setRefundDetailItemList($this->createFundBillArray($data['refund_detail_item_list']));
        }

        if (isset($data['store_name'])) {
            $result->setStoreName($data['store_name']);
        }

        if (isset($data['send_back_fee'])) {
            $result->setSendBackFee(floatval($data['send_back_fee']));
        }

        return $result;
    }

    /**
     * @param AliPayTradeRefundQueryRequest $request
     * @return bool|AliPayTradeRefundQueryResult
     */
    public function refundQuery(AliPayTradeRefundQueryRequest $request)
    {
        if (('' == $request->getTradeNo()) && ('' == $request->getOutTradeNo())) {
            return false;
        }

        if ('' == $request->getOutRequestNo()) {
            return false;
        }

        $request = $this->initAliPayBase($request);
        $data = $this->getOrderRefundQueryRequestParams($request);
        $data['sign'] = $this->generateSign($data);

        $client = $this->getClient();
        $client->setUrl($this->buildUrl($data));

        if (!$client->exec()) {
            $this->getLogWriter()->error("order refund query exec error:" . serialize($data));
            return false;
        }

        $result = $client->getResponse();

        $data = json_decode($result, true);
        if (!isset($data['alipay_trade_fastpay_refund_query_response'])) {
            $this->getLogWriter()->error("order refund query json_decode fail: " . $result);
            return false;
        }

        $sign = '';
        if (isset($data['sign'])) {
            $sign = $data['sign'];
        }

        $data = $data['alipay_trade_fastpay_refund_query_response'];

        if (self::CODE_SUCCESS != $data['code']) {
            $this->getLogWriter()->error("order refund query exec error:" . serialize($data));
            return false;
        }

        if (!$this->verify(json_encode($data), $sign)) {
            $this->getLogWriter()->error("order refund query rsaVerify fail: " . $result);
            return false;
        }

        $result = new AliPayTradeRefundQueryResult();
        $result->setCode($data['code']);
        $result->setMsg($data['msg']);

        if (isset($data['trade_no'])) {
            $result->setTradeNo($data['trade_no']);
        }

        if (isset($data['out_trade_no'])) {
            $result->setOutTradeNo($data['out_trade_no']);
        }

        if (isset($data['out_request_no'])) {
            $result->setOutRequestNo($data['out_request_no']);
        }

        if (isset($data['refund_reason'])) {
            $result->setRefundReason($data['refund_reason']);
        }

        if (isset($data['total_amount'])) {
            $result->setTotalAmount(floatval($data['total_amount']));
        }

        if (isset($data['refund_amount'])) {
            $result->setRefundAmount(floatval($data['refund_amount']));
        }

        return $result;
    }

    /**
     * 解析支付通知
     * @param array $data
     * @return AliPayNotify | false
     */
    public function parseNotify(array $data)
    {
        if (!$this->rsaVerify($data)) {
            $this->getLogWriter()->error("parseNotify rsaVerify fail: " . serialize($data));
            return false;
        }

        if ($this->getConfig()->getAppId() != $data['app_id']) {
            $this->getLogWriter()->error("parseNotify error app id: " . $data['app_id'] . "|" .$this->getConfig()->getAppId() . '|' . serialize($data));
            return false;
        }

        $result = new AliPayNotify();
        $result->setNotifyTime($data['notify_time']);
        $result->setNotifyType($data['notify_type']);
        $result->setNotifyId($data['notify_id']);
        $result->setSignType($data['sign_type']);
        $result->setSign($data['sign']);

        $result->setTradeNo($data['trade_no']);
        $result->setAppId($data['app_id']);
        $result->setOutTradeNo($data['out_trade_no']);

        if (isset($data['out_biz_no'])) {
            $result->setOutBizNo($data['out_biz_no']);
        }

        if (isset($data['buyer_id'])) {
            $result->setBuyerId($data['buyer_id']);
        }

        if (isset($data['buyer_logon_id'])) {
            $result->setBuyerLogonId($data['buyer_logon_id']);
        }

        if (isset($data['seller_id'])) {
            $result->setSellerId($data['seller_id']);
        }

        if (isset($data['seller_email'])) {
            $result->setSellerEmail($data['seller_email']);
        }

        if (isset($data['trade_status'])) {
            $result->setTradeStatus(new AliPayTradeStatus($data['trade_status']));
        }

        if (isset($data['total_amount'])) {
            $result->setTotalAmount(floatval($data['total_amount']));
        }

        if (isset($data['receipt_amount'])) {
            $result->setReceiptAmount(floatval($data['receipt_amount']));
        }

        if (isset($data['invoice_amount'])) {
            $result->setInvoiceAmount(floatval($data['invoice_amount']));
        }

        if (isset($data['buyer_pay_amount'])) {
            $result->setBuyerPayAmount(floatval($data['buyer_pay_amount']));
        }

        if (isset($data['point_amount'])) {
            $result->setPointAmount(floatval($data['point_amount']));
        }

        if (isset($data['refund_fee'])) {
            $result->setRefundFee(floatval($data['refund_fee']));
        }

        if (isset($data['subject'])) {
            $result->setSubject($data['subject']);
        }

        if (isset($data['body'])) {
            $result->setBody($data['body']);
        }

        if (isset($data['gmt_create'])) {
            $result->setGmtCreate($data['gmt_create']);
        }

        if (isset($data['gmt_payment'])) {
            $result->setGmtPayment($data['gmt_payment']);
        }

        if (isset($data['gmt_refund'])) {
            $result->setGmtRefund(substr($data['gmt_refund'],0, 19));
        }

        if (isset($data['gmt_close'])) {
            $result->setGmtClose($data['gmt_close']);
        }

        if (isset($data['fund_bill_list'])) {
            $result->setFundBillList($this->createFundBillArray($data['fund_bill_list']));
        }

        return $result;
    }
}
