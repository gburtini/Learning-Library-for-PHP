<?php

namespace Giuseppe\LearningLibrary\Unsupervised;

use Giuseppe\LearningLibrary\Accessory;

class KNN
{
    /**
     * @var Accessory\Functions
     */
    private $helper;

    public function __construct()
    {
        $this->helper = new Accessory\Functions();
    }


// returns the predictions (sorted array) in an array ("bestprediction"=>count, "pred2"=>count2...)
    public function nn_predict($xs, $ys, $row, $k)
    {
        $distances = $this->nearestNeighbors($xs, $row);
        $distances = array_slice($distances, 0, $k); // get top k.

        $predictions = array();
        foreach ($distances as $neighbor => $distance) {
            $predictions[$ys[$neighbor]]++;
        }
        asort($predictions);

        return $predictions;
    }

// returns the nearest neighbors for the nth row of $xs (sorted euclidian distances).
    public function nearestNeighbors($xs, $row)
    {
        $testPoint = $xs[$row];

        $distances = $this->distances_to_point($xs, $testPoint);
        return $distances;
    }

    public function distances_to_point($xs, $x)
    {
        $distances = array();
        foreach ($xs as $index => $xi) {
            $distances[$index] = $this->helper->euclideanDistance($xi, $x);
        }
        asort($distances);
        array_shift($distances);   // has "self" as the smallest distance.
        return $distances;
    }
}
