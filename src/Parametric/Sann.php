<?php

namespace Giuseppe\LearningLibrary\Parametric;

use Giuseppe\LearningLibrary\Accessory;

class Sann
{
    /**
     * @var Accessory\Functions
     */
    private $helper;

    public function __construct()
    {
        $this->helper = new Accessory\Functions();
    }

    ## Default annealing functions #################################################
    public function linear_cooling($t, $max = 10, $subratio = 0.05)
    {
        return $max - ($subratio * $t);
    }

    public function exponential_cooling($t, $maxt = 1000, $initT = 10, $alpha = 0.9)
    {
        if ($t >= $maxt)
            return 0;
        else
            return $initT * pow($alpha, $t);
    }

    public function unbounded_exponential_cooling($t, $initT = 10, $alpha = 0.99, $delta = 0.0001)
    {
        $T = $initT * pow($alpha, $t);

        return ($T < $delta) ? 0 : $T;
    }

    public function logarithmic_cooling($t, $maxt = 151, $c = 2.0)
    {
        if ($t >= $maxt) {
            return 0;
        } else {
            $return = $c / (log($t + 1, 2) + 1);
            return $return;
        }
    }

    public function default_accept($T, $currVal, $candVal, $norm)
    {
        $delta = $norm * ($candVal - $currVal);

        // If the new score is better than the old score.
        if ($delta > 0)
            return true;

        // Randomly accept new worse solutions.
        $p = exp(-1 * $delta / $T);
        $q = $this->helper->unirandf();
        if ($p > $q) {
            return true;
        }

        return false;
    }
################################################################################

## Simulated Annealing Algorithms ##############################################
// Discrete Simulated annealing.
// $nF returns a randomly selected neighbour of next.
// $currS passed in as initial state to start from.
    public function sann($currS, $objF, $nF, $coolingF, $acceptF, $minimize = true)
    {
        $norm = 1;
        if ($minimize) {
            $norm = -1;
        }

        $currVal = $objF($currS);
        $bestS = $currS;
        $bestVal = $currVal;
        $t = 0;

        while (($T = $coolingF($t)) > 0) {
            $candidateS = $nF($currS);
            $candVal = $objF($currS);
            if ($acceptF($T, $currVal, $candVal,
                $norm)) {
                $currS = $candidateS;
                $currVal = $candVal;

                if ($norm * ($candVal - $bestVal) > 0) {
                    $bestVal = $candVal;
                    $bestS = $candidateS;
                }
            }
            $t++;
        }
        return $bestS;
    }
}
