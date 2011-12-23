<?php
/* kmeans clustering
 *
 * kmeans clustering works in three discrete steps; our goal is to break data in to
 * the k best possible ("most different"#) groups. the first step is to initialize the centroids to some
 * random points (we initialize to random points that exist in the dataset, as this
 * appears to be the best way to do it -- but you could change the _ll_init_centroids
 * function to behave differently). the second step is to assign all data points to
 * "belong" to a centroid, then the third step is to move the centroids to the average
 * of all the data points that belong to it.
 *
 * the second and third steps are repeated until the centroids have stopped moving in
 * our implementation.
 *
 * # while the goal is "most different", in fact there's the possibility of being
 * trapped in local minima. the random nature of our initialization means that if you
 * apply it multiple times, you may get different results. a score could be calculated
 * for each result (based on sum of squared distances between $xs and centroids), but
 * we have not yet provided a function to do that.
 */

require_once dirname(__FILE__) . "/../accessory/functions.php";

// $xs is a two dimensional array (one dimension being observations, the inner dimension being integral "dimensions" of the observation)
function ll_kmeans($xs, $k)
{
   if($k > count($xs))
      return false;

   $centroids = _ll_init_centroids($xs, $k);
   $belongs_to = array();
   do {
      for($i=0;$i<count($xs);$i++)
      {
         // I reversed the order here (to store the centroids as indexes in the array)
         // for complexity reasons.

         $belongs_to[_ll_closest_centroid($xs[$i], $centroids)][] = $i;
      }

      $old_centroids = $centroids;
      $centroids = _ll_reposition_centroids($centroids, $belongs_to, $xs);

      $continue = ($old_centroids == $centroids);
   } while($continue);

   return $belongs_to;
}

// repositions the centroids to the average of all their member elements
function _ll_reposition_centroids($centroids, $belongs_to, $xs)
{
   for($index=0; $index<count($centroids); $index++)
   {
      $my_observations = $belongs_to[$index];
      $my_obs_values = array();
      foreach($my_observations as $obs)
      {
         $my_obs_values[] = $xs[$obs];
      }
      $my_obs_values = __ll_flip($my_obs_values);

      $new_position = array();
      foreach($my_obs_values as $new_dimension)
      {
         // compute the average of all the observation's positions for the centroids new position.
         $new_position[] = array_sum($new_dimension) / count($new_dimension);
      }

      $centroids[$index] = $new_position;
   }
   return $centroids;
}

// makes rows in to columns; columns in to rows. "transposes" the matrix.
function __ll_flip($rows)
{
   return ll_transpose($rows);
}

// finds the closest centroid to a given $x, by Euclidian distance.
function _ll_closest_centroid($x, $centroids)
{
   $smallest = null;
   $smallest_distance = PHP_INT_MAX;
   foreach($centroids as $index=>$centroid)
   {
      $distance = __ll_distance_to_centroid($x, $centroid);
      if($distance < $smallest_distance)
      {
         $smallest = $index;
         $smallest_distance = $distance;
      }
   }
   return $smallest;
}

// computes the Euclidian distance from a point x to a centroid $centroid.
function __ll_distance_to_centroid($x, $centroid)
{
   return ll_euclidian_distance($x, $centroid);
}

// initializes the location of the centroids to a random data point.
function _ll_init_centroids($xs, $k)
{
   if($k > count($xs))
      return false;

   $centroids = array();
   for($i=0;$i<$k;$i++)
   {
      $temp_array = array();
      $random = rand(0, count($xs)-1); // random row from data.
      $temp_array = $xs[$random];
      unset($xs[$random]); // don't allow the same centroid to be set twice.
      $xs = array_values($xs);   // renumber the array

      $centroids[] = $temp_array;
   }

   return $centroids;
}

?>