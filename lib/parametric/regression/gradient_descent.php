<?php
require_once dirname(__FILE__) . "/../regression.php";

class LL_GradientDescent_Regression extends LL_Regression {
   protected $learningRate;
   protected $repetitions;
   protected $minrep = 2; 
   protected $badIterationsThreshold = 3;
   protected $convergenceThreshold;

   public function train() {
      $this->resetParameters = $this->parameters;
      $distance = $this->distance();

      $continue = true;
      $badIterationsCount = $iterationsCount = 0;

      do
      {
         $iterationsCount++;

         $parameters = $this->iteration();
         $newDistance = $this->distance($parameters);

         if($distance <= $newDistance)
         {
            $badIterationsCount++;
            // line search and backtrack for an appropriate learning rate here.
            
            if($badIterationsCount > $this->badIterationsThreshold)
               throw new BadIterationsException("Distance is increasing on iterations. You probably want to set a lower learning rate.");
         } else {
            $badIterationsCount = 0;   // reset bad iterations count on a good iteration.
         }

         if($this->repetitions !== null)
         {
            $continue = ($iterationsCount < $this->repetitions);
         } else if( $iterationsCount > $this->minrep && abs(($distance - $newDistance)) < $this->convergenceThreshold  ) {  // convergence test.
            $continue = false;
         } else {
            $continue = true;
         }

         $distance = $newDistance;
         $this->parameters = $parameters;
      } while($continue);

      $this->trained = true;
      return $parameters;
   }

   public function setLearningRate($alpha) {
      $this->learningRate = $alpha;
   }
   public function getLearningRate() { return $this->learningRate; }

   public function setRepetitions($reps) {
      $this->repetitions = $reps;
      $this->autodetectConvergence = false;
   }
   public function getRepetitions() { return $this->repetitions; }

   public function setConvergenceThreshold($autodetect) {
      $this->convergenceThreshold = $autodetect;
      if($autodetect)
         $this->repetitions = null;
   }
   public function getConvergenceThreshold() { return $this->convergenceThreshold; }
   public function setBadIterationsThreshold($autodetect) {
      $this->badIterationsThreshold = $autodetect;
   }
   public function getBadIterationsThreshold() { return $this->badIterationsThreshold; }



   protected function iteration($parameters=null) {
      if($parameters === null)
         $parameters = $this->parameters;

      $temp_parameters = array();
      foreach($parameters as $index=>$param)
      {
         $temp_parameters[] = $param - ($this->learningRate * $this->distanceDerivative($index));
      }
      return $temp_parameters;
   }

   protected function distance() {
      $result = 0;
      for($i=0,$count=count($this->ys);$i<$count;$i++)
      {
         $result += pow(($this->hypothesis($this->xs[$i]) - $this->ys[$i]), 2);
      }

      return $result;
   }

   // computes gradients by passing in with_regard_to_index.
   protected function distanceDerivative($with_regard_to_index) {
      $data_count = count($this->ys);
      $result = 0;
      for($i=0;$i<$data_count;$i++)
      {
         $result += (($this->hypothesis($this->xs[$i]) - $this->ys[$i]) * $this->xs[$i][$with_regard_to_index]);

      }
      $result *= (1/$data_count);

      return $result;
   }

   protected function hypothesis($row) {
      if(count($this->parameters) != count($row))
         return false;

      $result = 0;
      for($i=0, $count=count($this->parameters);$i<$count;$i++)
         $result += $row[$i] * $this->parameters[$i];

      return $result;
   }
}


class BadIterationsException extends Exception { }
class ImproperDataException extends Exception { }
?>
