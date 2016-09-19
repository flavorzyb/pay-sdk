<?php
namespace Pay;

use Pay\Modules\PayNotify;
use Pay\Modules\PayOrder;
use Pay\Modules\PayTradeStatus;
use Pay\WxPay\Modules\WxPayCloseOrder;
use Pay\WxPay\Modules\WxPayConfig;
use Pay\WxPay\Modules\WxPayNotifyReply;
use Pay\WxPay\Modules\WxPayOrderQuery;
use Pay\WxPay\Modules\WxPayTradeState;
use Pay\WxPay\Modules\WxPayUnifiedOrder;
use Pay\WxPay\WxJsApiPay;
use Pay\WxPay\WxNativePay;
use Pay\WxPay\WxPayApi;
use Simple\Log\Writer;
use Pay\Modules\PayOrderQuery;
use Pay\Modules\PayOrderQueryResult;
use Pay\Modules\PayOrderClose;

class WxPay extends PayAbstract
{
    /**
     * 微信支付配置文件
     * @var WxPayConfig
     */
    private $config = null;

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
     * @var WxPayApi
     */
    private $wxPayApi = null;

    /**
     * WxPay constructor.
     * @param WxPayConfig $config
     * @param Writer $logWriter
     */
    public function __construct(WxPayConfig $config, Writer $logWriter)
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
     * @return WxPayApi
     */
    public function getWxPayApi()
    {
        if (null == $this->wxPayApi) {
            $this->setWxPayApi(new WxPayApi($this->config, $this->getLogWriter()));
        }

        return $this->wxPayApi;
    }

