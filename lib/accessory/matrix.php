<?php
   /*
      Copyright (c) 2011 Giuseppe Burtini
      See LICENSE for more information.

      This library implements some basic matrix algebra constructs in PHP. It does not claim to do so
      in the most efficient known way (in particular, inversion is done in a relatively poor way), but
      rather to do so in a very clear way. When time permits, I may replace some of the less efficient
      methods with more efficient methods.

      Alternatively, if you accept that I may relicense code submitted to this repository (I will contact
      you specifically for approval), feel free to submit your own corrections or improvements via a
      pull request. Please make sure any changes pass the rudimentary testing under tests/matrix_test.php

      This class can standalone and is not dependent on the rest of the learning library.
   */


   class LL_Matrix {
      private $_data;

      // transforms array in to a LL_Matrix
      function __construct($array)
      {
         $this->set_data($array);
      }
      function LL_matrix($array) { return $this->__construct($array); }

      public static function identity($size=3)
      {
         $result = array();
         for($i=0;$i<$size; $i++)
         {
            for($j=0; $j<$size; $j++) {
               $result[$i][$j] = ($j == $i);
            }
         }
         return new LL_Matrix($result);
      }


      public function invert()
      {
         $return = array();

         // should really use LU decomp. instead of Cramer's here. much faster.
         for($i=0; $i<$this->rows(); $i++)
         {
            for($j=0; $j<$this->columns(); $j++)
            {
               $cofactor = $this->getCofactorMatrix($i, $j);
               $return[$i][$j] = (pow(-1, $i+$j) * $cofactor->determinant());
            }
         }
         $return = new LL_Matrix($return);
         $det = $return->determinant();

         if($det == 0)
            return false;

         $return = $return->scalarMultiply(1/$return->determinant());
         $return->transpose();
         return $return;
      }

      public function transpose()
      {
         $return = array();
         for($i=0; $i<$this->rows(); $i++)
         {
            for($j=0; $j<$this->columns(); $j++)
            {
               $return[$j][$i] = $this->get($i, $j);  // swap $i and $j
            }
         }

         return new LL_Matrix($return);
     }

     public function determinant()
     {
         $return = 0;
         if($this->columns() == 1)
            return $this->get(0,0);

         for($i=0; $i<$this->columns(); $i++)
         {
            // instead of using 0 here, we can probably do this more efficiently.
            $cofactor = $this->getCofactorMatrix(0, $i);
            $multipland = (pow((-1), $i) * $this->get(0, $i));
            $return += $cofactor->determinant() * $multipland;
         }
         return $return;
     }


      public function add(LL_Matrix $matrix)
      {
         if(is_array($matrix))
            $matrix = new LL_Matrix($matrix);

         if($this->rows() != $matrix->rows() || $this->columns() != $matrix->columns())
            return false;  // impossible operation.

         $return = array();
         for($i=0; $i<$this->rows(); $i++)
         {
            for($j=0; $j<$this->columns(); $j++)
            {
               $return[$i][$j] = $this->get($i,$j)+$matrix->get($i,$j);
            }
         }
         return new LL_Matrix($return);
      }

      public function scalarMultiply($value)
      {
         $return = array();
         for($i=0; $i<$this->rows(); $i++)
         {
            for($j=0; $j<$this->columns(); $j++)
            {
               $return[$i][$j] = $this->get($i,$j)*$value;
            }
         }
         return new LL_Matrix($return);
      }

      public function multiply(LL_Matrix $matrix)
      {
         if(is_array($matrix))   // make sure the matrix is an LL_Matrix
         {
            $matrix = new LL_Matrix($matrix);
         }
         if($this->columns() != $matrix->rows())  // impossible operation.
            return false;

         $result = array();
         for($a=0; $a<$this->rows(); $a++)   // our rows
         {
            for($b=0; $b<$matrix->columns(); $b++) // their columns
            {
               $result[$a][$b] = 0;
               for($i=0; $i<$this->columns(); $i++)   // our columns
               {
                  $result[$a][$b] += ($this->get($a,$i) * $matrix->get($i, $b));
               }
            }
         }

         return new LL_Matrix($result);
      }

      public function getCofactorMatrix($cofactorRow, $cofactorColumn)
      {
         $return = array();
         for($i=0, $a=0; $i<$this->rows(); $i++)
         {
            $b=0;
            if($i != $cofactorRow)
            {
               for($j=0; $j<$this->columns(); $j++)
               {
                  if($j != $cofactorColumn)
                  {
                     $return[$a][$b++] = $this->get($i,$j);
                  }
               }
               $a++;
            }

         }
         return new LL_Matrix($return);
      }

      public function get($row, $column)
      {
         return $this->_data[$row][$column];
      }

      public function set($row, $column, $value)
      {
         return ($this->_data[$row][$column] = $value);
      }

      public function columns()
      {
         return count($this->_data[0]);
      }
      public function rows() {
         return count($this->_data);
      }

      private function set_data($array)
      {
         foreach($array as $row=>$vector)
         {
               if(!is_array($vector)) {   // php hates foreach on single elements
                  $this->_data[$row][0] = $vector;
               } else {
                  foreach($vector as $col=>$cell)
                  {
                     $this->_data[$row][$col] = $cell;
                  }
              }
         }

         return $this->_data;
      }

      public function __toString()
      {
         $string = "";
         for($i=0;$i<$this->rows();$i++)
         {
            for($j=0;$j<$this->columns(); $j++) {
               $string .= $this->get($i, $j) . " ";
            }
            $string .= "\n";
         }
         return $string;
      }
   }
?>
