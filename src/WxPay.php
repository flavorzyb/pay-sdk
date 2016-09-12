<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/7/24
 * Time: 上午10:19
 */

namespace Apps\Pay;

use Apps\Common\Log;
use Apps\Pay\Model\PayNotifyModel;
use Apps\Pay\Model\PayOrderModel;
use Apps\Pay\WxPay\WxJsApiPay;
use Apps\Pay\WxPay\WxNativePay;
use Apps\Pay\WxPay\WxPayApi;
use Apps\Pay\WxPay\WxPayNotifyReply;
use Apps\Pay\WxPay\WxPayOrderQuery;
use Apps\Pay\WxPay\WxPayResults;
use Apps\Pay\WxPay\WxPayUnifiedOrder;

class WxPay extends PayAbstract
{
    // 2小时失效
    const EXPIRE_TIME = 7200;
    /**
     * 微信支付配置文件
     * @var array
     */
    private static $_CONFIG = array();

    /**
     *
     * @var WxJsApiPay
     */
    private $_wxJsApiPay    = null;

    /**
     * @var WxNativePay
     */
    private $_wxNativePay   = null;

    /**
     * open id
     * @var string
     */
    private $_openId        = '';

    /**
     * 加载支付宝配置文件
     */
    private static function loadConfig()
    {
        if (empty(self::$_CONFIG)) {
            self::$_CONFIG = include CONFIG_PATH . '/wxpay/wxpay_config.php';
        }
    }

    /**
     * app的微信支付使用另外的一套配置
     */
    public static function loadAppAndroidConfig()
    {
        self::$_CONFIG = include CONFIG_PATH . '/wxpay/wxpay_android_config.php';
    }

    /**
     * app的微信支付使用另外的一套配置
     */
    public static function loadAppIosConfig()
    {
        self::$_CONFIG = include CONFIG_PATH . '/wxpay/wxpay_ios_config.php';
    }

    /**
     * get WxJsApiPay instance
     * @return WxJsApiPay
     */
    public function getWxJsApiPay()
    {
        if (null == $this->_wxJsApiPay) {
            self::loadConfig();
            $this->setWxJsApiPay(new WxJsApiPay(self::$_CONFIG));
        }

        return $this->_wxJsApiPay;
    }

    /**
     * set WxJsApiPay instance
     *
     * @param WxJsApiPay $wxJsApiPay
     */
    public function setWxJsApiPay(WxJsApiPay $wxJsApiPay)
    {
        $this->_wxJsApiPay  = $wxJsApiPay;
    }

    /**
     * @return WxNativePay
     */
    public function getWxNativePay()
    {
        if (null == $this->_wxNativePay) {
            self::loadConfig();
            $this->setWxNativePay(new WxNativePay(self::$_CONFIG));
        }

        return $this->_wxNativePay;
    }

    /**
     * @param WxNativePay $wxNativePay
     */
    public function setWxNativePay(WxNativePay $wxNativePay)
    {
        $this->_wxNativePay = $wxNativePay;
    }


    /**
     * 检查
     * @param PayOrderModel $payOrder
     * @return bool
     * @override
     */
    protected function check(PayOrderModel $payOrder)
    {
        if (!parent::check($payOrder)) {
            return false;
        }

        if ('' == $payOrder->getIp()) {
            Log::pay("error: ip不能为空");
            return false;
        }

        return true;
    }

    /**
     * 设置open id
     * @param string $openId
     */
    public function setOpenId($openId)
    {
        $this->_openId  = trim($openId);
    }

    /**
     * 微信支付
     * @param   PayOrderModel $payOrder
     * @return  array | bool
     * @override
     */
    public function pay(PayOrderModel $payOrder)
    {
        if (!$this->check($payOrder)) {
            return false;
        }

        self::loadConfig();

        $notifyUrl      = ('' != $payOrder->getNotifyUrl() ? $payOrder->getNotifyUrl() : self::$_CONFIG['notify_url']);
        $wxPayApi       = new WxPayApi(self::$_CONFIG);

        $unifiedOrder   = $this->createPayUnifiedOrder($payOrder, $notifyUrl);
        $unifiedOrder->setTradeType("JSAPI");
        $unifiedOrder->setOpenId($this->_openId);
        // 签名
        $unifiedOrder->setSign($unifiedOrder->createSign(self::$_CONFIG['key']));

        $xmlString      = $unifiedOrder->toXml();
        $startTimeStamp = $wxPayApi->getMillisecond();
        $response       = $wxPayApi->postXmlCurl($xmlString, WxPayApi::UNIFIED_ORDER_URL, false);
        $result         = WxPayResults::getValuesFromXmlString($response, self::$_CONFIG['key']);

        if (false === $result) {
            return false;
        }

        $wxPayApi->reportCostTime($notifyUrl, $startTimeStamp, $result, $payOrder->getIp());

        return $result;
    }

