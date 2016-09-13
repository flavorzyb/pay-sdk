<?php
namespace Pay\AliPay;

use Simple\Http\Client;
use Simple\Log\Writer;

class AliPayBase
{
    /**
     * @var Writer
     */
    protected $logWriter = null;

    /**
     * AliPayBase constructor.
     * @param Writer $logWriter
     */
    public function __construct(Writer $logWriter)
    {
        $this->logWriter = $logWriter;
    }

    /**
     * @return Writer
     */
    protected function getLogWriter()
    {
        return $this->logWriter;
    }

    /**
     * 除去数组中的空值和签名参数
     * @param array $data   签名参数组
     * @return array        去掉空值与签名参数后的新签名参数组
     */
    protected function filter(array $data)
    {
        $result = array();

        foreach ($data as $key => $value) {
            if (!(('sign' == $key) || ('sign_type' == $key) || ('' == $value))) {
                $result[$key]   = $value;
            }
        }

        return $result;
    }

    /**
     * 对数组排序
     * @param array $data   排序前的数组
     * @return string       排序后的数组
     */
    protected function sort(array $data)
    {
        ksort($data);
        reset($data);
        return $data;
    }

    /**
     * 签名字符串
     * @param string $str   需要签名的字符串
     * @param string $key   私钥
     * @return string       签名结果
     */
    protected function md5Sign($str, $key)
    {
        return md5($str . $key);
    }

    /**
     * 验证签名
     * @param string $str   需要签名的字符串
     * @param string $sign  签名结果
     * @param string $key   私钥
     * @return bool
     */
    protected function md5Verify($str, $sign, $key)
    {
        $mySign = $this->md5Sign($str, $key);

        return ($mySign === $sign);
    }

    /**
     * RSA签名
     * @param string $str               待签名数据
     * @param string $privateKeyPath    商户私钥文件路径
     * @return string                   签名结果
     */
    protected function rsaSign($str, $privateKeyPath)
    {
        $priKey = file_get_contents($privateKeyPath);
        $res    = openssl_get_privatekey($priKey);
        openssl_sign($str, $sign, $res);
        openssl_free_key($res);
        //base64编码
        return base64_encode($sign);
    }

    /**
     * RSA验签
     * @param string $str           待签名数据
     * @param string $publicKeyPath 支付宝的公钥文件路径
     * @param string $sign          要校对的的签名结果
     * @return bool                 验证结果
     */
    protected function rsaVerify($str, $publicKeyPath, $sign)  {
        $pubKey = file_get_contents($publicKeyPath);
        $res    = openssl_get_publickey($pubKey);
        $result = openssl_verify($str, base64_decode($sign), $res);
        openssl_free_key($res);
        return (1 == $result);
    }

    /**
     * RSA解密
     * @param string $content           需要解密的内容，密文
     * @param string $privateKeyPath    商户私钥文件路径
     * @return string                   解密后内容，明文
     */
    protected function rsaDecrypt($content, $privateKeyPath) {
        $priKey     = file_get_contents($privateKeyPath);
        $res        = openssl_get_privatekey($priKey);
        //用base64将内容还原成二进制
        $content    = base64_decode($content);
        //把需要解密的内容，按128位拆开解密
        $result     = '';
        for($i = 0; $i < strlen($content)/128; $i++  ) {
            $data = substr($content, $i * 128, 128);
            openssl_private_decrypt($data, $decrypt, $res);
            $result .= $decrypt;
        }
        openssl_free_key($res);
        return $result;
    }

    protected function getClient()
    {
        return new Client();
    }

    /**
     * 远程获取数据，POST模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
     *
     * @param   string $url             指定URL完整路径地址
     * @param   string $certUrl       指定当前工作目录绝对路径
     * @param   string $postStr         请求的数据
     * @param   string $inputCharset    编码格式。默认值：空值
     * @return  string                  远程输出的数据
     */
    protected function getHttpResponseWithPOST($url, $certUrl, $postStr, $inputCharset = '')
    {
        if ('' != trim($inputCharset)) {
            $url = $url."_input_charset=".$inputCharset;
        }

        $client = $this->getClient();
        $client->setMethod(Client::METHOD_POST);
        $client->setUrl($url);
        $client->setSslVerifyHost(true);
        $client->setSslVerifyPeer(true);
        $client->setCaInfo($certUrl);
        $client->setHeader(false);
        $client->setPostFields($postStr);

        $result = false;
        if ($client->exec()) {
            $result = $client->getResponse();
        } else {
            $this->getLogWriter()->error("AliPay Post Data Error: " . $url . "  " . $postStr);
        }

        return $result;
    }

    /**
     * 远程获取数据，GET模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，
     * @param   string $url     指定URL完整路径地址
     * @param   string $certUrl 指定当前工作目录绝对路径
     * @return  string          远程输出的数据
     */
    protected function getHttpResponseWithGET($url, $certUrl)
    {
        $client = $this->getClient();
        $client->setMethod(Client::METHOD_GET);
        $client->setUrl($url);
        $client->setSslVerifyHost(true);
        $client->setSslVerifyPeer(true);
        $client->setCaInfo($certUrl);
        $client->setHeader(false);

        $result = false;
        if ($client->exec()) {
            $result = $client->getResponse();
        } else {
            $this->getLogWriter()->error("AliPay Get Data Error: " . $url);
        }

        return $result;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param array $data   需要拼接的数组
     * @return string       拼接完成以后的字符串
     */
    protected function createString(array $data)
    {
        $result  = "";
        foreach ($data as $k => $v) {
            $result .= $k . "=" .$v . '&';
        }

        $result = substr($result, 0, -1);

        return $result;
    }
}
