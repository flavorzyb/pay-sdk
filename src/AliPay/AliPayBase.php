<?php
namespace Pay\AliPay;

class AliPayBase
{
    /**
     * curl超时时间 单位:秒
     */
    const CURL_TIMEOUT  = 3;

    /**
     * 重试次数
     */
    const TRY_NUMBER    = 3;

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

        for ($i = 0; $i < self::TRY_NUMBER; $i++) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
            curl_setopt($curl, CURLOPT_CAINFO, $certUrl);//证书地址
            curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
            curl_setopt($curl, CURLOPT_TIMEOUT, self::CURL_TIMEOUT);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
            curl_setopt($curl, CURLOPT_POST, true); // post传输数据
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postStr);// post传输数据
            $result = curl_exec($curl);
            curl_close($curl);

            if (false !== $result) {
                break;
            }
            sleep(1);
        }

        if (false === $result) {
            Log::pay("AliPay Post Data Error: " . $url . "  " . $postStr);
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
        for ($i = 0; $i < self::TRY_NUMBER; $i++) {
            $curl = curl_init($url);
            curl_setopt($curl,  CURLOPT_HEADER,         0); // 过滤HTTP头
            curl_setopt($curl,  CURLOPT_TIMEOUT,        self::CURL_TIMEOUT);
            curl_setopt($curl,  CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
            curl_setopt($curl,  CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
            curl_setopt($curl,  CURLOPT_SSL_VERIFYHOST, 2);//严格认证
            curl_setopt($curl,  CURLOPT_CAINFO,         $certUrl);//证书地址
            $result = curl_exec($curl);
            curl_close($curl);

            if (false !== $result) {
                break;
            }
            sleep(1);
        }

        if (false === $result) {
            Log::pay("AliPay Get Data Error: " . $url);
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
