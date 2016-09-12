<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/7/24
 * Time: 下午2:53
 */

namespace Apps\Pay\WxPay;


use Apps\Common\Log;

class WxJsApiPay
{
    /**
     * 获取code
     */
    const OAUTH2_AUTHORIZE_URL      = "https://open.weixin.qq.com/connect/oauth2/authorize?";
    /**
     * 获取access token
     */
    const OAUTH2_ACCESSTOKEN_URL    = "https://api.weixin.qq.com/sns/oauth2/access_token?";
    /**
     *
     * 网页授权接口微信服务器返回的数据，返回样例如下
     * {
     *  "access_token":"ACCESS_TOKEN",
     *  "expires_in":7200,
     *  "refresh_token":"REFRESH_TOKEN",
     *  "openid":"OPENID",
     *  "scope":"SCOPE",
     *  "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
     * }
     * 其中access_token可用于获取共享收货地址
     * openid是微信支付jsapi支付接口必须的参数
     * @var array
     */
    public $data = null;

    /**
     * @var array
     */
    private $_config = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config  = $config;
    }

    /**
     * 获取配置
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
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
        if(!array_key_exists("appid", $unifiedOrderResult)
            || !array_key_exists("prepay_id", $unifiedOrderResult)
            || $unifiedOrderResult['prepay_id'] == "")
        {
            return false;
        }

        $jsPayData  = new WxPayJsApiPay();
        $jsPayData->setAppId($this->_config['appId']);
        $jsPayData->setTimeStamp(time());
        $jsPayData->setNonceStr(WxPayApi::getNonceStr());
        $jsPayData->setPackage("prepay_id=" . $unifiedOrderResult['prepay_id']);
        $jsPayData->setSignType("MD5");
        $jsPayData->setPaySign($jsPayData->createSign($this->_config['key']));

        return json_encode($jsPayData->getValues());
    }

    /**
     * 通过code从工作平台获取openid机器access_token
     *
     * @param   string $code 微信跳转回来带上的code
     * @return  string openid
     */
    public function getOpenId($code)
    {
        $code       = trim($code);
        $appId      = $this->_config['appId'];
        $proxyHost  = $this->_config['curlProxyHost'];
        $proxyPort  = $this->_config['curlProxyPort'];
        $appSecret  = $this->_config['appSecret'];

        $url        = $this->createOauthUrlForOpenid($code, $appId, $appSecret);

        $result     = '';
        //初始化curl
        for ($i = 0; $i < 3; $i++) {
            $ch     = curl_init();
            //设置超时
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            if(("0.0.0.0" != $proxyHost) && (0 != $proxyPort)) {
                curl_setopt($ch,CURLOPT_PROXY, $proxyHost);
                curl_setopt($ch,CURLOPT_PROXYPORT, $proxyPort);
            }
            //运行curl，结果以json形式返回
            $result = curl_exec($ch);
            curl_close($ch);

            if (false !== $result) {
                break;
            }

            // 如果超时,则记录下日志
            Log::error("[getOpenId] i = {$i}  Wx Pay Get Open Id Error:" . $url);
            usleep(100000);
        }

        if (false === $result) {
            Log::error("[getOpenId] Wx Pay Get Open Id Error:" . $url);
            return "";
        }

        //取出openid
        $data = json_decode($result, true);
        $this->data = $data;

        if (isset($data['openid'])) {
            return $data['openid'];
        }

        Log::error("[getOpenId] open id is not exists, url:" . $url . ', data:' . serialize($data));
        return '';
    }

    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     * @return string 返回已经拼接好的字符串
     */
    private function toUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     *
     * 获取地址js参数
     * @param   string $url
     * @return  string 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
     */
    public function getEditAddressParameters($url)
    {
        $getData = $this->data;
        $data = array();
        $data["appid"] = $this->_config['appId'];
        $data["url"] = trim($url);
        $time = time();
        $data["timestamp"] = "$time";
        $data["noncestr"] = "1234568";
        $data["accesstoken"] = $getData["access_token"];
        ksort($data);
        $params = $this->toUrlParams($data);
        $addrSign = sha1($params);

        $afterData = array(
            "addrSign" => $addrSign,
            "signType" => "sha1",
            "scope" => "jsapi_address",
            "appId" => $this->_config['appId'],
            "timeStamp" => $data["timestamp"],
            "nonceStr" => $data["noncestr"]
        );
        $parameters = json_encode($afterData);
        return $parameters;
    }

    /**
     * 构造获取code的url连接
     * @param   string $redirectUrl 微信服务器回跳的url，需要url编码
     * @param   string $appId
     * @return  string              返回构造好的url
     */
    public function createOauthUrlForCode($redirectUrl, $appId)
    {
        $dataArray = [];
        $dataArray["appid"]         = $appId;
        $dataArray["redirect_uri"]  = "$redirectUrl";
        $dataArray["response_type"] = "code";
        $dataArray["scope"]         = "snsapi_base";
        $dataArray["state"]         = "STATE"."#wechat_redirect";
        $result = $this->toUrlParams($dataArray);
        return self::OAUTH2_AUTHORIZE_URL.$result;
    }

    /**
     * 构造获取open和access_toke的url地址
     *
     * @param   string $code        微信跳转带回的code
     * @param   string $appId
     * @param   string $appSecret
     * @return  string              请求的url
     */
    private function createOauthUrlForOpenid($code, $appId, $appSecret)
    {
        $dataArray                  = [];
        $dataArray["appid"]         = $appId;
        $dataArray["secret"]        = $appSecret;
        $dataArray["code"]          = $code;
        $dataArray["grant_type"]    = "authorization_code";
        $result = $this->toUrlParams($dataArray);
        return self::OAUTH2_ACCESSTOKEN_URL . $result;
    }
}
