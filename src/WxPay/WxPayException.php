<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/7/24
 * Time: 下午2:23
 */

namespace Apps\Pay\WxPay;

use Exception;

class WxPayException extends Exception
{
    /**
     * @return string
     */
    public function errorMessage()
    {
        return $this->getMessage();
    }
}