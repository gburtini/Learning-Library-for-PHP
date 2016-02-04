<?php

namespace Giuseppe\LearningLibrary\Accessory;

class Functions
{
    public function sign($n)
    {
        return ($n > 0) - ($n < 0);
    }

    public function transpose(array $source)
    {
        $columns = [];
        $rowCount = count($source);
        for ($i = 0; $i < $rowCount; ++$i) {
            $columnCount = count($source[$i]);
            for ($k = 0; $k < $columnCount; ++$k) {
                $columns[$k][$i] = $source[$i][$k];
            }
        }
        return $columns;
    }

    public function mean($array)
    {
        return array_sum($array) / count($array);
    }

    public function variance(array $array)
    {
        $mean = $this->mean($array);

        $sum_difference = 0;
        $n = count($array);

        for ($i = 0; $i < $n; ++$i) {
            $sum_difference += pow($array[$i] - $mean, 2);
        }

        return $sum_difference / $n;
    }

    public function euclideanDistance(array $a, array $b)
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

    // Itearted Mann-Whitney. Takes a $list of values and a threshold (as a fraction of values) and returns a list of changepoints according to utilizing the Mann Whitney (rank-sum) statistic. See Pettitt, 1979. The data should be detrended before being specified as $list.
    public function iteratedMannWhitney($list, $threshold)
    {
        $changepoints = array();
        for ($i = 0; $i < count($list); $i++) {
            $sum = $counts = 0;
            for ($j = $i; $j < count($list); $j++) {
                for ($k = 0; $k < $i; $k++) {
                    $sum += $this->sign($list[$k] - $list[$j]);
                    $counts++;
                }
            }
            if (abs($sum) / $counts > $threshold)
                $changepoints[] = $i;
        }
        return $changepoints;
    }

    // Page-Hinkley test (found most predominantly in Mouss et al. 2004). $alpha represents the minimum detected amplitude and $lambda equals the probabilistic false alarm rate. According to Ikonomovska (2012), $alpha should be proportional to the true standard deviation of the data, and experimental results show that setting it equal to 0.1 * \sigma works out ideally. Returns a single true or false for whether we're detecting a changepoint or not.
    public function PageHinkley($list, $alpha, $lambda)
    {
        return $this->_PageHinkleyStatistic($list, count($list), $alpha) > $lambda;
    }

    public function _PageHinkleyStatistic($list, $t, $alpha, $mult = 1)
    {
        // the statistic should have a zero value under the null hypothesis (that a changepoint has not occurred).

        $used_list = array_slice($list, 0, $t);
        $mean = $this->mean($used_list);

        $sum = 0;
        $min = -INF;
        for ($i = 0; $i < $t; $i++) {
            $sum += ($used_list[$i] - $mean - $alpha) * $mult;
        }
        return $sum - $min;
    }


    // Ikonomovska (2012)'s improvement to the P-H test, letting $alpha be an online representation of the standard deviation.
    public function improvedPageHinkley($list, $lambda)
    {
        $alphaPH = function ($t) {
            $used_list = array_slice($list, 0, $t);
            return sqrt($this->variance($used_list));
        };

        $mean = $this->mean($used_list);
        return $this->_PageHinkleyStatistic($list, count($list), $this->sign($list[count($list) - 1] - $mean), $alphaPH(count($list)) / 2) > $lambda;
    }

    // NOTE: Hartland et al. (2006)'s adaptive version of Page-Hinkley for the bandits context involves updating $lambda = $lambda * $e where $e is set according to whether (ex post) the alarm was false or not, weighted by two parameters (true alarm, having a best value of -10^-4; false alarm, having a best value of 10^-2) times the difference in the best and second best option known (arms, for the bandit context).
}
