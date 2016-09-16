<?php
namespace Pay\AliPay\Modules;


class AliPayCharsetTest extends \PHPUnit_Framework_TestCase
{
    public function testCharset()
    {
        $result = AliPayCharset::createUTF8Charset();
        self::assertEquals(AliPayCharset::UTF8, $result->getValue());
        self::assertTrue($result->isUTF8());
        self::assertFalse($result->isGBK());
        self::assertFalse($result->isGB2312());

        $result = AliPayCharset::createGBKCharset();
        self::assertEquals(AliPayCharset::GBK, $result->getValue());
        self::assertFalse($result->isUTF8());
        self::assertTrue($result->isGBK());
        self::assertFalse($result->isGB2312());

        $result = AliPayCharset::createGB2312Charset();
        self::assertEquals(AliPayCharset::GB2312, $result->getValue());
        self::assertFalse($result->isUTF8());
        self::assertFalse($result->isGBK());
        self::assertTrue($result->isGB2312());

        $result = AliPayCharset::build(AliPayCharset::UTF8);
        self::assertEquals(AliPayCharset::UTF8, $result->getValue());

        $result = AliPayCharset::build(AliPayCharset::GBK);
        self::assertEquals(AliPayCharset::GBK, $result->getValue());

        $result = AliPayCharset::build(AliPayCharset::GB2312);
        self::assertEquals(AliPayCharset::GB2312, $result->getValue());

        $result = AliPayCharset::build('');
        self::assertEquals(AliPayCharset::UTF8, $result->getValue());
    }
}
