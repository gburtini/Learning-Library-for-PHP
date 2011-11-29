<?php
   require_once "../lib/regression.php";

   class GradientDescentTest extends PHPUnit_Framework_TestCase 
   {
      public function testHypothesisFunction()
      {  
         // with intercept, compute hypothesis, three value xs.
         $xs = array(1,2,3);
         $this->assertEquals(__ll_hypothesis_function($xs, array(2,2,2)), 12);
         $this->assertEquals(__ll_hypothesis_function($xs, array(-1,2,3)), 12);
         $this->assertEquals(__ll_hypothesis_function($xs, array(1,0,0)), 1);
         $this->assertEquals(__ll_hypothesis_function($xs, array(1500, 1,1)), 1505);
         $this->assertEquals(__ll_hypothesis_function($xs, array(1,1,1)), 6);

         // simple linear regression (small data)
         $this->assertEquals(__ll_hypothesis_function(array(1,5), array(9,9)), 54);

         // large data (1000 variables)
         $xs = array_fill(0, 1000, 1);
         $ys = array_fill(0, 1000, 5);
         $this->assertEquals(__ll_hypothesis_function($xs, $ys), 5000);
      }

      public function testCostFunctionDerivative()
      {
         $xs = array(
            array(1,2,2,2),
            array(1,3,3,3),
            array(1,4,4,4),
            array(1,5,5,5)
         );

         $ys = array(6,9,12,15);

         $parameters = array(0,0,0,0);
         $this->assertEquals(__ll_cost_function_derivative($xs,$ys,$parameters,0), -10.5); 
      }


      public function testGradientDescent()
      {
         $xs = array(
            array(1,2,2,2),
            array(1,3,3,3),
            array(1,4,4,4),
            array(1,5,5,5)
         );
         $ys = array(6,9,12,15);
         $parameters = array(0,0,0,0);
         $results = _ll_gradient_descent($xs, $ys, $parameters, 0.01, 10000);

         $this->assertLessThan(0.01, array_shift($results));
         foreach($results as $coefficient)
         {
            $this->assertGreaterThan(0.99, $coefficient);
         }
      }

      /**
         * @expectedException AlphaTooLargeException
      */
      public function testGradientDescentException()
      {
         $xs = array(
            array(1,2,2,2),
            array(1,3,3,3),
            array(1,4,4,4),
            array(1,5,5,5)
         );
         $ys = array(6,9,12,15);
         $parameters = array(0,0,0,0);
         $results = _ll_gradient_descent($xs, $ys, $parameters, 20,100);
      }

   }

?>
