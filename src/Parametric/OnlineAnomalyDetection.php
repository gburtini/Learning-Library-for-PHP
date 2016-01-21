<?php

namespace Giuseppe\LearningLibrary\Parametric;

class OnlineAnomalyDetection extends AnomalyDetection
{
    private $sums;
    private $m2s;
    private $counts;

    protected $toSave = ['sums', 'm2s', 'counts'];

    /*
     * addObservation($x)
     *   - pass in an array $x representing all the dimensions relevant to your observation.
     *   - if you only have one dimension, you don't need to pass it as an array.
     *   - updates mean and variance, and stores the relevant online data.
     *   - observations do not have to be non-anomalies, you can addObservation for all seen observations
     *     - this is probably the best way to use the anomaly detector for live data,
     *     as it will allow it to update to changes in what an anomaly is over time.
     *
     * Example Usage:
     *   $ll->addObservation(14);
     */
    public function addObservation($x)
    {
        if (!is_array($x))
            $x = array($x);

        for ($i = 0; $i < count($x); $i++) {
            $this->sums[$i] += $x[$i];
            $this->counts[$i]++;
            $this->m2s[$i] += (pow($x[$i] - $this->mean[$i], 2));

            $this->updateMean($i);
            $this->updateVariance($i);
        }
    }


    protected function computeParameters($xs)
    {
        if (!is_array($xs[0])) {
            $xs = $this->arrayNonArray($xs);
        }

        $xs_columns = $this->helper->transpose($xs);
        foreach ($xs_columns as $index => $column) {
            $this->sums[$index] = $sum = array_sum($column);
            $this->counts[$index] = $count = count($column);

            $mean = $this->helper->mean($column);
            $sum_difference = 0;
            $n = count($column);

            for ($i = 0; $i < $n; $i++) {
                $sum_difference += pow(($column[$i] - $mean), 2);
            }

            $this->m2s[$index] = $sum_difference;


            $this->updateMean($index);
            $this->updateVariance($index);
        }
    }

    protected function updateMean($index)
    {
        $this->mean[$index] = $this->sums[$index] / $this->counts[$index];
    }

    protected function updateVariance($index)
    {
        $this->variance[$index] = $this->m2s[$index] / $this->counts[$index];
    }
}