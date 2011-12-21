<?php
/*
 * This could be easily modified to use any reasonable distribution, but as it is implemented
 * below, only supports the Gaussian (normal) distribution by changing computeProbabilities.
 * Recommended to subclass if you're going to do that.
 */
require_once dirname(__FILE__) . "/../accessory/functions.php";

class LL_AnomalyDetection {
   private $mean = array();
   private $variance = array();
   private $oneSided;

   public function learn($xs)
   {
      $this->computeParameters($xs);
   }

   public function setOneSided($bool) {
      $this->oneSided = $bool;
   }
   public function getOneSided() { return $this->oneSided; }

   public function isAnomaly($data_point, $p=0.01)
   {
      $probability = $this->computeProbability($data_point);
      if($p === false)
         return $probability;

      return ($probability < $p);
   }

   protected function computeProbability($x) {
      $prod = 1;
      foreach($x as $index=>$xi) {
         $prod *= normalPdf($x, $this->mean[$index], $this->variance[$index]);
      }
      return $prod;
   }
   protected function normalPdf($x, $mean, $variance)
   {
      return (1/(sqrt(2 * pi()) * sqrt($variance)) * (exp((-1) * pow($x - $mean, 2) / (2 * $variance))));
   }
   private function computeParameters($xs)
   {
      $xs_columns = ll_tranpose($xs);
      foreach($xs_columns as $index=>$column) {
         $this->mean[$index] = ll_mean($column);
         $this->variance[$index] = ll_variance($column);
      }
   }

}
?>
