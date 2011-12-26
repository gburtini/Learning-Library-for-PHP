<?php
   require_once dirname(__FILE__) . "/../lib/parametric/regression.php";

   class GradientDescentTest extends PHPUnit_Framework_TestCase
   {
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

         $gd = new LL_GradientDescent_Regression($xs, $ys, $parameters);
         $gd->setLearningRate(0.01);
         $gd->setRepetitions(10000);
         $gd->train();

         $results = $gd->getParameters();

         $this->assertLessThan(0.01, array_shift($results));
         foreach($results as $coefficient)
         {
            $this->assertGreaterThan(0.999, $coefficient);
         }

         $gd->reset();
         $gd->setLearningRate(0.03);
         $gd->setRepetitions(5);

         $results = $gd->train();

         $this->assertLessThan(0.3, array_shift($results));
         $this->assertGreaterThan(0.2, array_shift($results));
         foreach($results as $coefficient)
         {
            $this->assertGreaterThan(0.99, $coefficient);
         }
      }
   }

?>