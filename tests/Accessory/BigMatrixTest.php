<?php

use Giuseppe\LearningLibrary\Accessory\Matrix;

class BigMatrixTest extends \PHPUnit_Framework_TestCase
{
    private $size = 32;

    public function testMultiply()
    {
        $identity = Matrix::identity($this->size);
        $array = array();
        for ($i = 0; $i < $this->size; $i++) {
            for ($j = 0; $j < $this->size; $j++) {
                $array[$i][$j] = rand();
            }
        }

        $matrix = new Matrix($array);

        $matrix->strassenMultiply($identity);
        $matrix->naiveMultiply($identity);
    }
}
