<?php
namespace Pay\AliPay;

use DOMDocument;

class AliPaySubmit extends AliPayBase
{
    /**
     *支付宝网关地址
     */
    const GATEWAY_URL   = 'http://wappaygw.alipay.com/service/rest.htm?';

    /**
     * 配置文件
     * @var array
     */
    private $_config    = array();

    /**
     * AliPaySubmit constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config  = $config;
    }

    /**
     * 建立请求，以模拟远程HTTP的POST请求方式构造并获取支付宝的处理结果
     * @param array $data   请求参数数组
     * @return string       支付宝处理结果
     */
    public function buildRequestHttp(array $data) {

        //待请求参数数组字符串
        $requestStr = $this->buildRequestParams($data);

        //远程获取数据
        return $this->getHttpResponseWithPOST(self::GATEWAY_URL,
                                            $this->_config['cert'],
                                            $requestStr,
                                            trim(strtolower($this->_config['input_charset'])));
    }

    /**
     * 构建请求支付的URL
     *
     * @param array $data
     * @return string
     */
    public function buildRequestHttpURL(array $data)
    {
        return self::GATEWAY_URL . $this->buildRequestParamsToString($data);
    }

    /**
     * 生成要请求给支付宝的参数数组
     * 要请求的参数数组字符串
     *
     * @param array $data 请求前的参数数组
     * @return string
     */
    private function buildRequestParamsToString(array $data)
    {
        //待请求参数数组
        $data = $this->buildRequestParams($data);

        //把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
        return $this->createStringWithUrlEncode($data);
    }

    /**
     * 生成要请求给支付宝的参数数组
     * 要请求的参数数组
     * @param array $data 请求前的参数数组
     * @return array
     */
    private function buildRequestParams(array $data)
    {
        //除去待签名参数数组中的空值和签名参数
        $filter = $this->filter($data);

        //对待签名参数数组排序
        $result = $this->sort($filter);

        //生成签名结果
        $mySign = $this->buildRequestMySign($result);

        //签名结果与签名方式加入请求提交参数组中
        $result['sign'] = $mySign;

        if (('alipay.wap.trade.create.direct' != $result['service']) &&
            ('alipay.wap.auth.authAndExecute' != $result['service'])) {

            $result['sign_type'] = strtoupper(trim($this->_config['sign_type']));
        }

        return $result;
    }

    /**
     * 生成签名结果
     * @param array $data 已排序要签名的数组
     * @return string 签名结果字符串
     */
    private function buildRequestMySign(array $data)
    {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $result = $this->createString($data);

        switch (strtoupper(trim($this->_config['sign_type']))) {
            case "MD5" :
                $mySign = $this->md5Sign($result, $this->_config['key']);
                break;
            case "RSA" :
            case "0001" :
                $mySign = $this->rsaSign($result, $this->_config['private_key_path']);
                break;
            default :
                $mySign = "";
        }

        return $mySign;
    }

    /**
     * 把数组所有元素，
     * 按照“参数=参数值”的模式用“&”字符拼接成字符串，
     * 并对字符串做urlencode编码
     * @param array $data   需要拼接的数组
     * @return string       拼接完成以后的字符串
     */
    private function createStringWithUrlEncode(array $data)
    {
        $result = '';
        foreach ($data as $k => $v) {
            $result .= $k . '=' . urlencode($v) . '&';
        }

        return substr($result, 0, -1);
    }

    /**
     * 解析远程模拟提交后返回的信息
     * @param string $str   要解析的字符串
     * @return array        解析结果
     */
    public function parseResponse($str)
    {
        //以“&”字符切割字符串
        $splitArray = explode('&',$str);
        $result     = array();

        //把切割后的字符串数组变成变量与数值组合的数组
        foreach ($splitArray as $item) {
            //获得第一个=字符的位置
            $nPos   = strpos($item,'=');
            //获得字符串长度
            $nLen   = strlen($item);
            //获得变量名
            $key    = substr($item,0,$nPos);
            //获得数值
            $value  = substr($item,$nPos+1,$nLen-$nPos-1);
            //放入数组中
            $result[$key] = $value;
        }

        if  (false == empty($result['res_data'])) {
            //解析加密部分字符串
            switch (strtoupper(trim($this->_config['sign_type']))) {
                case '0001':
                case 'RSA':
                    $result['res_data'] = $this->rsaDecrypt($result['res_data'], $this->_config['private_key_path']);
                    break;

            }

            //token从res_data中解析出来（也就是说res_data中已经包含token的内容）
            $doc = new DOMDocument();
            $doc->loadXML($result['res_data']);
            $result['request_token'] = $doc->getElementsByTagName( "request_token" )->item(0)->nodeValue;
        }

        return $result;
    }
}