    /**
     * @param WxPayApi $wxPayApi
     */
    public function setWxPayApi($wxPayApi)
    {
        $this->wxPayApi = $wxPayApi;
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
     * @return string
     */
    public function getOpenId()
    {
        return $this->openId;
    }

    /**
     * 微信支付
     * @param   PayOrder $payOrder
     * @param string $ip
     * @return  array | bool
     * @override
     */
    public function pay(PayOrder $payOrder, $ip)
    {
        $result = parent::pay($payOrder, $ip);
        if (!$result) {
            return false;
        }

        return $this->_pay($payOrder, 'JSAPI', $ip);
    }

    /**
     * @param PayOrder $payOrder
     * @param string $tradeType
     * @param string $ip
     * @return array|bool|false
     */
    protected function _pay(PayOrder $payOrder, $tradeType, $ip)
    {
        if ('' == $payOrder->getIp()) {
            $this->getLogWriter()->error("error: ip不能为空");
            return false;
        }

        $notifyUrl      = ('' != $payOrder->getNotifyUrl() ? $payOrder->getNotifyUrl() : $this->config->getNotifyUrl());
        $wxPayApi       = $this->getWxPayApi();

        $unifiedOrder   = $this->createPayUnifiedOrder($payOrder, $notifyUrl);

        $tradeType = strtoupper(trim($tradeType));
        $unifiedOrder->setTradeType($tradeType);
        switch ($tradeType) {
            case 'JSAPI':
                $unifiedOrder->setOpenId($this->openId);
                break;
            case 'APP':
            case 'NATIVE':
                $unifiedOrder->setProductId($payOrder->getOrderId());
                break;
            default:
                return false;
        }

        $result = $wxPayApi->unifiedOrder($unifiedOrder, $ip);

        if (false === $result) {
            return false;
        }

        $this->getLogWriter()->debug("{$tradeType}:" . serialize($result));

        if (is_array($result) &&
            isset($result['result_code']) && ("SUCCESS" == $result['result_code']) &&
            isset($result['return_code']) && ("SUCCESS" == $result['return_code'])) {

            return $result;
        }

        $this->getLogWriter()->error($tradeType. " Error: result is fail " . $payOrder->getOrderId(). ", " . serialize($result));
        return false;
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
        if ((strlen($body) > 120) && (mb_strlen($body) > 30)) {
            $body = mb_substr($body, 0, 30) . '...';
        }

        $result->setBody($body);

        if ('' != $payOrder->getExtra()) {
            $result->setAttach($payOrder->getExtra());
        }

        $result->setOutTradeNo($payOrder->getOrderId());

        if ($payOrder->getLimitPay()->isNoCredit()) {
            $result->setLimitPay($payOrder->getLimitPay()->getValue());
        }

        $result->setTotalFee(($payOrder->getPayAmount() * 100));

        if ($payOrder->getTimeoutExpress() > 300) {
            $result->setTimeStart(date("YmdHis"));
            $result->setTimeExpire(date("YmdHis", time() + $payOrder->getTimeoutExpress()));
        }

        $result->setNotifyUrl($notifyUrl);
        $result->setSpbillCreateIp($payOrder->getIp());

        return $result;
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

        $wxPayApi   = $this->getWxPayApi();
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
        if ((null == $orderQuery) || (!$orderQuery->isSuccess())) {
            $this->getLogWriter()->error("WxPay parseNotify Error: 校验订单失败 " . $data['transaction_id'] . ' ' . $xmlString);
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
            $notifyReply->setSign($notifyReply->createSign($this->config->getKey()));
        }

        $wxPayApi   = $this->getWxPayApi();

        $wxPayApi->replyNotify($notifyReply->toXml());
    }

    /**
     * 查询订单
     * @param int $transactionId
     * @param string $ip
     * @return WxPayTradeState | null
     */
    public function queryOrder($transactionId, $ip)
    {
        $input      = new WxPayOrderQuery();
        $input->setTransactionId($transactionId);
        $wxPayApi   = $this->getWxPayApi();
        $result     = $wxPayApi->orderQuery($input, $ip);

        //trade_state
        if (isset($result['return_code']) &&
            isset($result['result_code']) &&
            isset($result['trade_state']) &&
            ("SUCCESS" == $result['return_code']) &&
            ("SUCCESS" == $result['result_code'])) {
            return new WxPayTradeState($result['trade_state']);
        }

        return null;
    }

    /**
     * wap 静态支付
     * @param PayOrder $payOrder
     * @param string $ip
     * @return  array | bool
     */
    public function nativePay(PayOrder $payOrder, $ip)
    {
        return $this->_pay($payOrder, 'NATIVE', $ip);
    }

    /**
     * wap 静态支付
     * @param PayOrder $payOrder
     * @param string $ip
     * @return  array | bool
     */
    public function appPay(PayOrder $payOrder, $ip)
    {
        return $this->_pay($payOrder, 'APP', $ip);
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

    private function buildPayStatus($status)
    {
        $result = PayTradeStatus::createOthersStatus();
        switch ($status) {
            case 'CLOSED':
                $result = PayTradeStatus::createClosedStatus();
                break;
            case 'SUCCESS':
                $result = PayTradeStatus::createSuccessStatus();
                break;
            case 'NOTPAY':
                $result = PayTradeStatus::createNotPayStatus();
                break;
        }

        return $result;
    }

    /**
     * 订单查询
     * @param PayOrderQuery $query
     * @param string $ip
     * @return false|PayOrderQueryResult
     */
    public function orderQuery(PayOrderQuery $query, $ip)
    {
        if (!parent::orderQuery($query, $ip)) {
            return false;
        }

        $data = new WxPayOrderQuery();
        $data->setOutTradeNo($query->getOrderId());
        $data->setTransactionId($query->getTradeNo());

        $query = $this->getWxPayApi()->orderQuery($data, $ip);
        if (false === $query) {
            return false;
        }

        $result = new PayOrderQueryResult();
        $result->setTradeNo($query['transaction_id']);
        $result->setOrderId($query['out_trade_no']);
        $result->setTotalAmount(intval($query['total_fee']) / 100.0);
        $result->setReceiptAmount(intval($query['cash_fee']) / 100.0);
        $result->setTradeStatus($this->buildPayStatus($query['trade_state']));

        return $result;
    }

    /**
     * 关闭订单
     * @param PayOrderClose $query
     * @param string $ip
     * @return bool
     */
    public function closeOrder(PayOrderClose $query, $ip)
    {
        if ('' == $query->getOrderId()) {
            return false;
        }

        $data = new WxPayCloseOrder();
        $data->setOutTradeNo($query->getOrderId());

        $query = $this->getWxPayApi()->closeOrder($data, $ip);

        if (false === $query) {
            return false;
        }

        return true;
    }
}