    /**
     * 创建PayUnifiedOrder
     *
     * @param   PayOrderModel   $payOrder
     * @param   string          $notifyUrl
     * @return WxPayUnifiedOrder
     */
    private function createPayUnifiedOrder(PayOrderModel $payOrder, $notifyUrl)
    {
        $notifyUrl  = trim($notifyUrl);
        $result     = new WxPayUnifiedOrder();
        $body = $payOrder->getGoodsName();
        if (mb_strlen($body) > 10) {
            $body = mb_substr($body, 0, 10) . '...';
        }
        $result->setBody($body);
        $result->setAttach($payOrder->getExtra());
        $result->setOutTradeNo($payOrder->getOrderId());

        if ($payOrder->getLimitPay() != '') {
            $result->setLimitPay($payOrder->getLimitPay());
        }

        if (PAY_TESTING) {
            $result->setTotalFee(1);
        } else {
            $result->setTotalFee(($payOrder->getPayAmount() * 100));
        }
        $result->setTimeStart(date("YmdHis"));
        $result->setTimeExpire(date("YmdHis", time() + self::EXPIRE_TIME));
        $result->setNotifyUrl($notifyUrl);
        $result->setAppId(self::$_CONFIG['appId']);
        $result->setMchId(self::$_CONFIG['mchId']);
        $result->setSpbillCreateIp($payOrder->getIp());
        $result->setNonceStr(WxPayApi::getNonceStr());

        return $result;
    }
    /**
     * 微信支付 不实现此方法
     * @param PayOrderModel $payOrder
     * @return string
     */
    protected function _payUrl(PayOrderModel $payOrder)
    {
        return "";
    }

    /**
     * 解析支付回调数据
     * @param string $xmlString
     * @return PayNotifyModel | bool
     */
    public function parseNotify($xmlString)
    {
        $xmlString   = trim($xmlString);
        if ('' == $xmlString) {
            return false;
        }

        self::loadConfig();
        $wxPayApi   = new WxPayApi(self::$_CONFIG);
        $data = $wxPayApi->notify($xmlString);

        if (false === $data) {
            Log::pay("WxPay parseNotify Error: 验证签名错误 " . $xmlString);
            $result = new WxPayNotifyReply();
            $result->setReturnCode("FAIL");
            $result->setReturnMsg("签名验证失败");
            $this->replyNotify($result, false);
            return false;
        }

        // 检查订单
        $orderQuery = $this->queryOrder($data['transaction_id']);
        if (false == $orderQuery) {
            Log::pay("WxPay parseNotify Error: 校验订单失败 " . $data['transaction_id']);
            $result = new WxPayNotifyReply();
            $result->setReturnCode("FAIL");
            $result->setReturnMsg("校验订单失败");
            $this->replyNotify($result, false);
            return false;
        }

        $result = new PayNotifyModel();
        $result->setOrderId($data['out_trade_no']);
        if (isset($data['attach'])) {
            $result->setExtra($data['attach']);
        }
        $result->setPayAmount(floatval($data['total_fee']) / 100.0);
        $result->setStatus($data['result_code']);
        $result->setTradeNo($data['transaction_id']);

        return $result;
    }

    /**
     * 微信支付时，重新从微信平台获取openId
     * 1. 先从微信获取code
     * 2. 获取openId
     * 3. 构建订单
     * 4. 请求订单
     *
     * @param string $callBackUrl 回跳的url
     */
    public function login($callBackUrl)
    {
        self::loadConfig();
        $wxJsApi        = $this->getWxJsApiPay();
        $callBackUrl    = urlencode($callBackUrl);
        $url            = $wxJsApi->createOauthUrlForCode($callBackUrl, self::$_CONFIG['appId']);
        header("Location:" . $url);
        exit();
    }

    /**
     * 获取OpenId
     * @param $code
     * @return string
     */
    public function getOpenIdFromWx($code)
    {
        $wxJsApi    = $this->getWxJsApiPay();
        $openId     = $wxJsApi->getOpenId($code);
        return $openId;
    }

    /**
     *
     * 获取jsapi支付的参数
     *
     * @param   array   $unifiedOrderResult 统一支付接口返回的数据
     * @return  string                      json数据，可直接填入js函数作为参数
     */
    public function createJsApiParameters(array $unifiedOrderResult)
    {
        return $this->getWxJsApiPay()->createJsApiParameters($unifiedOrderResult);
    }

    /**
     *
     * 获取地址js参数
     *
     * @param   string  $url
     * @return string   获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
     */
    public function getEditAddressParameters($url)
    {
        return $this->getWxJsApiPay()->getEditAddressParameters($url);
    }

    /**
     * 回复通知
     * @param WxPayNotifyReply  $notifyReply
     * @param bool              $needSign 是否需要签名输出
     */
    public function replyNotify(WxPayNotifyReply $notifyReply, $needSign = true)
    {
        self::loadConfig();
        $needSign   = boolval($needSign);
        //如果需要签名
        if((true == $needSign) &&  ("SUCCESS" == $notifyReply->getReturnCode()))
        {
            $notifyReply->setSign($notifyReply->createSign(self::$_CONFIG['key']));
        }

        WxpayApi::replyNotify($notifyReply->toXml());
    }

