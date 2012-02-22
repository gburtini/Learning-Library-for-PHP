<?php
/*
 * Anomaly Detection Classes
 * 
 *  - dependent on accessory/functions.php
 *
 * This could be easily modified to use any reasonable distribution, but as it is implemented
 * below, only supports the Gaussian (normal) distribution by changing computeProbabilities.
 * Recommended to subclass if you're going to do that.
 *
 * Thoughts: if you have an n-modal distribution (say bimodal), can you use k-means
 * clustering to compute multiple anomaly detectors (one around each mode) and sum/f(x,y) their
 * probabilities? maybe not because this would chop the edges of the distribution off.
 */

require_once dirname(__FILE__) . "/../accessory/functions.php";

// online anomaly detector, can addObservations instead of only training once.
class LL_OnlineAnomalyDetection extends LL_AnomalyDetection {
   private $sums;
   private $m2s;
   private $counts;

   protected $toSave = array("sums", "m2s", "counts");
   /*
    * addObservation($x)
    *   - pass in an array $x representing all the dimensions relevant to your observation.
    *   - if you only have one dimension, you don't need to pass it as an array.
    *   - updates mean and variance, and stores the relevant online data.
    *   - observations do not have to be non-anomalies, you can addObservation for all seen observations
    *     - this is probably the best way to use the anomaly detector for live data, 
    *     as it will allow it to update to changes in what an anomaly is over time.
    *
    * Example Usage:
    *   $ll->addObservation(14);
    */
   public function addObservation($x) {
      if(!is_array($x))
         $x = array($x);

      for($i=0;$i<count($x);$i++)
      {
         $this->sums[$i] += $x[$i];
         $this->counts[$i]++;
         $this->m2s[$i] += (pow($x[$i] - $this->mean[$i], 2));

         $this->updateMean($i);
         $this->updateVariance($i);
      }
   }


   protected function computeParameters($xs) {
      if(!is_array($xs[0])) {
         $xs = $this->arrayNonArray($xs);
      }

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


// main implementation class
class LL_AnomalyDetection {
   protected $mean = array();
   protected $variance = array();

   private $learned = false;

   /*
    * learn($xs)
    *   - pass in an array of arrays (or an array of data points). each array represents the dimensions of the data
    *   being studied.
    *   - calculates all the appropriate parameters for computing anomalies.
    *
    * Example Usage:
    *   $ll = new LL_AnomalyDetection();
    *   $ll->learn(array(1, 2, 3, 4, 5, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 1, 1));
    *   $ll->isAnomaly(100);  // bool(true)
    *   $ll->isAnomaly(3);    // bool(false)
    */
   public function learn($xs)
   {
      $this->computeParameters($xs);
      $this->learned = true;
   }

   /*
    * isAnomaly($data_point, $p=0.01);
    *   - pass in data_point as an array of dimensions (or a single observation) and will return true or false if it is an anomaly
    *   - define an anomaly as something that has less than $p chance of occurring, given the distribution.
    *   - if you would rather know the $p that a particular observation has of occuring, pass $p=false.
    *
    * Example Usage:
    *   $ll->isAnomaly(array(144,100,200));
    *   $p_value = $ll->isAnomaly(array(1,2,3), false);
    */
   public function isAnomaly($data_point, $p=0.01)
   {
      if(!$this->learned)
         return null;

      $probability = $this->computeProbability($data_point);
      if($p === false)
         return $probability;

      return ($probability < $p);
   }


   protected $toSave = array("mean", "variance");
   public function save() {
      $saveString = "";
      foreach($this->toSave as $save)
      {
          $saveString .= implode(",", $this->$save) . "|";
      }
	  $s_learned = intval($this->learned);
	  
	  return $saveString . $s_learned;
   }

   public function load($saveString) {
      $saveArray = explode("|", $saveString);
      if(count($saveArray) != count($this->toSave)-1)
          return false;

      foreach($this->toSave as $key=>$load) 
      {
          $this->$load = explode(",", $saveArray[$key]);
      }
      
      $this->learned = (bool) end($saveArray);
   }

   protected function computeProbability($x) {
      if(!is_array($x)) 
         $x = array($x);

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
      if(!is_array($xs[0])) {
         $xs = $this->arrayNonArray($xs);
      }

      $xs_columns = ll_transpose($xs);
      foreach($xs_columns as $index=>$column) {
         $this->mean[$index] = ll_mean($column);
         $this->variance[$index] = ll_variance($column);
      }
   }
   
   private function arrayNonArray($array) {
      $return = array();
      foreach($array as $item) {
         $return[] = array($item);
      }
      return $return;
   }
}
?>
