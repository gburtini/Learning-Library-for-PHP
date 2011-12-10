<?php
   if(!defined("LL_AUTORESOLVE_LEARNING_RATE"))
      define("LL_AUTORESOLVE_LEARNING_RATE", false); // this is a bad idea (at least, for production), will attempt to automatically resolve "too small" alpha errors.

   if(!defined("LL_AUTODETECT_CONVERGENCE"))
      define("LL_AUTODETECT_CONVERGENCE", false);  // if false, requires a repetitions value; set to a number (not false) to require convergence within that distance.
      // define("LL_AUTODETECT_CONVERGENCE", 0.01); // will exit when it detects the distance between two loop iterations is less than 0.01
   
   function _ll_linear_gradient_desceent($xs, $ys, $initialization=null, $learning_rate=null, $repetitions=null, $convergence=null)
   {
   		return _ll_gradient_descent("__ll_linear_ols_function", "__ll_linear_cost_function_derivative", $xs, $ys);
   }
   
   // throws alphatoolargeexception if distance increases in any iteration.
   // only pass one of repetitions, convergence.
   // $distance_function = "__ll_linear_ols_function"
   // $distance_derivative = "__ll_linear_cost_function_derivative"
   function _ll_gradient_descent($distance_function, $distance_derivative, $xs, $ys, $initialization=null, $learning_rate=null, $repetitions=null, $convergence=null)
   {
      if($initialization === null)
         $initialization = array_fill(0, count($xs[0]), 0);
      $parameters = $initialization;

      if($learning_rate === null)
         $learning_rate = 1/(count($ys));

      if($convergence === null)
      	$convergence = LL_AUTODETECT_CONVERGENCE;
	  
      if($repetitions === null && $convergence === false)
         throw new RepetitionsNotSpecifiedException();

	  if(!function_exists($distance_function) || !function_exists($distance_derivative))
	  	throw new DistanceFunctionDoesntExistException();
	  	
      $old_distance = $distance_function($xs,$ys,$parameters);
      $continue = true;
      $i=0;
      do
      {
         $i++;
         $parameters = __ll_gradient_descent_iteration($xs, $ys, $parameters, $learning_rate);
         $new_distance = ($distance_function($xs,$ys,$parameters));
         if($old_distance < $new_distance)
         {
            // we're going backwards.
            if(LL_AUTORESOLVE_LEARNING_RATE)
            {
               // try a smaller learning rate.
               return _ll_gradient_descent($distance_function, $distance_derivative, $xs, $ys, $initialization, $learning_rate/2, $repetitions*2, $convergence);
            } else {
               throw new AlphaTooLargeException("Set alpha smaller. Distance is increasing on iterations.");
            }
         
         }
         if($repetitions !== null)
         {
            $continue = ($i++ < $repetitions);
         } else if(abs(($old_distance - $new_distance)) < LL_AUTODETECT_CONVERGENCE) {  // convergence test.
            $continue = false;
         } else {
            $continue = true;
         }

         $old_distance = $new_distance;
      } while($continue);


      return $parameters;
   }

   function __ll_gradient_descent_iteration($distance_derivative, $xs, $ys, $parameters=null, $alpha=0.3)
   {
      $temp_parameters = array();

      foreach($parameters as $index=>$param)
      {
         $temp_parameters[] = $param - ($alpha * $distance_derivative($xs, $ys, $parameters, $index));
      }
      return $temp_parameters; 
   }

   function __ll_linear_ols_function($xs, $ys, $parameters)
   {
      $result = 0;
      for($i=0;$i<count($ys);$i++)
      {
         $result += pow((__ll_linear_hypothesis_function($xs[$i], $parameters) - $ys[$i]), 2);
      }
      return $result;
   }

   function __ll_linear_cost_function_derivative($xs, $ys, $parameters, $wrt=0)
   {
      $data_count = count($ys);
      $result = 0;
      for($i=0;$i<$data_count;$i++)
      {
         $result += ((__ll_linear_hypothesis_function($xs[$i], $parameters) - $ys[$i]) * $xs[$i][$wrt]);
      }
      $result *= (1/$data_count);
      return $result;
   }

   function __ll_linear_hypothesis_function($x_row, $parameters)
   {
      if(count($parameters) != count($x_row)) 
         return false;

      $result = 0;
      for($i=0;$i<count($parameters);$i++)
         $result += $x_row[$i] * $parameters[$i];

      return $result;
   }

   class AlphaTooLargeException extends Exception { }
   class RepetitionsNotSpecifiedException extends Exception { }
   class DistanceFunctionDoesntExistException extends Exception { }
?>