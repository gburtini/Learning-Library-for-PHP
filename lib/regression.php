<?php

   // first column of xs should be 1,1,1,...,1,1 if you want an intercept in your regression (if you don't know if you do, you do)
   // xs is array of arrays, where the inner arrays represent the data for each parameter, and the outer array is the llist of data rows
   // ys is an array of answer values
   function ll_linear_regression($xs, $ys, $method="gradient", $alpha=null, $initialization=null, $repetitions=null)
   {
      switch($method) {
         case "gradient":
            return _ll_gradient_descent($xs, $ys, $initialization, $alpha, $repetitions);
         break;

         case "normal": default:
            // compute the normal equations method... parameters = (x' x)^-1 x' y
            trigger_error("Normal equations not implemented yet.");
         break;
      }
   }

   //////
   // everything below here is for gradient descent.
   //////

   if(!defined("LL_AUTORESOLVE_LEARNING_RATE"))
      define("LL_AUTORESOLVE_LEARNING_RATE", false); // this is a bad idea (at least, for production), will attempt to automatically resolve "too small" alpha errors.

   if(!defined("LL_AUTODETECT_CONVERGENCE"))
      define("LL_AUTODETECT_CONVERGENCE", false);  // if false, requires a repetitions value; set to a number (not false) to require convergence within that distance.
      // define("LL_AUTODETECT_CONVERGENCE", 0.01); // will exit when it detects the distance between two loop iterations is less than 0.01
   
   // throws alphatoolargeexception if distance increases in any iteration.
   function _ll_gradient_descent($xs, $ys, $initialization=null, $alpha=null, $repetitions=null)
   {
      if($initialization === null)
         $initialization = array_fill(0, count($xs[0]), 0);
      $parameters = $initialization;

      if($alpha === null)
         $alpha = 1/(count($ys));

      if($repetitions === null && LL_AUTODETECT_CONVERGENCE === false)
         throw new RepetitionsNotSpecifiedException();


      $old_distance = (__ll_ols_function($xs,$ys,$parameters));
      $continue = true;
      $i=0;
      do
      {
         $i++;
         $parameters = __ll_gradient_descent_iteration($xs, $ys, $parameters, $alpha);
         $new_distance = (__ll_ols_function($xs,$ys,$parameters));
         if($old_distance < $new_distance)
         {
            // we're going backwards.
            if(LL_AUTORESOLVE_LEARNING_RATE)
            {
               // try a smaller learning rate.
               return _ll_gradient_descent($xs, $ys, $initialization, $alpha/2, $repetitions*2);
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

   function __ll_gradient_descent_iteration($xs, $ys, $parameters=null, $alpha=0.3)
   {
      $temp_parameters = array();

      foreach($parameters as $index=>$param)
      {
         $temp_parameters[] = $param - ($alpha * __ll_cost_function_derivative($xs, $ys, $parameters, $index));
      }
      return $temp_parameters; 
   }

   function __ll_ols_function($xs, $ys, $parameters)
   {
      $result = 0;
      for($i=0;$i<count($ys);$i++)
      {
         $result += pow((__ll_hypothesis_function($xs[$i], $parameters) - $ys[$i]), 2);
      }
      return $result;
   }

   function __ll_cost_function_derivative($xs, $ys, $parameters, $wrt=0)
   {
      $data_count = count($ys);
      $result = 0;
      for($i=0;$i<$data_count;$i++)
      {
         $result += ((__ll_hypothesis_function($xs[$i], $parameters) - $ys[$i]) * $xs[$i][$wrt]);
      }
      $result *= (1/$data_count);
      return $result;
   }

   function __ll_hypothesis_function($x_row, $parameters)
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
