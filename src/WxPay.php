<?php
namespace Pay;

use Pay\Modules\PayNotify;
use Pay\Modules\PayOrder;
use Pay\WxPay\Modules\WxPayNotifyReply;
use Pay\WxPay\Modules\WxPayOrderQuery;
use Pay\WxPay\Modules\WxPayResults;
use Pay\WxPay\Modules\WxPayUnifiedOrder;
use Pay\WxPay\WxJsApiPay;
use Pay\WxPay\WxNativePay;
use Pay\WxPay\WxPayApi;
use Simple\Log\Writer;

class WxPay extends PayAbstract
{
    // 2小时失效
    const EXPIRE_TIME = 7200;
    /**
     * 微信支付配置文件
     * @var array
     */
    private $config = array();

    /**
     *
     * @var WxJsApiPay
     */
    private $wxJsApiPay    = null;

    /**
     * @var WxNativePay
     */
    private $wxNativePay   = null;

    /**
     * open id
     * @var string
     */
    private $openId        = '';

    /**
     * WxPay constructor.
     * @param array $config
     * @param Writer $logWriter
     */
    public function __construct(array $config, Writer $logWriter)
    {
        parent::__construct($logWriter);
        $this->config = $config;
    }

    /**
     * get WxJsApiPay instance
     * @return WxJsApiPay
     */
    public function getWxJsApiPay()
    {
        if (null == $this->wxJsApiPay) {
            $this->setWxJsApiPay(new WxJsApiPay($this->config));
        }

        return $this->wxJsApiPay;
    }

    /**
     * set WxJsApiPay instance
     *
     * @param WxJsApiPay $wxJsApiPay
     */
    public function setWxJsApiPay(WxJsApiPay $wxJsApiPay)
    {
        $this->wxJsApiPay  = $wxJsApiPay;
    }

    /**
     * @return WxNativePay
     */
    public function getWxNativePay()
    {
        if (null == $this->wxNativePay) {
            $this->setWxNativePay(new WxNativePay($this->config));
        }

        return $this->wxNativePay;
    }

    /**
     * @param WxNativePay $wxNativePay
     */
    public function setWxNativePay(WxNativePay $wxNativePay)
    {
        $this->wxNativePay = $wxNativePay;
    }


