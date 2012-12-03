<?php
require_once "../lib/accessory/matrix.php";

class MatrixTest extends PHPUnit_Framework_TestCase
{

	private $size = 128;
	private $strassen = false;//true;
	public function testMultiply() {
		$identity = $this->getIdentityMatrix($this->size);
		$array = array();
		for($i = 0; $i < $this->size; $i++) {
			for($j = 0; $j < $this->size; $j++) {
				$array[$i][$j] = rand();
			}
		}

		$matrix = new LL_Matrix($array);

		if($this->strassen) 
			$result = $matrix->strassenMultiply($identity);
		else
			$result = $matrix->naiveMultiply($identity);

	}

	// test helpers
	private function getIdentityMatrix($size=3)
	{
		$result = array();
		for($i=0;$i<$size; $i++)
		{
			for($j=0; $j<$size; $j++) {
				$result[$i][$j] = ($j == $i);
			}
		}
		return new LL_Matrix($result);
	}
}

