<?php

namespace Giuseppe\LearningLibrary\Accessory;

class Matrix
{
    private $data;

    public function __construct(array $array)
    {
        $this->setData($array);
    }

    public static function identity($size = 3)
    {
        $result = [];
        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $result[$i][$j] = $j == $i;
            }
        }
        return new Matrix($result);
    }

    public function dotProduct(Matrix $x)
    {
        $a = $this->data;
        $b = $x->getData();
        return array_sum(array_map(create_function('$a, $b', 'return $a * $b;'), $a, $b));
    }

    public function invert()
    {
        $return = [];

        // should really use LU decomp. instead of Cramer's here. much faster.
        for ($i = 0; $i < $this->rows(); $i++) {
            for ($j = 0; $j < $this->columns(); $j++) {
                $cofactor = $this->getCofactorMatrix($i, $j);
                $return[$i][$j] = (pow(-1, $i + $j) * $cofactor->determinant());
            }
        }
        $return = new Matrix($return);
        $det = $this->determinant();

        if ($det == 0)
            return false;

        $return = $return->scalarMultiply(1 / $det);
        $return->transpose();
        return $return;
    }

    /**
     * @return Matrix
     */
    public function transpose()
    {
        $return = [];
        for ($i = 0; $i < $this->rows(); $i++) {
            for ($j = 0; $j < $this->columns(); $j++) {
                $return[$j][$i] = $this->get($i, $j);  // swap $i and $j
            }
        }

        return new Matrix($return);
    }

    public function determinant()
    {
        $return = 0;
        if ($this->columns() == 1)
            return $this->get(0, 0);

        for ($i = 0; $i < $this->columns(); $i++) {
            // instead of using 0 here, we can probably do this more efficiently.
            $cofactor = $this->getCofactorMatrix(0, $i);
            $multipland = (pow((-1), $i) * $this->get(0, $i));
            $return += $cofactor->determinant() * $multipland;
        }
        return $return;
    }


    public function subtract(Matrix $matrix)
    {
        if (is_array($matrix))
            $matrix = new Matrix($matrix);

        if ($this->rows() != $matrix->rows() || $this->columns() != $matrix->columns())
            return false;  // impossible operation.

        $return = [];
        for ($i = 0; $i < $this->rows(); $i++) {
            for ($j = 0; $j < $this->columns(); $j++) {
                $return[$i][$j] = $this->get($i, $j) - $matrix->get($i, $j);
            }
        }
        return new Matrix($return);
    }

    public function add(Matrix $matrix)
    {
        if (is_array($matrix))
            $matrix = new Matrix($matrix);

        if ($this->rows() != $matrix->rows() || $this->columns() != $matrix->columns())
            return false;  // impossible operation.

        $return = [];
        for ($i = 0; $i < $this->rows(); $i++) {
            for ($j = 0; $j < $this->columns(); $j++) {
                $return[$i][$j] = $this->get($i, $j) + $matrix->get($i, $j);
            }
        }
        return new Matrix($return);
    }

    public function scalarMultiply($value)
    {
        $return = [];
        for ($i = 0; $i < $this->rows(); $i++) {
            for ($j = 0; $j < $this->columns(); $j++) {
                $return[$i][$j] = $this->get($i, $j) * $value;
            }
        }
        return new Matrix($return);
    }


    /**
     * @param $array
     * @return Matrix
     */
    private function rebuild($array)
    {
        $return = [];

        $tiles_width = count($array);
        $tiles_height = count($array[0]);
        for ($n = 0; $n < $tiles_width; $n++) {
            for ($m = 0; $m < $tiles_height; $m++) {

                if (is_array($array[$n][$m])) {  // if we have an array, we'll "flatten" the tiles.
                    $division_width = count($array[$n][$m]);
                    $division_height = count($array[$n][$m][$i]);

                    for ($i = 0; $i < $division_width; $i++) {
                        for ($j = 0; $j < $division_height; $j++) {
                            $destination_n = $n * $division_width + $i;
                            $destination_m = $m * $division_height + $j;

                            $return[$destination_n][$destination_m] = $array[$n][$m][$i][$j];
                        }
                    }
                } else if (is_a($array[$n][$m], "Matrix")) {
                    $division_width = $array[$n][$m]->columns();
                    $division_height = $array[$n][$m]->rows();

                    for ($i = 0; $i < $division_width; $i++) {
                        for ($j = 0; $j < $division_height; $j++) {
                            $destination_n = $n * $division_width + $i;
                            $destination_m = $m * $division_height + $j;

                            $return[$destination_n][$destination_m] = $array[$n][$m]->get($i, $j);
                        }
                    }
                }
            }
        }

        return new Matrix($return);
    }

    private function subdivide(Matrix $matrix, $n_wide, $m_tall)
    {
        $per_width = $this->columns() / $n_wide;
        $per_height = $this->rows() / $m_tall;

        if (
            (float)$per_width != (float)round($per_width)
            ||
            (float)$per_height != (float)round($per_height)
        )
            return false;  // impossible operation

        $return = [];   // an n x m dimensional array of arrays $per_width by $per_height
        for ($n = 0; $n < $n_wide; $n++) {
            for ($m = 0; $m < $m_tall; $m++) {
                for ($i = 0; $i < $per_width; $i++) {
                    for ($j = 0; $j < $per_height; $j++) {
                        $return[$n][$m][$i][$j] = $matrix->get($i + ($n * $per_width), $j + ($m * $per_height));
                    }
                }
            }
        }

        return $return;
    }

    /**
     * @param Matrix $matrix
     * @return Matrix|false
     */
    public function multiply(Matrix $matrix)
    {
        return $this->strassenMultiply($matrix);
    }

    /**
     * there's a VERY good chance that the naïve implementation is actually faster than Strassen's algorithm
     * expected to be O(2^log(7)) or so.
     * @param Matrix $matrix
     * @return Matrix|false
     */
    public function strassenMultiply(Matrix $matrix)
    {
        // impossible operation
        if ($this->columns() !== $matrix->rows()) {
            return false;
        }

        // threshold for just doing regular multiply
        if ($this->columns() < 32) {
            return $this->naiveMultiply($matrix);
        }

        // get these from $this.
        $subdivisions = $this->subdivide($this, 2, 2);
        if ($subdivisions === false) {
            return $this->naiveMultiply($matrix);
        }

        $a11 = new Matrix($subdivisions[0][0]);
        $a12 = new Matrix($subdivisions[0][1]);
        $a21 = new Matrix($subdivisions[1][0]);
        $a22 = new Matrix($subdivisions[1][1]);

        // get these from $matrix
        $subdivisions = $this->subdivide($matrix, 2, 2);
        if ($subdivisions === false) {
            // fall back on naïve if even subdivide isn't possible.
            return $this->naiveMultiply($matrix);
        }

        $b11 = new Matrix($subdivisions[0][0]);
        $b12 = new Matrix($subdivisions[0][1]);
        $b21 = new Matrix($subdivisions[1][0]);
        $b22 = new Matrix($subdivisions[1][1]);

        // intermediaries
        /*
           M1 = (A11 + A22) (B11 + B22)
           M2 = (A21 + A22) B11
           M3 = A11 (B12 – B22)
           M4 = A22 (B21 – B11)
           M5 = (A11 + A12) B22
           M6 = (A21 – A11) (B11 + B12) M7 = (A12 – A22) (B21 + B22)
         */

        $m1_1 = ($a11->add($a22));
        $m1_2 = ($b11->add($b22));
        $m1 = $m1_1->strassenMultiply($m1_2);
        unset($m1_1);
        unset($m1_2);

        $m2_1 = $a21->add($a22);
        $m2 = $m2_1->strassenMultiply($b11);
        unset($m2_1);

        $m3_1 = $b12->subtract($b22);
        $m3 = $a11->strassenMultiply($m3_1);
        unset($m3_1);

        $m4_1 = $b21->subtract($b11);
        $m4 = $a22->strassenMultiply($m4_1);

        $m5_1 = $a11->add($a12);
        $m5 = $m5_1->strassenMultiply($b22);

        $m6_1 = $a21->subtract($a11);
        $m6_2 = $b11->add($b12);
        $m6 = $m6_1->strassenMultiply($m6_2);

        $m7_1 = $a12->add($a22);
        $m7_2 = $b21->add($b22);
        $m7 = $m7_1->strassenMultiply($m7_2);

        // result
        $c11 = $m1->add($m1);
        $c11 = $c11->add($m4);
        $c11 = $c11->subtract($m5);
        $c11 = $c11->add($m7);

        $c12 = $m3->add($m5);
        $c21 = $m2->add($m4);

        $c22 = $m1->subtract($m2);
        $c22 = $c22->add($m3);
        $c22 = $c22->add($m6);

        $result = array(array($c11, $c12), array($c21, $c22));
        return $this->rebuild($result);
    }

    // naïve implementation... O(n^3) or worse because of the new Matrix calls.
    public function naiveMultiply(Matrix $matrix)
    {
        if (is_array($matrix))   // make sure the matrix is an Matrix
        {
            $matrix = new Matrix($matrix);
        }
        if ($this->columns() != $matrix->rows())  // impossible operation.
            return false;

        $result = [];
        for ($a = 0; $a < $this->rows(); $a++)   // our rows
        {
            for ($b = 0; $b < $matrix->columns(); $b++) // their columns
            {
                $result[$a][$b] = 0;
                for ($i = 0; $i < $this->columns(); $i++)   // our columns
                {
                    $result[$a][$b] += ($this->get($a, $i) * $matrix->get($i, $b));
                }
            }
        }

        return new Matrix($result);
    }

    public function getCofactorMatrix($cofactorRow, $cofactorColumn)
    {
        $return = [];
        for ($i = 0, $a = 0; $i < $this->rows(); $i++) {
            $b = 0;
            if ($i != $cofactorRow) {
                for ($j = 0; $j < $this->columns(); $j++) {
                    if ($j != $cofactorColumn) {
                        $return[$a][$b++] = $this->get($i, $j);
                    }
                }
                $a++;
            }

        }
        return new Matrix($return);
    }

    public function get($row, $column)
    {
        return $this->data[$row][$column];
    }

    public function set($row, $column, $value)
    {
        return $this->data[$row][$column] = $value;
    }

    public function columns()
    {
        return count($this->data[0]);
    }

    public function rows()
    {
        return count($this->data);
    }

    public function getData()
    {
        return $this->data;
    }

    private function setData($array)
    {
        foreach ($array as $row => $vector) {
            if (is_array($vector)) {
                foreach ($vector as $col => $cell) {
                    $this->data[$row][$col] = $cell;
                }
            } else {
                $this->data[$row][0] = $vector;
            }
        }

        return $this->data;
    }

    public function __toString()
    {
        $string = '';
        for ($i = 0; $i < $this->rows(); $i++) {
            for ($j = 0; $j < $this->columns(); $j++) {
                $string .= $this->get($i, $j) . " ";
            }
            $string .= PHP_EOL;
        }
        return $string;
    }
}
