<?php

namespace Giuseppe\LearningLibrary\Accessory;

class Functions
{
    public function sign($n)
    {
        return ($n > 0) - ($n < 0);
    }

    public function transpose($rows)
    {
        $columns = array();
        for ($i = 0; $i < count($rows); $i++) {
            for ($k = 0; $k < count($rows[$i]); $k++) {
                $columns[$k][$i] = $rows[$i][$k];
            }
        }
        return $columns;
    }

    public function mean($array)
    {
        return array_sum($array) / count($array);
    }

    public function variance($array)
    {
        $mean = $this->mean($array);

        $sum_difference = 0;
        $n = count($array);

        for ($i = 0; $i < $n; ++$i) {
            $sum_difference += pow(($array[$i] - $mean), 2);
        }

        return $sum_difference / $n;
    }

    public function euclideanDistance($a, $b)
    {
        $count = count($a);
        if ($count !== count($b)) {
            return false;
        }

        $distance = 0;
        for ($i = 0; $i < $count; ++$i) {
            $distance += pow($a[$i] - $b[$i], 2);
        }

        return sqrt($distance);
    }

    public function unirandf()
    {
        return mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax();
    }
}
