<?php
require_once dirname(__FILE__) . "/gradient_descent.php";

class LL_GradientDescent_Logistic_Regression extends LL_GradientDescent_Regression {
   public function predict($x, $threshold=0.5) {
      $prob = $this->sigmoid(parent::predict($x));
      if($threshold === null)
         return $prob;
      return ($prob > $threshold);
   }
   protected function distance() {
      $result = 0;

      for($i=0,$count=count($this->ys);$i<$count;$i++)
      {
         // do we need to check for $this->ys[$i] != {0, 1} here?

         $h_xi = $this->hypothesis($this->xs[$i]);
      	$result += (((-1) * $this->ys[$i] * log($h_xi)) - (1-$this->ys[$i])*log(1-$h_xi));
      }

      $result /= count($this->ys);

      return $result;
   }

   protected function hypothesis($row=null) {
      return $this->sigmoid(parent::hypothesis($row));
   }

   private function sigmoid($z) {
      return (1/(1+exp((-1)*$z)));
   }
}

?>