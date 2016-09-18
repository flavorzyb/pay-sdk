<?php
namespace Pay;

use ConfigFactory;

class AliPayTest extends PayAbstractTest
{
    /**
     * @var AliPay
     */
    protected $pay = null;

    protected function setUp()
    {
        parent::setUp();
        $writer = m::mock('Simple\Log\Writer');
        $writer->shouldReceive('error')->andReturn(true);
        $writer->shouldReceive('info')->andReturn(true);
        $writer->shouldReceive('debug')->andReturn(true);
        $this->pay = new AliPay(ConfigFactory::createAliPayConfig(), $writer);
    }

    protected function getPay()
    {
        return $this->pay;
    }


}
