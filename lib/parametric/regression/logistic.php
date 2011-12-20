<?php

   function ll_logistic_predict($xs, $parameters, $threshold=0.5)
   {
      $result = 0;
      for($i=0; $i<count($parameters); $i++)
      {
         $result += $xs[$i]*$parameters[$i];
      }
      return (__ll_sigmoid($result) > $threshold);
   }
   
   function _ll_logistic_gradient_descent($xs, $ys, $initialization=null, $learning_rate=null, $regularization=null, $repetitions=null, $convergence=null)
   {
   		return _ll_gradient_descent("__ll_logistic_min_function", "__ll_logistic_cost_function_derivative", $xs, $ys, $initialization, $learning_rate, $regularization, $repetitions, $convergence);

   }

   function __ll_sigmoid($z)
   {
   		return (1/(1+exp((-1)*$z)));
   }

   function __ll_logistic_min_function($xs, $ys, $parameters)
   {
      $result = 0;

      for($i=0;$i<count($ys);$i++)
      {
         // check for $ys[$i] != 0, 1 here?
         $h_xi = __ll_logistic_hypothesis_function($xs[$i], $parameters);
      	$result += (((-1) * $ys[$i] * log($h_xi)) - (1-$ys[$i])*log(1-$h_xi));
      }

      $result /= count($ys);

      return $result;
   }

   // TODO: abstract this, its the same as the linear one but with a different hypothesis function
   function __ll_logistic_cost_function_derivative($xs, $ys, $parameters, $wrt=0)
   {
      $data_count = count($ys);
      $result = 0;
      for($i=0;$i<$data_count;$i++)
      {
         $result += ((__ll_logistic_hypothesis_function($xs[$i], $parameters) - $ys[$i]) * $xs[$i][$wrt]);
      }
      $result /= $data_count;

      return $result;
   }

   function __ll_logistic_hypothesis_function($x_row, $parameters, $regularization=null)
   {
      return __ll_sigmoid(__ll_linear_hypothesis_function($x_row, $parameters));
   }
?>