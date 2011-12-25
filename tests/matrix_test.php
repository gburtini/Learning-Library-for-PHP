<?php
   require_once "../lib/accessory/matrix.php";

   class MatrixTest extends PHPUnit_Framework_TestCase
   {
      // we need to test determinants and invert and such
      public function testInvert()
      {
         $a = array(array(2,3), array(3,4));
         $b = array(array(-4,3), array(3,-2));

         $am = new LL_Matrix($a);
         $bm = new LL_Matrix($b);

         $this->assertEquals($am->invert(), $bm);
         $this->assertEquals($bm->invert(), $am);

         $this->assertEquals($am->multiply($am->invert()), $this->getIdentityMatrix(2));  // this tests a property about matrix multiplication. could find bugs in a variety of places though, not necessarily just in invert
         $this->assertEquals($bm->multiply($bm->invert()), $this->getIdentityMatrix(2));  // this tests a property about matrix multiplication. could find bugs in a variety of places though, not necessarily just in invert

         $this->assertEquals($this->getIdentityMatrix(4)->invert(), $this->getIdentityMatrix(4));
      }

      public function testDeterminant()
      {
         $matrix = new LL_Matrix(array(array(5,7), array(2,-3)));
         $result = $matrix->determinant();
         $this->assertEquals($result, -29);

         $matrix = new LL_Matrix(array(array(7,4,2,0), array(6,3,-1,2), array(4,6,2,5), array(8,2,-7,1)));  // this tests a series of subdeterminants (and cofactor matrices)
         $result = $matrix->determinant();
         $this->assertEquals($result, -279);

         $matrix = new LL_Matrix(array(array(-2, 3), array(5, 0.5)));
         $result = $matrix->determinant();
         $this->assertEquals($result, -16);

         $matrix = new LL_Matrix(array(array(2, -2, 0), array(-1,5,1), array(3,4,5)));
         $result = $matrix->determinant();
         $this->assertEquals($result, 26);

      }

      public function testScalarMultiply()
      {
         $matrix = new LL_Matrix(array(array(1,1,1), array(1,1,1), array(1,1,1)));
         $result = $matrix->scalarMultiply(2);
         $this->checkAllValues($result, 2);
         $result = $result->scalarMultiply(1/2);
         $this->checkAllValues($result, 1);
         $result = $result->scalarMultiply(0);
         $this->checkAllValues($result, 0);

         $result = $this->getIdentityMatrix()->scalarMultiply(0);
         $this->checkAllValues($result, 0);

         $matrix = $this->getIdentityMatrix(4);
         $result = $matrix->scalarMultiply(5);
         for($i=0;$i<4;$i++)
         {
            $this->assertEquals($result->get($i,$i), 5);
         }
      }

      public function testMultiply() {
         $identity3 = $this->getIdentityMatrix();
         $input = array(array(1,2,3), array(3,2,1), array(1,2,1));
         $matrix = new LL_Matrix($input);
         $result = $matrix->multiply($identity3);

         for($i=0; $i<count($input); $i++) {
            for($j = 0; $j<count($input[0]); $j++)
            {
               $this->assertEquals($result->get($i, $j), $input[$i][$j]);  // identity multiplication should have no effect
            }
         }

         $result = $matrix->multiply(new LL_Matrix($input));   // test multiplying by itself.
         $this->assertNotEquals($result, false);

         $this->assertEquals($result->get(0,0), 10);
         $this->assertEquals($result->get(0,1), 12);
         $this->assertEquals($result->get(0,2), 8);

         $this->assertEquals($result->get(1,0), 10);
         $this->assertEquals($result->get(1,1), 12);
         $this->assertEquals($result->get(1,2), 12);

         $this->assertEquals($result->get(2,0), 8);
         $this->assertEquals($result->get(2,1), 8);
         $this->assertEquals($result->get(2,2), 6);

         // test uneven shapes
         $input = array(array(1,2,3), array(0,2,0));
         $input2 = array(array(1,2), array(2,2), array(1,1));
         $matrix = new LL_Matrix($input);
         $matrix2 = new LL_Matrix($input2);

         $this->assertEquals($matrix->multiply($matrix), false);
         $this->assertEquals($matrix2->multiply($matrix2), false);

         $result = $matrix->multiply($matrix2);
         $correct = array(array(8,9), array(4,4));
         for($i=0;$i<count($correct);$i++) {
            for($j=0;$j<count($correct[0]);$j++) {
               $this->assertEquals($result->get($i,$j), $correct[$i][$j]);
            }
         }

         $result = $matrix2->multiply($matrix);
         $this->assertEquals($result->rows(), 3);
         $this->assertEquals($result->columns(), 3);
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

      private function checkAllValues($result, $value) {
         for($i=0; $i<$result->rows(); $i++)
         {
            for($j=0; $j<$result->columns(); $j++)
            {
               $this->assertEquals($result->get($i, $j), $value);
            }
         }
      }

   }

