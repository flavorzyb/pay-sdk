<?php
namespace Pay\WxPay;

use Pay\WxPay\Modules\WxPayCheckName;
use Pay\WxPay\Modules\WxPayCloseOrder;
use Pay\WxPay\Modules\WxPayOrderQuery;
use Pay\WxPay\Modules\WxPayRefund;
use Pay\WxPay\Modules\WxPayRefundQuery;
use Pay\WxPay\Modules\WxPayReport;
use Pay\WxPay\Modules\WxPayResults;
use Pay\WxPay\Modules\WxPayConfig;
use Pay\WxPay\Modules\WxPayTransfer;
use Pay\WxPay\Modules\WxPayUnifiedOrder;
use Simple\Http\Client;
use Simple\Log\Writer;

class WxPayApi
{
    /**
     * 统一下单URL
     */
    const UNIFIED_ORDER_URL  = "https://api.mch.weixin.qq.com/pay/unifiedorder";
    /**
     * 上报错误接口
     */
    const REPORT_URL        = "https://api.mch.weixin.qq.com/payitil/report";
    /**
     * 订单查询
     */
    const ORDER_QUERY_URL   = "https://api.mch.weixin.qq.com/pay/orderquery";

    /**
     * 关闭订单
     */
    const CLOSED_ORDER_URL  = 'https://api.mch.weixin.qq.com/pay/closeorder';
    /**
     * 申请退款
     */
    const REFUND_URL  = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

    /**
     * 查询退款
     */
    const REFUND_QUERY_URL  = 'https://api.mch.weixin.qq.com/pay/refundquery';

    /**
     * 企业付款
     */
    const TRANSFERS_URL  ='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

    /**
     * 随机字符串 32 位
     */
    const NONCE_STRING_LENGTH = 32;

    /**
     * 配置文件
     * @var WxPayConfig
     */
    private $config    = null;

    /**
     * @var Writer
     */
    protected $log = null;

    /**
     * @return Client
     */
    public function getClient()
    {
        return new Client();
    }

    /**
     * AliPaySubmit constructor.
     * @param WxPayConfig $config
     * @param Writer $writer
     */
    public function __construct(WxPayConfig $config, Writer $writer)
    {
        $this->config  = $config;
        $this->log = $writer;
    }

    /**
     * 获取配置
     * @return WxPayConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return Writer
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     *
     * 产生随机字符串，不长于32位
     * @return string 产生的随机字符串
     */
    public static function getNonceStr()
    {
        $chars  = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str    = "";
        for ( $i = 0; $i < self::NONCE_STRING_LENGTH; $i++ ) {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }

        return $str;
    }

