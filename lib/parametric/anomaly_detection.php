<?php
/*
 * This could be easily modified to use any reasonable distribution, but as it is implemented
 * below, only supports the Gaussian (normal) distribution by changing computeProbabilities.
 * Recommended to subclass if you're going to do that.
 *
 * Thoughts: if you have an n-modal distribution (say bimodal), can you use k-means
 * clustering to compute multiple anomaly detectors (one around each mode) and sum/f(x,y) their
 * probabilities? maybe not because this would chop the edges of the distribution off.
 */
require_once dirname(__FILE__) . "/../accessory/functions.php";

class LL_OnlineAnomalyDetection extends LL_AnomalyDetection {
   private $sums;
   private $m2s;
   private $counts;

   public function addObservation($x) {
      for($i=0;$i<count($x);$i++)
      {
         $this->updateMean($i);
         $this->updateVariance($i);

         $this->sums[$i] += $x[$i];
         $this->counts[$i]++;
         $this->m2s[$i] += (pow($x[$i] - $this->mean[$i], 2));

      }
   }

   protected function computeParameters($xs) {
      $xs_columns = ll_transpose($xs);
      foreach($xs_columns as $index=>$column) {
         $this->sums[$index] = $sum = array_sum($column);
         $this->counts[$index] = $count = count($column);

         $mean = ll_mean($column);
         $sum_difference = 0;
         $n = count($column);

         for($i=0; $i<$n; $i++) {
            $sum_difference += pow(($column[$i] - $mean),2);
         }

         $this->m2s[$index] = $sum_difference;


         $this->updateMean($index);
         $this->updateVariance($index);
      }
   }

   protected function updateMean($index) {
      $this->mean[$index] = $this->sums[$index] / $this->counts[$index];
   }
   protected function updateVariance($index) {
      $this->variance[$index] = $this->m2s[$index] / $this->counts[$index];
   }
}

class LL_AnomalyDetection {
   protected $mean = array();
   protected $variance = array();

   private $learned = false;

   public function learn($xs)
   {
      $this->computeParameters($xs);
      $this->learned = true;
   }

   public function isAnomaly($data_point, $p=0.01)
   {
      if(!$this->learned)
         return null;

      $probability = $this->computeProbability($data_point);
      if($p === false)
         return $probability;

      return ($probability < $p);
   }

   protected function computeProbability($x) {
      $prod = 1;
      foreach($x as $index=>$xi) {
         $prob = $this->normalPdf($xi, $this->mean[$index], $this->variance[$index]);
         $prod *= $prob;
      }
      return $prod;
   }
   protected function normalPdf($x, $mean, $variance)
   {
      return (1/(sqrt(2 * pi()) * sqrt($variance)) * (exp((-1) * pow($x - $mean, 2) / (2 * $variance))));
   }
   protected function computeParameters($xs)
   {
      $xs_columns = ll_transpose($xs);
      foreach($xs_columns as $index=>$column) {
         $this->mean[$index] = ll_mean($column);
         $this->variance[$index] = ll_variance($column);
      }
   }

}
?>