    /**
     * 查询订单
     * @param int $transactionId
     * @return bool
     */
    private function queryOrder($transactionId)
    {
        $input      = new WxPayOrderQuery();
        $input->setTransactionId($transactionId);
        $wxPayApi   = new WxPayApi(self::$_CONFIG);
        $result     = $wxPayApi->orderQuery($input);

        //trade_state
        if (isset($result['return_code']) &&
            isset($result['result_code']) &&
            isset($result['trade_state']) &&
            ("SUCCESS" == $result['return_code']) &&
            ("SUCCESS" == $result['result_code']) &&
            ("SUCCESS" == $result['trade_state'])) {
            return true;
        }

        return false;
    }

    /**
     * wap 静态支付
     * @param PayOrderModel $payOrder
     * @return  array | bool
     */
    public function nativePay(PayOrderModel $payOrder)
    {
        if (!$this->check($payOrder)) {
            return false;
        }

        self::loadConfig();

        $notifyUrl      = ('' != $payOrder->getNotifyUrl() ? $payOrder->getNotifyUrl() : self::$_CONFIG['notify_url']);
        $wxPayApi       = new WxPayApi(self::$_CONFIG);

        $unifiedOrder   = $this->createPayUnifiedOrder($payOrder, $notifyUrl);
        $unifiedOrder->setTradeType("NATIVE");
        $unifiedOrder->setProductId($payOrder->getOrderId());
        // 签名
        $unifiedOrder->setSign($unifiedOrder->createSign(self::$_CONFIG['key']));

        $xmlString      = $unifiedOrder->toXml();
        $startTimeStamp = $wxPayApi->getMillisecond();
        $response       = $wxPayApi->postXmlCurl($xmlString, WxPayApi::UNIFIED_ORDER_URL, false);
        $result         = WxPayResults::getValuesFromXmlString($response, self::$_CONFIG['key']);

        if (false === $result) {
            Log::pay("nativePay  Error: result is false " . $payOrder->getOrderId());
            return false;
        }

        $wxPayApi->reportCostTime($notifyUrl, $startTimeStamp, $result, $payOrder->getIp());

        Log::debug("wapPay:" . serialize($result));

        if (is_array($result) &&
            isset($result['result_code']) && ("SUCCESS" == $result['result_code']) &&
            isset($result['return_code']) && ("SUCCESS" == $result['return_code'])) {

            return $result;
        }

        Log::pay("nativePay  Error: result is fail " . $payOrder->getOrderId());
        return false;
    }

    /**
     * wap 静态支付
     * @param PayOrderModel $payOrder
     * @return  array | bool
     */
    public function appPay(PayOrderModel $payOrder)
    {
        if (!$this->check($payOrder)) {
            return false;
        }

        self::loadConfig();

        $notifyUrl      = ('' != $payOrder->getNotifyUrl() ? $payOrder->getNotifyUrl() : self::$_CONFIG['notify_url']);
        $wxPayApi       = new WxPayApi(self::$_CONFIG);

        $unifiedOrder   = $this->createPayUnifiedOrder($payOrder, $notifyUrl);
        $unifiedOrder->setTradeType("APP");
        $unifiedOrder->setProductId($payOrder->getOrderId());
        // 签名
        $unifiedOrder->setSign($unifiedOrder->createSign(self::$_CONFIG['key']));

        $xmlString      = $unifiedOrder->toXml();
        $startTimeStamp = $wxPayApi->getMillisecond();
        $response       = $wxPayApi->postXmlCurl($xmlString, WxPayApi::UNIFIED_ORDER_URL, false);
        $result         = WxPayResults::getValuesFromXmlString($response, self::$_CONFIG['key']);

        if (false === $result) {
            Log::pay("appPay  Error: result is false " . $payOrder->getOrderId());
            return false;
        }

        $wxPayApi->reportCostTime($notifyUrl, $startTimeStamp, $result, $payOrder->getIp());

        if (is_array($result) &&
            isset($result['result_code']) && ("SUCCESS" == $result['result_code']) &&
            isset($result['return_code']) && ("SUCCESS" == $result['return_code'])) {

            return $result;
        }

        Log::pay("appPay  Error: result is fail " . $payOrder->getOrderId());
        return false;
    }

    /**
     * 生成native支付url
     * @param array $data
     * @return string
     */
    public function createNativeUrl(array $data)
    {
        return $this->getWxNativePay()->createNativeUrl($data);
    }

    /**
     * 创建app支付所需要的json数据
     * @param array $data
     * @return array
     */
    public function createAppPayParams(array $data)
    {
        return $this->getWxNativePay()->createAppPayParams($data);
    }
}
