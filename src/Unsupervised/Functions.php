<?php

namespace Giuseppe\LearningLibrary\Unsupervised;

use Giuseppe\LearningLibrary\Accessory;

class Functions
{
    /**
     * @var Accessory\Functions
     */
    private $helper;

    public function __construct()
    {
        $this->helper = new Accessory\Functions();
    }

    public function kmeans($xs, $k)
    {
        if ($k > count($xs))
            return false;

        $centroids = $this->init_centroids($xs, $k);
        $belongs_to = array();
        do {
            for ($i = 0; $i < count($xs); $i++) {
                // I reversed the order here (to store the centroids as indexes in the array)
                // for complexity reasons.

                $belongs_to[$this->closest_centroid($xs[$i], $centroids)][] = $i;
            }

            $old_centroids = $centroids;
            $centroids = $this->reposition_centroids($centroids, $belongs_to, $xs);

            $continue = ($old_centroids == $centroids);
        } while ($continue);

        return $belongs_to;
    }

    /**
     * repositions the centroids to the average of all their member elements
     */
    public function reposition_centroids($centroids, $belongs_to, $xs)
    {
        for ($index = 0; $index < count($centroids); $index++) {
            $my_observations = $belongs_to[$index];
            $my_obs_values = array();
            foreach ($my_observations as $obs) {
                $my_obs_values[] = $xs[$obs];
            }
            $my_obs_values = $this->flip($my_obs_values);

            $new_position = array();
            foreach ($my_obs_values as $new_dimension) {
                // compute the average of all the observation's positions for the centroids new position.
                $new_position[] = array_sum($new_dimension) / count($new_dimension);
            }

            $centroids[$index] = $new_position;
        }
        return $centroids;
    }

    /**
     * makes rows in to columns; columns in to rows. "transposes" the matrix.
     * @param $rows
     * @return array
     */
    public function flip($rows)
    {
        return $this->helper->transpose($rows);
    }

    /**
     * finds the closest centroid to a given $x, by Euclidian distance.
     * @param $x
     * @param $centroids
     * @return int|null|string
     */
    public function closest_centroid($x, $centroids)
    {
        $smallest = null;
        $smallest_distance = PHP_INT_MAX;
        foreach ($centroids as $index => $centroid) {
            $distance = $this->distance_to_centroid($x, $centroid);
            if ($distance < $smallest_distance) {
                $smallest = $index;
                $smallest_distance = $distance;
            }
        }
        return $smallest;
    }

    /**
     * computes the Euclidian distance from a point x to a centroid $centroid.
     * @param $x
     * @param $centroid
     * @return bool|float
     */
    public function distance_to_centroid($x, $centroid)
    {
        return $this->helper->euclideanDistance($x, $centroid);
    }

    /**
     * initializes the location of the centroids to a random data point.
     * @param $xs
     * @param $k
     * @return array
     */
    public function init_centroids($xs, $k)
    {
        if ($k > count($xs))
            return false;

        $centroids = array();
        for ($i = 0; $i < $k; $i++) {
            $temp_array = array();
            $random = rand(0, count($xs) - 1); // random row from data.
            $temp_array = $xs[$random];
            unset($xs[$random]); // don't allow the same centroid to be set twice.
            $xs = array_values($xs);   // renumber the array

            $centroids[] = $temp_array;
        }

        return $centroids;
    }
}
