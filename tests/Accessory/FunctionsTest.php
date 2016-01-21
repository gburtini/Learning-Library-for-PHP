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
            [1, 1],
            [3, 1],
            [-1, -1],
            [-3, -1],
            [0, 0],
        ];
    }

    /**
     * @dataProvider meanDataProvider
     * @param array $array
     * @param $mean
     */
    public function testMean(array $array, $mean)
    {
        $this->assertSame($this->instance->mean($array), $mean);
    }

    public function meanDataProvider()
    {
        return [
            [
                [1], 1
            ],
            [
                [2], 2
            ],
            [
                [1, 2, 3], 2
            ],
        ];
    }

    /**
     * @dataProvider euclideanDistanceDataProvider
     * @param array $a
     * @param array $b
     * @param $result
     */
    public function testEuclideanDistance(array $a, array $b, $result)
    {
        $this->assertSame($this->instance->euclideanDistance($a, $b), $result);
    }

    public function euclideanDistanceDataProvider()
    {
        return [

            [
                [1],
                [1],
                0.0
            ],

            [
                [1],
                [5],
                4.0
            ],

            [
                [0, 0],
                [3, 4],
                5.0
            ],

        ];
    }

    /**
     * @dataProvider transposeDataProvider
     * @param array $source
     * @param array $transpose
     */
    public function testTranspose(array $source, array $transpose)
    {
        $this->assertSame($this->instance->transpose($source), $transpose);
    }

    public function transposeDataProvider()
    {
        return [
            [
                [],
                []
            ],
        ];
    }
}
