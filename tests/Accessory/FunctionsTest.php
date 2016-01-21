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
     * @covers \Giuseppe\LearningLibrary\Accessory\Functions::sign
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
     * @covers \Giuseppe\LearningLibrary\Accessory\Functions::mean
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
     * @covers \Giuseppe\LearningLibrary\Accessory\Functions::euclideanDistance
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
            [
                [1, 2],
                [3],
                false
            ]
        ];
    }

    /**
     * @dataProvider transposeDataProvider
     * @covers \Giuseppe\LearningLibrary\Accessory\Functions::transpose
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
            [
                [[1]],
                [[1]]
            ],

            [
                [
                    [1, 2],
                    [3, 4],
                ],
                [
                    [1, 3],
                    [2, 4],
                ]
            ],

            [
                [
                    [1, 2, 3],
                    [4, 5, 6],
                    [7, 8, 9],
                ],
                [
                    [1, 4, 7],
                    [2, 5, 8],
                    [3, 6, 9],
                ]
            ],
        ];
    }

    /**
     * @dataProvider varianceDataProvider
     * @covers \Giuseppe\LearningLibrary\Accessory\Functions::variance
     * @param array $array
     * @param $expected
     */
    public function testVariance(array $array, $expected)
    {
        $this->assertSame($this->instance->variance($array), $expected);
    }

    public function varianceDataProvider()
    {
        return [
            [
                [1, 1, 1],
                0
            ],
            [
                [1, 2, 3, 4, 5],
                2
            ],
        ];
    }
}
