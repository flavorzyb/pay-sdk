<?php
namespace Pay;

use DOMDocument;
use Pay\AliPay\AliConfig;
use Pay\Modules\PayNotify;
use Pay\Modules\PayOrder;
use Simple\Log\Writer;
use Pay\AliPay\AliPaySubmit;
use Pay\AliPay\AliPayNotify;

class AliPay extends PayAbstract
{
    const FORMAT    = "xml";
    const VERSION   = "2.0";
    /**
     * 支付宝配置文件
     * @var AliConfig
     */
    private $config = array();

    /**
     * AliPay constructor.
     * @param AliConfig $config
     * @param Writer $logWriter
     */
    public function __construct(AliConfig $config, Writer $logWriter)
    {
        parent::__construct($logWriter);
        $this->config = $config;
    }

    /**
     * @return AliPaySubmit
     */
    protected function createAliPaySubmit()
    {
        return new AliPaySubmit($this->config, $this->getLogWriter());
    }

    /**
     * 支付宝支付
     * @param PayOrder $payOrder
     * @return string
     * @override
     */
    protected function _payUrl(PayOrder $payOrder)
    {
        //请求号
        $reqId = date('Ymdhis').uniqid();
        //必填，须保证每次请求都是唯一

        $params         = $this->createParamToken($payOrder, $reqId);
        $aliPaySubmit   = $this->createAliPaySubmit();
        $result         = $aliPaySubmit->buildRequestHttp($params);
        //URLDECODE返回的信息
        $result         = urldecode($result);

        //解析远程模拟提交后返回的信息
        $tokenArray     = $aliPaySubmit->parseResponse($result);

        //获取request_token
        $requestToken   = $tokenArray['request_token'];

        //建立请求
        $params         = $this->createParamToRequestToken($requestToken, $reqId);
        return $aliPaySubmit->buildRequestHttpURL($params);
    }

    /**
     * @return AliPayNotify
     */
    protected function createAliPayNotify()
    {
        return new AliPayNotify($this->config, $this->getLogWriter());
    }
    /**
     * 解析支付回调数据
     * 出现错误时返回 空数组
     * @param array $data
     * @return PayNotify | null
     */
    public function parseNotify(array $data)
    {
        if (!isset($data['notify_data'])) {
            return null;
        }

        $notify = $this->createAliPayNotify();
        if (false==$notify->verifyNotify($data)) {
            $this->getLogWriter()->error("AliPay verifyNotify Fail:" . serialize($data));
            return null;
        }

        return $this->parseNotifyData($notify, $data['notify_data']);
    }

    /**
     * 解析支付回调数据 -- notify_data
     * @param AliPayNotify $notify
     * @param $notifyData
     * @return PayNotify | null
     */
    private function parseNotifyData(AliPayNotify $notify, $notifyData)
    {
        $doc = new DOMDocument();
        switch (strtoupper(trim($this->config->getSignType()))) {
            case 'MD5':
                $doc->loadXML($notifyData);
                break;
            case '0001':
            case 'RSA':
                $doc->loadXML($notify->decrypt($notifyData));
                break;
        }

        if (!empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue)) {
            //商户订单号
            $outTradeNo     = $doc->getElementsByTagName("out_trade_no")->item(0)->nodeValue;
            //支付宝交易号
            $tradeNo        = $doc->getElementsByTagName("trade_no")->item(0)->nodeValue;
            //交易状态
            $tradeStatus    = $doc->getElementsByTagName("trade_status")->item(0)->nodeValue;
            // 总金额
            $totalFee       = $doc->getElementsByTagName("total_fee")->item(0)->nodeValue;
            // 商品名称
            $name           = $doc->getElementsByTagName( "subject" )->item(0)->nodeValue;

            $strArray = explode('|', $outTradeNo);

            $result = new PayNotify();
            $result->setGoodsName($name);
            $result->setPayAmount($totalFee);
            $result->setTradeNo($tradeNo);
            $result->setStatus($tradeStatus);
            if (!isset($strArray[1])) {
                $result ->setOrderId($outTradeNo);
            } else {
                $result->setOrderId($strArray[1]);
                $result->setExtra($strArray[0]);
            }

            return $result;
        }

