<?php
namespace Pay\WxPay;

use Pay\WxPay\Modules\WxPayOrderQuery;
use Pay\WxPay\Modules\WxPayReport;
use Pay\WxPay\Modules\WxPayResults;
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
     * 随机字符串 32 位
     */
    const NONCE_STRING_LENGTH = 32;

    /**
     * curl time out
     */
    const CURL_TIME_OUT     = 6;

    /**
     * 重试次数
     */
    const TRY_NUMBER        = 3;

    /**
     * 配置文件
     * @var array
     */
    private $config    = array();

    /**
     * @var Writer
     */
    protected $log = null;

    /**
     * @var Client
     */
    protected $client = null;

    /**
     * @return Client
     */
    public function getClient()
    {
        return new Client();
    }

    /**
     * AliPaySubmit constructor.
     * @param array $config
     * @param Writer $writer
     */
    public function __construct(array $config, Writer $writer)
    {
        $this->config  = $config;
        $this->log = $writer;
    }

    /**
     * 获取配置
     * @return array
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
    public function postXmlCurl($xml, $url, $useCert = false)
    {
        $xml        = trim($xml);
        $url        = trim($url);
        $useCert    = boolval($useCert);

        $client = $this->getClient();
        $client->setUrl($url);
        $client->setMethod(Client::METHOD_POST);
        $client->setPostFields($xml);

        if (isset($this->config['curlProxyHost']) && ("0.0.0.0" != $this->config['curlProxyHost'])) {
            $client->setProxyHost($this->config['curlProxyHost']);
        }

        if (isset($this->config['curlProxyPort']) && (0 != $this->config['curlProxyPort'])) {
            $client->setProxyPort($this->config['curlProxyPort']);
        }

        $client->setSslVerifyPeer(true);
        $client->setSslVerifyHost(true);
        $client->setHeader(false);

        if ($useCert && isset($this->config['sslCertPath']) && isset($this->config['sslKeyPath'])) {
            $client->useCert(Client::CERT_TYPE_PEM, $this->config['sslCertPath'], $this->config['sslKeyPath']);
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
        if (0 == $this->config['reportLevel']) {
            return true;
        }

        //如果仅失败上报
        if((1 == $this->config['reportLevel']) &&
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

        if (!$report->isSetReturnCode()) {
            $this->log->error("WxPay reportCostTime Error: 缺少必填参数return_code url = $url data=" . serialize($data));
            return false;
        }

        if (!$report->isSetResultCode()) {
            $this->log->error("WxPay reportCostTime Error: 缺少必填参数result_code url = $url data=" . serialize($data));
            return false;
        }

        $report->setAppId($this->config['appId']);
        $report->setMchId($this->config['mchId']);
        $report->setNonceStr($this->getNonceStr());
        $report->setTime(date("YmdHis"));
        $report->setUserIp($clientIp);
        $report->setSign($report->createSign($this->config['key']));

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
        $result     = WxPayResults::getValuesFromXmlString($xmlString, $this->config['key']);
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
     *
     * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayOrderQuery $orderQuery
     * @param string $ip
     * @return bool 成功时返回，其他抛异常
     */
    public function orderQuery(WxPayOrderQuery $orderQuery, $ip)
    {
        //检测必填参数
        if(!($orderQuery->isSetOutTradeNo() || $orderQuery->isSetTransactionId())) {
            return false;
        }

        $orderQuery->setAppId($this->config['appId']);//公众账号ID
        $orderQuery->setMchId($this->config['mchId']);//商户号
        $orderQuery->setNonceStr(self::getNonceStr());//随机字符串

        $orderQuery->setSign($orderQuery->createSign($this->config['key']));//签名

        $xmlString      = $orderQuery->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = $this->postXmlCurl($xmlString, self::ORDER_QUERY_URL, false);

        $result = WxPayResults::getValuesFromXmlString($response, $this->config['key']);

        $this->reportCostTime(self::ORDER_QUERY_URL, $startTimeStamp, $result, $ip);//上报请求花费时间

        return $result;
    }
}
