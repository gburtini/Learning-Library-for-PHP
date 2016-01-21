<?php

/*
 * Anomaly Detection Classes
 * This could be easily modified to use any reasonable distribution, but as it is implemented
 * below, only supports the Gaussian (normal) distribution by changing computeProbabilities.
 * Recommended to subclass if you're going to do that.
 *
 * Thoughts: if you have an n-modal distribution (say bimodal), can you use k-means
 * clustering to compute multiple anomaly detectors (one around each mode) and sum/f(x,y) their
 * probabilities? maybe not because this would chop the edges of the distribution off.
 */

namespace Giuseppe\LearningLibrary\Parametric;

// online anomaly detector, can addObservations instead of only training once.
use Giuseppe\LearningLibrary\Accessory\Functions;

// main implementation class
class AnomalyDetection
{
    /**
     * @var Functions
     */
    protected $helper;

    public function __construct()
    {
        $this->helper = new Functions();
    }

    protected $mean = array();
    protected $variance = array();

    private $learned = false;

    /*
     * learn($xs)
     *   - pass in an array of arrays (or an array of data points). each array represents the dimensions of the data
     *   being studied.
     *   - calculates all the appropriate parameters for computing anomalies.
     *
     * Example Usage:
     *   $ll = new AnomalyDetection();
     *   $ll->learn(array(1, 2, 3, 4, 5, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 1, 1));
     *   $ll->isAnomaly(100);  // bool(true)
     *   $ll->isAnomaly(3);    // bool(false)
     */
    public function learn($xs)
    {
        $this->computeParameters($xs);
        $this->learned = true;
    }

    /*
     * isAnomaly($data_point, $p=0.01);
     *   - pass in data_point as an array of dimensions (or a single observation) and will return true or false if it is an anomaly
     *   - define an anomaly as something that has less than $p chance of occurring, given the distribution.
     *   - if you would rather know the $p that a particular observation has of occuring, pass $p=false.
     *
     * Example Usage:
     *   $ll->isAnomaly(array(144,100,200));
     *   $p_value = $ll->isAnomaly(array(1,2,3), false);
     */
    public function isAnomaly($data_point, $p = 0.01)
    {
        if (!$this->learned)
            return null;

        $probability = $this->computeProbability($data_point);
        if ($p === false)
            return $probability;

        return ($probability < $p);
    }


    protected $toSave = array("mean", "variance");

    public function save()
    {
        $saveString = "";
        foreach ($this->toSave as $save) {
            $saveString .= implode(",", $this->$save) . "|";
        }
        $s_learned = intval($this->learned);

        return $saveString . $s_learned;
    }

    public function load($saveString)
    {
        $saveArray = explode("|", $saveString);
        if (count($saveArray) != count($this->toSave) - 1)
            return false;

        foreach ($this->toSave as $key => $load) {
            $this->$load = explode(",", $saveArray[$key]);
        }

        $this->learned = (bool)end($saveArray);
    }

    protected function computeProbability($x)
    {
        if (!is_array($x))
            $x = array($x);

        $prod = 1;
        foreach ($x as $index => $xi) {
            $prob = $this->normalPdf($xi, $this->mean[$index], $this->variance[$index]);
            $prod *= $prob;
        }
        return $prod;
    }

    protected function normalPdf($x, $mean, $variance)
    {
        return (1 / (sqrt(2 * pi()) * sqrt($variance)) * (exp((-1) * pow($x - $mean, 2) / (2 * $variance))));
    }


    protected function computeParameters($xs)
    {
        if (!is_array($xs[0])) {
            $xs = $this->arrayNonArray($xs);
        }

        $xs_columns = $this->helper->transpose($xs);
        foreach ($xs_columns as $index => $column) {
            $this->mean[$index] = $this->helper->mean($column);
            $this->variance[$index] = $this->helper->variance($column);
        }
    }

    protected function arrayNonArray($array)
    {
        $return = array();
        foreach ($array as $item) {
            $return[] = array($item);
        }
        return $return;
    }
}

?>
