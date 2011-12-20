<?php

   class LL_NeuralNetwork
   {
      var $inputNodes;
      var $outputNodes;
      var $hiddenNodes = array();

      var $alpha = 0.3;
      var $activationFunction = "ll_sigmoid";
      /* Algorithm thanks to: Chris Marriott, Ryan Shirley, CJ Baker, Thomas Tannahill */

      public function predict($inputNodes)
      {
         // iterates through the NN and computes an output array.
      }

      public function learn()
      {
         // trains the parameters in the array (via backprop?)
      }
   }
?>