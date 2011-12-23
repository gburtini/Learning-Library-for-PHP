<?php

require_once dirname(__FILE__) . "/../accessory/functions.php";


// returns the predictions (sorted array) in an array ("bestprediction"=>count, "pred2"=>count2...)
function ll_nn_predict($xs, $ys, $row, $k)
{
   $distances = ll_nearestNeighbors($xs, $row);
   $distances = array_slice($distances, 0, $k); // get top k.

   $predictions = array();
   foreach($distances as $neighbor=>$distance)
   {
      $predictions[$ys[$neighbor]]++;
   }
   asort($predictions);

   return $predictions;
}

// returns the nearest neighbors for the nth row of $xs (sorted euclidian distances).
function ll_nearestNeighbors($xs, $row) {
   $testPoint = $xs[$row];

   $distances = _ll_distances_to_point($xs, $testPoint);
   return $distances;
}

function _ll_distances_to_point($xs, $x) {
   $distances = array();
   foreach($xs as $index=>$xi) {
      $distances[$index] = ll_euclidian_distance($xi, $x);
   }
   asort($distances);
   array_shift($distances);   // has "self" as the smallest distance.
   return $distances;
}

?>