<?php
namespace Pay\AliPay;

use DOMDocument;
use Simple\Log\Writer;

class AliPayNotify extends AliPayBase
{
    /**
     * HTTPS形式消息验证地址
     */
    const HTTPS_VERIFY_URL  = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
    /**
     * HTTP形式消息验证地址
     */
    const HTTP_VERIFY_URL   = 'http://notify.alipay.com/trade/notify_query.do?';

    /**
     * 配置文件
     * @var array
     */
    private $_config = array();

    /**
     * AliPayNotify constructor.
     * @param array $_config
     * @param Writer $logWriter
     */
    public function __construct(array $_config, Writer $logWriter)
    {
        parent::__construct($logWriter);
        $this->_config = $_config;
    }

    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @param array $data POST 过来的数据
     * @return bool 验证结果
     */
    public function verifyNotify(array $data)
    {
        $this->getLogWriter()->info("AliPay Notify Data:" . serialize($data));

        if (empty($data)) {
            return false;
        }

        //对notify_data解密
        switch ($this->_config['sign_type']) {
            case '0001':
            case 'RSA':
                $data['notify_data'] = $this->rsaDecrypt($data['notify_data'], $this->_config['private_key_path']);
                break;
        }

        if (!isset($data['notify_data'])) {
            return false;
        }

        if (!isset($data["sign"])) {
            return false;
        }

        //notify_id从decrypt_post_para中解析出来（也就是说decrypt_post_para中已经包含notify_id的内容）
        $doc        = new DOMDocument();
        $doc->loadXML($data['notify_data']);
        $notify_id  = $doc->getElementsByTagName( "notify_id" )->item(0)->nodeValue;

        //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
        $responseTxt = 'true';
        if (! empty($notify_id)) {
            $responseTxt = $this->getResponse($notify_id);
        }

        //生成签名结果
        $isSign = $this->getSignVerify($data, $data["sign"], false);

        //验证
        //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
        //isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
        return ($isSign && preg_match("/true$/i",$responseTxt));
    }

    /**
     * 解密
     * @param   string $str 要解密数据
     * @return  string      解密后结果
     */
    public function decrypt($str)
    {
        return $this->rsaDecrypt($str, trim($this->_config['private_key_path']));
    }

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     *
     * @param   string $notifyId    通知校验ID
     * @return  string              服务器ATN结果
     */
    private function getResponse($notifyId) {
        $transport  = strtolower(trim($this->_config['transport']));
        $partner    = trim($this->_config['partner']);
        $verifyUrl  = self::HTTP_VERIFY_URL;

        if('https' == $transport) {
            $verifyUrl = self::HTTPS_VERIFY_URL;
        }

        $verifyUrl = $verifyUrl."partner=" . $partner . "&notify_id=" . $notifyId;

        return $this->getHttpResponseWithGET($verifyUrl, $this->_config['cert']);
    }

    /**
     * 获取返回时的签名验证结果
     * @param   array   $data   通知返回来的参数数组
     * @param   string  $sign   返回的签名结果
     * @param   bool    $isSort 是否对待签名数组排序
     * @return  bool            签名验证结果
     */
    private function getSignVerify($data, $sign, $isSort) {
        //除去待签名参数数组中的空值和签名参数
        $data = $this->filter($data);

        //对待签名参数数组排序
        if($isSort) {
            $data = $this->sort($data);
        } else {
            $data = $this->sortNotify($data);
        }

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $str    = $this->createString($data);

        $isSgin = false;
        switch (strtoupper(trim($this->_config['sign_type']))) {
            case "MD5" :
                $isSgin = $this->md5Verify($str, $sign, $this->_config['key']);
                break;
            case "RSA" :
            case "0001" :
                $isSgin = $this->rsaVerify($str, trim($this->_config['ali_public_key_path']), $sign);
                break;
        }

        return $isSgin;
    }

    /**
     * 异步通知时，对参数做固定排序
     * @param   array   $data   排序前的参数组
     * @return  array           排序后的参数组
     */
    private function sortNotify(array $data)
    {
        $result                 = [];
        $result['service']      = isset($data['service'])       ? $data['service'] : '';
        $result['v']            = isset($data['v'])             ? $data['v'] : '';
        $result['sec_id']       = isset($data['sec_id'])        ? $data['sec_id'] : '';
        $result['notify_data']  = isset($data['notify_data'])   ? $data['notify_data'] : '';

        return $result;
    }
}
