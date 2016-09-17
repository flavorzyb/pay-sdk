<?php
namespace Pay\AliPay;

use Pay\AliPay\Modules\AliPayBase;
use Pay\AliPay\Modules\AliPayCharset;
use Pay\AliPay\Modules\AliPayRequest;
use Pay\AliPay\Modules\AliPayTradeCloseRequest;
use Pay\AliPay\Modules\AliPayTradeCloseResult;
use Pay\AliPay\Modules\AliPayTradeFundBill;
use Pay\AliPay\Modules\AliPayTradeQueryRequest;
use Pay\AliPay\Modules\AliPayTradeQueryResult;
use Pay\AliPay\Modules\AliPayTradeRefundQueryRequest;
use Pay\AliPay\Modules\AliPayTradeRefundRequest;
use Pay\AliPay\Modules\AliPayTradeStatus;
use Pay\AliPay\Modules\AliPayTradeWapPayRequest;
use Pay\AliPay\Modules\AliPayTradeWapPayResult;
use Simple\Log\Writer;
use Simple\Http\Client;

class AliPayApi
{
    /**
     * 成功返回的code代码
     */
    const CODE_SUCCESS = '10000';

    /**
     * @var AliConfig
     */
    private $config = null;

    /**
     * @var Writer
     */
    private $logWriter = null;

    /**
     * AliPayApi constructor.
     * @param AliConfig $config
     * @param Writer $writer
     */
    public function __construct(AliConfig $config, Writer $writer)
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
     * @return AliConfig
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
//
//
//    /**
//     * 加密方法
//     * @param string $str
//     * @return string
//     */
//    protected function encrypt($str,$screct_key){
//        //AES, 128 模式加密数据 CBC
//        $screct_key = base64_decode($screct_key);
//        $str = trim($str);
//        $str = $this->addPKCS7Padding($str);
//        mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
//        $encrypt_str =  mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
//        return base64_encode($encrypt_str);
//    }
//
//    /**
//     * 解密方法
//     * @param string $str
//     * @return string
//     */
//    protected function decrypt($str,$screct_key){
//        //AES, 128 模式加密数据 CBC
//        $str = base64_decode($str);
//        $screct_key = base64_decode($screct_key);
//        mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
//        $encrypt_str =  mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
//        $encrypt_str = trim($encrypt_str);
//
//        $encrypt_str = $this->stripPKSC7Padding($encrypt_str);
//        return $encrypt_str;
//    }
//
//    /**
//     * 填充算法
//     * @param string $source
//     * @return string
//     */
//    protected function addPKCS7Padding($source){
//        $source = trim($source);
//        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
//
//        $pad = $block - (strlen($source) % $block);
//        if ($pad <= $block) {
//            $char = chr($pad);
//            $source .= str_repeat($char, $pad);
//        }
//        return $source;
//    }
//    /**
//     * 移去填充算法
//     * @param string $source
//     * @return string
//     */
//    protected function stripPKSC7Padding($source){
//        $source = trim($source);
//        $char = substr($source, -1);
//        $num = ord($char);
//        if($num==62)return $source;
//        $source = substr($source,0,-$num);
//        return $source;
//    }

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
     * @return null|AliPayTradeWapPayResult
     */
    public function parsePayReturnResult(array $data)
    {
        if (!$this->rsaVerify($data)) {
            $this->getLogWriter()->error("parsePayReturnResult rsaVerify fail: " . serialize($data));
            return null;
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
            $fb->setFundChannel($v['fund_channel']);
            $fb->setAmount(floatval($v['amount']));
            $fb->setRealAmount(floatval($v['real_amount']));
            $result[] = $fb;
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
            $result->setFundBillList($this->createFundBillArray($data['fund_bill_list']));
        }

        return $result;
    }

    /**
     * 关闭交易
     * @param AliPayTradeCloseRequest $request
     * @return bool|AliPayTradeCloseResult
     */
    public function orderClose(AliPayTradeCloseRequest $request)
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

    public function refund(AliPayTradeRefundRequest $request)
    {
    }

    public function refundQuery(AliPayTradeRefundQueryRequest $request)
    {
    }
}
