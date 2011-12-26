<?php
   require_once "../lib/parametric/regression.php";

   class LogisticGradientDescentTest extends PHPUnit_Framework_TestCase
   {
      // These are valid tests because the data are loaded such that the results are "obvious" and perfectly predictable by the algorithm
      public function testLogisticGradientDescentExperimental()
      {
         $xs = array(
            array(1,1,2,2),
            array(1,1,3,0),
            array(1,2,4,1),
            array(1,3,5,9)
         );
         $ys = array(1,1,0,1);

         $lgd = new LL_GradientDescent_Logistic_Regression($xs, $ys);
         $lgd->setLearningRate(0.03);
         $lgd->setRepetitions(1000);
         $lgd->train();

         foreach($xs as $index=>$x) {
            $prediction = $lgd->predict($x);
            $this->assertTrue((bool)$ys[$index] == $prediction);
         }

         $xs = array(
            array(1,1,2,2,7,8,1),
            array(1,4,3,5,9,4,1),
            array(1,0,1,1,0,0,2),   // fail row
            array(1,3,5,9,3,5,2),
            array(1,4,2,2,7,8,1),
            array(1,2,3,5,9,4,1),
            array(1,0,0,1,0,0,2),   // fail row
            array(1,7,5,9,3,5,2)
         );
         $ys = array(1,1,0,1,1,1,0,1);

         $lgd->setData($xs, $ys);
         $lgd->train();

         foreach($xs as $index=>$x) {
            $prediction = $lgd->predict($x);
            $this->assertTrue((bool)$ys[$index] == $prediction);
         }
      }
   }
?>