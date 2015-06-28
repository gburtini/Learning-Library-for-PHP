<?php
require_once dirname(__FILE__) . "/../regression.php";

// you'll want to setBadIterationsThreshold($autodetect) higher than normal with stochastic gradient descent,
// as it isn't rare to have growing distance.

class LL_StochasticGradientDescent_Regression extends LL_GradientDescent_Regression {
   private $hasShuffled = false;
   function __construct($xs, $ys, $initialParameters=null) {
      parent::__construct($xs, $ys, $initialParameters);
      $this->setBadIterationsThreshold(100); // set a higher default ootb.
   }

   private function stochasticShuffle() {
      if($this->hasShuffled)
         return;

      // shuffle Xs and Ys simultaneously.
      $temp = array();
      for ($i=0, $count=count($this->xs); $i<$count; $i++) {
         $temp[$i] = array($this->xs[$i], $this->ys[$i]);
      }  // build a shuffleable array (paired array)

      shuffle($temp);
      for ($i=0, $count=count($this->xs); $i<$count; $i++) {
         $this->xs[$i] = $temp[$i][0];
         $this->ys[$i] = $temp[$i][1];
      }

      $this->hasShuffled = true;
   }

   protected function iteration($parameters=null) {
      if($parameters === null)
         $parameters = $this->parameters;

      if(!$this->hasShuffled)
         $this->stochasticShuffle();

      $temp_parameters = $parameters;
      for($xi=0; $xi<count($this->ys); $xi++) {
         foreach($parameters as $index=>$param)
         {
            $temp_parameters[$index] = $temp_parameters[$index] - ($this->learningRate * $this->distanceDerivative($index, $xi));
         }
      }

      return $temp_parameters;
   }

   protected function distanceDerivative($with_regard_to_index, $i=0) {
      $result = (($this->hypothesis($this->xs[$i]) - $this->ys[$i]) * $this->xs[$i][$with_regard_to_index]);
      return $result;
   }

}

?>