    /**
     * 获取毫秒级别的时间戳
     * @return string
     */
    public function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode( " ", microtime ());
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode(".", $time);
        $time = $time2[0];
        return $time;
    }

    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml  需要post的xml数据
     * @param string $url  url
     * @param bool $useCert 是否需要证书，默认不需要
     * @return string
     */
    protected function postXmlCurl($xml, $url, $useCert = false)
    {
        $xml        = trim($xml);
        $url        = trim($url);
        $useCert    = boolval($useCert);

        $client = $this->getClient();
        $client->setUrl($url);
        $client->setMethod(Client::METHOD_POST);
        $client->setPostFields($xml);

        if ("0.0.0.0" != $this->config->getCurlProxyHost()) {
            $client->setProxyHost($this->config->getCurlProxyHost());
        }

        if (0 != $this->config->getCurlProxyPort()) {
            $client->setProxyPort($this->config->getCurlProxyPort());
        }

        $client->setSslVerifyPeer(true);
        $client->setSslVerifyHost(true);
        $client->setCaInfo($this->config->getRootCaPath());
        $client->setHeader(false);

        if ($useCert && ('' != $this->config->getSslCertPath()) && ('' != $this->config->getSslKeyPath())) {
            $client->useCert(Client::CERT_TYPE_PEM, $this->config->getSslCertPath(), $this->config->getSslKeyPath());
        }

        if ($client->exec()) {
            $result = $client->getResponse();
            return $result;
        }

        $this->log->error("WxPayApi postXmlCurl Error:" . $url . " | " . $xml);

        return false;
    }

    /**
     *
     * 上报数据， 上报的时候将屏蔽所有异常流程
     * @param   string  $url
     * @param   int     $startTimeStamp
     * @param   array   $data
     * @param   string  $clientIp
     * @return  string | false
     */
    public function reportCostTime($url, $startTimeStamp, $data, $clientIp)
    {
        //如果不需要上报数据
        if (0 == $this->config->getReportLevel()) {
            return true;
        }

        //如果仅失败上报
        if((1 == $this->config->getReportLevel()) &&
            isset($data['return_code']) &&
            $data["return_code"] == "SUCCESS" &&
            isset($data['result_code']) &&
            $data["result_code"] == "SUCCESS")
        {
            return true;
        }

        //上报逻辑
        $endTimeStamp = self::getMillisecond();

        $report = new WxPayReport();
        $report->setInterfaceUrl($url);
        $report->setExecuteTime($endTimeStamp - $startTimeStamp);
        //返回状态码
        if (isset($data['return_code'])) {
            $report->setReturnCode($data['return_code']);
        }
        //返回信息
        if (isset($data['return_msg'])) {
            $report->setReturnMsg($data['return_msg']);
        }
        //业务结果
        if (isset($data['result_code'])) {
            $report->setResultCode($data['result_code']);
        }
        //错误代码
        if (isset($data['err_code'])) {
            $report->setErrCode($data['err_code']);
        }
        //错误代码描述
        if (isset($data['err_code_des'])) {
            $report->setErrCodeDes($data['err_code_des']);
        }
        //商户订单号
        if (isset($data['out_trade_no'])) {
            $report->setOutTradeNo($data['out_trade_no']);
        }
        //设备号
        if (isset($data['device_info'])) {
            $report->setDeviceInfo($data['device_info']);
        }

        if ('' == $report->getReturnCode()) {
            $this->log->error("WxPay reportCostTime Error: 缺少必填参数return_code url = $url data=" . serialize($data));
            return false;
        }

        if ('' == $report->getResultCode()) {
            $this->log->error("WxPay reportCostTime Error: 缺少必填参数result_code url = $url data=" . serialize($data));
            return false;
        }

        $this->log->info("WxPay reportCostTime url = $url data=" . serialize($data));

        $report->setAppId($this->config->getAppId());
        $report->setMchId($this->config->getMchId());
        $report->setNonceStr($this->getNonceStr());
        $report->setTime(date("YmdHis"));
        $report->setUserIp($clientIp);
        $report->setSign($report->createSign($this->config->getKey()));

        return $this->report($report);
    }

    /**
     *
     * 测速上报，该方法内部封装在report中，使用时请注意异常流程
     * WxPayReport中interface_url、return_code、result_code、user_ip、execute_time_必填
     * @param WxPayReport $payReport
     * @return string | false
     */
    private function report(WxPayReport $payReport)
    {
        $xml = $payReport->toXml();
        return $this->postXmlCurl($xml, self::REPORT_URL, false);
    }

    /**
     * 支付结果通用通知
     * @param string $xmlString 微信支付回调的xml字符串
     * @return array | false 如果成功 返回订单数组，否则返回false
     */
    public function notify($xmlString)
    {
        //获取通知的数据
        $xmlString  = trim($xmlString);
        $result     = WxPayResults::getValuesFromXmlString($xmlString, $this->config->getKey());
        return $result;
    }

    /**
     * 直接输出xml
     * @param string $xml
     */
    public function replyNotify($xml)
    {
        echo $xml;
    }

    /**
     * 统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
     *
     * @param WxPayUnifiedOrder $order
     * @param $ip
     * @return array|bool|false
     */
    public function unifiedOrder(WxPayUnifiedOrder $order, $ip)
    {
        //检测必填参数
        if (('' == $order->getOutTradeNo())
            || ('' == $order->getBody())
            || (0 == $order->getTotalFee())
            || ('' == $order->getTradeType())) {
            return false;
        }

        if (('JSAPI' == $order->getTradeType()) && ('' == $order->getOpenId())) {
            return false;
        }

        if (('NATIVE' == $order->getTradeType()) && ('' == $order->getProductId())) {
            return false;
        }

        $order->setAppId($this->config->getAppId());//公众账号ID
        $order->setMchId($this->config->getMchId());//商户号
        $order->setNonceStr(self::getNonceStr());//随机字符串

        $order->setSign($order->createSign($this->config->getKey()));//签名

        $xmlString      = $order->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = $this->postXmlCurl($xmlString, self::UNIFIED_ORDER_URL, false);

        if (false === $response) {
            return false;
        }

        $result = WxPayResults::getValuesFromXmlString($response, $this->config->getKey());

        $this->reportCostTime(self::ORDER_QUERY_URL, $startTimeStamp, $result, $ip);//上报请求花费时间

        return $result;
    }

    /**
     *
     * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayOrderQuery $orderQuery
     * @param string $ip
     * @return array|bool 成功时返回，其他抛异常
     */
    public function orderQuery(WxPayOrderQuery $orderQuery, $ip)
    {
        //检测必填参数
        if(('' == $orderQuery->getOutTradeNo()) && ('' == $orderQuery->getTransactionId())) {
            return false;
        }

        $orderQuery->setAppId($this->config->getAppId());//公众账号ID
        $orderQuery->setMchId($this->config->getMchId());//商户号
        $orderQuery->setNonceStr(self::getNonceStr());//随机字符串

        $orderQuery->setSign($orderQuery->createSign($this->config->getKey()));//签名

        $xmlString      = $orderQuery->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = $this->postXmlCurl($xmlString, self::ORDER_QUERY_URL, false);

        if (false === $response) {
            return false;
        }

        $result = WxPayResults::getValuesFromXmlString($response, $this->config->getKey());

        $this->reportCostTime(self::ORDER_QUERY_URL, $startTimeStamp, $result, $ip);//上报请求花费时间

        return $result;
    }

    /**
     * 关闭订单，WxPayCloseOrder中out_trade_no必填
     * @param WxPayCloseOrder $closeOrder
     * @param string $ip
     * @return array|bool|false
     */
    public function closeOrder(WxPayCloseOrder $closeOrder, $ip)
    {
        //检测必填参数
        if('' == $closeOrder->getOutTradeNo()) {
            return false;
        }

        $closeOrder->setAppId($this->config->getAppId());//公众账号ID
        $closeOrder->setMchId($this->config->getMchId());//商户号
        $closeOrder->setNonceStr(self::getNonceStr());//随机字符串

        $closeOrder->setSign($closeOrder->createSign($this->config->getKey()));//签名

        $xmlString      = $closeOrder->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = $this->postXmlCurl($xmlString, self::CLOSED_ORDER_URL, false);

        if (false === $response) {
            return false;
        }

        $result = WxPayResults::getValuesFromXmlString($response, $this->config->getKey());

        $this->reportCostTime(self::ORDER_QUERY_URL, $startTimeStamp, $result, $ip);//上报请求花费时间

        return $result;
    }

    /**
     * 申请退款，WxPayRefund中out_trade_no、transaction_id至少填一个且
     * out_refund_no、total_fee、refund_fee、op_user_id为必填参数
     *
     * @param WxPayRefund $refund
     * @param string $ip
     * @return array|bool|false
     */
    public function refund(WxPayRefund $refund, $ip)
    {
        //检测必填参数
        if(('' == $refund->getOutTradeNo()) && ('' == $refund->getTransactionId())) {
            return false;
        }

        if (('' == $refund->getOutRefundNo())
            || (0 == $refund->getTotalFee())
            || (0 == $refund->getRefundFee())
            || ('' == $refund->getOpUserId())) {
            return false;
        }

        $refund->setAppId($this->config->getAppId());//公众账号ID
        $refund->setMchId($this->config->getMchId());//商户号
        $refund->setNonceStr(self::getNonceStr());//随机字符串

        $refund->setSign($refund->createSign($this->config->getKey()));//签名

        $xmlString      = $refund->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = $this->postXmlCurl($xmlString, self::REFUND_URL, true);

        if (false === $response) {
            return false;
        }

        $result = WxPayResults::getValuesFromXmlString($response, $this->config->getKey());

        $this->reportCostTime(self::ORDER_QUERY_URL, $startTimeStamp, $result, $ip);//上报请求花费时间

        return $result;
    }

    /**
     * 查询退款
     * 提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，
     * 用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。
     * WxPayRefundQuery中out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个
     *
     * @param WxPayRefundQuery $query
     * @param string $ip
     * @return array|bool|false
     */
    public function refundQuery(WxPayRefundQuery $query, $ip)
    {
        //检测必填参数
        if(('' == $query->getOutTradeNo())
            && ('' == $query->getTransactionId())
            && ('' == $query->getOutRefundNo())
            && ('' == $query->getRefundId())) {
            return false;
        }

        $query->setAppId($this->config->getAppId());//公众账号ID
        $query->setMchId($this->config->getMchId());//商户号
        $query->setNonceStr(self::getNonceStr());//随机字符串

        $query->setSign($query->createSign($this->config->getKey()));//签名

        $xmlString      = $query->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = $this->postXmlCurl($xmlString, self::REFUND_QUERY_URL, false);

        if (false === $response) {
            return false;
        }

        $result = WxPayResults::getValuesFromXmlString($response, $this->config->getKey());

        $this->reportCostTime(self::ORDER_QUERY_URL, $startTimeStamp, $result, $ip);//上报请求花费时间

        return $result;
    }

    /**
     * 企业付款业务
     * ◆ 给同一个实名用户付款，单笔单日限额2W/2W
     * ◆ 给同一个非实名用户付款，单笔单日限额2000/2000
     * ◆ 一个商户同一日付款总额限额100W
     * ◆ 单笔最小金额默认为1元
     * ◆ 每个用户每天最多可付款10次，可以在商户平台--API安全进行设置
     * ◆ 给同一个用户付款时间间隔不得低于15秒
     *
     * 必填项目 partner_trade_no openid check_name amount desc spbill_create_ip
     * 如果check_name设置为FORCE_CHECK或OPTION_CHECK，则必填用户真实姓名
     * @param WxPayTransfer $transfer
     * @param $ip
     * @return array|bool|false
     */
    public function transfers(WxPayTransfer $transfer, $ip)
    {
        //检测必填参数
        if (('' == $transfer->getPartnerTradeNo()) ||
            ('' == $transfer->getOpenId()) ||
            (0 == $transfer->getAmount()) ||
            ('' == $transfer->getDescription()) ||
            ('' == $transfer->getSpbillCreateIp())) {
            return false;
        }

        //如果check_name设置为FORCE_CHECK或OPTION_CHECK，则必填用户真实姓名
        if (($transfer->getCheckName() != WxPayCheckName::NO_CHECK) && ('' == $transfer->getReUserName())) {
            return false;
        }

        $transfer->setAppId($this->config->getAppId());//公众账号ID
        $transfer->setMchId($this->config->getMchId());//商户号
        $transfer->setNonceStr(self::getNonceStr());//随机字符串

        $transfer->setSign($transfer->createSign($this->config->getKey()));//签名

        $xmlString      = $transfer->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = $this->postXmlCurl($xmlString, self::TRANSFERS_URL, true);

        if (false === $response) {
            return false;
        }

        $result = WxPayResults::getValuesFromXmlString($response, $this->config->getKey(), false);

        $this->reportCostTime(self::ORDER_QUERY_URL, $startTimeStamp, $result, $ip);//上报请求花费时间

        return $result;
    }
}
