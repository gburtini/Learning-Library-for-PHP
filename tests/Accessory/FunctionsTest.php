<?php

use Giuseppe\LearningLibrary\Accessory\Functions;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Functions
     */
    private $instance;

    protected function setUp()
    {
        $this->instance = new Functions();
    }

    /**
     * @dataProvider signDataProvider
     * @param $n
     * @param $sign
     */
    public function testSign($n, $sign)
    {
        $this->assertSame($this->instance->sign($n), $sign);
    }

    public function signDataProvider()
    {
        return [
            [1, 1]
        ];
    }
}
