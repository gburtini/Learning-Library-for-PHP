<?php
   /* 
      Copyright (c) 2011 Giuseppe Burtini
      See LICENSE for more information.

      This library contributes a single function ll_linear_regression which performs a linear regression on
      a set of data. It supports two methods, gradient descent (recommended) and normal equations (not-recommended).
      The normal equations method is dependent on the LL_Matrix class being loaded for use, but this library
      otherwise stands alone and requires no other parts of the Learning Library to function.

      $xs represents the data you want to regress on.
      $ys represents the list of "answers" (i.e., y = b0 + b1x1 + b2x2) 
      $method is either gradient or normal (right now)
      $alpha is used in gradient descent and represents the "learning rate", set this as high as you can get away with.
      $initialization is the vector of values to start your b0, b1, b2 values at during the gradient descent. if you don't pass it, will use a vector of 0s.
      $repetitions is the number of times to repeat. This is required unless LL_AUTODETECT_CONVERGENCE is defined to be a floating point value, in which case, we will repeat until we're within that distance from the previous iteration.
   */

   require_once "regression/normal_equations.php";
   require_once "regression/gradient_descent.php";
   require_once "regression/logistic.php";
   
   // first column of xs should be 1,1,1,...,1,1 if you want an intercept in your regression (if you don't know if you do, you do)
   // xs is array of arrays, where the inner arrays represent the data for each parameter, and the outer array is the llist of data rows
   // ys is an array of answer values
   function ll_linear_regression($xs, $ys, $method="gradient", $alpha=null, $initialization=null, $repetitions=null, $convergence=null)
   {
      switch($method) {
         case "gradient": default:
            return _ll_linear_gradient_descent($xs, $ys, $initialization, $alpha, $repetitions, $convergence);
         break;

         case "stochastic":
            trigger_error("Stochastic gradient descent not implemented.");
         break;

         case "conjugate":
            trigger_error("Conjugate gradient not implemented.");
         break;

         case "bfgs": case "lbfgs": case "l-bfgs":
            trigger_error("BFGS not implemented.");
         break;

         case "normal": 
            // compute the normal equations method... parameters = (x' x)^-1 x' y
            return _ll_normal_equation($xs, $ys);
         break;
      }
   }

   