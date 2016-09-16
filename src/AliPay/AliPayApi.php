<?php
namespace Pay\AliPay;

use Pay\AliPay\Modules\AliPayTradeCloseRequest;
use Pay\AliPay\Modules\AliPayTradeQueryRequest;
use Pay\AliPay\Modules\AliPayTradeRefundQueryRequest;
use Pay\AliPay\Modules\AliPayTradeRefundRequest;
use Pay\AliPay\Modules\AliPayTradeWapPayRequest;

class AliPayApi
{
    /**
     * 外部商户创建订单并支付
     */
    const GATE_WAY_URL = 'https://openapi.alipay.com/gateway.do';

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
     * 加密方法
     * @param string $str
     * @return string
     */
    protected function encrypt($str,$screct_key){
        //AES, 128 模式加密数据 CBC
        $screct_key = base64_decode($screct_key);
        $str = trim($str);
        $str = $this->addPKCS7Padding($str);
        mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
        $encrypt_str =  mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
        return base64_encode($encrypt_str);
    }

    /**
     * 解密方法
     * @param string $str
     * @return string
     */
    protected function decrypt($str,$screct_key){
        //AES, 128 模式加密数据 CBC
        $str = base64_decode($str);
        $screct_key = base64_decode($screct_key);
        mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
        $encrypt_str =  mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
        $encrypt_str = trim($encrypt_str);

        $encrypt_str = $this->stripPKSC7Padding($encrypt_str);
        return $encrypt_str;
    }

    /**
     * 填充算法
     * @param string $source
     * @return string
     */
    protected function addPKCS7Padding($source){
        $source = trim($source);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

        $pad = $block - (strlen($source) % $block);
        if ($pad <= $block) {
            $char = chr($pad);
            $source .= str_repeat($char, $pad);
        }
        return $source;
    }
    /**
     * 移去填充算法
     * @param string $source
     * @return string
     */
    protected function stripPKSC7Padding($source){
        $source = trim($source);
        $char = substr($source, -1);
        $num = ord($char);
        if($num==62)return $source;
        $source = substr($source,0,-$num);
        return $source;
    }

    public function pay(AliPayTradeWapPayRequest $request)
    {
        if (('' == $request->getSubject()) ||
            ('' == $request->getOutTradeNo()) ||
            (0.01 > $request->getTotalAmount()) ||
            ('' == $request->getProductCode())) {
            return false;
        }


    }

    public function orderQuery(AliPayTradeQueryRequest $request)
    {
    }

    public function orderClose(AliPayTradeCloseRequest $request)
    {
    }

    public function refund(AliPayTradeRefundRequest $request)
    {
    }

    public function refundQuery(AliPayTradeRefundQueryRequest $request)
    {
    }
}
