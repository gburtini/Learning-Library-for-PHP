<?php

namespace Giuseppe\LearningLibrary\Parametric\Regression;

use Giuseppe\LearningLibrary\Accessory\Matrix;
use Giuseppe\LearningLibrary\Parametric\Regression;

class NormalEquations extends Regression
{
    public function train() {
        $this->resetParameters = $this->parameters;

        $matrix_x = new Matrix($this->xs);
        $matrix_y = new Matrix($this->ys);

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
