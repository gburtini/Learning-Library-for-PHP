<?php
   function _ll_linear_normal_equation($xs, $ys, $regularization=null)
   {
      if(!class_exists("LL_Matrix"))
         throw new Exception("Missing LL_Matrix class.");

      $matrix_x = new LL_Matrix($xs);
      $matrix_y = new LL_Matrix($ys);

      $inner = $matrix_x->transpose()->multiply($matrix_x);
      if($regularization !== null)  // if $regularization > 0, this solves invertibility issues (but not the underlying statistical problems).
      {
         $regularization_matrix = LL_Matrix::identity();
         $regularization_matrix->set(0,0, 0);
         $regularization_matrix->scalarMultiply($regularization);
         $inner->add($regularization_matrix);
      }

      $inner = $inner->invert();
      $inner = $inner->multiply($matrix_x->transpose());
      $result = $inner->multiply($matrix_y);

      $return = array();
      for($i=0; $i<(count($xs[0])); $i++)
      {
         $return[$i] = $result->get($i, 0);
      }
      // this should be an estimate of our parameters now.

      return $return;
   }
?>