<?php
namespace Pay\AliPay;

use Pay\AliPay\Modules\AliPayBase;
use Pay\AliPay\Modules\AliPayCharset;
use Pay\AliPay\Modules\AliPayRequest;
use Pay\AliPay\Modules\AliPayTradeCloseRequest;
use Pay\AliPay\Modules\AliPayTradeQueryRequest;
use Pay\AliPay\Modules\AliPayTradeRefundQueryRequest;
use Pay\AliPay\Modules\AliPayTradeRefundRequest;
use Pay\AliPay\Modules\AliPayTradeWapPayRequest;
use Simple\Log\Writer;

class AliPayApi
{
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
     * 加密方法
     * @param string $str
     * @return string
     */
    protected function encrypt($str,$screct_key){
        //AES, 128 模式加密数据 CBC
        $screct_key = base64_decode($screct_key);
        $str = trim($str);
        $str = $this->addPKCS7Padding($str);
        mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
        $encrypt_str =  mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
        return base64_encode($encrypt_str);
    }

    /**
     * 解密方法
     * @param string $str
     * @return string
     */
    protected function decrypt($str,$screct_key){
        //AES, 128 模式加密数据 CBC
        $str = base64_decode($str);
        $screct_key = base64_decode($screct_key);
        mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
        $encrypt_str =  mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
        $encrypt_str = trim($encrypt_str);

        $encrypt_str = $this->stripPKSC7Padding($encrypt_str);
        return $encrypt_str;
    }

    /**
     * 填充算法
     * @param string $source
     * @return string
     */
    protected function addPKCS7Padding($source){
        $source = trim($source);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

        $pad = $block - (strlen($source) % $block);
        if ($pad <= $block) {
            $char = chr($pad);
            $source .= str_repeat($char, $pad);
        }
        return $source;
    }
    /**
     * 移去填充算法
     * @param string $source
     * @return string
     */
    protected function stripPKSC7Padding($source){
        $source = trim($source);
        $char = substr($source, -1);
        $num = ord($char);
        if($num==62)return $source;
        $source = substr($source,0,-$num);
        return $source;
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
        $request = $this->initAliPayBase($request);
        $request->setNotifyUrl($config->getNotifyUrl());
        $request->setReturnUrl($config->getNotifyUrl());
        $request->setSellerId($config->getSellerId());

        return $request;
    }

    /**
     *
     * @param AliPayTradeWapPayRequest $request
     * @return bool|string
     */
    public function pay(AliPayTradeWapPayRequest $request)
    {
        $request = $this->initAliPayTradeWapPayRequest($request);

        if (('' == $request->getSubject()) ||
            ('' == $request->getOutTradeNo()) ||
            (0.01 > $request->getTotalAmount()) ||
            ('' == $request->getProductCode())) {
            return false;
        }

        $data = $this->getWapPayRequestParams($request);
        //待签名字符串
        $data['sign'] = $this->generateSign($data);
        return $this->buildRequestForm($data, $request);
    }

    public function orderQuery(AliPayTradeQueryRequest $request)
    {
    }

    public function orderClose(AliPayTradeCloseRequest $request)
    {
    }

    public function refund(AliPayTradeRefundRequest $request)
    {
    }

    public function refundQuery(AliPayTradeRefundQueryRequest $request)
    {
    }
}
