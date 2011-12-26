<?php
require_once dirname(__FILE__) . "/../regression.php";

class LL_NormalEquations_Regression extends LL_Regression {
   public function train() {
      $this->resetParameters = $this->parameters;

      if(!class_exists("LL_Matrix"))
         throw new Exception("Missing LL_Matrix class.");

      $matrix_x = new LL_Matrix($this->xs);
      $matrix_y = new LL_Matrix($this->ys);

      $inner = $matrix_x->transpose()->multiply($matrix_x);

      //if($regularization !== null)  // if $regularization > 0, this solves invertibility issues (but not the underlying statistical problems).
      //{
      //   $regularization_matrix = LL_Matrix::identity();
      //   $regularization_matrix->set(0,0, 0);
      //   $regularization_matrix->scalarMultiply($regularization);
      //   $inner->add($regularization_matrix);
      //}

      $inner = $inner->invert();
      $inner = $inner->multiply($matrix_x->transpose());
      $result = $inner->multiply($matrix_y);

      $return = array();
      for($i=0, $count=count($this->xs[0]); $i<$count; $i++)
      {
         $return[$i] = $result->get($i, 0);
      }

      $this->parameters = $return;
      $this->trained = true;
      return $return;
   }
}

?>