<?php
   function ll_linear_regression($xs, $ys, $method="gradient", $alpha=null, $initialization=null)
   {
      switch($method) {
         case "gradient":
            if($initialization === null)
            {
               $initialization = array_fill(0, count($xs[0]), 0);
            }

            if($alpha === null)
            {
               $alpha = 0.3;  // long term this should be an indication that the script should guess an alpha.
            }

            return _ll_gradient_descent($xs, $ys, $initialization, $alpha);
         break;

         case "normal": default:
            // compute the normal equations method
            trigger_error("Normal equations not implemented yet.");
         break;
      }
   }

   function _ll_gradient_descent($xs, $ys, $initialization, $alpha=0.3, $repetitions=1000)
   {
      $parameters = $initialization;
      $temp_parameters = array();
      $data_rows = count($ys); 
      for($i=0;$i<$repetitions;$i++)
      {
         $temp_parameters = array();
         $cost = __ll_distance_cost($xs, $ys, $parameters);

         foreach($parameters as $param)
         {
            $temp_parameters[] = $param - $alpha * ((1/$data_rows)  * $cost); // this is the descent step (1/data_rows * cost) is the derivative of the cost func.
         }
         $parameters = $temp_parameters;
      }

      return $parameters;
   }

   function __ll_distance_cost($xs, $ys, $parameters)
   {
      //$intercept = array_shift($parameters);
      $sum_cost = 0;
      for($i=0;$i<count($ys);$i++)
      {
         $row_values = $xs[$i];
         $answer = $ys[$i];
         $hypothesis = 0;
         for($j = 0;$j<count($parameters);$j++)
         {
            $hypothesis += ($parameters[$j] * $row_values[$j]);
         }

         $cost = $hypothesis - $answer;
         $sum_cost += $cost;
      }
      return $sum_cost;
   }
