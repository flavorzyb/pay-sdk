<?php
namespace Pay\AliPay\Modules;

class AliPayTradeWapPayRequest extends AliPayRequest
{
    const METHOD = 'alipay.trade.wap.pay';

    /**
     * 回跳地址
     * @var string
     */
    private $returnUrl = '';

    /**
     * 通知地址
     * @var string
     */
    private $notifyUrl = '';

    /**
     * 对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body。
     * @var string
     */
    private $body = '';

    /**
     * 商品的标题/交易标题/订单标题/订单关键字等
     * @var string
     */
    private $subject = '';

    /**
     * 商户网站唯一订单号
     * @var string
     */
    private $outTradeNo = '';

    /**
     * 该笔订单允许的最晚付款时间，逾期将关闭交易。
     * 取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。
     * 该参数数值不接受小数点， 如 1.5h，可转换为 90m。
     * @var string
     */
    private $timeoutExpress = '';

    /**
     * 订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]
     * @var float
     */
    private $totalAmount = 0;

    /**
     * 收款支付宝用户ID。 如果该值为空，则默认为商户签约账号对应的支付宝用户ID
     * @var string
     */
    private $sellerId = '';

    /**
     * 针对用户授权接口，获取用户相关数据时，用于标识用户授权关系
     * @var string
     */
    private $authToken = '';

    /**
     * 销售产品码，商家和支付宝签约的产品码
     * @var string
     */
    private $productCode = 'QUICK_WAP_PAY';

    /**
     * @return string
     */
    public function getMethod()
    {
        return self::METHOD;
    }

    public function getBizContent()
    {
        $data = [];
        $data['subject'] = $this->getSubject();
        $data['out_trade_no'] = $this->getOutTradeNo();
        $data['total_amount'] = $this->getTotalAmount();
        $data['product_code'] = $this->getProductCode();

        if ('' != $this->getBody()) {
            $data['body'] = $this->getBody();
        }

        if ('' != $this->getTimeoutExpress()) {
            $data['timeout_express'] = $this->getTimeoutExpress();
        }

        if ('' != $this->getSellerId()) {
            $data['seller_id'] = $this->getSellerId();
        }

        if ('' != $this->getAuthToken()) {
            $data['auth_token'] = $this->getAuthToken();
        }

        return json_encode($data);
    }

    /**
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * @param string $returnUrl
     */
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;
    }

    /**
     * @return string
     */
    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }

    /**
     * @param string $notifyUrl
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getOutTradeNo()
    {
        return $this->outTradeNo;
    }

    /**
     * @param string $outTradeNo
     */
    public function setOutTradeNo($outTradeNo)
    {
        $this->outTradeNo = $outTradeNo;
    }

    /**
     * @return string
     */
    public function getTimeoutExpress()
    {
        return $this->timeoutExpress;
    }

    /**
     * @param string $timeoutExpress
     */
    public function setTimeoutExpress($timeoutExpress)
    {
        $this->timeoutExpress = $timeoutExpress;
    }

    /**
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return string
     */
    public function getSellerId()
    {
        return $this->sellerId;
    }

    /**
     * @param string $sellerId
     */
    public function setSellerId($sellerId)
    {
        $this->sellerId = $sellerId;
    }

    /**
     * @return string
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    /**
     * @param string $authToken
     */
    public function setAuthToken($authToken)
    {
        $this->authToken = $authToken;
    }

    /**
     * @return string
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * @param string $productCode
     */
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;
    }
}
