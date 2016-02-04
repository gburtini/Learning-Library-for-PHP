<?php

use Giuseppe\LearningLibrary\Parametric\Regression\NormalEquations;

class NormalEquationsTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalRegression()
    {
        $xs = array(
            array(1, 1),
            array(1, 2),
            array(1, 3),
            array(1, 4)
        );
        $ys = array(2, 4, 6, 8);

        $ner = new NormalEquations($xs, $ys);
        $result = $ner->train();

        $this->assertEquals($result[0], 0);
        $this->assertEquals($result[1], 2);
    }
}

