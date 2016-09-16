<?php
namespace Pay\AliPay\Modules;

final class AliPayCharset
{
    const UTF8 = 'utf-8';
    const GBK = 'gbk';
    const GB2312 = 'gb2312';

    private $value = self::UTF8;

    /**
     * AliPayCharset constructor.
     * @param string $value
     */
    private function __construct($value)
    {
        $this->setValue($value);
    }

    /**
     * @param string $value
     */
    private function setValue($value)
    {
        $this->value = $value;
    }


    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * build charset
     * @param string $charset
     * @return AliPayCharset
     */
    public static function build($charset)
    {
        $charset = trim($charset);
        switch ($charset) {
            case self::UTF8:
            case self::GBK:
            case self::GB2312:
                break;
            default:
                $charset = self::UTF8;
        }

        return new AliPayCharset($charset);
    }

    /**
     * create Utf8 charset
     *
     * @return AliPayCharset
     */
    public static function createUTF8Charset()
    {
        return new self(self::UTF8);
    }

    /**
     * create gbk charset
     * @return AliPayCharset
     */
    public static function createGBKCharset()
    {
        return new self(self::GBK);
    }

    /**
     * create gb2312 charset
     * @return AliPayCharset
     */
    public static function createGB2312Charset()
    {
        return new self(self::GB2312);
    }

    /**
     * is utf8
     * @return bool
     */
    public function isUTF8()
    {
        return self::UTF8 == $this->value;
    }

    /**
     * is gbk
     * @return bool
     */
    public function isGBK()
    {
        return self::GBK == $this->value;
    }

    /**
     * is gb2312
     * @return bool
     */
    public function isGB2312()
    {
        return self::GB2312 == $this->value;
    }
}