    /**
     * 检查
     * @param PayOrder $payOrder
     * @return bool
     * @override
     */
    protected function check(PayOrder $payOrder)
    {
        if (!parent::check($payOrder)) {
            return false;
        }

        if ('' == $payOrder->getIp()) {
            $this->getLogWriter()->error("error: ip不能为空");
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
        $this->openId  = trim($openId);
    }

    /**
     * 微信支付
     * @param   PayOrder $payOrder
     * @return  array | bool
     * @override
     */
    public function pay(PayOrder $payOrder)
    {
        if (!$this->check($payOrder)) {
            return false;
        }

        $notifyUrl      = ('' != $payOrder->getNotifyUrl() ? $payOrder->getNotifyUrl() : $this->config['notify_url']);
        $wxPayApi       = new WxPayApi($this->config, $this->getLogWriter());

        $unifiedOrder   = $this->createPayUnifiedOrder($payOrder, $notifyUrl);
        $unifiedOrder->setTradeType("JSAPI");
        $unifiedOrder->setOpenId($this->openId);
        // 签名
        $unifiedOrder->setSign($unifiedOrder->createSign($this->config['key']));

        $xmlString      = $unifiedOrder->toXml();
        $startTimeStamp = $wxPayApi->getMillisecond();
        $response       = $wxPayApi->postXmlCurl($xmlString, WxPayApi::UNIFIED_ORDER_URL, false);
        $result         = WxPayResults::getValuesFromXmlString($response, $this->config['key']);

        if (false === $result) {
            return false;
        }

        $wxPayApi->reportCostTime($notifyUrl, $startTimeStamp, $result, $payOrder->getIp());

        return $result;
    }

    /**
     * 创建PayUnifiedOrder
     *
     * @param   PayOrder   $payOrder
     * @param   string          $notifyUrl
     * @return WxPayUnifiedOrder
     */
    private function createPayUnifiedOrder(PayOrder $payOrder, $notifyUrl)
    {
        $notifyUrl  = trim($notifyUrl);
        $result     = new WxPayUnifiedOrder();
        $body = $payOrder->getGoodsName();
        if (mb_strlen($body) > 30) {
            $body = mb_substr($body, 0, 30) . '...';
        }
        $result->setBody($body);
        $result->setAttach($payOrder->getExtra());
        $result->setOutTradeNo($payOrder->getOrderId());

        if ($payOrder->getLimitPay()->isNoCredit()) {
            $result->setLimitPay($payOrder->getLimitPay()->getValue());
        }

        $result->setTotalFee(($payOrder->getPayAmount() * 100));

        $result->setTimeStart(date("YmdHis"));
        $result->setTimeExpire(date("YmdHis", time() + self::EXPIRE_TIME));
        $result->setNotifyUrl($notifyUrl);
        $result->setAppId($this->config['appId']);
        $result->setMchId($this->config['mchId']);
        $result->setSpbillCreateIp($payOrder->getIp());
        $result->setNonceStr(WxPayApi::getNonceStr());

        return $result;
    }
    /**
     * 微信支付 不实现此方法
     * @param PayOrder $payOrder
     * @return string
     */
    protected function _payUrl(PayOrder $payOrder)
    {
        return "";
    }

    /**
     * 解析支付回调数据
     * @param string $xmlString
     * @param string $ip
     * @return PayNotify | bool
     */
    public function parseNotify($xmlString, $ip)
    {
        $xmlString   = trim($xmlString);
        if ('' == $xmlString) {
            return false;
        }

        $wxPayApi   = new WxPayApi($this->config, $this->getLogWriter());
        $data = $wxPayApi->notify($xmlString);

        if (false === $data) {
            $this->getLogWriter()->error("WxPay parseNotify Error: 验证签名错误 " . $xmlString);
            $result = new WxPayNotifyReply();
            $result->setReturnCode("FAIL");
            $result->setReturnMsg("签名验证失败");
            $this->replyNotify($result, false);
            return false;
        }

        // 检查订单
        $orderQuery = $this->queryOrder($data['transaction_id'], $ip);
        if (false == $orderQuery) {
            $this->getLogWriter()->error("WxPay parseNotify Error: 校验订单失败 " . $data['transaction_id']);
            $result = new WxPayNotifyReply();
            $result->setReturnCode("FAIL");
            $result->setReturnMsg("校验订单失败");
            $this->replyNotify($result, false);
            return false;
        }

        $result = new PayNotify();
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
     * 回复通知
     * @param WxPayNotifyReply  $notifyReply
     * @param bool              $needSign 是否需要签名输出
     */
    public function replyNotify(WxPayNotifyReply $notifyReply, $needSign = true)
    {
        $needSign   = boolval($needSign);
        //如果需要签名
        if((true == $needSign) &&  ("SUCCESS" == $notifyReply->getReturnCode()))
        {
            $notifyReply->setSign($notifyReply->createSign($this->config['key']));
        }

        $wxPayApi   = new WxPayApi($this->config, $this->getLogWriter());

        $wxPayApi->replyNotify($notifyReply->toXml());
    }

    /**
     * 查询订单
     * @param int $transactionId
     * @param string $ip
     * @return bool
     */
    private function queryOrder($transactionId, $ip)
    {
        $input      = new WxPayOrderQuery();
        $input->setTransactionId($transactionId);
        $wxPayApi   = new WxPayApi($this->config, $this->getLogWriter());
        $result     = $wxPayApi->orderQuery($input, $ip);

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
     * @param PayOrder $payOrder
     * @return  array | bool
     */
    public function nativePay(PayOrder $payOrder)
    {
        if (!$this->check($payOrder)) {
            return false;
        }

        $notifyUrl      = ('' != $payOrder->getNotifyUrl() ? $payOrder->getNotifyUrl() : $this->config['notify_url']);
        $wxPayApi   = new WxPayApi($this->config, $this->getLogWriter());

        $unifiedOrder   = $this->createPayUnifiedOrder($payOrder, $notifyUrl);
        $unifiedOrder->setTradeType("NATIVE");
        $unifiedOrder->setProductId($payOrder->getOrderId());
        // 签名
        $unifiedOrder->setSign($unifiedOrder->createSign($this->config['key']));

        $xmlString      = $unifiedOrder->toXml();
        $startTimeStamp = $wxPayApi->getMillisecond();
        $response       = $wxPayApi->postXmlCurl($xmlString, WxPayApi::UNIFIED_ORDER_URL, false);
        $result         = WxPayResults::getValuesFromXmlString($response, $this->config['key']);

        if (false === $result) {
            $this->getLogWriter()->error("nativePay  Error: result is false " . $payOrder->getOrderId());
            return false;
        }

        $wxPayApi->reportCostTime($notifyUrl, $startTimeStamp, $result, $payOrder->getIp());

        $this->getLogWriter()->debug("wapPay:" . serialize($result));

        if (is_array($result) &&
            isset($result['result_code']) && ("SUCCESS" == $result['result_code']) &&
            isset($result['return_code']) && ("SUCCESS" == $result['return_code'])) {

            return $result;
        }

        $this->getLogWriter()->error("nativePay  Error: result is fail " . $payOrder->getOrderId());
        return false;
    }

    /**
     * wap 静态支付
     * @param PayOrder $payOrder
     * @return  array | bool
     */
    public function appPay(PayOrder $payOrder)
    {
        if (!$this->check($payOrder)) {
            return false;
        }

        $notifyUrl      = ('' != $payOrder->getNotifyUrl() ? $payOrder->getNotifyUrl() : $this->config['notify_url']);
        $wxPayApi       = new WxPayApi($this->config, $this->getLogWriter());

        $unifiedOrder   = $this->createPayUnifiedOrder($payOrder, $notifyUrl);
        $unifiedOrder->setTradeType("APP");
        $unifiedOrder->setProductId($payOrder->getOrderId());
        // 签名
        $unifiedOrder->setSign($unifiedOrder->createSign($this->config['key']));

        $xmlString      = $unifiedOrder->toXml();
        $startTimeStamp = $wxPayApi->getMillisecond();
        $response       = $wxPayApi->postXmlCurl($xmlString, WxPayApi::UNIFIED_ORDER_URL, false);
        $result         = WxPayResults::getValuesFromXmlString($response, $this->config['key']);

        if (false === $result) {
            $this->getLogWriter()->error("appPay  Error: result is false " . $payOrder->getOrderId());
            return false;
        }

        $wxPayApi->reportCostTime($notifyUrl, $startTimeStamp, $result, $payOrder->getIp());

        if (is_array($result) &&
            isset($result['result_code']) && ("SUCCESS" == $result['result_code']) &&
            isset($result['return_code']) && ("SUCCESS" == $result['return_code'])) {

            return $result;
        }

        $this->getLogWriter()->error("appPay  Error: result is fail " . $payOrder->getOrderId());
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