        $this->getLogWriter()->error("AliPay parseNotifyData error: " . $notifyData);

        return null;
    }

    /**
     * 构建支付宝支付参数Token的参数列表
     *
     * @param PayOrder $payOrder
     * @param string        $reqId
     * @return array
     */
    private function createParamToken(PayOrder $payOrder, $reqId)
    {
        //**req_data详细信息**
        //服务器异步通知页面路径
        $notifyUrl = ('' != $payOrder->getNotifyUrl() ? $payOrder->getNotifyUrl() : $this->config->getNotifyUrl());
        //需http://格式的完整路径，不允许加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $callBackUrl = ('' != $payOrder->getCallBackUrl() ? $payOrder->getCallBackUrl() : $this->config->getCallBackUrl());
        //需http://格式的完整路径，不允许加?id=123这类自定义参数

        //操作中断返回地址
        $merchantUrl   = ('' != $payOrder->getMerchantUrl() ? $payOrder->getMerchantUrl() : $this->config->getMerchantUrl());
        //用户付款中途退出返回商户的地址。需http://格式的完整路径，不允许加?id=123这类自定义参数

        //卖家支付宝帐户
        $sellerEmail   = $this->config->getAccount();
        //必填

        //商户订单号
        $outTradeNo   = $payOrder->getExtra() . '|' . $payOrder->getOrderId();
        //商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject        = $payOrder->getGoodsName();
        //必填

        //付款金额
        $totalFee      = $payOrder->getPayAmount();
        //必填

        //请求业务参数详细
        $reqData        = '<direct_trade_create_req>'.
                            '<notify_url>' . $notifyUrl . '</notify_url>' .
                            '<call_back_url>' . $callBackUrl . '</call_back_url>'.
                            '<seller_account_name>' . $sellerEmail . '</seller_account_name>'.
                            '<out_trade_no>' . $outTradeNo . '</out_trade_no>'.
                            '<subject>' . $subject . '</subject>'.
                            '<total_fee>' . $totalFee . '</total_fee>'.
                            '<merchant_url>' . $merchantUrl . '</merchant_url>'.
                         '</direct_trade_create_req>';
        //必填
        /************************************************************/

        //构造要请求的参数数组，无需改动
        return  [
                    "service"           => "alipay.wap.trade.create.direct",
                    "partner"           => trim($this->config->getPartnerId()),
                    "sec_id"            => trim($this->config->getSignType()),
                    "format"            => self::FORMAT,
                    "v"                 => self::VERSION,
                    "req_id"            => $reqId,
                    "req_data"          => $reqData,
                    "_input_charset"    => trim(strtolower($this->config->getInputCharset()))
                ];
    }

    /**
     * 构建请求支付的参数列表
     *
     * @param string $token
     * @param string $reqId
     * @return array
     */
    private function createParamToRequestToken($token, $reqId)
    {
        /**************************根据授权码token调用交易接口alipay.wap.auth.authAndExecute**************************/
        //业务详细
        $reqData = '<auth_and_execute_req><request_token>' . $token . '</request_token></auth_and_execute_req>';
        //必填
        //构造要请求的参数数组，无需改动
        return [
                "service"           => "alipay.wap.auth.authAndExecute",
                "partner"           => trim($this->config->getPartnerId()),
                "sec_id"            => trim($this->config->getSignType()),
                "format"            => self::FORMAT,
                "v"                 => self::VERSION,
                "req_id"            => $reqId,
                "req_data"          => $reqData,
                "_input_charset"    => trim(strtolower($this->config->getInputCharset()))
            ];
    }
}
